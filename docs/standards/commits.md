# Commit Standards

Guide for writing clear, semantic, and useful commit messages.

## Philosophy

> "A commit message shows whether a developer is a good collaborator."
> — Peter Hintjens

Good commits:
- Tell a story of how the code evolved
- Make code reviews easier
- Help with debugging and troubleshooting
- Enable better changelogs and release notes
- Show professionalism

---

## Semantic Commit Format

### Structure
```
<type>: <subject>

<body>

<footer>
```

### Components

#### Type (Required)

Describes the kind of change:

- `feat`: New feature
- `fix`: Bug fix
- `test`: Adding or updating tests
- `refactor`: Code change that neither fixes a bug nor adds a feature
- `docs`: Documentation changes
- `style`: Formatting, missing semicolons, etc. (no code change)
- `perf`: Performance improvement
- `chore`: Maintenance tasks, dependencies, build changes

#### Subject (Required)

- Short summary (50 characters or less)
- Imperative mood ("add" not "added" or "adds")
- No period at the end
- Lowercase after type

#### Body (Optional but Recommended)

- Explain WHAT and WHY, not HOW
- Wrap at 72 characters
- Leave blank line after subject
- Use bullet points for multiple items

#### Footer (Optional)

- Reference issues: `Closes #123` or `Refs #456`
- Breaking changes: `BREAKING CHANGE: description`

---

## Examples

### Example 1: Simple Feature
```
feat: add Task model and migration

- Created Task model with fillable fields
- Migration includes id, title, description, status, timestamps
- Status defaults to 'pendiente'
```

### Example 2: Test Addition
```
test: add comprehensive Task API tests

- Store method with valid/invalid data
- Index method returns all tasks
- Show method with existing/non-existent IDs
- Update and destroy methods
- All tests use AAA pattern
- Coverage: 87%
```

### Example 3: Implementation
```
feat: implement Task API endpoints

- TaskController with store, index, show, update, destroy
- StoreTaskRequest with validation rules
- API routes for full CRUD
- Returns appropriate HTTP status codes
- Follows RESTful conventions
```

### Example 4: Bug Fix
```
fix: correct status validation in StoreTaskRequest

The status field was accepting invalid values. Changed validation
rule from 'string' to 'in:pendiente,en progreso,completada'.

Closes #123
```

### Example 5: Refactoring
```
refactor: extract task creation logic to service

Moved task creation logic from controller to TaskService to improve
testability and separation of concerns. Controller now delegates to
service while maintaining the same API contract.
```

### Example 6: Documentation
```
docs: add Postman collection for Task API

- All CRUD endpoints documented
- Request/response examples included
- Environment variables configured
```

### Example 7: Multiple Changes (NOT Recommended - Split Instead)
```
feat: implement Task factory and seeder

- TaskFactory generates realistic test data using Faker
- Status randomly assigned from valid options
- TaskSeeder creates 20 sample tasks
- Registered TaskSeeder in DatabaseSeeder
```

---

## Type Guidelines

### `feat:` - New Features

**When to use:**
- Adding new functionality
- Creating new files (models, controllers, etc.)
- Implementing new API endpoints
- Adding new business logic

**Examples:**
```
feat: add Task model and migration
feat: implement store method in TaskController
feat: add due_date field to tasks
feat: create Postman collection for Task API
```

### `test:` - Tests

**When to use:**
- Adding new tests
- Updating existing tests
- Improving test coverage
- Fixing flaky tests

**Examples:**
```
test: add Task API feature tests
test: add validation tests for StoreTaskRequest
test: improve coverage for TaskController
test: fix flaky test in TaskTest
```

### `fix:` - Bug Fixes

**When to use:**
- Fixing bugs
- Correcting validation rules
- Resolving errors
- Patching security issues

**Examples:**
```
fix: correct status validation rule
fix: handle null description in Task model
fix: resolve 500 error on task creation
fix: prevent SQL injection in search query
```

### `refactor:` - Code Improvements

**When to use:**
- Restructuring code without changing behavior
- Extracting methods
- Renaming for clarity
- Improving performance
- Removing dead code

**Examples:**
```
refactor: extract task validation to FormRequest
refactor: simplify TaskController store method
refactor: rename confusing variable names
refactor: remove unused imports
```

### `docs:` - Documentation

**When to use:**
- Adding/updating README
- Creating documentation files
- Adding code comments
- Updating API documentation

**Examples:**
```
docs: add API documentation for Task endpoints
docs: update README with setup instructions
docs: add inline comments for complex logic
docs: create workflow documentation
```

### `style:` - Formatting

**When to use:**
- Code formatting (PSR-12)
- Fixing linting issues
- Whitespace changes
- No functional changes

**Examples:**
```
style: apply PSR-12 formatting
style: fix indentation in TaskController
style: remove trailing whitespace
```

### `perf:` - Performance

**When to use:**
- Improving performance
- Optimizing queries
- Reducing memory usage
- Caching implementations

**Examples:**
```
perf: add index on tasks.status column
perf: eager load relationships in TaskController
perf: implement query caching for task list
```

### `chore:` - Maintenance

**When to use:**
- Dependency updates
- Build configuration changes
- CI/CD updates
- Tool configuration

**Examples:**
```
chore: update Laravel to 12.x
chore: configure PHPUnit for coverage
chore: add GitHub Actions workflow
chore: update composer dependencies
```

---

## Commit Workflow for This Project

### Scenario: Implementing CRUD API

**Step 1: Database Layer**
```bash
git add app/Models/Task.php database/migrations/*create_tasks_table.php
git commit -m "feat: add Task model and migration

- Created Task model with fillable fields
- Migration includes id, title, description, status, timestamps
- Status defaults to 'pendiente'"
```

**Step 2: Test Data**
```bash
git add database/factories/TaskFactory.php database/seeders/TaskSeeder.php
git commit -m "feat: add Task factory and seeder

- TaskFactory generates realistic test data with Faker
- Status randomly assigned from valid options  
- TaskSeeder creates 20 sample tasks
- Registered in DatabaseSeeder"
```

**Step 3: Tests (TDD)**
```bash
git add tests/Feature/TaskTest.php
git commit -m "test: add comprehensive Task API tests

- Store method with valid/invalid data (AAA pattern)
- Index method returns all tasks
- Show method with existing/non-existent IDs
- Update and destroy methods
- Validation error scenarios
- Coverage: 87%"
```

**Step 4: Implementation**
```bash
git add app/Http/Controllers/TaskController.php \
       app/Http/Requests/StoreTaskRequest.php \
       routes/api.php
git commit -m "feat: implement Task API endpoints

- TaskController with full CRUD operations
- StoreTaskRequest with validation rules
- API routes following RESTful conventions
- Returns appropriate HTTP status codes (201, 200, 204, 422)"
```

**Step 5: Documentation**
```bash
git add docs/postman/task-api-collection.json
git commit -m "docs: add Postman collection for Task API

- All CRUD endpoints documented
- Request examples with sample data
- Response examples for success/error cases
- Environment variables configured"
```

---

## Best Practices

### ✅ Do

1. **Write in imperative mood**
```
   ✅ feat: add Task model
   ❌ feat: added Task model
   ❌ feat: adds Task model
```

2. **Be specific and descriptive**
```
   ✅ feat: add validation for task status field
   ❌ feat: add validation
```

3. **Keep subject line short** (≤50 chars)
```
   ✅ feat: add Task API endpoints
   ❌ feat: add Task API endpoints with full CRUD operations including validation
```

4. **Explain WHY in body when needed**
```
   refactor: extract validation to FormRequest

   Moving validation from controller to FormRequest improves
   testability and follows Laravel best practices. Also makes
   validation rules reusable across multiple methods.
```

5. **Reference issues in footer**
```
   fix: correct task status validation

   Status field was accepting any string value. Updated to
   restrict to valid enum values.

   Closes #123
```

6. **Make atomic commits** (one logical change)
```
   ✅ Commit 1: feat: add Task model
   ✅ Commit 2: test: add Task model tests
   ✅ Commit 3: feat: add TaskController

   ❌ Commit 1: feat: add everything for tasks
```

7. **Capitalize after colon**
```
   ❌ feat: Add task model (uppercase A)
   ✅ feat: add Task model (lowercase a, uppercase T for proper noun)
```

### ❌ Don't

1. **Don't use vague messages**
```
   ❌ fix stuff
   ❌ update code
   ❌ WIP
   ❌ testing
   ❌ asdfasdf
```

2. **Don't mix multiple types**
```
   ❌ feat: add Task model, fix bug in User controller, update docs
```

3. **Don't include unnecessary details**
```
   ❌ feat: add Task model (spent 2 hours on this)
   ❌ feat: add Task model (finally working!)
```

4. **Don't use past tense**
```
   ❌ feat: added Task model
   ❌ feat: implementing Task API
```

5. **Don't commit commented code**
```php
   ❌ 
   $newCode = 'use this';
```

6. **Don't commit debug statements**
```php
   ❌ 
   dd($data);
   var_dump($user);
```

7. **Don't end subject with period**
```
   ❌ feat: add Task model.
   ✅ feat: add Task model
```

---

## Commit Size

### Ideal Commit

**Characteristics:**
- Single logical change
- Can be reviewed in 5-10 minutes
- Tests included with implementation
- Easy to understand purpose
- Easy to revert if needed

**Size indicators:**
- 1-5 files changed
- 50-200 lines changed (excluding tests)
- Focused on one feature/fix

### Too Small
```
❌ Commit 1: feat: create Task.php
❌ Commit 2: feat: add fillable to Task
❌ Commit 3: feat: add casts to Task
```

**Better:**
```
✅ feat: add Task model with configuration
```

### Too Large
```
❌ feat: implement complete Task management system

- Model, migration, factory, seeder
- Controller with all methods
- All tests (20+ tests)
- FormRequests
- Routes
- Documentation
```

**Better: Split into logical commits**
```
✅ Commit 1: feat: add Task model and migration
✅ Commit 2: feat: add Task factory and seeder
✅ Commit 3: test: add Task API comprehensive tests
✅ Commit 4: feat: implement TaskController with CRUD
✅ Commit 5: docs: add Task API documentation
```

---

## Special Cases

### Work in Progress

**Use branches, not WIP commits:**
```
❌ git commit -m "WIP"
✅ git checkout -b feature/task-crud
   (commit normally, squash later if needed)
```

### Emergency Fixes

**Still follow conventions:**
```
✅ fix: critical - resolve SQL injection vulnerability

Sanitized user input in search functionality to prevent SQL
injection attacks. Added parameterized queries.

URGENT: Deploy immediately
Refs #SECURITY-123
```

### Breaking Changes

**Use footer:**
```
feat: change Task API response format

Changed response format from flat structure to nested structure
for better API versioning support.

BREAKING CHANGE: Task API now returns tasks wrapped in 'data' key.
Clients need to update: response.tasks -> response.data
```

### Reverts
```
revert: feat: add Task soft delete functionality

This reverts commit abc123def456.

Reverting due to performance issues with large datasets.
Will re-implement with better indexing strategy.
```

---

## Commit History

### Good Commit History Example
```
* docs: add Postman collection for Task API
* feat: implement Task API endpoints  
* test: add comprehensive Task API tests
* feat: add Task factory and seeder
* feat: add Task model and migration
* docs: update README with project setup
```

**Characteristics:**
- ✅ Clear progression
- ✅ Each commit is logical
- ✅ Easy to understand evolution
- ✅ Can cherry-pick if needed
- ✅ Easy to find when bug was introduced

### Bad Commit History Example
```
* fixed stuff
* WIP
* more changes
* testing
* it works now
* final commit (for real this time)
```

**Problems:**
- ❌ Unclear what changed
- ❌ Can't understand progression
- ❌ Hard to review
- ❌ Difficult to revert
- ❌ Unprofessional

---

## Tools

### Check Commit Message
```bash
git log -1

git log -5 --oneline

git log --grep="feat"
```

### Amend Last Commit
```bash
git commit --amend -m "feat: corrected commit message"

git add forgotten-file.php
git commit --amend --no-edit
```

### Interactive Rebase (Before Push)
```bash
git rebase -i HEAD~3
```

---

## Commit Checklist

Before committing, verify:

- [ ] Commit message follows format: `<type>: <subject>`
- [ ] Type is appropriate (feat, fix, test, etc.)
- [ ] Subject is imperative mood
- [ ] Subject is ≤50 characters
- [ ] Body explains WHY (if needed)
- [ ] Body lines wrap at 72 characters
- [ ] References issue if applicable
- [ ] Commit contains one logical change
- [ ] All tests pass
- [ ] No debug code (dd, var_dump, console.log)
- [ ] No commented code
- [ ] No unrelated changes

---

## Related Documentation

- [Phase 4: Integration](../workflow/phase-4-integration.md)
- [API Design Standards](./api-design.md)
- [Code Style Standards](./code-style.md)

---

## Quick Reference

### Commit Template
```
<type>: <subject line max 50 chars>

<body - explain what and why, wrap at 72 chars>

<footer - references, breaking changes>
```

### Common Types Quick Guide
```
feat:     New feature
fix:      Bug fix
test:     Tests
refactor: Code restructuring
docs:     Documentation
style:    Formatting
perf:     Performance
chore:    Maintenance
```

### Imperative Verbs
```
✅ add, create, implement, introduce
✅ remove, delete, drop
✅ update, change, modify
✅ fix, correct, resolve
✅ refactor, extract, simplify
✅ improve, optimize, enhance
```