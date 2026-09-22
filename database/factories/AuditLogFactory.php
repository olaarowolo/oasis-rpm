<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'user_id' => User::factory(),
            'action_type' => $this->faker->randomElement(['created', 'updated', 'deleted', 'login', 'logout']),
            'model_type' => $this->faker->word,
            'model_id' => $this->faker->numberBetween(1, 1000),
            'old_values' => null,
            'new_values' => json_encode(['test' => 'value']),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
        ];
    }

    public function forAction(string $action, ?array $old = null, ?array $new = null): Factory
    {
        return $this->state(function (array $attributes) use ($action, $old, $new) {
            return [
                'action_type' => $action,
                'old_values' => $old,
                'new_values' => $new,
            ];
        });
    }
}
