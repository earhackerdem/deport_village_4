# Testing Standards

Comprehensive guide for writing high-quality tests in this project.

## Philosophy

> "Write tests. Not too many. Mostly integration."
> — Guillermo Rauch

We follow TDD (Test-Driven Development) with emphasis on:
- **Feature tests** over unit tests
- **Behavior** over implementation
- **Readability** over brevity
- **Confidence** over coverage percentage

---

## Test-Driven Development (TDD)

### The Red-Green-Refactor Cycle

**MANDATORY for all new code**
```
🔴 RED Phase
├─ Write a failing test
├─ Test describes desired behavior
├─ Run test: MUST fail
└─ Commit: "test: add failing test for X"

🟢 GREEN Phase
├─ Write minimum code to pass test
├─ Don't worry about perfection
├─ Run test: MUST pass
└─ Commit: "feat: implement X"

🔵 REFACTOR Phase
├─ Improve code quality
├─ Extract methods, rename variables
├─ Run test: MUST still pass
└─ Commit: "refactor: improve X implementation"
```

### Why TDD?

✅ **Better Design**: Tests force you to think about API first
✅ **Confidence**: Know your code works before deployment
✅ **Documentation**: Tests show how to use your code
✅ **Regression Prevention**: Future changes won't break existing features
✅ **Faster Development**: Less time debugging, more time building

---

## AAA Pattern (Arrange-Act-Assert)

**REQUIRED structure for ALL tests**

### Structure
```php
public function test_descriptive_name(): void
{
}
```

### Example: Basic AAA
```php
public function test_store_with_valid_data_creates_task(): void
{
    $data = [
        'title' => 'New Task',
        'description' => 'Task description',
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $response->assertJsonStructure(['id', 'title', 'description', 'status']);
    $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
}
```

### Example: Using Factories
```php
public function test_update_modifies_existing_task(): void
{
    $task = Task::factory()->create(['status' => 'pendiente']);
    $updates = ['status' => 'completada'];

    $response = $this->putJson("/api/tasks/{$task->id}", $updates);

    $response->assertStatus(200);
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'completada'
    ]);
}
```

### AAA Best Practices

✅ **Do:**
- Use comment separators (// Arrange, // Act, // Assert)
- Keep each section focused and clear
- Arrange: Set up all preconditions
- Act: Single action/behavior being tested
- Assert: Multiple assertions OK if related

❌ **Don't:**
- Mix sections together
- Multiple "Act" steps (split into multiple tests)
- Setup in Act section
- Assert in Arrange section

---

## Test Types

### Feature Tests (Primary)

**Use for:** Testing full HTTP request/response cycle
```php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_tasks(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }
}
```

**Feature Test Characteristics:**
- Tests complete user flows
- Uses HTTP verbs (GET, POST, PUT, DELETE)
- Verifies database changes
- Checks JSON responses
- Uses `RefreshDatabase` trait

### Unit Tests (When Needed)

**Use for:** Testing isolated business logic
```php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;

class TaskTest extends TestCase
{
    public function test_is_overdue_returns_true_when_past_due_date(): void
    {
        $task = new Task([
            'title' => 'Overdue Task',
            'due_date' => now()->subDay()
        ]);

        $result = $task->isOverdue();

        $this->assertTrue($result);
    }
}
```

**When to Write Unit Tests:**
- Complex business rules
- Utility methods
- Calculations or algorithms
- String/data manipulation
- NOT for simple getters/setters

---

## Test Naming

### Convention
```
test_{action}_{condition}_{expected_result}
```

### Examples

✅ **Good:**
```php
test_store_with_valid_data_creates_task()
test_store_without_title_returns_422()
test_update_nonexistent_task_returns_404()
test_delete_removes_task_from_database()
test_index_returns_empty_array_when_no_tasks()
```

❌ **Bad:**
```php
test_it_works()
test_task_creation()
test_validation()
test_api()
testStore()
```

### Naming Rules

1. **Always start with** `test_`
2. **Use snake_case** not camelCase
3. **Be descriptive** - name should tell the story
4. **Include condition** - what scenario is being tested
5. **State expectation** - what should happen

---

## Common Test Patterns

### Pattern 1: Testing Successful Creation
```php
public function test_store_with_valid_data_creates_resource_and_returns_201(): void
{
    $data = ['title' => 'Test', 'status' => 'pendiente'];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $response->assertJsonStructure(['id', 'title', 'status', 'created_at']);
    $this->assertDatabaseHas('tasks', ['title' => 'Test']);
}
```

### Pattern 2: Testing Validation Errors
```php
public function test_store_without_required_field_returns_422(): void
{
    $data = ['description' => 'Missing title'];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
}
```

### Pattern 3: Testing with Factories
```php
public function test_show_returns_existing_resource(): void
{
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);
    $response->assertJson(['id' => $task->id]);
}
```

### Pattern 4: Testing Updates
```php
public function test_update_modifies_resource_and_returns_200(): void
{
    $task = Task::factory()->create(['status' => 'pendiente']);
    $updates = ['status' => 'completada'];

    $response = $this->putJson("/api/tasks/{$task->id}", $updates);

    $response->assertStatus(200);
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'completada'
    ]);
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
        'status' => 'pendiente'
    ]);
}
```

### Pattern 5: Testing Deletion
```php
public function test_destroy_removes_resource_and_returns_204(): void
{
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
}
```

### Pattern 6: Testing Not Found
```php
public function test_show_nonexistent_resource_returns_404(): void
{
    $nonexistentId = 99999;

    $response = $this->getJson("/api/tasks/{$nonexistentId}");

    $response->assertStatus(404);
}
```

### Pattern 7: Testing Collections
```php
public function test_index_returns_paginated_results(): void
{
    Task::factory()->count(25)->create();

    $response = $this->getJson('/api/tasks?page=1&per_page=10');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'title', 'status']
        ],
        'meta' => ['current_page', 'total']
    ]);
    $response->assertJsonCount(10, 'data');
}
```

### Pattern 8: Testing Edge Cases
```php
public function test_store_with_maximum_length_title_succeeds(): void
{
    $data = [
        'title' => str_repeat('a', 255),
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
}

public function test_store_with_too_long_title_returns_422(): void
{
    $data = [
        'title' => str_repeat('a', 256),
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
}
```

---

## Assertions

### HTTP Response Assertions
```php
$response->assertStatus(200);
$response->assertOk();
$response->assertCreated();
$response->assertNoContent();
$response->assertNotFound();
$response->assertUnprocessable();

$response->assertJsonStructure([
    'id',
    'title',
    'status',
    'created_at'
]);

$response->assertJson(['status' => 'pendiente']);
$response->assertJsonFragment(['title' => 'Task']);

$response->assertJsonCount(3);
$response->assertJsonCount(10, 'data');

$response->assertJsonValidationErrors(['title']);
$response->assertJsonValidationErrors(['title', 'status']);
```

### Database Assertions
```php
$this->assertDatabaseHas('tasks', [
    'title' => 'Task Title',
    'status' => 'pendiente'
]);

$this->assertDatabaseMissing('tasks', [
    'id' => $task->id
]);

$this->assertDatabaseCount('tasks', 10);
```

### Model Assertions
```php
$this->assertTrue($user->tasks->contains($task));
$this->assertCount(3, $user->tasks);

$this->assertTrue($task->exists);
$this->assertFalse($task->trashed());
$this->assertTrue($task->wasRecentlyCreated);
```

---

## Test Coverage

### Requirements

- **Minimum**: 80%
- **Target**: 90%+
- **Critical paths**: 100%

### Check Coverage
```bash
php artisan test --coverage

php artisan test --coverage-html coverage
```

### What to Cover

✅ **Must Cover:**
- All API endpoints (CRUD operations)
- Validation rules (valid and invalid cases)
- Business logic methods
- Edge cases and error handling
- Database constraints

🤷 **Optional Coverage:**
- Simple getters/setters
- Constructors with no logic
- Framework-provided methods
- Third-party package code

❌ **Don't Cover:**
- Migrations (tested through features)
- Config files
- Routes (tested through features)

---

## Best Practices

### ✅ Do

1. **Write tests first** (TDD)
2. **Use AAA pattern** consistently
3. **Test behavior**, not implementation
4. **One assertion concept per test**
5. **Use factories** for test data
6. **Use RefreshDatabase** for feature tests
7. **Name tests descriptively**
8. **Test edge cases** and errors
9. **Keep tests independent** (no order dependency)
10. **Make tests fast** (use factories, not seeders in tests)

### ❌ Don't

1. **Don't skip tests** ("I'll add them later")
2. **Don't test implementation details**
3. **Don't use production data** in tests
4. **Don't make tests depend on each other**
5. **Don't test framework code**
6. **Don't ignore failing tests**
7. **Don't write tests after implementation**
8. **Don't use sleep()** in tests (use mocks/fakes)
9. **Don't test too many things** in one test
10. **Don't use vague test names**

---

## Common Mistakes

### ❌ Testing Implementation
```php
public function test_store_calls_create_method(): void
{
    Task::shouldReceive('create')->once();
    $this->postJson('/api/tasks', $data);
}
```

### ✅ Testing Behavior
```php
public function test_store_creates_task_in_database(): void
{
    $this->postJson('/api/tasks', $data);
    $this->assertDatabaseHas('tasks', $data);
}
```

### ❌ Multiple Unrelated Assertions
```php
public function test_everything(): void
{
    $response = $this->postJson('/api/tasks', $data);
    $response->assertStatus(201);
    
    $response = $this->getJson('/api/tasks');
    $response->assertStatus(200);
    
    $response = $this->deleteJson('/api/tasks/1');
    $response->assertStatus(204);
}
```

### ✅ Focused Tests
```php
public function test_store_creates_task(): void { ... }
public function test_index_returns_tasks(): void { ... }
public function test_destroy_deletes_task(): void { ... }
```

### ❌ Sharing State Between Tests
```php
private static $taskId;

public function test_create_task(): void
{
    $response = $this->postJson('/api/tasks', $data);
    self::$taskId = $response->json('id');
}

public function test_update_task(): void
{
    $this->putJson("/api/tasks/" . self::$taskId, $updates);
}
```

### ✅ Independent Tests
```php
public function test_update_task(): void
{
    $task = Task::factory()->create();
    
    $response = $this->putJson("/api/tasks/{$task->id}", $updates);
    $response->assertStatus(200);
}
```

---

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
php artisan test tests/Feature/TaskTest.php
```

### Run Specific Test Method
```bash
php artisan test --filter=test_store_with_valid_data
```

### Run with Coverage
```bash
php artisan test --coverage
php artisan test --coverage --min=80
```

### Run in Parallel (faster)
```bash
php artisan test --parallel
```

---

## Test Organization

### File Structure
```
tests/
├── Feature/
│   ├── TaskTest.php
│   ├── UserTest.php
│   └── ...
├── Unit/
│   ├── TaskBusinessLogicTest.php
│   └── ...
└── TestCase.php
```

### Grouping Tests
```php
class TaskTest extends TestCase
{
    public function test_store_with_valid_data_creates_task(): void { ... }
    public function test_store_without_title_fails(): void { ... }
    
    public function test_index_returns_all_tasks(): void { ... }
    public function test_show_returns_single_task(): void { ... }
    
    public function test_update_modifies_task(): void { ... }
    
    public function test_destroy_removes_task(): void { ... }
}
```

---

## Related Documentation

- [Test Examples](../examples/test-examples.md)
- [Factory Examples](../examples/factory-examples.md)
- [Phase 2: Implementation](../workflow/phase-2-implementation.md)

---

## Quick Reference

### Test Checklist

- [ ] Using RefreshDatabase trait
- [ ] Follows AAA pattern
- [ ] Descriptive test name
- [ ] Tests behavior, not implementation
- [ ] Includes assertions for database state
- [ ] Includes assertions for response status
- [ ] Tests edge cases
- [ ] Tests validation errors
- [ ] No hardcoded IDs or data
- [ ] Independent (no shared state)
- [ ] Fast (< 100ms per test)

### Coverage Targets
```
Overall:     >80%
Controllers: >90%
Models:      >85%
Requests:    100%
```

### Time Estimates

- Feature test: 2-5 minutes to write
- Unit test: 1-3 minutes to write
- Test should run: < 100ms each