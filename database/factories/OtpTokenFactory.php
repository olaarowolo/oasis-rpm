<?php

namespace Database\Factories;

use App\Models\OtpToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtpTokenFactory extends Factory
{
    protected $model = OtpToken::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'role' => $this->faker->randomElement(['student', 'supervisor', 'admin', 'super_admin']),
            'email' => $this->faker->email,
            'code' => str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(5),
            'used_at' => null,
        ];
    }
}
