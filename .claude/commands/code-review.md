# Claude Command: /code-review
## Description
Review code quality, standards compliance, security vulnerabilities, and best practices for a specific file or PR.

## Usage
```
/code-review [file|pr] [--strict] [--security]
```

## Examples
```
/code-review app/Services/PaymentService.php
/code-review --strict
/code-review --security
```

## Trigger
This command triggers the **Code Reviewer** subagent to perform:

1. **Code style analysis**
   - Laravel Pint (PHP)
   - ESLint (JavaScript)
   - Prettier (Formatting)

2. **Static analysis**
   - PHPStan (PHP type checking)
   - TypeScript strict mode

3. **Security scanning**
   - Dependency vulnerabilities
   - OWASP Top 10 issues
   - Credential detection

4. **Architecture review**
   - Design pattern compliance
   - SOLID principles
   - Layer separation

5. **Test coverage analysis**
   - Untested code paths
   - Missing test files
   - Coverage trends

## Output
```
📋 CODE REVIEW REPORT
═════════════════════════════════════════

File: app/Services/PaymentService.php
Status: ⚠️  NEEDS IMPROVEMENT (3 issues)

🎨 CODE STYLE
  ✅ Passes Laravel Pint (0 issues)

🔍 STATIC ANALYSIS
  ⚠️  PHPStan Level 9 (2 issues)
    1. Line 45: Mixed type in union - expected string|int, got mixed
    2. Line 67: Undefined method call

🔒 SECURITY
  ✅ No vulnerabilities detected

🏗️  ARCHITECTURE
  ✅ Follows Service layer pattern
  ⚠️  Missing dependency injection in __construct (line 12)
     Suggestion: Use constructor DI instead of container resolution

📊 TEST COVERAGE
  ⚠️  Coverage: 65% (below 70% threshold)
     Uncovered lines: 12, 45-48, 89
     Suggestion: Add tests for edge cases

💡 RECOMMENDATIONS
  1. Add type hints to parameters
  2. Extract payment processing logic to separate method
  3. Add unit tests for exception handling
```
