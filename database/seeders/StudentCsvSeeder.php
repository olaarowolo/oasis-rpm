<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class StudentCsvSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('import:students', [
            '--csv' => database_path('seeders/students_data.csv'),
            '--force' => true,
        ]);

        $this->command->info(Artisan::output());
    }
}
