<?php

namespace Database\Factories;

use App\Models\SystemConfig;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemConfigFactory extends Factory
{
    protected $model = SystemConfig::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'config_key' => $this->faker->word,
            'config_value' => json_encode(['test' => 'value']),
            'data_type' => $this->faker->randomElement(['string', 'integer', 'boolean', 'json', 'array']),
        ];
    }

    public function withKey(string $key): Factory
    {
        return $this->state(function (array $attributes) use ($key) {
            return ['config_key' => $key];
        });
    }
}
