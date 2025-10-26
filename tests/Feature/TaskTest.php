<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    // ========== STORE TESTS ==========

    public function test_store_with_valid_data_creates_task_and_returns_201(): void
    {
        // Arrange
        $data = [
            'title' => 'Test Task',
            'description' => 'Test description',
            'status' => 'pendiente'
        ];

        // Act
        $response = $this->postJson('/api/tasks', $data);

        // Assert
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
        // Arrange
        $data = [
            'description' => 'Description without title',
            'status' => 'pendiente'
        ];

        // Act
        $response = $this->postJson('/api/tasks', $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_store_with_invalid_status_returns_422(): void
    {
        // Arrange
        $data = [
            'title' => 'Task',
            'status' => 'invalid-status'
        ];

        // Act
        $response = $this->postJson('/api/tasks', $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_store_without_description_creates_task(): void
    {
        // Arrange
        $data = [
            'title' => 'Task without description'
        ];

        // Act
        $response = $this->postJson('/api/tasks', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without description',
            'description' => null
        ]);
    }

    public function test_store_without_status_uses_default_pendiente(): void
    {
        // Arrange
        $data = [
            'title' => 'Task without status'
        ];

        // Act
        $response = $this->postJson('/api/tasks', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without status',
            'status' => 'pendiente'
        ]);
    }

    // ========== INDEX TESTS ==========

    public function test_index_returns_all_tasks(): void
    {
        // Arrange
        Task::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/tasks');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_index_returns_empty_array_when_no_tasks(): void
    {
        // Arrange
        // No tasks created

        // Act
        $response = $this->getJson('/api/tasks');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }

    // ========== SHOW TESTS ==========

    public function test_show_returns_existing_task(): void
    {
        // Arrange
        $task = Task::factory()->create();

        // Act
        $response = $this->getJson("/api/tasks/{$task->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $task->id,
            'title' => $task->title
        ]);
    }

    public function test_show_with_invalid_id_returns_404(): void
    {
        // Arrange
        $nonexistentId = 99999;

        // Act
        $response = $this->getJson("/api/tasks/{$nonexistentId}");

        // Assert
        $response->assertStatus(404);
    }

    // ========== UPDATE TESTS ==========

    public function test_update_with_valid_data_updates_task(): void
    {
        // Arrange
        $task = Task::factory()->create(['status' => 'pendiente']);
        $updates = ['status' => 'en progreso'];

        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updates);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'en progreso'
        ]);
    }

    public function test_update_status_from_pendiente_to_en_progreso_succeeds(): void
    {
        // Arrange
        $task = Task::factory()->create(['status' => 'pendiente']);
        $updates = ['status' => 'en progreso'];

        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updates);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'en progreso'
        ]);
    }

    public function test_update_status_from_en_progreso_to_completada_succeeds(): void
    {
        // Arrange
        $task = Task::factory()->create(['status' => 'en progreso']);
        $updates = ['status' => 'completada'];

        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updates);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completada'
        ]);
    }

    public function test_update_status_from_pendiente_to_completada_succeeds(): void
    {
        // Arrange
        $task = Task::factory()->create(['status' => 'pendiente']);
        $updates = ['status' => 'completada'];

        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updates);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completada'
        ]);
    }

    public function test_update_status_invalid_transition_returns_422(): void
    {
        // Arrange
        $task = Task::factory()->create(['status' => 'completada']);
        $updates = ['status' => 'pendiente'];

        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updates);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_update_with_invalid_id_returns_404(): void
    {
        // Arrange
        $nonexistentId = 99999;
        $updates = ['title' => 'Updated Title'];

        // Act
        $response = $this->putJson("/api/tasks/{$nonexistentId}", $updates);

        // Assert
        $response->assertStatus(404);
    }

    // ========== DESTROY TESTS ==========

    public function test_destroy_deletes_task_and_returns_204(): void
    {
        // Arrange
        $task = Task::factory()->create();

        // Act
        $response = $this->deleteJson("/api/tasks/{$task->id}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_destroy_with_invalid_id_returns_404(): void
    {
        // Arrange
        $nonexistentId = 99999;

        // Act
        $response = $this->deleteJson("/api/tasks/{$nonexistentId}");

        // Assert
        $response->assertStatus(404);
    }
}
