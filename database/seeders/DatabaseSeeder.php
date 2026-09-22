<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UniversitySeeder::class,
            UserSeeder::class,
            SupervisorSeeder::class,
            StudentSeeder::class,
            ResourceSeeder::class,
            ProposalSeeder::class,
            MeetingLogSeeder::class,
            StageHistorySeeder::class,
        ]);
    }
}
