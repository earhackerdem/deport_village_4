# Phase 2: Implementation

Code implementation following TDD principles.

## Prerequisites

✅ Phase 1 completed and approved
✅ User confirmation received
✅ Implementation plan reviewed and accepted

## Initial Steps

### 1. Create Feature Branch
```bash
git checkout -b feature/task-description
```

**Branch Naming Convention:**
- `feature/` - New features
- `fix/` - Bug fixes
- `refactor/` - Code refactoring
- `test/` - Adding tests

### 2. Update Jira Status
Change task status to **"En Curso"**

## TDD Cycle (MANDATORY)

Follow strict Red-Green-Refactor cycle for EVERY feature:
```
🔴 RED: Write failing test
    ↓
    Run test (must fail)
    ↓
🟢 GREEN: Write minimum code to pass
    ↓
    Run test (must pass)
    ↓
🔵 REFACTOR: Improve code quality
    ↓
    Run test (must still pass)
    ↓
    Repeat for next test
```

**NEVER write implementation code before writing the test!**

## Implementation Order

### 1. Model + Migration

#### a) Generate Files
```bash
make shell
php artisan make:model Task -m
```

This creates:
- `app/Models/Task.php`
- `database/migrations/YYYY_MM_DD_HHMMSS_create_tasks_table.php`

#### b) Define Migration First
```php
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('status')->default('pendiente');
        $table->timestamps();
    });
}
```

#### c) Run Migration
```bash
php artisan migrate
```

#### d) Configure Model
```php
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

### 2. Factory

#### Generate Factory
```bash
php artisan make:factory TaskFactory
```

#### Define Factory
```php
public function definition(): array
{
    return [
        'title' => fake()->sentence(3),
        'description' => fake()->paragraph(),
        'status' => fake()->randomElement(['pendiente', 'en progreso', 'completada']),
    ];
}
```

**Factory Best Practices:**
- Use realistic data via Faker
- Keep data concise (sentence(3) not sentence())
- Match validation rules
- Consider edge cases (nullable fields)

See: `docs/examples/factory-examples.md`

### 3. Seeder

#### Generate Seeder
```bash
php artisan make:seeder TaskSeeder
```

#### Define Seeder
```php
public function run(): void
{
    Task::factory()->count(20)->create();
}
```

#### Register in DatabaseSeeder
```php
public function run(): void
{
    $this->call([
        TaskSeeder::class,
    ]);
}
```

### 4. Form Request (Validation)

#### Generate Request
```bash
php artisan make:request StoreTaskRequest
```

#### Define Validation
```php
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
```

**Validation Best Practices:**
- Match database constraints
- Use appropriate rules (required, nullable, sometimes)
- Validate enums with `in:` rule
- Add custom messages if needed

See: `docs/examples/request-examples.md`

### 5. Tests + Controller (TDD!)

**THIS IS WHERE TDD REALLY HAPPENS**

#### Step A: Create Test File FIRST
```bash
php artisan make:test TaskTest
```

#### Step B: Write First Test (RED)
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_with_valid_data_creates_task_and_returns_201(): void
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'Test description',
            'status' => 'pendiente'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'title',
            'description',
            'status',
            'created_at',
            'updated_at'
        ]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'status' => 'pendiente'
        ]);
    }
}
```

#### Step C: Run Test (MUST FAIL - RED)
```bash
php artisan test --filter=test_store_with_valid_data
```

Expected: ❌ Test fails (route doesn't exist yet)

#### Step D: Create Controller
```bash
php artisan make:controller TaskController --api
```

#### Step E: Implement Minimum Code (GREEN)
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

#### Step F: Define Route
```php
use App\Http\Controllers\TaskController;

Route::post('/tasks', [TaskController::class, 'store']);
```

#### Step G: Run Test Again (MUST PASS - GREEN)
```bash
php artisan test --filter=test_store_with_valid_data
```

Expected: ✅ Test passes

#### Step H: Refactor If Needed (REFACTOR)

Check if code can be improved:
- Extract methods?
- Simplify logic?
- Add type hints?
- Improve naming?

Run test after refactoring to ensure it still passes.

### 6. Additional Tests (Continue TDD Cycle)

#### Test 2: Invalid Data (RED → GREEN → REFACTOR)
```php
public function test_store_without_title_returns_422_validation_error(): void
{
    $data = [
        'description' => 'Description without title',
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
}
```

Run test → Should pass (FormRequest handles validation automatically)

#### Test 3: Invalid Status
```php
public function test_store_with_invalid_status_returns_422(): void
{
    $data = [
        'title' => 'Task',
        'status' => 'invalid-status'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status']);
}
```

#### Test 4: Nullable Fields
```php
public function test_store_without_description_creates_task(): void
{
    $data = [
        'title' => 'Task without description'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Task without description',
        'description' => null
    ]);
}
```

#### Test 5: Default Values
```php
public function test_store_without_status_uses_default_pendiente(): void
{
    $data = [
        'title' => 'Task without status'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Task without status',
        'status' => 'pendiente'
    ]);
}
```

### 7. If More Endpoints Required

Repeat TDD cycle for each endpoint:

**Index (GET /api/tasks):**
```php
public function test_index_returns_all_tasks(): void
{
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200);
    $response->assertJsonCount(3);
}

public function index(): JsonResponse
{
    return response()->json(Task::all());
}

Route::get('/tasks', [TaskController::class, 'index']);
```

**Show (GET /api/tasks/{id}):**
```php
public function test_show_returns_existing_task(): void
{
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $task->id,
        'title' => $task->title
    ]);
}

public function show(Task $task): JsonResponse
{
    return response()->json($task);
}

Route::get('/tasks/{task}', [TaskController::class, 'show']);
```

## Quality Checklist

Before moving to Phase 3, verify:

### Run All Tests
```bash
php artisan test
```
All must pass ✅

### Check Test Coverage
```bash
php artisan test --coverage
```
Must be >80% ✅

### Code Style (if configured)
```bash
./vendor/bin/pint --test
```

### Static Analysis (if configured)
```bash
./vendor/bin/phpstan analyse
```

### Manual Verification
```bash
php artisan migrate:fresh

php artisan db:seed --class=TaskSeeder

php artisan route:list | grep tasks
```

## Common Patterns

### RESTful Controller Methods
```php
public function index(): JsonResponse
{
    return response()->json(Task::all());
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
```

### Response Status Codes

- `200` - OK (successful GET, PUT)
- `201` - Created (successful POST)
- `204` - No Content (successful DELETE)
- `422` - Unprocessable Entity (validation failed)
- `404` - Not Found (resource doesn't exist)
- `401` - Unauthorized (not authenticated)
- `403` - Forbidden (not authorized)

## TDD Benefits You're Achieving

✅ **Confidence**: Tests prove code works
✅ **Design**: Tests force good API design
✅ **Documentation**: Tests show how to use code
✅ **Refactoring**: Tests enable safe refactoring
✅ **Regression**: Tests prevent future breaks

## Tips

### ✅ Do:
- Write test FIRST, always
- Keep tests focused (one assertion per test)
- Use AAA pattern consistently
- Run tests frequently (after every change)
- Keep commits small and logical
- Follow Laravel conventions
- Use type hints and return types
- Name things clearly and descriptively

### ❌ Don't:
- Skip tests ("I'll add them later")
- Write implementation before test
- Create files manually (use artisan)
- Ignore failing tests
- Mix multiple features in one commit
- Use vague variable names
- Forget to run migrations
- Skip FormRequest validation

## Common Mistakes to Avoid

### ❌ Writing Implementation First
```php
public function store() { ... }
```

### ✅ Writing Test First
```php
public function test_store_creates_task() { ... }
```

### ❌ Testing Implementation Details
```php
$this->assertTrue($task->wasRecentlyCreated);
```

### ✅ Testing Behavior
```php
$response->assertStatus(201);
$this->assertDatabaseHas('tasks', $data);
```

### ❌ Multiple Assertions Without Clear Sections
```php
$response = $this->postJson('/api/tasks', $data);
$response->assertStatus(201);
$this->assertDatabaseHas('tasks', $data);
```

### ✅ Clear AAA Sections
```php
$data = ['title' => 'Task'];

$response = $this->postJson('/api/tasks', $data);

$response->assertStatus(201);
$this->assertDatabaseHas('tasks', $data);
```

## After Implementation

Once all tests pass and coverage is >80%:

1. ⚠️ Do NOT commit yet
2. ⚠️ Do NOT create PR yet
3. ✅ Move to [Phase 3: Validation](./phase-3-validation.md)
4. ✅ Validate with fixtures and cURL
5. ✅ Then proceed to Phase 4 for commits and PR

## Related Documentation

- [Testing Standards](../standards/testing.md)
- [Test Examples](../examples/test-examples.md)
- [API Design Standards](../standards/api-design.md)
- [Controller Examples](../examples/controller-examples.md)
- [Artisan Commands](../quick-reference/artisan-commands.md)

## Time Estimate

Phase 2 typically takes: **20-40 minutes**
- Model + Migration: 3-5 min
- Factory + Seeder: 3-5 min
- Form Request: 2-3 min
- Tests + Controller (TDD): 15-25 min
- Additional endpoints: +5-10 min each

**Remember:** Time spent on tests is NOT wasted time. 
Tests save time by preventing bugs and enabling confident refactoring.

## Success Criteria

Phase 2 is complete when:

✅ All files generated with artisan commands
✅ All tests written BEFORE implementation
✅ All tests pass
✅ Test coverage >80%
✅ Code follows Laravel conventions
✅ Migrations run cleanly
✅ Seeders generate data correctly
✅ No manual file creation
✅ AAA pattern used in all tests
✅ Type hints and return types present