<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'section' => $this->faker->word,
            'type' => $this->faker->randomElement(['video', 'document', 'link', 'quiz', 'assignment']),
            'title' => $this->faker->sentence(6),
            'url' => $this->faker->url,
            'description' => $this->faker->paragraphs(2, true),
            'stage' => $this->faker->numberBetween(1, 12),
            'sort_order' => $this->faker->numberBetween(1, 10),
            'points' => $this->faker->numberBetween(5, 25),
            'is_mandatory' => $this->faker->boolean(70),
        ];
    }
}
