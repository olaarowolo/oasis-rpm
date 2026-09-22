<?php

namespace Database\Factories;

use App\Models\StageHistory;
use App\Models\Student;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class StageHistoryFactory extends Factory
{
    protected $model = StageHistory::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'student_id' => Student::factory(),
            'stage_number' => $this->faker->numberBetween(1, 12),
            'stage_name' => $this->faker->word,
            'action' => $this->faker->randomElement(['entered', 'completed', 'gated', 'skipped']),
            'note' => $this->faker->text,
            'created_by_user_id' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function forStudent($studentId): Factory
    {
        return $this->state(function (array $attributes) use ($studentId) {
            return ['student_id' => $studentId];
        });
    }
}
