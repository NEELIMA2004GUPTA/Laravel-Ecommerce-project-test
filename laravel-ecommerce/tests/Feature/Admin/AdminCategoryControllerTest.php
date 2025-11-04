<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        return $this->actingAs($admin);
    }

    /** @test */
    public function it_displays_category_list()
    {
        $this->actingAsAdmin();

        Category::factory()->count(3)->create();

        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function it_can_create_a_category()
    {
        $this->actingAsAdmin();

        $data = ['name' => 'Electronics'];

        $response = $this->post(route('admin.categories.store'), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', $data);
    }

    /** @test */
    public function it_can_update_a_category()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => 'New Name',
            'parent_id' => null
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create();

        $response = $this->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function test_it_returns_subcategories_as_json()
{
    // Create admin
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);

    // Create parent category
    $parent = \App\Models\Category::factory()->create();

    // Create child subcategories
    $sub1 = \App\Models\Category::factory()->create(['parent_id' => $parent->id]);
    $sub2 = \App\Models\Category::factory()->create(['parent_id' => $parent->id]);

    // ACT as admin to avoid redirect login issue
    $response = $this->actingAs($admin)->getJson(
        route('admin.categories.subcategories', $parent->id)
    );

    $response->assertStatus(200)
             ->assertJsonCount(2) // expecting 2 subcategories
             ->assertJsonFragment(['id' => $sub1->id])
             ->assertJsonFragment(['id' => $sub2->id]);
}
}
