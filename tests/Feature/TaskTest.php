<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_with_valid_data_creates_task_and_returns_201(): void
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'Test description',
            'status' => 'pendiente'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'title',
            'description',
            'status',
            'created_at',
            'updated_at'
        ]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'status' => 'pendiente'
        ]);
    }

    public function test_store_without_title_returns_422_validation_error(): void
    {
        $data = [
            'description' => 'Description without title',
            'status' => 'pendiente'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_store_with_invalid_status_returns_422(): void
    {
        $data = [
            'title' => 'Task',
            'status' => 'invalid-status'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_store_without_description_creates_task(): void
    {
        $data = [
            'title' => 'Task without description'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without description',
            'description' => null
        ]);
    }

    public function test_store_without_status_uses_default_pendiente(): void
    {
        $data = [
            'title' => 'Task without status'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without status',
            'status' => 'pendiente'
        ]);
    }

    public function test_index_returns_all_tasks(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_show_returns_existing_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $task->id,
            'title' => $task->title
        ]);
    }

    public function test_show_with_invalid_id_returns_404(): void
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertStatus(404);
    }

    public function test_update_with_valid_data_updates_task(): void
    {
        $task = Task::factory()->create();
        $data = [
            'title' => 'Updated Task',
            'status' => 'en progreso'
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'status' => 'en progreso'
        ]);
    }

    public function test_destroy_deletes_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }
}
