# Controller Examples

Comprehensive examples for API controllers following RESTful conventions and Laravel best practices.

## Basic API Controller Structure
```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::all();
        
        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());
        
        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        
        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        
        return response()->json(null, 204);
    }
}
```

---

## Index Method Variations

### Simple List
```php
public function index(): JsonResponse
{
    $tasks = Task::all();
    
    return response()->json($tasks);
}
```

### With Eager Loading
```php
public function index(): JsonResponse
{
    $tasks = Task::with(['user', 'tags'])->get();
    
    return response()->json($tasks);
}
```

### With Filtering
```php
public function index(Request $request): JsonResponse
{
    $query = Task::query();

    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    if ($request->has('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->has('search')) {
        $query->where('title', 'like', "%{$request->search}%");
    }

    $tasks = $query->get();
    
    return response()->json($tasks);
}
```

### With Sorting
```php
public function index(Request $request): JsonResponse
{
    $sortBy = $request->input('sort_by', 'created_at');
    $order = $request->input('order', 'desc');

    $allowedSorts = ['created_at', 'title', 'status', 'priority'];
    
    if (!in_array($sortBy, $allowedSorts)) {
        $sortBy = 'created_at';
    }

    $tasks = Task::orderBy($sortBy, $order)->get();
    
    return response()->json($tasks);
}
```

### With Pagination
```php
public function index(Request $request): JsonResponse
{
    $perPage = $request->input('per_page', 15);
    
    $tasks = Task::paginate($perPage);
    
    return response()->json($tasks);
}
```

### Complete Index with All Features
```php
public function index(Request $request): JsonResponse
{
    $query = Task::query();

    $query->with(['user', 'tags']);

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%")
              ->orWhere('description', 'like', "%{$request->search}%");
        });
    }

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->filled('created_after')) {
        $query->where('created_at', '>=', $request->created_after);
    }

    if ($request->filled('created_before')) {
        $query->where('created_at', '<=', $request->created_before);
    }

    $sortBy = $request->input('sort_by', 'created_at');
    $order = $request->input('order', 'desc');
    
    $allowedSorts = ['created_at', 'updated_at', 'title', 'status', 'priority'];
    if (in_array($sortBy, $allowedSorts)) {
        $query->orderBy($sortBy, $order);
    }

    $perPage = $request->input('per_page', 15);
    $tasks = $query->paginate($perPage);
    
    return response()->json($tasks);
}
```

---

## Store Method Variations

### Basic Store
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create($request->validated());
    
    return response()->json($task, 201);
}
```

### Store with Authenticated User
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create([
        ...$request->validated(),
        'user_id' => auth()->id(),
    ]);
    
    return response()->json($task, 201);
}
```

### Store with Relationship
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create($request->validated());
    
    if ($request->has('tag_ids')) {
        $task->tags()->attach($request->tag_ids);
    }
    
    $task->load(['user', 'tags']);
    
    return response()->json($task, 201);
}
```

### Store with Additional Processing
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create($request->validated());
    
    $task->slug = Str::slug($task->title);
    $task->save();
    
    $task->activities()->create([
        'type' => 'created',
        'user_id' => auth()->id(),
    ]);
    
    return response()->json($task, 201);
}
```

### Store with File Upload
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $data = $request->validated();
    
    if ($request->hasFile('attachment')) {
        $path = $request->file('attachment')->store('tasks', 'public');
        $data['attachment_path'] = $path;
    }
    
    $task = Task::create($data);
    
    return response()->json($task, 201);
}
```

---

## Show Method Variations

### Basic Show
```php
public function show(Task $task): JsonResponse
{
    return response()->json($task);
}
```

### Show with Relationships
```php
public function show(Task $task): JsonResponse
{
    $task->load(['user', 'tags', 'comments']);
    
    return response()->json($task);
}
```

### Show with Conditional Loading
```php
public function show(Request $request, Task $task): JsonResponse
{
    $with = $request->input('with', []);
    
    $allowed = ['user', 'tags', 'comments', 'activities'];
    $with = array_intersect($with, $allowed);
    
    if (!empty($with)) {
        $task->load($with);
    }
    
    return response()->json($task);
}
```

### Show with Authorization Check
```php
public function show(Task $task): JsonResponse
{
    if (!$task->isVisibleTo(auth()->user())) {
        return response()->json([
            'message' => 'You do not have permission to view this task.'
        ], 403);
    }
    
    return response()->json($task);
}
```

### Show with Additional Data
```php
public function show(Task $task): JsonResponse
{
    $task->load(['user', 'tags']);
    
    return response()->json([
        'task' => $task,
        'meta' => [
            'comments_count' => $task->comments()->count(),
            'is_overdue' => $task->isOverdue(),
            'days_remaining' => $task->daysRemaining(),
        ],
    ]);
}
```

---

## Update Method Variations

### Basic Update
```php
public function update(UpdateTaskRequest $request, Task $task): JsonResponse
{
    $task->update($request->validated());
    
    return response()->json($task);
}
```

### Update with Partial Data
```php
public function update(UpdateTaskRequest $request, Task $task): JsonResponse
{
    $task->fill($request->validated());
    
    if ($task->isDirty()) {
        $task->save();
    }
    
    return response()->json($task);
}
```

### Update with Status Change Logic
```php
public function update(UpdateTaskRequest $request, Task $task): JsonResponse
{
    $oldStatus = $task->status;
    
    $task->update($request->validated());
    
    if ($task->status !== $oldStatus) {
        if ($task->status === 'completada') {
            $task->completed_at = now();
            $task->completed_by = auth()->id();
            $task->save();
        }
    }
    
    return response()->json($task);
}
```

### Update with Relationship Sync
```php
public function update(UpdateTaskRequest $request, Task $task): JsonResponse
{
    $task->update($request->validated());
    
    if ($request->has('tag_ids')) {
        $task->tags()->sync($request->tag_ids);
    }
    
    $task->load(['tags']);
    
    return response()->json($task);
}
```

### Update with File Replacement
```php
public function update(UpdateTaskRequest $request, Task $task): JsonResponse
{
    $data = $request->validated();
    
    if ($request->hasFile('attachment')) {
        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }
        
        $path = $request->file('attachment')->store('tasks', 'public');
        $data['attachment_path'] = $path;
    }
    
    $task->update($data);
    
    return response()->json($task);
}
```

---

## Destroy Method Variations

### Basic Destroy
```php
public function destroy(Task $task): JsonResponse
{
    $task->delete();
    
    return response()->json(null, 204);
}
```

### Destroy with Authorization
```php
public function destroy(Task $task): JsonResponse
{
    if ($task->user_id !== auth()->id()) {
        return response()->json([
            'message' => 'You do not have permission to delete this task.'
        ], 403);
    }
    
    $task->delete();
    
    return response()->json(null, 204);
}
```

### Soft Delete
```php
public function destroy(Task $task): JsonResponse
{
    $task->delete();
    
    return response()->json([
        'message' => 'Task archived successfully.'
    ], 200);
}
```

### Destroy with Cascade
```php
public function destroy(Task $task): JsonResponse
{
    $task->comments()->delete();
    $task->activities()->delete();
    
    $task->tags()->detach();
    
    $task->delete();
    
    return response()->json(null, 204);
}
```

### Destroy with File Cleanup
```php
public function destroy(Task $task): JsonResponse
{
    if ($task->attachment_path) {
        Storage::disk('public')->delete($task->attachment_path);
    }
    
    $task->delete();
    
    return response()->json(null, 204);
}
```

---

## Custom Actions

### Mark as Complete
```php
public function complete(Task $task): JsonResponse
{
    if ($task->status === 'completada') {
        return response()->json([
            'message' => 'Task is already completed.'
        ], 400);
    }
    
    $task->update([
        'status' => 'completada',
        'completed_at' => now(),
        'completed_by' => auth()->id(),
    ]);
    
    return response()->json($task);
}
```

### Bulk Operations
```php
public function bulkDestroy(Request $request): JsonResponse
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:tasks,id',
    ]);
    
    $count = Task::whereIn('id', $request->ids)->delete();
    
    return response()->json([
        'message' => "{$count} tasks deleted successfully.",
        'count' => $count,
    ]);
}

public function bulkUpdate(Request $request): JsonResponse
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:tasks,id',
        'status' => 'required|in:pendiente,en progreso,completada',
    ]);
    
    $count = Task::whereIn('id', $request->ids)
        ->update(['status' => $request->status]);
    
    return response()->json([
        'message' => "{$count} tasks updated successfully.",
        'count' => $count,
    ]);
}
```

### Statistics
```php
public function statistics(): JsonResponse
{
    $stats = [
        'total' => Task::count(),
        'by_status' => Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status'),
        'completed_today' => Task::where('status', 'completada')
            ->whereDate('completed_at', today())
            ->count(),
        'overdue' => Task::where('status', '!=', 'completada')
            ->where('due_date', '<', now())
            ->count(),
    ];
    
    return response()->json($stats);
}
```

### Export
```php
public function export(Request $request): JsonResponse
{
    $format = $request->input('format', 'json');
    
    $tasks = Task::all();
    
    if ($format === 'csv') {
        $csv = "ID,Title,Status,Created At\n";
        foreach ($tasks as $task) {
            $csv .= "{$task->id},{$task->title},{$task->status},{$task->created_at}\n";
        }
        
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="tasks.csv"');
    }
    
    return response()->json($tasks);
}
```

---

## Error Handling

### Try-Catch Pattern
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    try {
        $task = Task::create($request->validated());
        
        return response()->json($task, 201);
    } catch (\Exception $e) {
        Log::error('Failed to create task', [
            'error' => $e->getMessage(),
            'data' => $request->validated(),
        ]);
        
        return response()->json([
            'message' => 'Failed to create task. Please try again.',
        ], 500);
    }
}
```

### Custom Error Responses
```php
public function show(Task $task): JsonResponse
{
    if (!$task->isPublished() && $task->user_id !== auth()->id()) {
        return response()->json([
            'message' => 'Task not found.',
        ], 404);
    }
    
    return response()->json($task);
}
```

---

## Resource Controllers with Services

### Controller with Service Layer
```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(): JsonResponse
    {
        $tasks = $this->taskService->getAllTasks();
        
        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());
        
        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->updateTask($task, $request->validated());
        
        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->deleteTask($task);
        
        return response()->json(null, 204);
    }
}
```

---

## API Resources (Transformers)

### Using API Resources
```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskCollection;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::paginate(15);
        
        return (new TaskCollection($tasks))->response();
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());
        
        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Task $task): JsonResponse
    {
        return (new TaskResource($task))->response();
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        
        return (new TaskResource($task))->response();
    }
}
```

---

## Best Practices

### ✅ Do

1. **Use route model binding**
```php
   ✅ public function show(Task $task)
   ❌ public function show($id) { $task = Task::findOrFail($id); }
```

2. **Use FormRequests for validation**
```php
   ✅ public function store(StoreTaskRequest $request)
   ❌ public function store(Request $request) { $request->validate([...]); }
```

3. **Use type hints everywhere**
```php
   ✅ public function store(StoreTaskRequest $request): JsonResponse
   ❌ public function store($request)
```

4. **Return appropriate status codes**
```php
   ✅ return response()->json($task, 201);
   ✅ return response()->json(null, 204);
```

5. **Keep controllers thin**
```php
   ✅ Extract complex logic to services
   ❌ Put all business logic in controller
```

### ❌ Don't

1. **Don't validate in controller**
```php
   ❌ $request->validate([...]);
   ✅ Use FormRequest
```

2. **Don't use findOrFail manually**
```php
   ❌ $task = Task::findOrFail($id);
   ✅ Use route model binding
```

3. **Don't return views from API**
```php
   ❌ return view('tasks.index');
   ✅ return response()->json($tasks);
```

4. **Don't ignore status codes**
```php
   ❌ return response()->json($task);
   ✅ return response()->json($task, 201);
```

---

## Related Documentation

- [API Design Standards](../standards/api-design.md)
- [Request Examples](./request-examples.md)
- [Test Examples](./test-examples.md)
- [Phase 2: Implementation](../workflow/phase-2-implementation.md)