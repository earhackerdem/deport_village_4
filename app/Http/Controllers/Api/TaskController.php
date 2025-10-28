<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\TaskStatus;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();
        
        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? TaskStatus::Pendiente,
        ]);

        return new TaskResource($task);
    }
}
