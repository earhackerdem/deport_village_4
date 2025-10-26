<?php

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/tasks', function (Request $request) {
    
    if (!$request->has('title') || $request->input('title') === '') {
        return response()->json([
            'message' => 'El campo title es requerido.',
            'errors' => [
                'title' => ['El campo title es requerido.']
            ]
        ], 422);
    }

    if (strlen($request->input('title')) > 255) {
        return response()->json([
            'message' => 'El title no debe ser mayor a 255 caracteres.',
            'errors' => [
                'title' => ['El title no debe ser mayor a 255 caracteres.']
            ]
        ], 422);
    }

    if ($request->has('description') && !is_string($request->input('description'))) {
        return response()->json([
            'message' => 'La descripción debe ser una cadena de texto.',
        ], 422);
    }

    $allowedStatus = ['pendiente', 'en progreso', 'completada'];
    if ($request->has('status') && !in_array($request->input('status'), $allowedStatus)) {
        return response()->json([
            'message' => 'El status seleccionado es inválido.',
            'errors' => [
                'status' => ['El status debe ser uno de: pendiente, en progreso, completada']
            ]
        ], 422);
    }
    
    
    try {
        $task = new Task();
        $task->title = $request->input('title');
        $task->description = $request->input('description'); 
        $task->status = $request->input('status', 'pendiente'); 
        $task->save();

        
        return response()->json([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ]
        ], 201);

    } catch (\Exception $e) {
        
        return response()->json([
            'message' => 'Ocurrió un error inesperado al crear la tarea.',
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::get('/tasks', function (Request $request) {
    try {
        
        $tasks = Task::all();
        
        
        $tasksArray = [];
        foreach ($tasks as $task) {
            $tasksArray[] = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $task->updated_at->format('Y-m-d H:i:s'),
            ];
        }
        
        return response()->json($tasksArray, 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al obtener las tareas.',
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::get('/tasks/{id}', function ($id) {
    try {
        
        $task = Task::find($id);
        
        
        if (!$task) {
            return response()->json([
                'message' => 'Tarea no encontrada',
            ], 404);
        }
        
        
        return response()->json([
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ]
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al buscar la tarea.',
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::put('/tasks/{id}', function (Request $request, $id) {
    try {
        $task = Task::find($id);
        
        if (!$task) {
            return response()->json([
                'message' => 'La tarea no existe',
            ], 404);
        }
        
        
        if ($request->has('title')) {
            if ($request->input('title') === '') {
                return response()->json([
                    'message' => 'El campo title no puede estar vacío.',
                ], 422);
            }
            
            if (strlen($request->input('title')) > 255) {
                return response()->json([
                    'message' => 'El title no debe ser mayor a 255 caracteres.',
                ], 422);
            }
            
            $task->title = $request->input('title');
        }
        
        if ($request->has('description')) {
            if (!is_string($request->input('description'))) {
                return response()->json([
                    'message' => 'La descripción debe ser texto.',
                ], 422);
            }
            $task->description = $request->input('description');
        }
        
        if ($request->has('status')) {
            $allowedStatus = ['pendiente', 'en progreso', 'completada'];
            if (!in_array($request->input('status'), $allowedStatus)) {
                return response()->json([
                    'message' => 'Status inválido.',
                ], 422);
            }
            $task->status = $request->input('status');
        }
        
        $task->save();
        
        
        return response()->json([
            'success' => true,
            'data' => $task
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar.',
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::delete('/tasks/{id}', function ($id) {
    try {
        
        $task = Task::find($id);
        
        if (!$task) {
            return response()->json([
                'error' => 'No se encontró la tarea',
            ], 404);
        }
        
        $task->delete();
        
        
        return response()->json([
            'mensaje' => 'Tarea eliminada exitosamente',
            'deleted_id' => $id
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'No se pudo eliminar la tarea.',
            'error' => $e->getMessage()
        ], 500);
    }

    });