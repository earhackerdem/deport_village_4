# Test Examples

Comprehensive examples of tests following AAA pattern and TDD principles.

## Basic Feature Test Structure
```php
<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_with_valid_data_creates_task_and_returns_201(): void
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
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
            'title' => 'New Task',
            'status' => 'pendiente'
        ]);
    }
}
```

---

## POST/Create Tests

### Test: Successful Creation
```php
public function test_store_with_valid_data_creates_task(): void
{
    $data = [
        'title' => 'Complete documentation',
        'description' => 'Write comprehensive API docs',
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $response->assertJson([
        'title' => 'Complete documentation',
        'status' => 'pendiente'
    ]);
    $this->assertDatabaseHas('tasks', $data);
}
```

### Test: Required Field Validation
```php
public function test_store_without_title_returns_422(): void
{
    $data = [
        'description' => 'Missing title field',
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
}
```

### Test: Invalid Enum Value
```php
public function test_store_with_invalid_status_returns_422(): void
{
    $data = [
        'title' => 'Task with invalid status',
        'status' => 'invalid-status'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status']);
    $response->assertJsonFragment([
        'status' => ['The selected status is invalid.']
    ]);
}
```

### Test: Optional Field (Nullable)
```php
public function test_store_without_description_creates_task(): void
{
    $data = [
        'title' => 'Task without description',
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Task without description',
        'description' => null
    ]);
}
```

### Test: Default Values
```php
public function test_store_without_status_uses_default_pendiente(): void
{
    $data = [
        'title' => 'Task without explicit status'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Task without explicit status',
        'status' => 'pendiente'
    ]);
}
```

### Test: Maximum Length Validation
```php
public function test_store_with_title_exceeding_255_chars_returns_422(): void
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

### Test: Exact Maximum Length (Edge Case)
```php
public function test_store_with_title_exactly_255_chars_succeeds(): void
{
    $title = str_repeat('a', 255);
    $data = [
        'title' => $title,
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', ['title' => $title]);
}
```

---

## GET/Read Tests

### Test: List All Resources (Index)
```php
public function test_index_returns_all_tasks(): void
{
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200);
    $response->assertJsonCount(3);
    $response->assertJsonStructure([
        '*' => ['id', 'title', 'description', 'status', 'created_at', 'updated_at']
    ]);
}
```

### Test: Empty Collection
```php
public function test_index_returns_empty_array_when_no_tasks(): void
{

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200);
    $response->assertJsonCount(0);
    $response->assertExactJson([]);
}
```

### Test: Show Single Resource
```php
public function test_show_returns_existing_task(): void
{
    $task = Task::factory()->create([
        'title' => 'Specific Task',
        'status' => 'pendiente'
    ]);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $task->id,
        'title' => 'Specific Task',
        'status' => 'pendiente'
    ]);
}
```

### Test: Resource Not Found
```php
public function test_show_with_nonexistent_id_returns_404(): void
{
    $nonexistentId = 99999;

    $response = $this->getJson("/api/tasks/{$nonexistentId}");

    $response->assertStatus(404);
}
```

### Test: Filtering
```php
public function test_index_filters_by_status(): void
{
    Task::factory()->create(['status' => 'pendiente']);
    Task::factory()->create(['status' => 'pendiente']);
    Task::factory()->create(['status' => 'completada']);

    $response = $this->getJson('/api/tasks?status=pendiente');

    $response->assertStatus(200);
    $response->assertJsonCount(2);
    $response->assertJsonFragment(['status' => 'pendiente']);
}
```

### Test: Pagination
```php
public function test_index_returns_paginated_results(): void
{
    Task::factory()->count(25)->create();

    $response = $this->getJson('/api/tasks?page=1&per_page=10');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => ['*' => ['id', 'title']],
        'meta' => ['total', 'current_page', 'per_page']
    ]);
    $response->assertJsonCount(10, 'data');
    $response->assertJsonPath('meta.total', 25);
}
```

---

## PUT/Update Tests

### Test: Successful Update
```php
public function test_update_modifies_existing_task(): void
{
    $task = Task::factory()->create([
        'title' => 'Original Title',
        'status' => 'pendiente'
    ]);
    $updates = [
        'title' => 'Updated Title',
        'status' => 'completada'
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $updates);

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $task->id,
        'title' => 'Updated Title',
        'status' => 'completada'
    ]);
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
        'status' => 'completada'
    ]);
}
```

### Test: Partial Update
```php
public function test_update_with_only_status_changes_status_only(): void
{
    $task = Task::factory()->create([
        'title' => 'Keep This Title',
        'status' => 'pendiente'
    ]);
    $updates = ['status' => 'completada'];

    $response = $this->putJson("/api/tasks/{$task->id}", $updates);

    $response->assertStatus(200);
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Keep This Title',
        'status' => 'completada'
    ]);
}
```

### Test: Update Validation
```php
public function test_update_with_invalid_status_returns_422(): void
{
    $task = Task::factory()->create();
    $updates = ['status' => 'invalid-status'];

    $response = $this->putJson("/api/tasks/{$task->id}", $updates);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status']);
}
```

### Test: Update Nonexistent Resource
```php
public function test_update_nonexistent_task_returns_404(): void
{
    $nonexistentId = 99999;
    $updates = ['title' => 'Updated'];

    $response = $this->putJson("/api/tasks/{$nonexistentId}", $updates);

    $response->assertStatus(404);
}
```

### Test: Timestamps Updated
```php
public function test_update_changes_updated_at_timestamp(): void
{
    $task = Task::factory()->create();
    $originalUpdatedAt = $task->updated_at;
    
    sleep(1);
    
    $updates = ['title' => 'Updated Title'];

    $response = $this->putJson("/api/tasks/{$task->id}", $updates);

    $response->assertStatus(200);
    $task->refresh();
    $this->assertNotEquals($originalUpdatedAt, $task->updated_at);
}
```

---

## DELETE/Destroy Tests

### Test: Successful Deletion
```php
public function test_destroy_removes_task_from_database(): void
{
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
}
```

### Test: Delete Nonexistent Resource
```php
public function test_destroy_nonexistent_task_returns_404(): void
{
    $nonexistentId = 99999;

    $response = $this->deleteJson("/api/tasks/{$nonexistentId}");

    $response->assertStatus(404);
}
```

### Test: Delete Reduces Count
```php
public function test_destroy_reduces_task_count(): void
{
    Task::factory()->count(3)->create();
    $task = Task::factory()->create();
    $initialCount = Task::count();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);
    $this->assertEquals($initialCount - 1, Task::count());
}
```

---

## Tests with Relationships

### Test: Create with Relationship
```php
public function test_store_task_associates_with_user(): void
{
    $user = User::factory()->create();
    $data = [
        'title' => 'User Task',
        'user_id' => $user->id
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $task = Task::find($response->json('id'));
    $this->assertTrue($task->user->is($user));
}
```

### Test: Eager Loading
```php
public function test_index_eager_loads_user_relationship(): void
{
    $user = User::factory()->create();
    Task::factory()->for($user)->count(3)->create();

    $queryCount = 0;
    DB::listen(function ($query) use (&$queryCount) {
        $queryCount++;
    });
    
    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200);
    $this->assertEquals(2, $queryCount);
}
```

---

## Complex Validation Tests

### Test: Multiple Validation Errors
```php
public function test_store_with_multiple_errors_returns_all_validation_errors(): void
{
    $data = [
        'title' => '',
        'status' => 'invalid'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title', 'status']);
    $response->assertJsonFragment([
        'title' => ['The title field is required.']
    ]);
    $response->assertJsonFragment([
        'status' => ['The selected status is invalid.']
    ]);
}
```

### Test: Conditional Validation
```php
public function test_store_with_due_date_must_be_future_date(): void
{
    $data = [
        'title' => 'Task with past due date',
        'due_date' => now()->subDay()->format('Y-m-d')
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['due_date']);
}
```

---

## Authentication/Authorization Tests

### Test: Unauthenticated Request
```php
public function test_store_without_authentication_returns_401(): void
{
    $data = ['title' => 'Task'];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(401);
}
```

### Test: Authenticated Request
```php
public function test_store_with_authentication_succeeds(): void
{
    $user = User::factory()->create();
    $data = ['title' => 'Authenticated Task'];

    $response = $this->actingAs($user)->postJson('/api/tasks', $data);

    $response->assertStatus(201);
}
```

### Test: Unauthorized Action
```php
public function test_update_other_user_task_returns_403(): void
{
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($owner)->create();
    $updates = ['title' => 'Unauthorized Update'];

    $response = $this->actingAs($otherUser)
        ->putJson("/api/tasks/{$task->id}", $updates);

    $response->assertStatus(403);
}
```

---

## Testing with Factories

### Test: Using Factory States
```php
public function test_index_returns_only_completed_tasks(): void
{
    Task::factory()->count(2)->create(['status' => 'completada']);
    Task::factory()->count(3)->create(['status' => 'pendiente']);

    $response = $this->getJson('/api/tasks?status=completada');

    $response->assertStatus(200);
    $response->assertJsonCount(2);
}
```

### Test: Using Custom Factory Methods
```php
public function test_overdue_tasks_returns_only_past_due_date_tasks(): void
{
    Task::factory()->count(2)->create([
        'due_date' => now()->subDay()
    ]);
    Task::factory()->count(3)->create([
        'due_date' => now()->addDay()
    ]);

    $response = $this->getJson('/api/tasks/overdue');

    $response->assertStatus(200);
    $response->assertJsonCount(2);
}
```

---

## Database Assertion Helpers

### Multiple Database Checks
```php
public function test_store_creates_task_with_all_fields(): void
{
    $data = [
        'title' => 'Complete Task',
        'description' => 'Full description',
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('tasks', ['title' => 'Complete Task']);
    $this->assertDatabaseHas('tasks', ['description' => 'Full description']);
    $this->assertDatabaseHas('tasks', ['status' => 'pendiente']);
    
    $this->assertDatabaseHas('tasks', $data);
}
```

### Database Count
```php
public function test_store_increases_task_count_by_one(): void
{
    $initialCount = Task::count();
    $data = ['title' => 'New Task'];

    $this->postJson('/api/tasks', $data);

    $this->assertEquals($initialCount + 1, Task::count());
    $this->assertDatabaseCount('tasks', $initialCount + 1);
}
```

---

## Edge Cases and Error Handling

### Test: Empty String vs Null
```php
public function test_store_with_empty_description_string_stores_null(): void
{
    $data = [
        'title' => 'Task',
        'description' => ''
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Task',
        'description' => null
    ]);
}
```

### Test: Whitespace Handling
```php
public function test_store_trims_whitespace_from_title(): void
{
    $data = [
        'title' => '  Task with spaces  ',
        'status' => 'pendiente'
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('tasks', [
        'title' => 'Task with spaces'
    ]);
}
```

---

## Performance Tests

### Test: Query Count (N+1 Prevention)
```php
public function test_index_avoids_n_plus_1_queries(): void
{
    User::factory()
        ->has(Task::factory()->count(3))
        ->count(5)
        ->create();

    DB::enableQueryLog();
    $response = $this->getJson('/api/tasks');
    $queryCount = count(DB::getQueryLog());

    $response->assertStatus(200);
    $this->assertLessThanOrEqual(2, $queryCount);
}
```

---

## Test Organization Best Practices
```php
<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_with_valid_data_creates_task(): void { }
    public function test_store_without_title_returns_422(): void { }
    public function test_store_with_invalid_status_returns_422(): void { }
    
    public function test_index_returns_all_tasks(): void { }
    public function test_show_returns_existing_task(): void { }
    public function test_show_with_nonexistent_id_returns_404(): void { }
    
    public function test_update_modifies_existing_task(): void { }
    public function test_update_with_invalid_data_returns_422(): void { }
    
    public function test_destroy_removes_task(): void { }
    public function test_destroy_nonexistent_task_returns_404(): void { }
}
```

---

## Related Documentation

- [Testing Standards](../standards/testing.md)
- [Factory Examples](./factory-examples.md)
- [Phase 2: Implementation](../workflow/phase-2-implementation.md)