<?php

namespace Database\Factories;

use App\Models\ProjectResource;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectResourceFactory extends Factory
{
    protected $model = ProjectResource::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'resource_url' => $this->faker->url,
            'resource_type' => $this->faker->randomElement(['document', 'link', 'file']),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text,
            'sort_order' => $this->faker->numberBetween(1, 10),
            'is_required' => $this->faker->boolean,
        ];
    }
}
