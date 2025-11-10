<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Review;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'product_id' => 1, 
            'rating' => $this->faker->numberBetween(1,5),
            'comment' => $this->faker->sentence(),
        ];
    }
}
