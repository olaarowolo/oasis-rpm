<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Supervisor::truncate();
        Schema::enableForeignKeyConstraints();

        // olaarowolo.ng@gmail.com - NG Supervisor
        $this->createSupervisorProfile(
            'olaarowolo.ng@gmail.com',
            'Lecturer & Research Supervisor',
            'Dept. of Journalism & Media Studies',
            'Journalism & Media Studies, Digital Media, Communication Studies',
            'https://calendar.app.google/7hf8cS6m4yFj9f9y6',
            'LASU-Arowolo-2026'
        );

        // olaarowolo.uk@gmail.com - UK Supervisor (separate entity)
        $this->createSupervisorProfile(
            'olaarowolo.uk@gmail.com',
            'Research Supervisor',
            'UK Research Centre',
            'Research Methods, Academic Writing, Graduate Studies',
            'https://calendar.app.google/uk-booking-link',
            'UK-Arowolo-2026'
        );
    }

    protected function createSupervisorProfile(string $email, string $title, string $department, string $researchAreas, string $bookingUrl, string $passphrase): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return;
        }

        Supervisor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'university_id' => $user->university_id,
                'title' => $title,
                'department' => $department,
                'research_areas' => $researchAreas,
                'booking_url' => $bookingUrl,
                'pin_code' => Hash::make('2026'),
                'passphrase' => Hash::make($passphrase),
                'is_active' => true,
            ]
        );
    }
}
