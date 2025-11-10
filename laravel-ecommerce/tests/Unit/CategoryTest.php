<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]    
    public function it_can_create_a_category()
    {
        $category = Category::create([
            'name' => 'Electronics',
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'parent_id' => null,
        ]);

        $this->assertInstanceOf(Category::class, $category);
    }

    #[Test]    
    public function it_can_have_subcategories()
    {
        $parent = Category::create(['name' => 'Electronics']);
        $child = Category::create(['name' => 'Mobiles', 'parent_id' => $parent->id]);

        $this->assertTrue($parent->subcategories->contains($child));
        $this->assertEquals($parent->id, $child->parent->id);
    }

    #[Test]    
    public function it_can_have_products()
    {
        $category = Category::create(['name' => 'Electronics']);

        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($category->products->contains($product));
    }
}
