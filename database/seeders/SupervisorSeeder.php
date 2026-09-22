<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing supervisors to ensure clean state
        Supervisor::truncate();

        // Supervisor 1: Dr. Olasunkanmi Arowolo (olaarowolo.ng@gmail.com)
        // LASU - Department of Journalism & Media Studies
        
        Supervisor::create([
            'user_id' => 17,
            'university_id' => 1,
            'title' => 'Dr.',
            'department' => 'Journalism and Media Studies',
            'research_areas' => 'Digital Media, Communication Studies, Journalism',
            'pin_code' => Hash::make('2026'),
            'passphrase' => Hash::make('LASU-Arowolo-2026'),
            'is_active' => true
        ]);

        // Supervisor 2: Dr. Olasunkanmi Arowolo (olaarowolo.uk@gmail.com)
        // LASU - Department of Journalism & Media Studies
        
        Supervisor::create([
            'user_id' => 18,
            'university_id' => 1,
            'title' => 'Dr.',
            'department' => 'Journalism and Media Studies',
            'research_areas' => 'Digital Media, Communication Studies, Journalism',
            'pin_code' => Hash::make('2026'),
            'passphrase' => Hash::make('LASU-Arowolo-2026'),
            'is_active' => true
        ]);
    }
}
