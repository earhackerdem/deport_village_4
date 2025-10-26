# Implement Jira Task

## Command
````
Implement the highest priority Jira task
````

## What This Does

Executes the complete workflow:

1. **Analysis Phase** (refs: `docs/workflow/phase-1-analysis.md`)
   - Fetches highest priority Jira task
   - Updates status to "En Progreso"
   - Analyzes requirements
   - Presents implementation plan
   - **WAITS FOR CONFIRMATION**

2. **Implementation Phase** (refs: `docs/workflow/phase-2-implementation.md`, `docs/standards/testing.md`)
   - Creates feature branch
   - Updates Jira to "En Curso"
   - Applies TDD with AAA pattern
   - Generates files with artisan commands
   - Achieves >80% test coverage

3. **Validation Phase** (refs: `docs/workflow/phase-3-validation.md`)
   - Validates fixtures in database
   - Executes end-to-end tests with cURL
   - Verifies data persistence

4. **Integration Phase** (refs: `docs/workflow/phase-4-integration.md`)
   - Creates Postman collection
   - Makes semantic commits
   - Creates PR with description
   - Updates Jira with summary
   - Changes status to "En Revisión"

## Usage

Simply type:
````
Implement the highest priority Jira task
````

Or specify a task:
````
Implement Jira task PROJ-123
````

## References

- Complete workflow: `docs/workflow/README.md`
- All standards: `docs/standards/`
- Code examples: `docs/examples/`