# Phase 4: Integration

Ship the work: commits, PR, documentation, and Jira update.

## Prerequisites

✅ Phase 3 validation completed
✅ All tests passing
✅ Coverage >80%
✅ Fixtures validated
✅ End-to-end testing successful

## Steps

### 1. Create Postman Collection

Use Postman MCP to document all endpoints:

**Include:**
- All CRUD operations
- Request examples with sample data
- Expected responses (success and error cases)
- Environment variables if needed

**Example structure:**
```
Task API Collection
├── Create Task (POST /api/tasks)
├── List Tasks (GET /api/tasks)
├── Get Task (GET /api/tasks/{id})
├── Update Task (PUT /api/tasks/{id})
└── Delete Task (DELETE /api/tasks/{id})
```

### 2. Create Logical Commits

Follow semantic commit conventions.
See: [Commit Standards](../standards/commits.md)

**Commit Strategy:**
```bash
git add database/migrations/ app/Models/
git commit -m "feat: add Task model and migration

- Created Task model with fillable fields
- Migration includes id, title, description, status, timestamps
- Status defaults to 'pendiente'"

git add database/factories/ database/seeders/
git commit -m "feat: add Task factory and seeder

- TaskFactory generates realistic test data
- Status randomly assigned from valid options
- TaskSeeder creates 20 sample tasks"

git add tests/
git commit -m "test: add comprehensive Task API tests

- Store method with valid/invalid data
- Index method with pagination
- Show, update, delete methods
- AAA pattern throughout
- Coverage: 87%"

git add app/Http/Controllers/ app/Http/Requests/ routes/
git commit -m "feat: implement Task API endpoints

- TaskController with RESTful methods
- StoreTaskRequest with validation rules
- API routes for full CRUD
- Follows Laravel conventions"

git add docs/
git commit -m "docs: add Task API documentation

- Postman collection for all endpoints
- Request/response examples"
```

**Note:** Adjust number of commits based on task complexity. 
Aim for 2-5 logical commits, not one huge commit.

### 3. Create Pull Request

#### PR Title Format
```
feat: Add Task CRUD API
```

#### PR Description Template
```markdown
## Description
Implements Task management API with full CRUD operations.

Closes: PROJ-123

## Changes
- ✅ Task model with migration
- ✅ Factory and seeder for test data
- ✅ Form request validation
- ✅ RESTful API controller
- ✅ Comprehensive tests with AAA pattern

## Technical Details
**Test Coverage:** 87%
**Tests Added:**
- 8 feature tests
- 3 unit tests (validation)

**Files Created:**
- `app/Models/Task.php`
- `database/migrations/2024_xx_xx_create_tasks_table.php`
- `database/factories/TaskFactory.php`
- `database/seeders/TaskSeeder.php`
- `app/Http/Requests/StoreTaskRequest.php`
- `app/Http/Controllers/TaskController.php`
- `tests/Feature/TaskTest.php`

## Validation
✅ All tests passing
✅ Fixtures validated in database (20 records created)
✅ End-to-end testing with cURL completed
✅ Database persistence verified

## API Endpoints
- POST   `/api/tasks` - Create task
- GET    `/api/tasks` - List tasks
- GET    `/api/tasks/{id}` - Get task
- PUT    `/api/tasks/{id}` - Update task
- DELETE `/api/tasks/{id}` - Delete task

## Documentation
Postman Collection: [link or attached]

## Screenshots
[If applicable - database queries, test results, etc.]

## Checklist
- [x] Tests written (TDD)
- [x] Code follows PSR-12
- [x] No phpstan errors
- [x] Migration runs cleanly
- [x] Seeder works correctly
- [x] API tested with cURL
- [x] Database validated
- [x] Documentation complete
```

### 4. Update Jira

#### Add Comment to Task
```markdown
## ✅ Implementation Complete

### Summary
Implemented Task CRUD API with full RESTful endpoints, comprehensive tests, and validation.

### Technical Metrics
- **Test Coverage:** 87%
- **Feature Tests:** 8
- **Unit Tests:** 3
- **Files Created:** 7
- **Lines of Code:** ~350

### Validation Results
✅ **Fixture Validation:**
- 20 test tasks created successfully
- All fields populated correctly
- Status distribution verified

✅ **End-to-End Validation:**
- All CRUD operations tested with cURL
- Database persistence confirmed
- Responses match expected formats

### Links
- **PR:** [GitHub PR link]
- **Postman Collection:** [link or file]

### Commits
1. `feat: add Task model and migration`
2. `feat: add Task factory and seeder`
3. `test: add comprehensive Task API tests`
4. `feat: implement Task API endpoints`

### Ready for Review
All acceptance criteria met. Code is ready for review and merge.
```

#### Change Status to "En Revisión"

Update Jira task status from "En Curso" to "En Revisión"

### 5. Notify Team (if applicable)

- Post in Slack/Teams channel
- Tag relevant reviewers
- Mention any deployment considerations

## Post-Integration

### If Changes Requested in PR
1. Create new commits addressing feedback
2. Push to same branch
3. Update PR with summary of changes
4. Add comment to Jira: "Addressed review feedback"

### After PR Merged
1. Update Jira to "Done"
2. Delete feature branch (if policy allows)
3. Pull latest main branch

## Quality Gates

Before considering Phase 4 complete:
```bash
php artisan test --coverage
./vendor/bin/pint --test
./vendor/bin/phpstan analyse

php artisan migrate:fresh --seed

curl http://localhost/api/tasks
```

All must pass ✅

## Related Documentation

- [Commit Standards](../standards/commits.md)
- [PR Template](../templates/pull-request.md)
- [Jira Workflow](./jira-integration.md)

## Tips

✅ **Do:**
- Write descriptive commit messages
- Keep PR description detailed
- Update Jira with all relevant info
- Include links to everything

❌ **Don't:**
- Rush the PR description
- Forget to update Jira
- Skip the Postman collection
- Make vague commit messages
- Create one massive commit

## Time Estimate

Phase 4 typically takes: **5-10 minutes**
- Postman collection: 2-3 min
- Commits: 2-3 min
- PR creation: 2-3 min
- Jira update: 1-2 min