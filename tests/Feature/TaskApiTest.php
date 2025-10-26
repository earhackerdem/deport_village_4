<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task_successfully(): void
    {
        $taskData = [
            'title' => 'Nueva tarea de prueba',
            'description' => 'Descripción detallada de la tarea',
            'status' => 'pendiente',
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
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Nueva tarea de prueba',
                    'description' => 'Descripción detallada de la tarea',
                    'status' => 'pendiente',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Nueva tarea de prueba',
            'description' => 'Descripción detallada de la tarea',
            'status' => 'pendiente',
        ]);
    }

    public function test_can_create_task_without_description(): void
    {
        $taskData = [
            'title' => 'Tarea sin descripción',
            'status' => 'en progreso',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'Tarea sin descripción',
                    'description' => null,
                    'status' => 'en progreso',
                ],
            ]);
    }

    public function test_can_create_task_without_status(): void
    {
        $taskData = [
            'title' => 'Tarea con status default',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'Tarea con status default',
                    'status' => 'pendiente',
                ],
            ]);
    }


    public function test_fails_when_title_is_empty(): void
    {
        $taskData = [
            'description' => 'Descripción sin título',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_fails_when_title_exceeds_max_length(): void
    {
        $taskData = [
            'title' => str_repeat('a', 256), // 256 caracteres, máximo es 255
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_fails_when_status_is_invalid(): void
    {
        $taskData = [
            'title' => 'Tarea con status inválido',
            'status' => 'status_invalido',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_accepts_all_valid_status(): void
    {
        $validStatuses = ['pendiente', 'en progreso', 'completada'];

        foreach ($validStatuses as $status) {
            $taskData = [
                'title' => "Tarea con status {$status}",
                'status' => $status,
            ];

            $response = $this->postJson('/api/tasks', $taskData);

            $response->assertStatus(201)
                ->assertJson([
                    'data' => [
                        'status' => $status,
                    ],
                ]);
        }

        $this->assertDatabaseCount('tasks', 3);
    }
}