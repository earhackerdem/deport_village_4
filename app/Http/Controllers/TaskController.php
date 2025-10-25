<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::all();
        return response()->json(['data' => $tasks]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());
        return response()->json(['data' => $task], 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json(['data' => $task]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        return response()->json(['data' => $task]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json([], 204);
    }
}
