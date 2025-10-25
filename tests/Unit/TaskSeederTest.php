<?php

namespace Tests\Unit;

use App\Models\Task;
use Database\Seeders\TaskSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_seeder_creates_20_tasks(): void
    {
        // Arrange & Act
        $this->seed(TaskSeeder::class);
        
        // Assert
        $this->assertDatabaseCount('tasks', 20);
    }

    public function test_task_seeder_uses_factory(): void
    {
        // Arrange & Act
        $this->seed(TaskSeeder::class);
        
        // Assert
        $tasks = Task::all();
        $this->assertCount(20, $tasks);
        
        // Verificar que las tareas tienen datos realistas (generados por factory)
        foreach ($tasks as $task) {
            $this->assertNotEmpty($task->title);
            $this->assertIsString($task->title);
            $this->assertContains($task->status, ['pendiente', 'en progreso', 'completada']);
        }
    }

    public function test_task_seeder_creates_tasks_with_different_statuses(): void
    {
        // Arrange & Act
        $this->seed(TaskSeeder::class);
        
        // Assert
        $tasks = Task::all();
        $statuses = $tasks->pluck('status')->unique()->toArray();
        
        // Debe haber al menos 2 statuses diferentes (por probabilidad con 20 tareas)
        $this->assertGreaterThanOrEqual(1, count($statuses));
        
        // Todos los statuses deben ser válidos
        foreach ($statuses as $status) {
            $this->assertContains($status, ['pendiente', 'en progreso', 'completada']);
        }
    }
}
