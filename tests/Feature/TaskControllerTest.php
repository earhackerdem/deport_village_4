<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    // Tests para index()
    public function test_index_returns_all_tasks_as_json(): void
    {
        // Arrange
        $tasks = Task::factory(3)->create();
        
        // Act
        $response = $this->getJson('/api/tasks');
        
        // Assert
        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'title', 'description', 'status', 'created_at', 'updated_at']
                    ]
                ]);
    }

    public function test_index_returns_empty_array_when_no_tasks(): void
    {
        // Arrange & Act
        $response = $this->getJson('/api/tasks');
        
        // Assert
        $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
    }

    // Tests para store()
    public function test_store_creates_new_task_with_valid_data(): void
    {
        // Arrange
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'en progreso'
        ];
        
        // Act
        $response = $this->postJson('/api/tasks', $taskData);
        
        // Assert
        $response->assertStatus(201)
                ->assertJsonFragment([
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'status' => 'en progreso'
                ]);
        
        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_store_returns_201_status_code(): void
    {
        // Arrange
        $taskData = ['title' => 'Test Task'];
        
        // Act
        $response = $this->postJson('/api/tasks', $taskData);
        
        // Assert
        $response->assertStatus(201);
    }

    public function test_store_returns_created_task_as_json(): void
    {
        // Arrange
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description'
        ];
        
        // Act
        $response = $this->postJson('/api/tasks', $taskData);
        
        // Assert
        $response->assertJsonStructure([
            'data' => ['id', 'title', 'description', 'status', 'created_at', 'updated_at']
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        // Arrange & Act
        $response = $this->postJson('/api/tasks', []);
        
        // Assert
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title']);
    }

    public function test_store_sets_default_status_if_not_provided(): void
    {
        // Arrange
        $taskData = ['title' => 'Test Task'];
        
        // Act
        $response = $this->postJson('/api/tasks', $taskData);
        
        // Assert
        $response->assertStatus(201)
                ->assertJsonFragment(['status' => 'pendiente']);
    }

    // Tests para show()
    public function test_show_returns_specific_task(): void
    {
        // Arrange
        $task = Task::factory()->create([
            'title' => 'Specific Task',
            'status' => 'completada'
        ]);
        
        // Act
        $response = $this->getJson("/api/tasks/{$task->id}");
        
        // Assert
        $response->assertStatus(200)
                ->assertJsonFragment([
                    'id' => $task->id,
                    'title' => 'Specific Task',
                    'status' => 'completada'
                ]);
    }

    public function test_show_returns_200_status_code(): void
    {
        // Arrange
        $task = Task::factory()->create();
        
        // Act
        $response = $this->getJson("/api/tasks/{$task->id}");
        
        // Assert
        $response->assertStatus(200);
    }

    public function test_show_returns_404_for_non_existent_task(): void
    {
        // Arrange & Act
        $response = $this->getJson('/api/tasks/999');
        
        // Assert
        $response->assertStatus(404);
    }

    // Tests para update()
    public function test_update_modifies_existing_task(): void
    {
        // Arrange
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => 'pendiente'
        ]);
        
        $updateData = [
            'title' => 'Updated Title',
            'status' => 'en progreso'
        ];
        
        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);
        
        // Assert
        $response->assertStatus(200)
                ->assertJsonFragment([
                    'title' => 'Updated Title',
                    'status' => 'en progreso'
                ]);
        
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'en progreso'
        ]);
    }

    public function test_update_returns_200_status_code(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $updateData = ['title' => 'Updated Title'];
        
        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);
        
        // Assert
        $response->assertStatus(200);
    }

    public function test_update_allows_partial_updates(): void
    {
        // Arrange
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'status' => 'pendiente'
        ]);
        
        $updateData = ['title' => 'Updated Title Only'];
        
        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);
        
        // Assert
        $response->assertStatus(200);
        
        $task->refresh();
        $this->assertEquals('Updated Title Only', $task->title);
        $this->assertEquals('Original Description', $task->description);
        $this->assertEquals('pendiente', $task->status);
    }

    public function test_update_validates_data(): void
    {
        // Arrange
        $task = Task::factory()->create();
        $invalidData = [
            'title' => str_repeat('a', 256), // Más de 255 caracteres
            'status' => 'invalid_status'
        ];
        
        // Act
        $response = $this->putJson("/api/tasks/{$task->id}", $invalidData);
        
        // Assert
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_update_returns_404_for_non_existent_task(): void
    {
        // Arrange & Act
        $response = $this->putJson('/api/tasks/999', ['title' => 'Updated']);
        
        // Assert
        $response->assertStatus(404);
    }

    // Tests para destroy()
    public function test_destroy_deletes_task(): void
    {
        // Arrange
        $task = Task::factory()->create();
        
        // Act
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        
        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_destroy_returns_204_status_code(): void
    {
        // Arrange
        $task = Task::factory()->create();
        
        // Act
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        
        // Assert
        $response->assertStatus(204);
    }

    public function test_destroy_returns_404_for_non_existent_task(): void
    {
        // Arrange & Act
        $response = $this->deleteJson('/api/tasks/999');
        
        // Assert
        $response->assertStatus(404);
    }
}
