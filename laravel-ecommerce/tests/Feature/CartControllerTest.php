<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_cart_page()
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
        $response->assertViewIs('frontend.cart.index');
    }

    /** @test */
    public function user_can_add_product_to_cart()
    {
        $product = Product::factory()->create([
            'price' => 100,
            'discount' => 10,
            'stock' => 5,
        ]);

        $response = $this->post('/cart/add/'.$product->id, [
            'quantity' => 2
        ]);

        $response->assertSessionHas('success');
        $cart = session('cart');

        $this->assertEquals(2, $cart[$product->id]['quantity']);
        $this->assertEquals(90, $cart[$product->id]['price']); // 10% discount
    }

    /** @test */
    public function product_quantity_cannot_exceed_stock()
    {
        $product = Product::factory()->create([
            'price' => 200,
            'stock' => 3
        ]);

        $this->post('/cart/add/'.$product->id, ['quantity' => 10]);
        $cart = session('cart');

        $this->assertEquals(3, $cart[$product->id]['quantity']);
    }

    /** @test */
    public function user_can_update_cart_quantity()
    {
        $product = Product::factory()->create([
            'price' => 100,
            'stock' => 5,
        ]);

        Session::put('cart', [
            $product->id => [
                'price' => 100,
                'qty' => 1,
                'stock' => 5
            ]
        ]);

        $response = $this->post('/cart/update/'.$product->id, [
            'qty' => 4
        ]);

        $response->assertJson([
            'success' => true,
            'qty' => 4,
        ]);

        $cart = session('cart');
        $this->assertEquals(4, $cart[$product->id]['qty']);
    }

        /** @test */
public function user_can_remove_item_from_cart()
{
    // Arrange
    $user = \App\Models\User::factory()->create();
    $product = \App\Models\Product::factory()->create([
        'stock' => 10,
        'price' => 100,
        'discount' => 0,
    ]);

    // Simulate a cart in session
    $this->actingAs($user)->withSession([
        'cart' => [
            $product->id => [
                'title' => $product->title,
                'original_price' => $product->price,
                'price' => $product->price,
                'discount' => 0,
                'quantity' => 1,
                'stock' => $product->stock,
            ]
        ]
    ]);

    // Act
   $response = $this->get(route('cart.remove', $product));

    // Assert
    $response->assertRedirect();              
    $response->assertSessionHas('success');   
    $this->assertArrayNotHasKey($product->id, session('cart')); 
}

}
