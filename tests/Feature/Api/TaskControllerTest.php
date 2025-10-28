<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task_with_valid_data(): void
    {
        $user = User::factory()->create();
        
        $taskData = [
            'title' => 'Tarea de prueba',
            'description' => 'Descripción de la tarea de prueba',
            'status' => 'en progreso'
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
                    'title' => 'Tarea de prueba',
                    'description' => 'Descripción de la tarea de prueba',
                    'status' => 'en progreso'
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea de prueba',
            'description' => 'Descripción de la tarea de prueba',
            'status' => 'en progreso'
        ]);
    }

    public function test_can_create_task_with_only_required_fields(): void
    {
        $user = User::factory()->create();
        
        $taskData = [
            'title' => 'Tarea simple'
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
                    'title' => 'Tarea simple',
                    'description' => null,
                    'status' => 'pendiente'
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea simple',
            'description' => null,
            'status' => 'pendiente'
        ]);
    }

    public function test_cannot_create_task_without_title(): void
    {
        $user = User::factory()->create();
        
        $taskData = [
            'description' => 'Descripción sin título'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        $this->assertDatabaseMissing('tasks', [
            'description' => 'Descripción sin título'
        ]);
    }

    public function test_cannot_create_task_with_invalid_status(): void
    {
        $user = User::factory()->create();
        
        $taskData = [
            'title' => 'Tarea con status inválido',
            'status' => 'status_invalido'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseMissing('tasks', [
            'title' => 'Tarea con status inválido'
        ]);
    }

    public function test_task_has_default_status_pendiente(): void
    {
        $user = User::factory()->create();
        
        $taskData = [
            'title' => 'Tarea sin status especificado'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'status' => 'pendiente'
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea sin status especificado',
            'status' => 'pendiente'
        ]);
    }

    public function test_response_has_correct_structure(): void
    {
        $user = User::factory()->create();
        
        $taskData = [
            'title' => 'Tarea para verificar estructura',
            'description' => 'Verificar que la respuesta tenga la estructura correcta',
            'status' => 'completada'
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
            ]);

        $responseData = $response->json('data');
        
        $this->assertIsInt($responseData['id']);
        $this->assertIsString($responseData['title']);
        $this->assertIsString($responseData['status']);
        $this->assertIsString($responseData['created_at']);
        $this->assertIsString($responseData['updated_at']);
    }
}
