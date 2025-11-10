<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class ProductReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]    
    public function guest_cannot_submit_review()
    {
        $product = Product::factory()->create();

        $response = $this->post(route('products.reviews.store', $product->id), []);
        $response->assertRedirect(route('login'));
    }

    #[Test]    
    public function authenticated_user_can_submit_review_without_media()
    {
        $this->actingAs($user = User::factory()->create());
        $product = Product::factory()->create();

        $response = $this->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Great product!',
        ]);

        $response->assertRedirect(route('product.show', $product->slug))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Great product!',
        ]);
    }

    #[Test]    
    public function user_can_upload_images_and_video_with_review()
    {
        Storage::fake('public');

        $this->actingAs($user = User::factory()->create());
        $product = Product::factory()->create();

        $image1 = UploadedFile::fake()->image('photo1.jpg');
        $image2 = UploadedFile::fake()->image('photo2.jpg');
        $video = UploadedFile::fake()->create('video.mp4', 5000, 'video/mp4');

        $response = $this->post(route('products.reviews.store', $product), [
            'rating' => 4,
            'comment' => 'Nice product',
            'images' => [$image1, $image2],
            'video' => $video,
        ]);

        $response->assertRedirect();
        $review = Review::first();

        // Images stored
        Storage::disk('public')->assertExists($review->media[0]->path);
        Storage::disk('public')->assertExists($review->media[1]->path);

        // Video stored
        Storage::disk('public')->assertExists($review->media[2]->path);

        $this->assertCount(3, $review->media);
    }

    #[Test]    
    public function rating_is_required()
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $response = $this->post(route('products.reviews.store', $product), [
            'rating' => '', 
        ]);

        $response->assertSessionHasErrors('rating');
    }
}
