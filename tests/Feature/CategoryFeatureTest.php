<?php

namespace Tests\Feature;

use App\Helpers\CategoryHelpers;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Response;

class CategoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api'); // Authenticate as user
    }

    /**
     * Test listing categories.
     *
     * @return void
     */
    public function testListCategories()
    {
        // Seed some categories
        $categories = Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['name' => $categories->first()->name]);
    }

    /**
     * Test creating a category.
     *
     * @return void
     */
    public function testCreateCategory()
    {
        $categoryName = 'New Category';
        $response = $this->postJson('/api/categories', ['name' => $categoryName]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['name' => $categoryName]);

        $category = Category::where('name', $categoryName)->first();
        $this->assertNotNull($category);
        $this->assertEquals(CategoryHelpers::generateSlug($categoryName, $category->id), $category->slug);
    }

    /**
     * Test viewing a single category.
     *
     * @return void
     */
    public function testViewCategory()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['name' => $category->name])
            ->assertJsonFragment(['slug' => $category->slug]);
    }

    /**
     * Test updating a category.
     *
     * @return void
     */
    public function testUpdateCategory()
    {
        $category = Category::factory()->create();
        $updatedName = 'Updated Category';

        $response = $this->putJson("/api/categories/{$category->id}", ['name' => $updatedName]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['name' => $updatedName]);

        $category->refresh();
        $this->assertEquals(CategoryHelpers::generateSlug($updatedName, $category->id), $category->slug);
    }

    /**
     * Test deleting a category.
     *
     * @return void
     */
    public function testDeleteCategory()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
