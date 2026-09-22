<?php

namespace Database\Factories;

use App\Models\ResourceProgress;
use App\Models\Student;
use App\Models\University;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceProgressFactory extends Factory
{
    protected $model = ResourceProgress::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'student_id' => Student::factory(),
            'resource_id' => Resource::factory(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'submitted', 'reviewed', 'approved', 'rejected']),
            'points_earned' => $this->faker->numberBetween(0, 25),
            'submitted_date' => $this->faker->optional()->dateTimeBetween('-3 months', 'now'),
            'reviewed_date' => null,
            'supervisor_comment' => null,
        ];
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'reviewed_date' => now(),
            ];
        });
    }
}
