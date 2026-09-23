<?php

namespace Database\Seeders;

use App\Models\StageHistory;
use Illuminate\Database\Seeder;

class StageHistorySeeder extends Seeder
{
    public function run(): void
    {
        // LASU Student 1 progression
        $this->upsertStageHistory([
            'university_id' => 1,
            'student_id' => 1,
            'stage_number' => 1,
            'stage_name' => 'Topic Ideation & Approval',
            'action' => 'entered',
            'note' => 'Student registered',
        ]);

        $this->upsertStageHistory([
            'university_id' => 1,
            'student_id' => 1,
            'stage_number' => 2,
            'stage_name' => 'Research Gap Identification',
            'action' => 'entered',
            'note' => 'Moved from Stage 1',
        ]);

        // LASU Student 2 progression
        for ($i = 1; $i <= 3; $i++) {
            $this->upsertStageHistory([
                'university_id' => 1,
                'student_id' => 2,
                'stage_number' => $i,
                'stage_name' => config('research.stages')[$i - 1]['name'],
                'action' => 'entered',
                'note' => $i === 1 ? 'Student registered' : 'Moved from Stage ' . ($i - 1),
            ]);
        }

        // UI Student 1 progression
        for ($i = 1; $i <= 2; $i++) {
            $this->upsertStageHistory([
                'university_id' => 2,
                'student_id' => 6,
                'stage_number' => $i,
                'stage_name' => config('research.stages')[$i - 1]['name'],
                'action' => 'entered',
                'note' => $i === 1 ? 'Student registered' : 'Moved from Stage ' . ($i - 1),
            ]);
        }
    }

    protected function upsertStageHistory(array $attributes): void
    {
        StageHistory::updateOrCreate(
            [
                'student_id' => $attributes['student_id'],
                'stage_number' => $attributes['stage_number'],
                'action' => $attributes['action'],
            ],
            $attributes
        );
    }
}
