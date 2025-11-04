<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function admin()
    {
        return User::factory()->create([
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function admin_can_view_user_list()
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('admin.users'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    /** @test */
    public function test_admin_can_block_and_unblock_user()
{
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $user = \App\Models\User::factory()->create(['is_blocked' => 0]);

    // Block user
    $this->actingAs($admin)
        ->post(route('admin.users.toggle-block', $user));

    $user->refresh(); // <<< IMPORTANT

    $this->assertEquals(1, $user->is_blocked);

    // Unblock user
    $this->actingAs($admin)
        ->post(route('admin.users.toggle-block', $user));

    $user->refresh(); // <<< IMPORTANT

    $this->assertEquals(0, $user->is_blocked);
}


    /** @test */
    public function admin_can_change_user_role()
    {
        $admin = $this->admin();
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin)
            ->post(route('admin.users.changeRole', $user->id), [
                'role' => 'admin'
            ]);

        $this->assertEquals('admin', $user->fresh()->role);
    }
}
