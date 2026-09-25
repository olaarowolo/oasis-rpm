<?php

namespace Tests\Feature;

use App\Mail\PortalEmail;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupervisorStudentBulkImportTest extends TestCase
{
    use RefreshDatabase;

    private function supervisorSession(University $university, User $user, Supervisor $supervisor)
    {
        return $this->withSession([
            'user_id' => $user->id,
            'role' => 'supervisor',
            'supervisor_id' => $supervisor->id,
            'university_id' => $university->id,
            'last_activity' => time(),
            'session_started' => time(),
        ]);
    }

    private function makeLasuSupervisor(University $university, string $email, string $name, int $pin = 2024): User
    {
        $user = User::create([
            'university_id' => $university->id,
            'email' => $email,
            'password' => bcrypt('Supervisor@2026'),
            'name' => $name,
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        Supervisor::create([
            'user_id' => $user->id,
            'university_id' => $university->id,
            'title' => 'Dr.',
            'department' => 'Computer Science',
            'research_areas' => 'Software Engineering',
            'pin_code' => bcrypt((string) $pin),
            'passphrase' => bcrypt('SWU-Supervisor-2026'),
            'is_active' => true,
        ]);

        return $user;
    }

    public function test_template_download_returns_csv_with_header(): void
    {
        Mail::fake();
        $university = University::create([
            'name' => 'Lagos State University',
            'code' => 'LASU',
            'email' => 'info@lasu.edu.ng',
            'department' => 'Research',
            'phone' => '08010000000',
            'has_structured_departments' => true,
        ]);

        $user = $this->makeLasuSupervisor($university, 'supervisor@lasu.edu', 'Lead Supervisor');
        $supervisor = $user->supervisor;

        $response = $this->supervisorSession($university, $user, $supervisor)
            ->get('/supervisor/students/import/template');

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8')
            ->assertHeader('Content-Disposition', 'attachment; filename="student_bulk_import_template.csv"')
            ->assertSee('full_name,lastname,matric_number,email,degree_level', false);
    }

    public function test_csv_import_creates_students_with_faculty_department_and_programme(): void
    {
        Mail::fake();
        $university = University::create([
            'name' => 'Lagos State University',
            'code' => 'LASU',
            'email' => 'info@lasu.edu.ng',
            'department' => 'Research',
            'phone' => '08010000000',
            'has_structured_departments' => true,
        ]);

        $ownerUser = $this->makeLasuSupervisor($university, 'lead@lasu.edu', 'Lead Supervisor');
        $ownerSupervisor = $ownerUser->supervisor;

        $secondUser = $this->makeLasuSupervisor($university, 'other@lasu.edu', 'Other Supervisor', 2025);
        $otherSupervisor = $secondUser->supervisor;

        // Pre-existing student whose matric will collide with a CSV row.
        $existingUser = User::create([
            'university_id' => $university->id,
            'email' => 'existing@lasu.edu',
            'password' => bcrypt('Existing123!'),
            'name' => 'Existing Student',
            'role' => 'student',
        ]);
        Student::create([
            'user_id' => $existingUser->id,
            'university_id' => $university->id,
            'supervisor_id' => $ownerSupervisor->id,
            'matric_number' => 'CS/2020/001',
            'lastname' => 'Existing',
            'full_name' => 'Existing Student',
            'email' => 'existing@lasu.edu',
            'degree_level' => 'BSc',
        ]);

        $csv = "full_name,lastname,matric_number,email,degree_level,phone,supervisor_email,faculty,department,programme,research_topic\n"
            ."Adewale,Ogunsiji,CS/2024/001,adewale@lasu.edu,BSc,08030000001,,science,Department of Computer Science,BSc Computer Science,Machine Learning in Agriculture\n"
            ."Bola,Komolafe,CS/2024/002,bola@lasu.edu,MSc,08030000002,other@lasu.edu,social_sciences,Department of Economics,MSc Economics,Labour Market Dynamics\n"
            ."Tayo,Bad,CS/2024/003,tayo.bad@lasu.edu,XYZ,,,,science,Department of Computer Science,BSc Computer Science,\n"
            ."Chike,Nwankwo,CS/2024/004,chike@lasu.edu,BSc,,,,Fake Faculty,Department of Computer Science,BSc Computer Science,\n"
            ."Duplicate,Entry,CS/2020/001,dup@lasu.edu,BSc,,,science,Department of Computer Science,BSc Computer Science,\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

        $response = $this->supervisorSession($university, $ownerUser, $ownerSupervisor)
            ->post('/supervisor/students/import', ['csv_file' => $file], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.created', 2)
            ->assertJsonPath('data.total', 5)
            ->assertJsonPath('data.duplicates', 1);

        $adewale = Student::where('matric_number', 'CS/2024/001')->first();
        $this->assertNotNull($adewale);
        $this->assertSame('science', $adewale->faculty);
        $this->assertSame('Department of Computer Science', $adewale->department);
        $this->assertSame('BSc Computer Science', $adewale->programme);
        $this->assertSame($ownerSupervisor->id, $adewale->supervisor_id);
        $this->assertSame('active', $adewale->status);
        $this->assertSame('active', $adewale->account_status);

        $bola = Student::where('matric_number', 'CS/2024/002')->first();
        $this->assertNotNull($bola);
        $this->assertSame($otherSupervisor->id, $bola->supervisor_id);
        $this->assertSame('social_sciences', $bola->faculty);

        // Invalid degree_level and unrecognised faculty must be skipped.
        $this->assertNull(Student::where('matric_number', 'CS/2024/003')->first());
        $this->assertNull(Student::where('matric_number', 'CS/2024/004')->first());

        // Per-row errors reported.
        $errors = $response->json('data.errors');
        $this->assertCount(3, $errors);
        $this->assertSame('CS/2024/003', $errors[0]['identifier'] ?? null);
        $this->assertStringContainsString('degree_level', $errors[0]['reason']);

        // A welcome credentials email is delivered for each created student.
        Mail::assertSent(PortalEmail::class, 2);
        Mail::assertSent(PortalEmail::class, function (PortalEmail $mail) use ($adewale) {
            return $mail->hasTo($adewale->email)
                && $mail->viewName === 'student-welcome'
                && data_get($mail->data, 'matric') === $adewale->matric_number
                && data_get($mail->data, 'universityCode') === 'LASU';
        });
    }

    public function test_import_on_non_structured_university_accepts_free_text_programme(): void
    {
        Mail::fake();
        $university = University::create([
            'name' => 'Other University',
            'code' => 'OTH',
            'email' => 'info@oth.edu',
            'department' => 'Research',
            'phone' => '08020000000',
        ]);

        $user = $this->makeLasuSupervisor($university, 'sup@oth.edu', 'Lead', 3030);
        $supervisor = $user->supervisor;

        $csv = "full_name,lastname,matric_number,email,degree_level,phone,supervisor_email,faculty,department,programme,research_topic\n"
            ."Jane,Doe,OTH/2024/010,jane@oth.edu,BSc,08030000110,,Free Text Faculty,Some Department,BSc Narnia Studies,\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

        $response = $this->supervisorSession($university, $user, $supervisor)
            ->post('/supervisor/students/import', ['csv_file' => $file], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('data.created', 1)
            ->assertJsonPath('data.skipped', 0);

        $jane = Student::where('matric_number', 'OTH/2024/010')->first();
        $this->assertNotNull($jane);
        $this->assertSame('BSc Narnia Studies', $jane->programme);
        $this->assertSame($supervisor->id, $jane->supervisor_id);
    }

    public function test_import_rejects_oversized_file(): void
    {
        Mail::fake();
        $university = University::create([
            'name' => 'Lagos State University',
            'code' => 'LASU',
            'email' => 'info@lasu.edu.ng',
            'department' => 'Research',
            'phone' => '08010000000',
            'has_structured_departments' => true,
        ]);

        $user = $this->makeLasuSupervisor($university, 'sup@lasu.edu', 'Lead');
        $supervisor = $user->supervisor;

        $csv = str_repeat('x', 6 * 1024 * 1024);
        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

        $response = $this->supervisorSession($university, $user, $supervisor)
            ->post('/supervisor/students/import', ['csv_file' => $file], ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }
}
