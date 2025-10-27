<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task_with_valid_data()
    {
        $taskData = [
            'title' => 'Tarea de prueba',
            'description' => 'Descripción de la tarea de prueba',
            'status' => 'pendiente'
        ];

        $task = Task::create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Tarea de prueba', $task->title);
        $this->assertEquals('Descripción de la tarea de prueba', $task->description);
        $this->assertEquals('pendiente', $task->status);
        $this->assertNotNull($task->id);
    }

    public function test_can_create_task_with_minimal_data()
    {
        $taskData = [
            'title' => 'Tarea mínima'
        ];

        $task = Task::create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Tarea mínima', $task->title);
        $this->assertNull($task->description);
        $this->assertEquals('pendiente', $task->status);
    }

    public function test_task_has_fillable_attributes()
    {
        $task = new Task();
        $fillable = $task->getFillable();

        $this->assertContains('title', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('status', $fillable);
    }

    public function test_task_has_timestamps()
    {
        $task = Task::create(['title' => 'Tarea con timestamps']);

        $this->assertNotNull($task->created_at);
        $this->assertNotNull($task->updated_at);
    }

    public function test_task_status_defaults_to_pendiente()
    {
        $task = Task::create(['title' => 'Tarea sin status']);

        $this->assertEquals('pendiente', $task->status);
    }

    public function test_task_can_have_different_statuses()
    {
        $statuses = ['pendiente', 'en progreso', 'completada'];

        foreach ($statuses as $status) {
            $task = Task::create([
                'title' => "Tarea con status {$status}",
                'status' => $status
            ]);

            $this->assertEquals($status, $task->status);
        }
    }
}