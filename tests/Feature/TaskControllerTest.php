<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_tasks()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_get_empty_tasks_list()
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    public function test_can_store_task_with_valid_data()
    {
        $taskData = [
            'title' => 'Nueva tarea',
            'description' => 'Descripción de la nueva tarea',
            'status' => 'pendiente'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Nueva tarea',
                    'description' => 'Descripción de la nueva tarea',
                    'status' => 'pendiente'
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Nueva tarea',
            'description' => 'Descripción de la nueva tarea',
            'status' => 'pendiente'
        ]);
    }

    public function test_can_store_task_with_minimal_data()
    {
        $taskData = [
            'title' => 'Tarea mínima'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Tarea mínima',
                'status' => 'pendiente'
            ])
            ->assertJsonMissing(['description']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea mínima',
            'description' => null,
            'status' => 'pendiente'
        ]);
    }

    public function test_cannot_store_task_without_title()
    {
        $taskData = [
            'description' => 'Sin título'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_cannot_store_task_with_invalid_status()
    {
        $taskData = [
            'title' => 'Tarea con status inválido',
            'status' => 'status_invalido'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_can_show_specific_task()
    {
        $task = Task::factory()->create([
            'title' => 'Tarea específica',
            'description' => 'Descripción específica',
            'status' => 'en progreso'
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Tarea específica',
                    'description' => 'Descripción específica',
                    'status' => 'en progreso'
                ]
            ]);
    }

    public function test_cannot_show_nonexistent_task()
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertStatus(404);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create([
            'title' => 'Tarea original',
            'status' => 'pendiente'
        ]);

        $updateData = [
            'title' => 'Tarea actualizada',
            'description' => 'Nueva descripción',
            'status' => 'en progreso'
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Tarea actualizada',
                    'description' => 'Nueva descripción',
                    'status' => 'en progreso'
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Tarea actualizada',
            'description' => 'Nueva descripción',
            'status' => 'en progreso'
        ]);
    }

    public function test_can_update_task_partially()
    {
        $task = Task::factory()->create([
            'title' => 'Tarea original',
            'description' => 'Descripción original',
            'status' => 'pendiente'
        ]);

        $updateData = [
            'status' => 'completada'
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Tarea original',
                    'description' => 'Descripción original',
                    'status' => 'completada'
                ]
            ]);
    }

    public function test_cannot_update_nonexistent_task()
    {
        $updateData = [
            'title' => 'Tarea inexistente'
        ];

        $response = $this->putJson('/api/tasks/999', $updateData);

        $response->assertStatus(404);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_cannot_delete_nonexistent_task()
    {
        $response = $this->deleteJson('/api/tasks/999');

        $response->assertStatus(404);
    }
}