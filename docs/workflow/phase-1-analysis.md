# Phase 1: Analysis

Understanding and planning before coding.

## Objectives

- Fetch and understand task requirements
- Evaluate project readiness
- Create detailed implementation plan
- Get user confirmation before proceeding

## Steps

### 1. Fetch Task from Jira

Use Jira MCP to get highest priority assigned task:
- Filter by: assigned to me, status = Backlog or To Do
- Sort by: priority DESC
- Take: first result

### 2. Update Jira Status

Change status to **"En Progreso"**

### 3. Understand Requirements

Read carefully:
- Task description
- **All Acceptance Criteria** (critical!)
- Any attachments or references
- Related tasks or dependencies

### 4. Evaluate Project State

Check:
- Current codebase structure
- Existing similar features
- Dependencies available
- Potential conflicts
- Database schema

Commands to run:
````bash
# Check project structure
ls -la app/Models app/Http/Controllers

# Check migrations
ls -la database/migrations

# Check routes
cat routes/api.php

# Check existing tests
ls -la tests/Feature tests/Unit
````

### 5. Create Implementation Plan

Present a detailed plan including:

#### a) Strategy
Step-by-step approach to meet all Acceptance Criteria

#### b) Tests (TDD)
List of tests you'll write with AAA pattern:
````
Feature Tests:
- test_store_with_valid_data_creates_resource
- test_store_with_invalid_data_returns_422
- test_store_without_required_field_fails_validation
- test_index_returns_all_resources
- test_show_returns_existing_resource
- test_show_with_invalid_id_returns_404
- test_update_with_valid_data_updates_resource
- test_destroy_deletes_resource
````

#### c) Artisan Commands
Exact commands you'll use:
````bash
php artisan make:model Task -m
php artisan make:factory TaskFactory
php artisan make:seeder TaskSeeder
php artisan make:request StoreTaskRequest
php artisan make:controller TaskController --api
php artisan make:test TaskTest
````

#### d) Technical Considerations
- Edge cases
- Security considerations
- Performance implications
- Migration strategies
- Data validation rules

#### e) Database Schema
Show the migration structure:
````php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('status')->default('pendiente');
    $table->timestamps();
});
````

#### f) Questions
Any ambiguities or clarifications needed

## Plan Template
````markdown
## Implementation Plan for [Task Name]

### Overview
[Brief summary of what will be built]

### Acceptance Criteria Mapping
✅ AC1: Create Task model and migration
   → Will use: php artisan make:model Task -m
   → Fields: id, title, description, status, timestamps

✅ AC2: Create Factory and Seeder
   → TaskFactory with realistic Faker data
   → TaskSeeder generates 20 tasks
   → Called from DatabaseSeeder

✅ AC3: Form Request validation
   → StoreTaskRequest with rules
   → title: required|string|max:255
   → description: nullable|string
   → status: sometimes|in:pendiente,en progreso,completada

✅ AC4: Controller and Routes
   → TaskController --api
   → Implement store method
   → Route: POST /api/tasks

### Test Strategy (TDD with AAA Pattern)

#### Feature Tests:
1. test_store_with_valid_data_creates_task_and_returns_201
   - Arrange: valid task data
   - Act: POST to /api/tasks
   - Assert: 201 status, database has record

2. test_store_without_title_returns_422_validation_error
   - Arrange: data without title
   - Act: POST to /api/tasks
   - Assert: 422 status, validation errors present

3. test_store_with_invalid_status_returns_422
   - Arrange: data with invalid status
   - Act: POST to /api/tasks
   - Assert: 422 status, status validation error

4. test_store_saves_data_correctly_in_database
   - Arrange: complete task data
   - Act: POST to /api/tasks
   - Assert: assertDatabaseHas with exact data

### Files to Create (Artisan Commands)
```bash
# 1. Model + Migration
php artisan make:model Task -m

# 2. Factory
php artisan make:factory TaskFactory

# 3. Seeder
php artisan make:seeder TaskSeeder

# 4. Form Request
php artisan make:request StoreTaskRequest

# 5. API Controller
php artisan make:controller TaskController --api

# 6. Feature Test
php artisan make:test TaskTest
```

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('status')->default('pendiente');
    $table->timestamps();
});
```

```php
class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status'
    ];
}
```

```php
public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'sometimes|in:pendiente,en progreso,completada',
    ];
}
```

```php
Route::post('/tasks', [TaskController::class, 'store']);
```

**Edge Cases:**
- Empty title should fail validation
- Invalid status values should be rejected
- Nullable description should work with null or empty string
- Default status should be 'pendiente' if not provided

**Security:**
- Using Form Request for validation (automatically returns 422)
- Mass assignment protection via $fillable
- Input sanitization handled by Laravel

**Performance:**
- Simple CRUD operation, no performance concerns
- Index on status field could be added if querying by status frequently

**Data Integrity:**
- Status field constrained to specific values
- Title is required (NOT NULL in DB)
- Timestamps for auditing

1. Should we add an index endpoint (GET /api/tasks) as well, or only store for now?
2. Do we need authentication/authorization for this endpoint?
3. Should status transitions be validated (e.g., can only go from pendiente → en progreso → completada)?
4. Is pagination needed for the index endpoint if we add it?
````

## ⚠️ Critical Checkpoint

**STOP HERE**

Do NOT proceed to implementation until receiving explicit user confirmation.

### Valid Confirmations:
- "Proceed"
- "Go ahead"
- "Looks good, continue"
- "Approved"
- "Yes, implement it"
- "👍"

### Invalid (Need Clarification):
- "Maybe add X?" → Adjust plan first, re-present
- "What about Y?" → Answer question, re-present plan
- "I'm not sure about..." → Clarify, adjust plan
- Silence → Wait patiently for response

## After Confirmation

Once confirmed:
1. Acknowledge: "✅ Proceeding with implementation"
2. Move to Phase 2: Implementation
3. Update Jira status to "En Curso"
4. Create feature branch

## Related Documentation

- [Phase 2: Implementation](./phase-2-implementation.md)
- [Testing Standards](../standards/testing.md)
- [Code Examples](../examples/)

## Tips for Good Analysis

### ✅ Do:
- Be thorough and detailed
- Identify edge cases proactively
- Ask clarifying questions
- Reference existing code patterns
- Estimate complexity realistically
- Show exact commands to be used
- Map each Acceptance Criterion to implementation

### ❌ Don't:
- Rush to implementation
- Assume requirements
- Skip edge case analysis
- Forget to update Jira status
- Proceed without confirmation
- Be vague about approach
- Ignore existing patterns in codebase

## Time Estimate

Phase 1 typically takes: **3-5 minutes**
- Fetch task: 30 sec
- Update Jira: 30 sec
- Understand requirements: 1-2 min
- Evaluate project: 1 min
- Create plan: 1-2 min

## Success Criteria

A good analysis includes:
✅ Clear understanding of ALL acceptance criteria
✅ Complete list of tests to write
✅ Exact artisan commands listed
✅ Database schema defined
✅ Edge cases identified
✅ Questions asked if anything unclear
✅ User confirmation received before proceeding