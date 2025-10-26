# Artisan Commands Quick Reference

Essential Laravel Artisan commands for daily development.

## Model Commands

### Create Model
```bash
php artisan make:model Task

php artisan make:model Task -m
php artisan make:model Task --migration

php artisan make:model Task -f
php artisan make:model Task --factory

php artisan make:model Task -s
php artisan make:model Task --seeder

php artisan make:model Task -c
php artisan make:model Task --controller

php artisan make:model Task -mfsc
php artisan make:model Task --all
```

**Example:**
```bash
php artisan make:model Task -m
```

---

## Migration Commands

### Create Migration
```bash
php artisan make:migration create_tasks_table

php artisan make:migration add_status_to_tasks_table

php artisan make:migration update_tasks_table
```

### Run Migrations
```bash
php artisan migrate

php artisan migrate --verbose

php artisan migrate:rollback

php artisan migrate:rollback --step=2

php artisan migrate:reset

php artisan migrate:refresh

php artisan migrate:fresh

php artisan migrate:fresh --seed

php artisan migrate:status
```

**Common Workflow:**
```bash
php artisan migrate:fresh --seed
```

---

## Seeder Commands

### Create Seeder
```bash
php artisan make:seeder TaskSeeder

php artisan make:seeder UsersTableSeeder
```

### Run Seeders
```bash
php artisan db:seed

php artisan db:seed --class=TaskSeeder

php artisan migrate:fresh --seed

php artisan db:seed --force
```

**Example:**
```bash
php artisan make:seeder TaskSeeder
php artisan db:seed --class=TaskSeeder
```

---

## Factory Commands

### Create Factory
```bash
php artisan make:factory TaskFactory

php artisan make:factory TaskFactory --model=Task
```

**Example:**
```bash
php artisan make:factory TaskFactory --model=Task
```

---

## Controller Commands

### Create Controller
```bash
php artisan make:controller TaskController

php artisan make:controller TaskController --resource

php artisan make:controller TaskController --api

php artisan make:controller TaskController --model=Task

php artisan make:controller TaskController --api --model=Task

php artisan make:controller ProcessTaskController --invokable
```

**Most Common:**
```bash
php artisan make:controller TaskController --api
```

---

## Request Commands

### Create Form Request
```bash
php artisan make:request StoreTaskRequest

php artisan make:request UpdateTaskRequest
```

**Example:**
```bash
php artisan make:request StoreTaskRequest
```

---

## Test Commands

### Create Test
```bash
php artisan make:test TaskTest

php artisan make:test TaskTest --unit

php artisan make:test Task/CreateTaskTest
php artisan make:test Task/UpdateTaskTest
```

### Run Tests
```bash
php artisan test

php artisan test tests/Feature/TaskTest.php

php artisan test --filter=test_store_creates_task

php artisan test --coverage

php artisan test --coverage --min=80

php artisan test --parallel

php artisan test --stop-on-failure

php artisan test --verbose
```

**Common Testing Workflow:**
```bash
php artisan test --filter=test_store

php artisan test --coverage --min=80

php artisan test --parallel --coverage
```

---

## Route Commands

### View Routes
```bash
php artisan route:list

php artisan route:list --name=tasks

php artisan route:list --method=GET

php artisan route:list --path=api

php artisan route:list --columns=Method,URI,Name

php artisan route:list --compact

php artisan route:list --json
```

**Example:**
```bash
php artisan route:list --path=tasks
```

---

## Database Commands

### Database Inspection
```bash
php artisan db:show

php artisan db:table tasks

php artisan db:monitor
```

### Database Operations
```bash
php artisan db:wipe

php artisan db:seed
```

---

## Cache Commands
```bash
php artisan cache:clear

php artisan route:clear

php artisan config:clear

php artisan view:clear

php artisan optimize:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Queue Commands
```bash
php artisan make:job ProcessTask

php artisan queue:work

php artisan queue:work --queue=high,default

php artisan queue:work --once

php artisan queue:clear
php artisan queue:flush

php artisan queue:retry all
```

---

## Maintenance Commands
```bash
php artisan down

php artisan down --secret="my-secret-token"

php artisan up
```

---

## Code Generation Shortcuts

### Complete CRUD Setup
```bash
php artisan make:model Task -mfsc
php artisan make:request StoreTaskRequest
php artisan make:request UpdateTaskRequest
php artisan make:test TaskTest
```

### API Resource Setup
```bash
php artisan make:model Task -m

php artisan make:factory TaskFactory --model=Task
php artisan make:seeder TaskSeeder

php artisan make:controller TaskController --api

php artisan make:request StoreTaskRequest
php artisan make:request UpdateTaskRequest

php artisan make:test TaskTest
```

---

## Helper Commands

### Tinker (REPL)
```bash
php artisan tinker

>>> $task = Task::first()
>>> $task->title
>>> Task::count()
>>> User::factory()->create()
```

### List All Commands
```bash
php artisan list

php artisan list make
php artisan list db
php artisan list route
```

### Get Command Help
```bash
php artisan help make:model
php artisan help migrate
php artisan help test
```

---

## Custom Artisan Commands

### Create Custom Command
```bash
php artisan make:command ProcessTasks

php artisan make:command ProcessTasks --command=tasks:process
```

---

## Optimization Commands
```bash
php artisan optimize

php artisan optimize:clear

php artisan config:cache

php artisan route:cache

php artisan view:cache
```

---

## Common Workflows

### Starting New Feature
```bash
php artisan make:model Task -m

php artisan make:factory TaskFactory --model=Task

php artisan make:seeder TaskSeeder

php artisan make:controller TaskController --api

php artisan make:request StoreTaskRequest
php artisan make:request UpdateTaskRequest

php artisan make:test TaskTest

php artisan migrate

php artisan test --filter=TaskTest
```

### Testing Workflow
```bash
php artisan migrate:fresh --seed

php artisan test

php artisan test --coverage

php artisan test --filter=test_store_creates_task
```

### Deployment Workflow
```bash
php artisan optimize:clear

php artisan migrate --force

php artisan db:seed --force

php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Docker Workflow (This Project)

### Access PHP Container
```bash
make shell

php artisan migrate
php artisan test
```

### Common Commands with Docker
```bash
make shell

php artisan migrate:fresh --seed
php artisan test
php artisan route:list

exit
```

---

## Quick Command Cheatsheet

| Task | Command |
|------|---------|
| Create model + migration | `php artisan make:model Task -m` |
| Create API controller | `php artisan make:controller TaskController --api` |
| Create factory | `php artisan make:factory TaskFactory --model=Task` |
| Create seeder | `php artisan make:seeder TaskSeeder` |
| Create form request | `php artisan make:request StoreTaskRequest` |
| Create test | `php artisan make:test TaskTest` |
| Run migrations | `php artisan migrate` |
| Fresh migration + seed | `php artisan migrate:fresh --seed` |
| Run tests | `php artisan test` |
| Run tests with coverage | `php artisan test --coverage` |
| List routes | `php artisan route:list` |
| Clear cache | `php artisan optimize:clear` |
| Interactive console | `php artisan tinker` |

---

## Tips

### Aliases (Add to .bashrc or .zshrc)
```bash
alias pa="php artisan"

alias pam="php artisan migrate"
alias pat="php artisan test"
alias par="php artisan route:list"
alias paf="php artisan migrate:fresh --seed"
```

**Usage after aliases:**
```bash
pa make:model Task -m
pam
pat --coverage
```

### VSCode Snippets

Create `.vscode/php.code-snippets`:
```json
{
  "Make Model": {
    "prefix": "artisan:model",
    "body": ["php artisan make:model ${1:ModelName} -m"]
  },
  "Make Controller": {
    "prefix": "artisan:controller",
    "body": ["php artisan make:controller ${1:ControllerName} --api"]
  },
  "Run Tests": {
    "prefix": "artisan:test",
    "body": ["php artisan test --coverage"]
  }
}
```

---

## Related Documentation

- [Phase 2: Implementation](../workflow/phase-2-implementation.md)
- [Testing Standards](../standards/testing.md)
- [Laravel Artisan Documentation](https://laravel.com/docs/11.x/artisan)