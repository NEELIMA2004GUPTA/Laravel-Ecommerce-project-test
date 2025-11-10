<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class AdminCouponControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser()
    {
        return User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function index_displays_coupons()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        Coupon::factory()->count(3)->create();

        $response = $this->get(route('admin.coupons.index'));
        $response->assertStatus(200);
        $response->assertViewHas('coupons');
    }

    #[Test]
    public function create_displays_form()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $response = $this->get(route('admin.coupons.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function store_creates_coupon_with_valid_data()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $response = $this->post(route('admin.coupons.store'), [
            'code' => 'TEST10',
            'discount' => 10,
            'min_amount' => 100,
            'expires_at' => now()->addDays(5)->format('Y-m-d'),
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'TEST10']);
    }

    #[Test]
    public function store_validation_fails_with_invalid_data()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $response = $this->post(route('admin.coupons.store'), [
            'code' => '',
            'discount' => 0,
            'min_amount' => -10,
        ]);

        $response->assertSessionHasErrors(['code', 'discount', 'min_amount']);
    }

    #[Test]
    public function edit_displays_coupon()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $coupon = Coupon::factory()->create();

        $response = $this->get(route('admin.coupons.edit', $coupon));
        $response->assertStatus(200);
        $response->assertViewHas('coupon', $coupon);
    }

    #[Test]
    public function update_edits_coupon_with_valid_data()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $coupon = Coupon::factory()->create(['code' => 'OLD']);

        $response = $this->put(route('admin.coupons.update', $coupon), [
            'code' => 'NEWCODE',
            'discount' => 20,
            'min_amount' => 50,
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'NEWCODE', 'discount' => 20]);
    }

    #[Test]
    public function update_validation_fails_with_invalid_data()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $coupon = Coupon::factory()->create();

        $response = $this->put(route('admin.coupons.update', $coupon), [
            'code' => '',
            'discount' => 0,
            'min_amount' => -10,
        ]);

        $response->assertSessionHasErrors(['code', 'discount', 'min_amount']);
    }

    #[Test]
    public function destroy_deletes_coupon()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $coupon = Coupon::factory()->create();

        $response = $this->delete(route('admin.coupons.destroy', $coupon));
        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    #[Test]
    public function applyCoupon_applies_successfully()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'discount' => 10,
            'min_amount' => 50,
            'status' => 1,
            'expires_at' => now()->addDays(1),
        ]);

        // Mock cart in session
        $this->withSession([
            'cart' => [
                ['price' => 100, 'qty' => 1]
            ]
        ]);

        $response = $this->post(route('apply.coupon'), [
            'coupon_code' => 'SAVE10'
        ]);

        $response->assertSessionHas('coupon', ['code' => 'SAVE10', 'discount' => 10]);
    }

    #[Test]
    public function applyCoupon_fails_for_invalid_code()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $response = $this->post(route('apply.coupon'), [
            'coupon_code' => 'INVALID'
        ]);

        $response->assertSessionHas('error', 'Invalid Coupon Code.');
    }

    #[Test]
    public function applyCoupon_fails_if_cart_total_less_than_min_amount()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $coupon = Coupon::factory()->create(['min_amount' => 500, 'status' => 1]);

        $this->withSession([
            'cart' => [['price' => 100, 'qty' => 1]]
        ]);

        $response = $this->post(route('apply.coupon'), ['coupon_code' => $coupon->code]);
        $response->assertSessionHas('error');
    }

    #[Test]
    public function applyCoupon_fails_if_coupon_expired()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $coupon = Coupon::factory()->create([
            'expires_at' => now()->subDays(1),
            'status' => 1,
            'min_amount' => 100,
        ]);

        $this->withSession([
            'cart' => [['price' => 100, 'qty' => 1]]
        ]);

        $response = $this->post(route('apply.coupon'), ['coupon_code' => $coupon->code]);
        $response->assertSessionHas('error', 'This coupon has expired.');

    }

    #[Test]
    public function removeCoupon_clears_coupon_from_session()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $this->withSession(['coupon' => ['code' => 'TEST']]);

        $response = $this->get(route('remove.coupon'));
        $response->assertSessionMissing('coupon');
    }
}

