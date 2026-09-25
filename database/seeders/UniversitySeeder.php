<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = [
            [
                'name' => 'Lagos State University',
                'code' => 'LASU',
                'email' => 'research@lasu.edu.ng',
                'phone' => '+234 (0)1 123-4567',
                'logo_url' => null,
                'branding_color' => '#003366',
                'ai_model_config' => [
                    'primary' => 'gemini-pro',
                    'fallback' => 'gpt-4-turbo',
                    'timeout' => 30,
                ],
                'email_config' => [
                    'from_address' => 'research@lasu.edu.ng',
                    'from_name' => 'LASU Research Portal',
                    'reply_to' => 'support@lasu.edu.ng',
                ],
                'google_chat_webhook_url' => null,
                'features_enabled' => [
                    'ai_assistant' => true,
                    'google_chat' => true,
                    'analytics' => true,
                    'resource_tracking' => true,
                ],
                'has_structured_departments' => true,
            ],
            [
                'name' => 'University of Ibadan',
                'code' => 'UI',
                'email' => 'research@ui.edu.ng',
                'department' => 'Academic Affairs',
                'phone' => '+234 (0)2 123-4567',
                'logo_url' => null,
                'branding_color' => '#004B87',
                'ai_model_config' => [
                    'primary' => 'gemini-pro',
                    'fallback' => 'gpt-4-turbo',
                    'timeout' => 30,
                ],
                'email_config' => [
                    'from_address' => 'research@ui.edu.ng',
                    'from_name' => 'UI Research Portal',
                    'reply_to' => 'support@ui.edu.ng',
                ],
                'google_chat_webhook_url' => null,
                'features_enabled' => [
                    'ai_assistant' => true,
                    'google_chat' => true,
                    'analytics' => true,
                    'resource_tracking' => true,
                ],
                'has_structured_departments' => false,
            ],
        ];

        foreach ($universities as $data) {
            $hasStructured = $data['has_structured_departments'] ?? false;

            $university = University::updateOrCreate(
                ['code' => $data['code']],
                $data
            );

            if ($hasStructured && $data['code'] === 'LASU') {
                $this->seedLasuDepartments($university);
            }
        }
    }

    protected function seedLasuDepartments(University $university): void
    {
        Department::where('university_id', $university->id)->delete();

        $config = config('lasu_departments', []);

        $sources = [
            'faculties' => $config['faculties'] ?? [],
            'schools_and_directorates' => $config['schools_and_directorates'] ?? [],
        ];

        foreach ($sources as $sectionKey => $units) {
            $unitType = $sectionKey === 'faculties' ? 'faculty' : 'school';

            foreach ($units as $unitKey => $unit) {
                $facultyName = $unit['name'] ?? $unitKey;

                foreach ($unit['departments'] ?? [] as $deptKey => $deptName) {
                    Department::create([
                        'university_id' => $university->id,
                        'name' => $deptName,
                        'code' => $deptKey,
                        'faculty' => $facultyName,
                        'unit_type' => $unitType,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
