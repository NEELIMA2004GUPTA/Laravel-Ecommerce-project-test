<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->bothify('SAVE###')),
            'discount' => $this->faker->randomFloat(2, 5, 50),
            'min_amount' => $this->faker->randomFloat(2, 0, 500),
            'expires_at' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'status' => $this->faker->boolean(80), // 80% active
        ];
    }
}
