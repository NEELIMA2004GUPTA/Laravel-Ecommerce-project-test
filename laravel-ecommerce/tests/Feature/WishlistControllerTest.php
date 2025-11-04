<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_is_redirected_when_viewing_wishlist()
    {
        $response = $this->get('/wishlist');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_can_view_wishlist_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/wishlist');

        $response->assertStatus(200);
        $response->assertViewIs('frontend.wishlist.index');
    }

    /** @test */
    public function user_can_add_product_to_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post("/wishlist/add/{$product->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product added to wishlist successfully!');

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /** @test */
    public function user_cannot_add_duplicate_products_to_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)->post("/wishlist/add/{$product->id}");

        $response->assertRedirect();
        $response->assertSessionHas('warning', 'Product is already in your wishlist!');
    }

    /** @test */
    public function user_can_remove_product_from_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)->delete("/wishlist/remove/{$product->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product removed from wishlist!');

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }
}
