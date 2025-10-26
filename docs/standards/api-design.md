# API Design Standards

Guidelines for designing consistent, RESTful, and maintainable APIs.

## Philosophy

> "Good API design is about making the simple things easy and the complex things possible."

Our APIs should be:
- **Predictable** - Follow conventions consistently
- **Self-documenting** - Clear naming and structure
- **Forgiving** - Handle errors gracefully
- **Versioned** - Support evolution without breaking clients
- **Secure** - Protected against common vulnerabilities

---

## RESTful Principles

### Resources

Use **nouns**, not verbs, for resource names:

✅ **Good:**
```
GET    /api/tasks
POST   /api/tasks
GET    /api/tasks/1
PUT    /api/tasks/1
DELETE /api/tasks/1
```

❌ **Bad:**
```
GET    /api/getTasks
POST   /api/createTask
GET    /api/showTask/1
PUT    /api/updateTask/1
DELETE /api/deleteTask/1
```

### HTTP Methods (Verbs)

Use HTTP methods to represent actions:

| Method | Action | Response | Idempotent |
|--------|--------|----------|------------|
| GET    | Retrieve | 200 + resource | Yes |
| POST   | Create | 201 + resource | No |
| PUT    | Update (full) | 200 + resource | Yes |
| PATCH  | Update (partial) | 200 + resource | Yes |
| DELETE | Remove | 204 (no content) | Yes |

**Idempotent:** Multiple identical requests have the same effect as a single request.

---

## Standard CRUD Endpoints

### Index (List All)

**Request:**
```
GET /api/tasks
```

**Response: 200 OK**
```json
[
  {
    "id": 1,
    "title": "First Task",
    "description": "Description here",
    "status": "pendiente",
    "created_at": "2025-10-25T10:00:00.000000Z",
    "updated_at": "2025-10-25T10:00:00.000000Z"
  },
  {
    "id": 2,
    "title": "Second Task",
    "description": null,
    "status": "completada",
    "created_at": "2025-10-25T11:00:00.000000Z",
    "updated_at": "2025-10-25T11:00:00.000000Z"
  }
]
```

**Controller:**
```php
public function index(): JsonResponse
{
    $tasks = Task::all();
    return response()->json($tasks);
}
```

### Store (Create)

**Request:**
```
POST /api/tasks
Content-Type: application/json

{
  "title": "New Task",
  "description": "Task description",
  "status": "pendiente"
}
```

**Response: 201 Created**
```json
{
  "id": 3,
  "title": "New Task",
  "description": "Task description",
  "status": "pendiente",
  "created_at": "2025-10-25T12:00:00.000000Z",
  "updated_at": "2025-10-25T12:00:00.000000Z"
}
```

**Controller:**
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create($request->validated());
    return response()->json($task, 201);
}
```

### Show (Get Single)

**Request:**
```
GET /api/tasks/1
```

**Response: 200 OK**
```json
{
  "id": 1,
  "title": "First Task",
  "description": "Description here",
  "status": "pendiente",
  "created_at": "2025-10-25T10:00:00.000000Z",
  "updated_at": "2025-10-25T10:00:00.000000Z"
}
```

**Response: 404 Not Found** (if doesn't exist)
```json
{
  "message": "No query results for model [App\\Models\\Task] 999"
}
```

**Controller:**
```php
public function show(Task $task): JsonResponse
{
    return response()->json($task);
}
```

### Update (Modify)

**Request:**
```
PUT /api/tasks/1
Content-Type: application/json

{
  "title": "Updated Task",
  "description": "Updated description",
  "status": "completada"
}
```

**Response: 200 OK**
```json
{
  "id": 1,
  "title": "Updated Task",
  "description": "Updated description",
  "status": "completada",
  "created_at": "2025-10-25T10:00:00.000000Z",
  "updated_at": "2025-10-25T12:30:00.000000Z"
}
```

**Controller:**
```php
public function update(UpdateTaskRequest $request, Task $task): JsonResponse
{
    $task->update($request->validated());
    return response()->json($task);
}
```

### Destroy (Delete)

**Request:**
```
DELETE /api/tasks/1
```

**Response: 204 No Content**
```
(empty body)
```

**Controller:**
```php
public function destroy(Task $task): JsonResponse
{
    $task->delete();
    return response()->json(null, 204);
}
```

---

## Status Codes

### Success Codes (2xx)

| Code | Meaning | When to Use |
|------|---------|-------------|
| 200 | OK | Successful GET, PUT, PATCH |
| 201 | Created | Successful POST (resource created) |
| 204 | No Content | Successful DELETE |

### Client Error Codes (4xx)

| Code | Meaning | When to Use |
|------|---------|-------------|
| 400 | Bad Request | Malformed request syntax |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Authenticated but not authorized |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |

### Server Error Codes (5xx)

| Code | Meaning | When to Use |
|------|---------|-------------|
| 500 | Internal Server Error | Unexpected server error |
| 503 | Service Unavailable | Temporary maintenance |

---

## Response Formats

### Success Response Structure

**Simple resource:**
```json
{
  "id": 1,
  "title": "Task",
  "status": "pendiente",
  "created_at": "2025-10-25T10:00:00.000000Z",
  "updated_at": "2025-10-25T10:00:00.000000Z"
}
```

**Collection (simple):**
```json
[
  { "id": 1, "title": "Task 1" },
  { "id": 2, "title": "Task 2" }
]
```

**Collection (with metadata):**
```json
{
  "data": [
    { "id": 1, "title": "Task 1" },
    { "id": 2, "title": "Task 2" }
  ],
  "meta": {
    "total": 50,
    "count": 2,
    "per_page": 15,
    "current_page": 1,
    "total_pages": 4
  }
}
```

### Error Response Structure

**Validation Error (422):**
```json
{
  "message": "The title field is required. (and 1 more error)",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "status": [
      "The selected status is invalid."
    ]
  }
}
```

**Not Found (404):**
```json
{
  "message": "No query results for model [App\\Models\\Task] 999"
}
```

**Server Error (500):**
```json
{
  "message": "Server Error",
  "error": "Something went wrong"
}
```

---

## Validation

### FormRequest Pattern (Preferred)

**Create Request:**
```php
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
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'status.in' => 'El estado debe ser: pendiente, en progreso o completada.',
        ];
    }
}
```

**Controller:**
```php
public function store(StoreTaskRequest $request): JsonResponse
{
    $task = Task::create($request->validated());
    return response()->json($task, 201);
}
```

### Common Validation Rules
```php
'title' => 'required|string|max:255'

'description' => 'nullable|string'

'status' => 'required|in:pendiente,en progreso,completada'

'due_date' => 'nullable|date|after:today'

'priority' => 'sometimes|integer|between:1,5'

'email' => 'required|email|unique:users'

'user_id' => 'required|exists:users,id'

'tags' => 'sometimes|array'
'tags.*' => 'string|max:50'

'attachment' => 'sometimes|file|mimes:pdf,doc,docx|max:10240'
```

---

## Naming Conventions

### Resource Names

✅ **Use plural nouns:**
```
/api/tasks
/api/users
/api/projects
```

❌ **Don't use singular:**
```
/api/task
/api/user
```

### Nested Resources

✅ **Good - Clear hierarchy:**
```
GET /api/projects/1/tasks        # All tasks in project 1
GET /api/projects/1/tasks/5      # Task 5 in project 1
POST /api/projects/1/tasks       # Create task in project 1
```

❌ **Bad - Too deep:**
```
GET /api/organizations/1/departments/2/teams/3/projects/4/tasks/5
```

**Limit nesting to 2 levels maximum**

### Query Parameters

✅ **Use snake_case:**
```
GET /api/tasks?status=pendiente
GET /api/tasks?per_page=20&page=2
GET /api/tasks?sort_by=created_at&order=desc
GET /api/tasks?search=urgent
```

### Field Names

✅ **Use snake_case in JSON:**
```json
{
  "id": 1,
  "title": "Task",
  "created_at": "2025-10-25T10:00:00.000000Z",
  "updated_at": "2025-10-25T10:00:00.000000Z",
  "is_completed": false,
  "due_date": "2025-10-30"
}
```

❌ **Don't use camelCase:**
```json
{
  "id": 1,
  "createdAt": "...",
  "isCompleted": false
}
```

---

## Filtering, Sorting, and Pagination

### Filtering
```
GET /api/tasks?status=pendiente
GET /api/tasks?status=pendiente&created_after=2025-10-01
GET /api/tasks?search=urgent
```

**Implementation:**
```php
public function index(Request $request): JsonResponse
{
    $query = Task::query();

    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    if ($request->has('search')) {
        $query->where('title', 'like', "%{$request->search}%");
    }

    return response()->json($query->get());
}
```

### Sorting
```
GET /api/tasks?sort_by=created_at&order=desc
GET /api/tasks?sort_by=title&order=asc
```

**Implementation:**
```php
public function index(Request $request): JsonResponse
{
    $sortBy = $request->input('sort_by', 'created_at');
    $order = $request->input('order', 'desc');

    $tasks = Task::orderBy($sortBy, $order)->get();
    
    return response()->json($tasks);
}
```

### Pagination
```
GET /api/tasks?page=1&per_page=15
```

**Implementation:**
```php
public function index(Request $request): JsonResponse
{
    $perPage = $request->input('per_page', 15);
    $tasks = Task::paginate($perPage);
    
    return response()->json($tasks);
}
```

**Response:**
```json
{
  "data": [
    { "id": 1, "title": "Task 1" },
    { "id": 2, "title": "Task 2" }
  ],
  "links": {
    "first": "http://api.example.com/tasks?page=1",
    "last": "http://api.example.com/tasks?page=4",
    "prev": null,
    "next": "http://api.example.com/tasks?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 4,
    "per_page": 15,
    "to": 15,
    "total": 50
  }
}
```

---

## Route Definition

### Using apiResource (Recommended)
```php
use App\Http\Controllers\TaskController;

Route::apiResource('tasks', TaskController::class);
```

**Generates:**
```
GET    /api/tasks          → TaskController@index
POST   /api/tasks          → TaskController@store
GET    /api/tasks/{task}   → TaskController@show
PUT    /api/tasks/{task}   → TaskController@update
PATCH  /api/tasks/{task}   → TaskController@update
DELETE /api/tasks/{task}   → TaskController@destroy
```

### Partial Resources
```php
Route::apiResource('tasks', TaskController::class)
    ->only(['index', 'show', 'store']);

Route::apiResource('tasks', TaskController::class)
    ->except(['destroy']);
```

### Custom Routes
```php
Route::get('tasks/completed', [TaskController::class, 'completed']);
Route::post('tasks/{task}/complete', [TaskController::class, 'markComplete']);
```

---

## Headers

### Request Headers

**Required:**
```
Content-Type: application/json
Accept: application/json
```

**Optional (if authentication):**
```
Authorization: Bearer {token}
```

### Response Headers

**Laravel automatically includes:**
```
Content-Type: application/json
```

**Set custom headers:**
```php
return response()->json($data, 200)
    ->header('X-Custom-Header', 'value');
```

---

## Best Practices

### ✅ Do

1. **Use consistent naming**
```
   /api/tasks (not /api/task or /api/todo)
```

2. **Return appropriate status codes**
```php
   return response()->json($task, 201);
   return response()->json($task);
   return response()->json(null, 204);
```

3. **Use FormRequests for validation**
```php
   public function store(StoreTaskRequest $request)
```

4. **Use route model binding**
```php
   public function show(Task $task)
```

5. **Return created resource after POST**
```php
   $task = Task::create($request->validated());
   return response()->json($task, 201);
```

6. **Use timestamps**
```php
   $table->timestamps();
```

7. **Version your API (when needed)**
```
   /api/v1/tasks
   /api/v2/tasks
```

### ❌ Don't

1. **Don't use verbs in URLs**
```
   ❌ POST /api/createTask
   ✅ POST /api/tasks
```

2. **Don't return HTML from API**
```php
   ❌ return view('tasks.show', $task);
   ✅ return response()->json($task);
```

3. **Don't ignore validation**
```php
   ❌ Task::create($request->all());
   ✅ Task::create($request->validated());
```

4. **Don't expose internal errors**
```php
   ❌ return response()->json(['error' => $exception->getMessage()]);
   ✅ return response()->json(['message' => 'Server Error'], 500);
```

5. **Don't nest too deeply**
```
   ❌ /api/orgs/1/depts/2/teams/3/projects/4/tasks/5
   ✅ /api/projects/4/tasks/5
```

6. **Don't use sessions**
```php
   ❌ session(['user_id' => $id]);
   ✅ Use tokens or JWT
```

---

## API Documentation

### Postman Collection Structure
```
Task API
├── Tasks
│   ├── List All Tasks (GET)
│   ├── Create Task (POST)
│   ├── Get Task (GET)
│   ├── Update Task (PUT)
│   └── Delete Task (DELETE)
├── Validation Examples
│   ├── Missing Title (422)
│   └── Invalid Status (422)
└── Environment Variables
    ├── base_url: http:
    └── token: (if needed)
```

### Example Request Documentation

**Endpoint:** Create Task

**Method:** `POST`

**URL:** `/api/tasks`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
  "title": "New Task",
  "description": "Optional description",
  "status": "pendiente"
}
```

**Success Response (201):**
```json
{
  "id": 1,
  "title": "New Task",
  "description": "Optional description",
  "status": "pendiente",
  "created_at": "2025-10-25T10:00:00.000000Z",
  "updated_at": "2025-10-25T10:00:00.000000Z"
}
```

**Error Response (422):**
```json
{
  "message": "The title field is required.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

---

## Security Considerations

### Input Validation
```php
public function store(StoreTaskRequest $request)
{
    $task = Task::create($request->validated());
    return response()->json($task, 201);
}

public function store(Request $request)
{
    $task = Task::create($request->all());
    return response()->json($task, 201);
}
```

### Mass Assignment Protection
```php
class Task extends Model
{
    protected $fillable = ['title', 'description', 'status'];
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
```

### SQL Injection Prevention
```php
Task::where('status', $request->status)->get();

DB::select('SELECT * FROM tasks WHERE status = ?', [$status]);

DB::select("SELECT * FROM tasks WHERE status = '$status'");
```

---

## Related Documentation

- [Testing Standards](./testing.md)
- [Controller Examples](../examples/controller-examples.md)
- [Request Examples](../examples/request-examples.md)
- [Phase 2: Implementation](../workflow/phase-2-implementation.md)

---

## Quick Reference

### HTTP Methods
```
GET    → Retrieve
POST   → Create
PUT    → Update (full)
PATCH  → Update (partial)
DELETE → Remove
```

### Status Codes
```
200 → OK
201 → Created
204 → No Content
404 → Not Found
422 → Validation Error
500 → Server Error
```

### Standard Routes
```
GET    /api/resources
POST   /api/resources
GET    /api/resources/{id}
PUT    /api/resources/{id}
DELETE /api/resources/{id}
```