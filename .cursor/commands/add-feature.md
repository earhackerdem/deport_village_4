# Add Feature to Existing Code

## Command
```
Add {feature_description} to {model}
```

## What This Does

Adds a new feature to existing code following TDD:

1. Analyzes current implementation
2. Presents plan for the new feature
3. **WAITS FOR CONFIRMATION**
4. Creates tests first (TDD)
5. Implements feature
6. Validates with tests + cURL (if API changes)
7. Verifies database (if schema changes)

## Usage Examples
```
Add due_date field to Task with future date validation
Add soft deletes to User model
Add search functionality to Task API
Add email notification to Task creation
```

## Process

1. Reviews existing code
2. Identifies files to modify
3. Plans tests to add
4. **WAITS FOR CONFIRMATION**
5. Writes tests (RED)
6. Implements feature (GREEN)
7. Refactors if needed (BLUE)
8. Validates changes

## References

- `docs/workflow/phase-2-implementation.md`
- `docs/standards/testing.md`
- `docs/examples/`