You are an expert Laravel auditor. Perform a comprehensive audit of this Laravel 12 project and create Jira tickets for all issues found.

## Audit Scope

Analyze the following areas:

### 1. Code Quality & Best Practices
- PSR-12 coding standards compliance
- Proper use of Laravel conventions and patterns
- Code duplication and refactoring opportunities
- Proper use of service providers, facades, and contracts
- Dependency injection implementation

### 2. Security
- Authentication and authorization implementations
- SQL injection vulnerabilities
- XSS vulnerabilities
- CSRF protection
- Mass assignment vulnerabilities
- Sensitive data exposure
- Outdated dependencies with known vulnerabilities
- Missing security headers
- Proper validation and sanitization

### 3. Performance
- N+1 query problems
- Missing database indexes
- Inefficient queries
- Caching opportunities
- Eager loading issues
- Large file uploads handling
- Job queue optimization

### 4. Database
- Migration quality and reversibility
- Foreign key constraints
- Index optimization
- Database normalization issues
- Missing soft deletes where appropriate

### 5. Testing
- Test coverage gaps
- Missing unit tests for critical logic
- Missing feature tests
- Testing best practices violations

### 6. Architecture
- Controller bloat (logic that should be in services)
- Missing form requests for validation
- Resource/transformation layer usage
- Repository pattern implementation (if used)
- Event/listener organization

### 7. Configuration & Environment
- Environment variable usage
- Config caching compatibility
- Missing or insecure configurations

### 8. Dependencies
- Outdated Laravel version
- Deprecated package usage
- Unused dependencies

## Instructions

1. Scan the entire project systematically
2. For each issue found, create a Jira ticket using the MCP Jira integration with:
   - **Summary**: Brief description of the issue
   - **Description**: Detailed explanation including:
     - Location (file path and line numbers)
     - Current problematic code/pattern
     - Why it's an issue
     - Recommended solution
     - Example of correct implementation
   - **Priority**: 
     - Critical: Security vulnerabilities, data loss risks
     - High: Performance issues, major bugs
     - Medium: Code quality, minor bugs
     - Low: Refactoring suggestions, minor improvements
   - **Issue Type**: Bug, Task, or Improvement
   - **Labels**: Add relevant labels (security, performance, refactoring, testing, etc.)

3. After creating all tickets, provide a summary report with:
   - Total number of issues found by category
   - Critical issues requiring immediate attention
   - Overall project health assessment
   - Recommendations for prioritization

## Output Format

For each issue, before creating the Jira ticket, briefly state:
- Issue type and location
- Severity level
- Brief description

Then create the ticket and confirm it was created successfully.

Begin the audit now.