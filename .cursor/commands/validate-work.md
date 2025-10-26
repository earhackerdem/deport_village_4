# Validate Current Work

## Command
```
Validate current work
```

## What This Does

Runs complete validation following Phase 3:

**Layer 1 - Fixture Validation:**
1. Executes migrations if needed
2. Runs seeders
3. Accesses database: `make sql`
4. Verifies test data created correctly
5. Shows count, structure, and sample data

**Layer 2 - End-to-End Validation:**
1. Executes cURL commands for all endpoints
2. Shows request and response for each
3. Accesses database again: `make sql`
4. Verifies API operations persisted correctly
5. Compares database state before/after

## Usage
```
Validate current work
```

Or after specific changes:
```
Validate Task API implementation
Validate database changes
```

## What Gets Validated

✅ Migrations ran successfully
✅ Seeders generate expected data
✅ All endpoints respond correctly
✅ Data persists in database
✅ Response formats are correct
✅ Status codes are appropriate

## References

- `docs/workflow/phase-3-validation.md`
- `docs/quick-reference/curl-templates.md`
- `docs/quick-reference/mysql-queries.md`