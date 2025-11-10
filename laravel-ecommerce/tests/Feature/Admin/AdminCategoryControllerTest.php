<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser()
    {
        return User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function index_displays_categories_and_search()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $parent = Category::factory()->create(['name' => 'Parent Category']);
        $child = Category::factory()->create(['name' => 'Child Category', 'parent_id' => $parent->id]);

        // Test index without search
        $response = $this->get(route('admin.categories.index'));
        $response->assertStatus(200);
        $response->assertViewHas('categories');

        // Test index with search
        $response = $this->get(route('admin.categories.index', ['search' => 'Parent']));
        $response->assertStatus(200);
        $response->assertViewHas('categories', function($categories) use ($parent) {
            return $categories->contains($parent);
        });
    }

    #[Test]
    public function create_displays_form_with_parents()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $parent = Category::factory()->create();

        $response = $this->get(route('admin.categories.create'));
        $response->assertStatus(200);
        $response->assertViewHas('parents');
    }

    #[Test]
    public function store_creates_category_and_redirects()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $parent = Category::factory()->create();

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'New Category',
            'parent_id' => $parent->id,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'New Category', 'parent_id' => $parent->id]);
    }

    #[Test]
    public function store_validation_fails_with_invalid_data()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $response = $this->post(route('admin.categories.store'), [
            'name' => '', // required
            'parent_id' => 9999, // non-existing
        ]);

        $response->assertSessionHasErrors(['name', 'parent_id']);
    }

    #[Test]
    public function show_displays_category_with_subcategories()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->get(route('admin.categories.show', $parent));
        $response->assertStatus(200);
        $response->assertViewHas('category', function($category) use ($parent) {
            return $category->id === $parent->id && $category->subcategories->contains(function($sub){ return true; });
        });
    }

    #[Test]
    public function edit_displays_category_and_parents()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $category = Category::factory()->create();

        $response = $this->get(route('admin.categories.edit', $category));
        $response->assertStatus(200);
        $response->assertViewHasAll(['category', 'parents']);
    }

    #[Test]
    public function update_edits_category_and_redirects()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $category = Category::factory()->create();
        $parent = Category::factory()->create();

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => 'Updated Name',
            'parent_id' => $parent->id,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Name', 'parent_id' => $parent->id]);
    }

    #[Test]
    public function update_validation_fails_with_invalid_data()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $category = Category::factory()->create();

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => '', // required
            'parent_id' => 9999, // non-existing
        ]);

        $response->assertSessionHasErrors(['name', 'parent_id']);
    }

    #[Test]
    public function destroy_deletes_category()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $category = Category::factory()->create();

        $response = $this->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    #[Test]
    public function get_subcategories_returns_json()
    {
        $admin = $this->adminUser();
        $this->actingAs($admin);

        $parent = Category::factory()->create();
        $child1 = Category::factory()->create(['parent_id' => $parent->id]);
        $child2 = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->get(route('admin.categories.getSubcategories', $parent));
        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['id' => $child1->id, 'name' => $child1->name]);
        $response->assertJsonFragment(['id' => $child2->id, 'name' => $child2->name]);
    }
}
