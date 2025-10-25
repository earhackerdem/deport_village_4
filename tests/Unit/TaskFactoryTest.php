<?php

namespace Tests\Unit;

use App\Models\Task;
use Database\Factories\TaskFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_factory_creates_task_with_valid_status(): void
    {
        // Arrange
        $validStatuses = ['pendiente', 'en progreso', 'completada'];
        
        // Act
        $task = Task::factory()->create();
        
        // Assert
        $this->assertContains($task->status, $validStatuses);
        $this->assertNotNull($task->title);
        $this->assertInstanceOf(Task::class, $task);
    }

    public function test_task_factory_generates_realistic_data(): void
    {
        // Arrange & Act
        $task = Task::factory()->create();
        
        // Assert
        $this->assertNotEmpty($task->title);
        $this->assertIsString($task->title);
        $this->assertLessThanOrEqual(255, strlen($task->title));
        
        // Description puede ser null o string
        if ($task->description !== null) {
            $this->assertIsString($task->description);
        }
    }

    public function test_task_factory_can_create_multiple_tasks(): void
    {
        // Arrange & Act
        $tasks = Task::factory()->count(5)->create();
        
        // Assert
        $this->assertCount(5, $tasks);
        $this->assertDatabaseCount('tasks', 5);
        
        foreach ($tasks as $task) {
            $this->assertInstanceOf(Task::class, $task);
            $this->assertNotNull($task->title);
        }
    }

    public function test_task_factory_uses_faker_for_realistic_data(): void
    {
        // Arrange & Act
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        
        // Assert - Los títulos deben ser diferentes (Faker genera datos aleatorios)
        $this->assertNotEquals($task1->title, $task2->title);
    }
}
