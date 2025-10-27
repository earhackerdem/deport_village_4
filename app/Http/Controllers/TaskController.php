<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        
        return response()->json([
            'data' => $tasks
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        
        return response()->json([
            'data' => $task
        ], 201);
    }

    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        
        return response()->json([
            'data' => $task
        ]);
    }

    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pendiente,en progreso,completada',
        ]);
        
        $task->update($request->only(['title', 'description', 'status']));
        
        return response()->json([
            'data' => $task
        ]);
    }

    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        
        return response()->json(null, 204);
    }
}
