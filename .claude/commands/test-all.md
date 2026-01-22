# Claude Command: /test-all
## Description
Run comprehensive tests across all applications (unit, feature, integration tests) and report coverage metrics.

## Usage
```
/test-all [filter] [--coverage] [--fail-on-low-coverage]
```

## Examples
```
/test-all
/test-all --coverage
/test-all e-kredit --coverage --fail-on-low-coverage
/test-all laravel
/test-all --fail-on-low-coverage
```

## Parameters
- **filter** (optional): Filter by app name or technology (e.g., laravel, react, all)
- **--coverage**: Generate and report code coverage metrics
- **--fail-on-low-coverage**: Fail if coverage < 70% (configured in settings)

## Trigger
This command triggers the **Code Reviewer** subagent to:

1. **Identify testable applications**
   - Scan all apps for test files
   - Detect test framework (PHPUnit, Pest, Jest, Vitest)
   - Filter based on provided filter parameter

2. **Setup test environment**
   - Ensure dependencies are installed
   - Clear test caches
   - Prepare test databases (if applicable)

3. **Run tests in parallel** (where possible)
   
   **Backend (Laravel/Express) tests:**
   ```bash
   # For e-kredit-pranata-ti/backend
   php artisan test --env=testing
   
   # For exam-scheduler-app/backend
   npm run test -- --silent
   ```
   
   **Frontend (React/Next.js) tests:**
   ```bash
   # For fticms, aset-fti, etc.
   npm run test -- --coverage
   ```

4. **Collect coverage metrics**
   - Generate coverage reports
   - Compare with coverage baseline
   - Identify untested files

5. **Compile and report results**
   ```
   📊 TEST RESULTS SUMMARY
   ======================
   
   ✅ Backend Tests
      ├─ e-kredit-pranata-ti/backend: 45 passed, 0 failed, Coverage 82%
      ├─ simlab/backend: 28 passed, 0 failed, Coverage 75%
      ├─ exam-scheduler/backend: 12 passed, 0 failed, Coverage 68%
      └─ Total: 85 passed, 0 failed
   
   ✅ Frontend Tests
      ├─ e-kredit-web: 32 passed, 0 failed, Coverage 78%
      ├─ exam-scheduler-frontend: 21 passed, 0 failed, Coverage 71%
      ├─ fticms: 15 passed, 0 failed, Coverage 65%
      └─ Total: 68 passed, 0 failed
   
   ⚠️  Coverage Analysis
      ├─ Minimum coverage threshold: 70%
      ├─ Applications below threshold:
      │  └─ fticms: 65% (BELOW THRESHOLD)
      └─ Action: Fix untested code or increase coverage
   
   ⏱️  Execution Time: 3m 45s
   ```

6. **Generate artifacts**
   - Create coverage reports (HTML, JSON)
   - Generate test report (JUnit XML for CI)
   - Create coverage badge (for README)

7. **Conditional failure**
   - If --fail-on-low-coverage: Exit with error if any app < 70%
   - Block CI/CD pipeline if tests fail
   - Create GitHub Issue for failing tests

## Coverage Requirements
- **Minimum coverage threshold**: 70%
- **Critical apps** (e-kredit, exam-scheduler): 75% minimum
- **UI components**: 60% minimum (challenging to test UI)

## Test Structure

### Backend Tests (Laravel)
```
tests/
├── Feature/          # Integration tests (HTTP endpoints)
├── Unit/             # Unit tests (services, models)
└── Integration/      # Multi-component workflows
```

### Frontend Tests (React)
```
src/
├── __tests__/
│   ├── components/   # Component tests
│   ├── hooks/        # Custom hook tests
│   └── utils/        # Utility function tests
└── ...
```

## Success Criteria
✅ All tests pass
✅ Code coverage >= 70% (for most apps)
✅ No console errors or warnings
✅ Test execution time < 5 minutes

## Failure Handling
If tests fail:
1. Display which tests failed and why
2. Show relevant code snippets
3. Suggest fixes for common issues
4. Block PR merge until tests pass

## Output Example
```
🧪 Running tests for all applications...

Backend (Laravel):
  ✅ e-kredit-pranata-ti/backend
     45 tests passed (coverage: 82%)
  
  ✅ simlab/backend
     28 tests passed (coverage: 75%)
  
  ❌ exam-scheduler/backend
     12 tests passed, 1 FAILED (coverage: 68%)
     FAIL: PaymentServiceTest::testInsufficientBalance
     Error: Expected exception not thrown

Frontend (React):
  ✅ e-kredit-web
     32 tests passed (coverage: 78%)
  
  ⚠️  fticms
     15 tests passed (coverage: 65% - BELOW THRESHOLD)

═════════════════════════════════════════════════════════════
Summary: 132 passed, 1 failed
Coverage: 72% average (5 apps below threshold)
Duration: 3m 45s
═════════════════════════════════════════════════════════════

❌ TEST RUN FAILED - Fix issues before merging
```
