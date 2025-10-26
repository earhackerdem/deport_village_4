# Quick API Endpoint

## Command
````
Create {resource} API endpoint
````

## What This Does

Creates a complete REST API endpoint with:
- Model + Migration
- Factory + Seeder
- Form Request (validation)
- API Controller
- Feature Tests (TDD, AAA pattern)
- >80% coverage

## Usage Examples
````
Create Task API endpoint
Create User API endpoint with soft deletes
Create Product API endpoint with slug
````

## Process

Follows the standard workflow but optimized for new endpoints:

1. Presents implementation plan
2. **WAITS FOR CONFIRMATION**
3. Creates all files with artisan commands
4. Implements with TDD
5. Validates with fixtures + cURL
6. Prepares commits

## References

- `docs/quick-reference/artisan-commands.md`
- `docs/examples/controller-examples.md`
- `docs/standards/api-design.md`