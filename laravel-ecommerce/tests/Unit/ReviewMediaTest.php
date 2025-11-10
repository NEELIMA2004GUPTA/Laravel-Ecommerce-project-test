<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewMedia;
use PHPUnit\Framework\Attributes\Test;

class ReviewMediaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_review_media()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $media = ReviewMedia::create([
            'review_id' => $review->id,
            'type' => 'image',
            'path' => 'uploads/review1.jpg',
            'mime' => 'image/jpeg',
            'size' => 1024,
        ]);

        $this->assertDatabaseHas('review_media', [
            'review_id' => $review->id,
            'type' => 'image',
            'path' => 'uploads/review1.jpg',
        ]);

        $this->assertInstanceOf(ReviewMedia::class, $media);
    }

    #[Test]
    public function it_belongs_to_a_review()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $media = ReviewMedia::create([
            'review_id' => $review->id,
            'type' => 'video',
            'path' => 'uploads/review1.mp4',
            'mime' => 'video/mp4',
            'size' => 2048,
        ]);

        $this->assertInstanceOf(Review::class, $media->review);
        $this->assertEquals($review->id, $media->review->id);
    }

    #[Test]    
    public function a_review_can_have_multiple_media()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $media1 = ReviewMedia::create([
            'review_id' => $review->id,
            'type' => 'image',
            'path' => 'uploads/review1.jpg',
            'mime' => 'image/jpeg',
            'size' => 1024,
        ]);

        $media2 = ReviewMedia::create([
            'review_id' => $review->id,
            'type' => 'video',
            'path' => 'uploads/review2.mp4',
            'mime' => 'video/mp4',
            'size' => 2048,
        ]);

        $review->load('media'); 

        $this->assertCount(2, $review->media);
        $this->assertTrue($review->media->contains($media1));
        $this->assertTrue($review->media->contains($media2));
    }
}
