<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCouponControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        return $admin;
    }

    /** @test */
    public function admin_can_view_coupon_list()
    {
        $this->loginAdmin();

        Coupon::factory()->count(3)->create();

        $response = $this->get(route('admin.coupons.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupons.index');
        $response->assertViewHas('coupons');
    }

    /** @test */
    public function admin_can_create_coupon()
    {
        $this->loginAdmin();

        $data = [
            'code' => 'SAVE10',
            'discount' => 10,
            'min_amount' => 100,
            'expires_at' => now()->addDays(5)->format('Y-m-d'),
            'status' => 1
        ];

        $response = $this->post(route('admin.coupons.store'), $data);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'SAVE10']);
    }

    /** @test */
    public function admin_can_edit_coupon()
    {
        $this->loginAdmin();

        $coupon = Coupon::factory()->create();

        $response = $this->get(route('admin.coupons.edit', $coupon->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupons.edit');
    }

    /** @test */
    public function admin_can_update_coupon()
    {
        $this->loginAdmin();

        $coupon = Coupon::factory()->create(['code' => 'OLD10']);

        $response = $this->put(route('admin.coupons.update', $coupon->id), [
            'code' => 'NEW20',
            'discount' => 20,
            'min_amount' => 200,
            'expires_at' => now()->addDays(10)->format('Y-m-d'),
            'status' => 1
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'NEW20']);
    }

    /** @test */
    public function admin_can_delete_coupon()
    {
        $this->loginAdmin();

        $coupon = Coupon::factory()->create();

        $response = $this->delete(route('admin.coupons.destroy', $coupon->id));

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }
}
