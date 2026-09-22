<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'university_id' => $this->faker->numberBetween(1, 2),
            'creator_id' => User::factory(),
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['active', 'completed', 'on_hold', 'cancelled']),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'visibility' => $this->faker->randomElement(['private', 'team', 'public']),
        ];
    }
}
