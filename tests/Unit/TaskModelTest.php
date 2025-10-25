<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_model_has_fillable_attributes(): void
    {
        // Arrange
        $fillable = ['title', 'description', 'status'];
        
        // Act
        $task = new Task();
        
        // Assert
        $this->assertEquals($fillable, $task->getFillable());
    }

    public function test_task_model_has_default_status(): void
    {
        // Arrange & Act
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description'
        ]);
        
        // Assert
        $this->assertEquals('pendiente', $task->status);
    }

    public function test_task_belongs_to_tasks_table(): void
    {
        // Arrange
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'en progreso'
        ];
        
        // Act
        $task = Task::create($taskData);
        
        // Assert
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'en progreso'
        ]);
    }

    public function test_task_can_have_nullable_description(): void
    {
        // Arrange & Act
        $task = Task::create([
            'title' => 'Test Task Without Description'
        ]);
        
        // Assert
        $this->assertNull($task->description);
        $this->assertEquals('pendiente', $task->status);
    }

    public function test_task_accepts_valid_status_values(): void
    {
        // Arrange
        $validStatuses = ['pendiente', 'en progreso', 'completada'];
        
        foreach ($validStatuses as $status) {
            // Act
            $task = Task::create([
                'title' => "Test Task - $status",
                'status' => $status
            ]);
            
            // Assert
            $this->assertEquals($status, $task->status);
        }
    }
}
