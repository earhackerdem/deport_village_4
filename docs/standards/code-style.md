# Code Style Standards

Guidelines for writing clean, consistent, and maintainable code.

## Philosophy

> "Any fool can write code that a computer can understand. Good programmers write code that humans can understand."
> — Martin Fowler

Code should be:
- **Readable** - Easy to understand at a glance
- **Consistent** - Follows conventions throughout
- **Simple** - Avoids unnecessary complexity
- **Maintainable** - Easy to modify and extend
- **Self-documenting** - Clear naming reduces need for comments

---

## PSR-12 Compliance

This project follows [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/).

### Automated Formatting

Use Laravel Pint for automatic formatting:
```bash
./vendor/bin/pint --test

./vendor/bin/pint
```

---

## File Structure

### PHP Files
```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());
        
        return response()->json($task, 201);
    }
}
```

**Rules:**
- Opening `<?php` tag on first line
- One blank line after namespace
- Use statements alphabetically ordered
- One blank line after use statements
- One class per file
- Class name matches filename

---

## Naming Conventions

### Classes

**PascalCase** for class names:
```php
✅ TaskController
✅ StoreTaskRequest
✅ TaskFactory
✅ TaskSeeder

❌ taskController
❌ Task_Controller
❌ task_controller
```

### Methods

**camelCase** for method names:
```php
✅ public function store()
✅ public function getUserTasks()
✅ public function isCompleted()

❌ public function Store()
❌ public function get_user_tasks()
❌ public function is_completed()
```

### Variables

**camelCase** for variable names:
```php
✅ $task
✅ $userData
✅ $isCompleted
✅ $completedTasks

❌ $Task
❌ $user_data
❌ $is_completed
```

### Constants

**UPPER_SNAKE_CASE** for constants:
```php
✅ const MAX_TASKS = 100;
✅ const DEFAULT_STATUS = 'pendiente';
✅ const CACHE_TTL = 3600;

❌ const maxTasks = 100;
❌ const default_status = 'pendiente';
```

### Database

**snake_case** for tables and columns:
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('status');
    $table->timestamp('due_date')->nullable();
    $table->timestamps();
});
```

**Plural** for table names:
```
✅ tasks
✅ users
✅ task_user (pivot table)

❌ task
❌ Task
❌ user_task (wrong order)
```

---

## Type Declarations

### Always Use Type Hints
```php
✅ Good - Type hints everywhere
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create($request->validated());
    return response()->json($task, 201);
}

❌ Bad - No type hints
public function store($request)
{
    $task = Task::create($request->validated());
    return response()->json($task, 201);
}
```

### Scalar Types
```php
public function calculatePriority(int $urgency, bool $isBlocked): int
{
    return $isBlocked ? $urgency * 2 : $urgency;
}
```

### Nullable Types
```php
public function findTask(?int $id): ?Task
{
    return $id ? Task::find($id) : null;
}
```

### Union Types (PHP 8+)
```php
public function process(Task|array $data): void
{
}
```

### Return Types
```php
✅ public function getTask(): Task
✅ public function getTasks(): Collection
✅ public function count(): int
✅ public function isValid(): bool
✅ public function process(): void

❌ public function getTask()
```

---

## Method Structure

### Controller Methods
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $data = $request->validated();
    
    $task = Task::create($data);
    
    return response()->json($task, 201);
}
```

### Keep Methods Short

✅ **Good - Single responsibility:**
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = $this->createTask($request->validated());
    
    return response()->json($task, 201);
}

private function createTask(array $data): Task
{
    return Task::create($data);
}
```

❌ **Bad - Too much in one method:**
```php
public function store(Request $request): JsonResponse
{
    $validated = $request->validate([...]);
    
    $task = Task::create($validated);
    
    Mail::to($user)->send(new TaskCreated($task));
    
    Log::info('Task created', ['task_id' => $task->id]);
    
    Cache::forget('tasks');
    
    return response()->json($task, 201);
}
```

**Rule of thumb:** If method is >20 lines, consider extracting logic.

---

## Indentation and Spacing

### Indentation

Use **4 spaces**, not tabs:
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create($request->validated());
    
    return response()->json($task, 201);
}
```

### Blank Lines
```php
public function index(): JsonResponse
{
    
    $tasks = Task::all();
    
    return response()->json($tasks);
}
```

### Method Chaining
```php
✅ Good - One method per line
$tasks = Task::query()
    ->where('status', 'pendiente')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

❌ Bad - All on one line
$tasks = Task::query()->where('status', 'pendiente')->orderBy('created_at', 'desc')->limit(10)->get();
```

---

## Arrays

### Short Array Syntax
```php
✅ $tasks = ['task1', 'task2'];
✅ $data = ['title' => 'Task', 'status' => 'pendiente'];

❌ $tasks = array('task1', 'task2');
❌ $data = array('title' => 'Task', 'status' => 'pendiente');
```

### Multi-line Arrays
```php
✅ Good - Trailing comma, aligned
$data = [
    'title' => 'New Task',
    'description' => 'Task description',
    'status' => 'pendiente',
    'priority' => 1,
];

❌ Bad - No trailing comma
$data = [
    'title' => 'New Task',
    'description' => 'Task description',
    'status' => 'pendiente'
];
```

---

## Control Structures

### If Statements
```php
✅ Good - Consistent spacing
if ($task->isCompleted()) {
    return response()->json($task);
}

❌ Bad - Inconsistent spacing
if($task->isCompleted()){
    return response()->json($task);
}
```

### Early Returns
```php
✅ Good - Early return reduces nesting
public function show(Task $task): JsonResponse
{
    if (!$task->isPublished()) {
        return response()->json(['message' => 'Not found'], 404);
    }
    
    return response()->json($task);
}

❌ Bad - Unnecessary nesting
public function show(Task $task): JsonResponse
{
    if ($task->isPublished()) {
        return response()->json($task);
    } else {
        return response()->json(['message' => 'Not found'], 404);
    }
}
```

### Ternary Operators
```php
✅ Simple ternary
$status = $task->isCompleted() ? 'completada' : 'pendiente';

❌ Nested ternary (hard to read)
$status = $task->isCompleted() ? 'completada' : ($task->isStarted() ? 'en progreso' : 'pendiente');

✅ Use if/else for complex logic
if ($task->isCompleted()) {
    $status = 'completada';
} elseif ($task->isStarted()) {
    $status = 'en progreso';
} else {
    $status = 'pendiente';
}
```

### Null Coalescing
```php
✅ $description = $task->description ?? 'No description';
✅ $status = $request->input('status') ?? 'pendiente';

❌ $description = isset($task->description) ? $task->description : 'No description';
```

---

## Comments

### When to Comment

✅ **Do comment:**
- Complex business logic
- Non-obvious decisions
- Workarounds for bugs
- TODO items

❌ **Don't comment:**
- Obvious code
- Bad code (refactor instead)
- Outdated information

### Good Comments
```php
✅ Explains WHY
$results = DB::select('SELECT ...');

✅ Explains complex logic
$priority = $task->isUrgent() ? $baseScore * 2 : $baseScore;
if ($task->isBlocked()) {
    $priority *= 3;
}

✅ TODO with context
Mail::to($user)->send(new TaskCreated($task));
```

### Bad Comments
```php
❌ States the obvious
$task = Task::create($data);

❌ Commented out code (use git instead)
$newCode = 'use this';

❌ Outdated information
$this->notifyUser($task);
```

### DocBlocks

Use for **complex methods only**:
```php
✅ Complex method - DocBlock helpful
public function calculatePriority(Task $task, int $baseScore = 10): int
{
}

❌ Simple method - DocBlock redundant
public function getTask(int $id): Task
{
    return Task::findOrFail($id);
}
```

---

## Laravel-Specific Conventions

### Models
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    public function getTitleUppercaseAttribute(): string
    {
        return strtoupper($this->title);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }
}
```

### Controllers
```php
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
    
    public function completed(): JsonResponse
    {
        $tasks = Task::completed()->get();
        
        return response()->json($tasks);
    }
}
```

### Requests
```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pendiente,en progreso,completada',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'status.in' => 'El estado debe ser válido.',
        ];
    }
}
```

---

## String Handling

### String Concatenation
```php
✅ Use string interpolation
$message = "Task '{$task->title}' was created";

✅ Or concatenation for simple cases
$message = 'Task ' . $task->title . ' was created';

❌ Avoid complex concatenation
$message = 'Task ' . $task->title . ' was created by ' . $user->name . ' on ' . $date;

✅ Better - use sprintf or interpolation
$message = sprintf('Task %s was created by %s on %s', $task->title, $user->name, $date);
```

### Single vs Double Quotes
```php
✅ Single quotes for simple strings
$status = 'pendiente';

✅ Double quotes for interpolation
$message = "Task status: {$task->status}";

✅ Use heredoc for multi-line
$html = <<<HTML
<div>
    <h1>{$task->title}</h1>
    <p>{$task->description}</p>
</div>
HTML;
```

---

## Best Practices Summary

### ✅ Do

1. **Use type hints** everywhere
2. **Follow PSR-12** standards
3. **Use meaningful names** for variables/methods
4. **Keep methods short** (<20 lines ideal)
5. **Use early returns** to reduce nesting
6. **Use route model binding** in controllers
7. **Use FormRequests** for validation
8. **Use Eloquent** over raw queries
9. **Use factories** for test data
10. **Run Pint** before committing

### ❌ Don't

1. **Don't use abbreviations** (`$usr`, `$tsk`)
2. **Don't ignore type hints**
3. **Don't write long methods** (>50 lines)
4. **Don't use magic numbers** (use constants)
5. **Don't leave debug code** (`dd()`, `var_dump()`)
6. **Don't comment out code** (use git)
7. **Don't use `array()` syntax** (use `[]`)
8. **Don't chain too many methods** (readability)
9. **Don't ignore validation**
10. **Don't use deprecated functions**

---

## Code Review Checklist

Before submitting code for review:

- [ ] Follows PSR-12 (run `./vendor/bin/pint --test`)
- [ ] All methods have type hints
- [ ] All methods have return types
- [ ] Variable names are descriptive
- [ ] No magic numbers (use constants)
- [ ] No commented-out code
- [ ] No debug statements (`dd`, `dump`, `var_dump`)
- [ ] Methods are short and focused
- [ ] Complex logic is commented
- [ ] Consistent naming conventions
- [ ] Early returns used where appropriate

---

## Tools

### Laravel Pint
```bash
./vendor/bin/pint --test

./vendor/bin/pint

./vendor/bin/pint app/Http/Controllers
```

### PHPStan (Static Analysis)
```bash
./vendor/bin/phpstan analyse

./vendor/bin/phpstan analyse app/Models
```

### PHP CS Fixer (Alternative)
```bash
./vendor/bin/php-cs-fixer fix --dry-run --diff

./vendor/bin/php-cs-fixer fix
```

---

## Related Documentation

- [Testing Standards](./testing.md)
- [API Design Standards](./api-design.md)
- [Commit Standards](./commits.md)
- [PSR-12 Official Spec](https://www.php-fig.org/psr/psr-12/)

---

## Quick Reference

### Naming Quick Guide
```
Classes:      PascalCase     (TaskController)
Methods:      camelCase      (store, getUserTasks)
Variables:    camelCase      ($task, $userData)
Constants:    UPPER_SNAKE    (MAX_TASKS)
Tables:       snake_case     (tasks, user_tasks)
Columns:      snake_case     (created_at, user_id)
```

### Type Hints
```php
string, int, float, bool, array
?int (nullable)
Task (object)
Collection (Laravel collection)
JsonResponse (response)
void (no return)
```

### File Order
```
1. Opening PHP tag
2. Namespace
3. Use statements (alphabetical)
4. Class declaration
5. Class body
```