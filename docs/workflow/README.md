# Development Workflow

Complete guide for all development tasks in this project.

## Overview

Every task follows a 4-phase workflow:

1. **Analysis** - Understand and plan
2. **Implementation** - Code with TDD
3. **Validation** - Verify correctness
4. **Integration** - Ship the work

## Phase Documentation

- [Phase 1: Analysis](./phase-1-analysis.md)
- [Phase 2: Implementation](./phase-2-implementation.md)
- [Phase 3: Validation](./phase-3-validation.md)
- [Phase 4: Integration](./phase-4-integration.md)

## Quick Start

For most tasks:
````
Implement the highest priority Jira task
````

This command executes all phases automatically while respecting checkpoints.

## Related Documentation

- [Testing Standards](../standards/testing.md)
- [Commit Standards](../standards/commits.md)
- [API Design](../standards/api-design.md)
- [Artisan Commands](../quick-reference/artisan-commands.md)

## Critical Rules

1. ⛔ **NEVER skip Phase 1 analysis**
2. ⛔ **NEVER proceed without user confirmation**
3. ⛔ **NEVER skip tests** (TDD is mandatory)
4. ⛔ **NEVER skip validation** (both layers required)

## Workflow Diagram
````
Task Assigned
    ↓
Phase 1: Analysis → Present Plan
    ↓
[CHECKPOINT: User Confirmation Required]
    ↓
Phase 2: Implementation (TDD)
    ↓
Phase 3: Validation (2 layers)
    ↓
Phase 4: Integration (PR + Jira)
    ↓
Done
````

## Time Estimates

- Phase 1: 3-5 minutes
- Phase 2: 20-40 minutes (depending on complexity)
- Phase 3: 5-10 minutes
- Phase 4: 3-5 minutes

**Total**: 30-60 minutes for typical CRUD task