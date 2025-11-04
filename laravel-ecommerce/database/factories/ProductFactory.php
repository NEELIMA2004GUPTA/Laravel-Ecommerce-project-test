<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'images' => json_encode([]), // storing empty initially
            'price' => $this->faker->randomFloat(2, 50, 9999),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'stock' => $this->faker->numberBetween(5, 200),
            'variants' => json_encode([]),
        ];
    }
}
