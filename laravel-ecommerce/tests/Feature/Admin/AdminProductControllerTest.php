<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use PHPUnit\Framework\Attributes\Test;


class AdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create category
        $this->category = Category::factory()->create();
    }

    #[TEST]
    public function admin_can_store_product_with_images_and_variants()
    {
        Storage::fake('public');

        $images = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.png'),
        ];

        $variants = ['Size' => 'M', 'Color' => 'Red'];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'title' => 'Test Product',
                'description' => 'Test description',
                'category_id' => $this->category->id,
                'price' => 99.99,
                'discount' => 10,
                'sku' => 'SKU001',
                'stock' => 50,
                'images' => $images,
                'variants' => $variants,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['title' => 'Test Product']);

        $product = Product::first();

        $this->assertIsArray($product->images);
        $this->assertCount(2, $product->images);
        Storage::disk('public')->assertExists($product->images[0]);
        Storage::disk('public')->assertExists($product->images[1]);

        $this->assertIsArray($product->variants);
        $this->assertEquals($variants, $product->variants);
    }

    #[TEST]
    public function admin_can_update_product_and_add_remove_images()
    {
        Storage::fake('public');

        $oldImage = UploadedFile::fake()->image('old.jpg');
        $product = Product::factory()->create([
            'title' => 'Original Title',
            'category_id' => $this->category->id,
            'images' => [$oldImage->store('products/images', 'public')],
        ]);

        $newImage = UploadedFile::fake()->image('new.jpg');

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product->id), [
                'title' => 'Updated Title',
                'category_id' => $this->category->id,
                'price' => 100,
                'discount' => 0,
                'sku' => $product->sku,
                'stock' => 10,
                'images' => [$newImage],
                'remove_images' => [$product->images[0]],
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('Updated Title', $product->title);
        $this->assertCount(1, $product->images);

        Storage::disk('public')->assertMissing('products/images/old.jpg'); // old removed
        Storage::disk('public')->assertExists($product->images[0]); // new exists
    }

    #[TEST]
    public function admin_can_delete_product()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('delete.jpg');
        $product = Product::factory()->create([
            'images' => [$image->store('products/images', 'public')],
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product->id));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($product->images[0]);
    }

    #[TEST]
    public function admin_can_delete_single_image_via_ajax()
    {
        Storage::fake('public');

        $image1 = UploadedFile::fake()->image('img1.jpg');
        $image2 = UploadedFile::fake()->image('img2.jpg');

        $product = Product::factory()->create([
            'images' => [
                $image1->store('products/images', 'public'),
                $image2->store('products/images', 'public')
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.products.deleteImage', $product->id), [
                'image' => $product->images[0]
            ]);

        $response->assertJson(['success' => true]);

        $product->refresh();
        $this->assertCount(1, $product->images);
        $this->assertEquals($image2->hashName(), basename($product->images[0]));

        Storage::disk('public')->assertMissing($image1->hashName());
        Storage::disk('public')->assertExists($product->images[0]);
    }
}
