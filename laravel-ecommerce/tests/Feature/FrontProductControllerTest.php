<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class FrontProductControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]    
    public function products_page_loads_successfully()
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
        $response->assertViewIs('frontend.products.index');
    }

    #[Test]    
    public function it_can_search_products_by_title()
    {
        Product::factory()->create(['title' => 'Red Shoes']);
        Product::factory()->create(['title' => 'Blue Shirt']);

        $response = $this->get('/products?search=Red');
        $response->assertSee('Red Shoes');
        $response->assertDontSee('Blue Shirt');
    }

    #[Test]    
    public function it_can_filter_by_category()
    {
        $category = Category::factory()->create();
        $subcategory = Category::factory()->create(['parent_id' => $category->id]);

        $productInCategory = Product::factory()->create(['category_id' => $category->id]);
        $productInSubcategory = Product::factory()->create(['category_id' => $subcategory->id]);
        $otherProduct = Product::factory()->create();

        $response = $this->get("/products?category={$category->id}");

        $response->assertSee($productInCategory->title);
        $response->assertSee($productInSubcategory->title);
        $response->assertDontSee($otherProduct->title);
    }

    #[Test]    
    public function it_can_filter_by_subcategory()
    {
        $subcategory = Category::factory()->create();
        $productInSub = Product::factory()->create(['category_id' => $subcategory->id]);
        $otherProduct = Product::factory()->create();

        $response = $this->get("/products?subcategory={$subcategory->id}");

        $response->assertSee($productInSub->title);
        $response->assertDontSee($otherProduct->title);
    }

    #[Test]    
    public function it_can_filter_products_by_price_range()
    {
        $cheap = Product::factory()->create(['price' => 100]);
        $mid = Product::factory()->create(['price' => 300]);
        $expensive = Product::factory()->create(['price' => 900]);

        $response = $this->get("/products?min=200&max=500");

        $response->assertSee($mid->title);
        $response->assertDontSee($cheap->title);
        $response->assertDontSee($expensive->title);
    }

    #[Test]    
    public function it_sorts_products_by_newest()
    {
        $oldProduct = Product::factory()->create(['created_at' => now()->subDays(5)]);
        $newProduct = Product::factory()->create(['created_at' => now()]);

        $response = $this->get('/products?sort=newest');

        $response->assertSeeInOrder([$newProduct->title, $oldProduct->title]);
    }

    #[Test]
    public function it_sorts_products_by_oldest()
    {
        $oldProduct = Product::factory()->create(['created_at' => now()->subDays(5)]);
        $newProduct = Product::factory()->create(['created_at' => now()]);

        $response = $this->get('/products?sort=oldest');

        $response->assertSeeInOrder([$oldProduct->title, $newProduct->title]);
    }

#[Test]
public function product_detail_page_loads()
{
    
    $category = Category::factory()->create();

    $product = Product::factory()->create([
        'slug' => 'red-shoes',
        'category_id' => $category->id
    ]);

    $response = $this->get(route('product.show', $product->slug));

    $response->assertStatus(200);
    $response->assertViewIs('frontend.products.show');
    $response->assertViewHas('product', function($viewProduct) use ($product) {
        return $viewProduct->id === $product->id;
    });
}
}

