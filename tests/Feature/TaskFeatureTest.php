<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Response;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    /**
     * Test listing tasks.
     *
     * @return void
     */
    public function testListTasks()
    {
        $tasks = Task::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['title' => $tasks->first()->title]);
    }

    /**
     * Test creating a task.
     *
     * @return void
     */
    public function testCreateTask()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'user_id' => $this->user->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment($taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    /**
     * Test viewing a single task.
     *
     * @return void
     */
    public function testViewTask()
    {
        // Create a category
        $category = Category::factory()->create();

        // Create a task with a valid category ID
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'title' => 'Sample Task Title',
            'description' => 'Sample task description.',
            'status' => 'pending',
            'due_date' => now()->addDays(10)->toDateString(),
        ]);

        // Make a GET request to the endpoint
        $response = $this->getJson("/api/tasks/{$task->id}");

        // Assert the response status is OK
        $response->assertStatus(Response::HTTP_OK)
            // Assert that the response contains all the fields
            ->assertJsonFragment([
                'title' => 'Sample Task Title',
                'description' => 'Sample task description.',
                'status' => 'pending',
                'due_date' => now()->addDays(10)->toDateString(),
                'user_id' => $this->user->id,
                'category_id' => $category->id, // Ensure this matches the created category ID
            ]);
    }

    /**
     * Test updating a task.
     *
     * @return void
     */
    public function testUpdateTask()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updatedData = [
            'title' => 'Updated Task',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(Response::HTTP_OK);

        $task->refresh();
        $this->assertEquals($updatedData['title'], $task->title);
        $this->assertEquals($updatedData['description'], $task->description);
    }

    /**
     * Test deleting a task.
     *
     * @return void
     */
    public function testDeleteTask()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
