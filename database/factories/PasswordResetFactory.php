<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PasswordReset>
 */
class PasswordResetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'token' => $this->faker->password(32),
            'requested_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'token_created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}