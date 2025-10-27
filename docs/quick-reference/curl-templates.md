# cURL Templates Quick Reference

Ready-to-use cURL commands for testing API endpoints.

## Basic Structure
```bash
curl [METHOD] [URL] \
  -H "Header: Value" \
  -d 'data'
```

---

## Common Headers

### JSON API Headers
```bash
-H "Content-Type: application/json"
-H "Accept: application/json"
```

### With Authentication
```bash
-H "Authorization: Bearer {token}"
```

### Complete Headers Example
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer your-token-here" \
  -d '{"title": "Task"}'
```

---

## POST Requests (Create)

### Basic POST
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "New Task",
    "description": "Task description",
    "status": "pendiente"
  }'
```

### POST with Minimal Data
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Minimal Task"
  }'
```

### POST with All Fields
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Complete Task",
    "description": "Full description here",
    "status": "en progreso",
    "priority": 3,
    "due_date": "2025-12-31"
  }'
```

### POST from File
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d @task.json
```

**task.json:**
```json
{
  "title": "Task from File",
  "description": "This data comes from a file",
  "status": "pendiente"
}
```

---

## GET Requests (Read)

### List All Resources
```bash
curl http://localhost/api/tasks \
  -H "Accept: application/json"
```

### Get Single Resource
```bash
curl http://localhost/api/tasks/1 \
  -H "Accept: application/json"
```

### With Query Parameters
```bash
curl "http://localhost/api/tasks?status=pendiente" \
  -H "Accept: application/json"

curl "http://localhost/api/tasks?status=pendiente&priority=5" \
  -H "Accept: application/json"

curl "http://localhost/api/tasks?page=2&per_page=10" \
  -H "Accept: application/json"

curl "http://localhost/api/tasks?search=urgent" \
  -H "Accept: application/json"

curl "http://localhost/api/tasks?sort_by=created_at&order=desc" \
  -H "Accept: application/json"
```

### Complex Query String
```bash
curl "http://localhost/api/tasks?status=pendiente&priority=5&page=1&per_page=15&sort_by=due_date&order=asc" \
  -H "Accept: application/json"
```

---

## PUT Requests (Update - Full)

### Basic PUT
```bash
curl -X PUT http://localhost/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Updated Task",
    "description": "Updated description",
    "status": "completada"
  }'
```

### PUT with Partial Data
```bash
curl -X PUT http://localhost/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "completada"
  }'
```

### PUT - Change Status Only
```bash
curl -X PUT http://localhost/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "en progreso"
  }'
```

---

## PATCH Requests (Update - Partial)

### Basic PATCH
```bash
curl -X PATCH http://localhost/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "completada"
  }'
```

### PATCH Multiple Fields
```bash
curl -X PATCH http://localhost/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "completada",
    "priority": 1
  }'
```

---

## DELETE Requests (Destroy)

### Basic DELETE
```bash
curl -X DELETE http://localhost/api/tasks/1 \
  -H "Accept: application/json"
```

### DELETE with Confirmation
```bash
curl -X DELETE http://localhost/api/tasks/1 \
  -H "Accept: application/json" \
  -d '{
    "confirm": true
  }'
```

---

## Testing Validation Errors

### Missing Required Field
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "description": "Missing title field"
  }'
```

### Invalid Enum Value
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Task",
    "status": "invalid-status"
  }'
```

### Exceeding Max Length
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"title\": \"$(printf 'a%.0s' {1..256})\",
    \"status\": \"pendiente\"
  }"
```

### Multiple Validation Errors
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "description": "Both title and status missing/invalid"
  }'
```

---

## Testing Edge Cases

### Empty String vs Null
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Task",
    "description": ""
  }'

curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Task",
    "description": null
  }'
```

### Boundary Values
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"title\": \"$(printf 'a%.0s' {1..255})\",
    \"status\": \"pendiente\"
  }"

curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"title\": \"$(printf 'a%.0s' {1..256})\",
    \"status\": \"pendiente\"
  }"
```

---

## Pretty Output

### Using jq for Formatting
```bash

curl http://localhost/api/tasks \
  -H "Accept: application/json" | jq
```

### Using Python for Formatting
```bash
curl http://localhost/api/tasks \
  -H "Accept: application/json" | python -m json.tool
```

### Save Response to File
```bash
curl http://localhost/api/tasks \
  -H "Accept: application/json" \
  -o response.json
```

---

## Verbose Output

### Show Request/Response Headers
```bash
curl -v http://localhost/api/tasks \
  -H "Accept: application/json"

curl -i http://localhost/api/tasks \
  -H "Accept: application/json"
```

### Show Only HTTP Status Code
```bash
curl -o /dev/null -s -w "%{http_code}\n" http://localhost/api/tasks
```

---

## Authentication Examples

### Bearer Token
```bash
curl http://localhost/api/tasks \
  -H "Accept: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

### Basic Auth
```bash
curl http://localhost/api/tasks \
  -H "Accept: application/json" \
  -u username:password
```

---

## File Upload

### Upload Single File
```bash
curl -X POST http://localhost/api/tasks \
  -H "Accept: application/json" \
  -F "title=Task with File" \
  -F "attachment=@/path/to/file.pdf"
```

### Upload with JSON Data
```bash
curl -X POST http://localhost/api/tasks \
  -H "Accept: application/json" \
  -F "data={\"title\":\"Task\",\"status\":\"pendiente\"};type=application/json" \
  -F "attachment=@/path/to/file.pdf"
```

---

## Complete Testing Sequence

### Full CRUD Test
```bash
echo "Creating task..."
RESPONSE=$(curl -s -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Test Task",
    "description": "Testing full CRUD",
    "status": "pendiente"
  }')
echo $RESPONSE | jq

TASK_ID=$(echo $RESPONSE | jq -r '.id')
echo "Created task ID: $TASK_ID"

echo -e "\nReading task..."
curl -s http://localhost/api/tasks/$TASK_ID \
  -H "Accept: application/json" | jq

echo -e "\nUpdating task..."
curl -s -X PUT http://localhost/api/tasks/$TASK_ID \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Updated Test Task",
    "status": "completada"
  }' | jq

echo -e "\nListing all tasks..."
curl -s http://localhost/api/tasks \
  -H "Accept: application/json" | jq

echo -e "\nDeleting task..."
curl -s -X DELETE http://localhost/api/tasks/$TASK_ID \
  -H "Accept: application/json"

echo -e "\nVerifying deletion..."
curl -s http://localhost/api/tasks/$TASK_ID \
  -H "Accept: application/json" | jq
```

---

## Testing Script Template

### Save as `test-api.sh`
```bash

BASE_URL="http://localhost/api"
HEADERS=(-H "Content-Type: application/json" -H "Accept: application/json")

echo "========================================="
echo "API Testing Script"
echo "========================================="

echo -e "\n[TEST 1] Creating task..."
RESPONSE=$(curl -s -X POST "$BASE_URL/tasks" \
  "${HEADERS[@]}" \
  -d '{
    "title": "Test Task",
    "status": "pendiente"
  }')
echo "$RESPONSE" | jq
TASK_ID=$(echo "$RESPONSE" | jq -r '.id')

echo -e "\n[TEST 2] Getting task $TASK_ID..."
curl -s "$BASE_URL/tasks/$TASK_ID" \
  "${HEADERS[@]}" | jq

echo -e "\n[TEST 3] Updating task $TASK_ID..."
curl -s -X PUT "$BASE_URL/tasks/$TASK_ID" \
  "${HEADERS[@]}" \
  -d '{
    "status": "completada"
  }' | jq

echo -e "\n[TEST 4] Deleting task $TASK_ID..."
curl -s -X DELETE "$BASE_URL/tasks/$TASK_ID" \
  "${HEADERS[@]}"

echo -e "\n========================================="
echo "Tests completed!"
echo "========================================="
```

**Usage:**
```bash
chmod +x test-api.sh
./test-api.sh
```

---

## Environment-Specific URLs

### Local Development
```bash
BASE_URL="http://localhost/api"
```

### Docker
```bash
BASE_URL="http://localhost:8000/api"
```

### Staging
```bash
BASE_URL="https://staging.example.com/api"
```

### Production
```bash
BASE_URL="https://api.example.com"
```

---

## Common Status Codes Reference

| Code | Meaning | When You'll See It |
|------|---------|-------------------|
| 200 | OK | Successful GET, PUT, PATCH |
| 201 | Created | Successful POST |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Malformed JSON syntax |
| 401 | Unauthorized | Missing/invalid authentication |
| 403 | Forbidden | Authenticated but not authorized |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Internal Server Error | Server-side error |

---

## Troubleshooting

### Connection Refused
```bash
curl http://localhost/api/tasks
```

### Invalid JSON
```bash
curl -X POST http://localhost/api/tasks \
  -d '{title: "Task"}'

curl -X POST http://localhost/api/tasks \
  -d '{"title": "Task"}'
```

### CORS Issues
```bash
curl http://localhost/api/tasks \
  -H "Origin: http://example.com" \
  -H "Accept: application/json"
```

---

## Tips & Tricks

### Save Common Commands

Create aliases in `~/.bashrc` or `~/.zshrc`:
```bash
alias api-get='curl -H "Accept: application/json"'
alias api-post='curl -X POST -H "Content-Type: application/json" -H "Accept: application/json"'
alias api-put='curl -X PUT -H "Content-Type: application/json" -H "Accept: application/json"'
alias api-delete='curl -X DELETE -H "Accept: application/json"'
```

**Usage:**
```bash
api-get http://localhost/api/tasks
api-post http://localhost/api/tasks -d '{"title":"Task"}'
```

### Use Variables
```bash
BASE_URL="http://localhost/api"
TASK_ID=1

curl "$BASE_URL/tasks/$TASK_ID" \
  -H "Accept: application/json"
```

---

## Related Documentation

- [API Design Standards](../standards/api-design.md)
- [Phase 3: Validation](../workflow/phase-3-validation.md)
- [MySQL Query Templates](./mysql-queries.md)