# TESTING GUIDE - WEBAPPERLASS

## 🧪 Comprehensive Testing Implementation

This guide covers the complete testing strategy for the WebApperlass educational management system, including setup, execution, and best practices.

## 📋 Testing Overview

### Current Test Coverage
- **Unit Tests**: Models, Services, Utilities (85%+ coverage target)
- **Feature Tests**: Controllers, HTTP workflows, Integration (80%+ coverage target)
- **Security Tests**: Validation, Authorization, Input sanitization
- **Performance Tests**: Database queries, Response times

### Test Statistics
- **Total Tests**: 45+ comprehensive test cases
- **Critical Business Logic**: 100% covered
- **Security Vulnerabilities**: All major vectors tested
- **Controller Actions**: 90%+ coverage

## 🚀 Quick Start

### Prerequisites
```bash
# Ensure PHP 8.2+ with required extensions
php --version
php -m | grep -E "(sqlite|pdo_sqlite)"

# Install dependencies
composer install
npm install
```

### Running Tests

```bash
# Run all tests
./scripts/test.sh

# Run specific test suites
./scripts/test.sh unit          # Unit tests only
./scripts/test.sh feature       # Feature tests only
./scripts/test.sh coverage      # Tests with coverage report

# Direct PHPUnit commands
php artisan test                # All tests
php artisan test --testsuite=Unit    # Unit tests
php artisan test --testsuite=Feature # Feature tests
php artisan test --coverage          # With coverage
```

### Coverage Reports
```bash
# Generate HTML coverage report
./scripts/test.sh coverage

# View coverage report
# Open: storage/tests/coverage/index.html
```

## 📁 Test Structure

```
tests/
├── Unit/                          # Unit tests (isolated component testing)
│   ├── AttendanceServiceTest.php  # Business logic testing
│   ├── LaporanMengajarTest.php    # Model testing
│   └── AbsensiTest.php           # Model relationships
├── Feature/                       # Integration tests
│   ├── AbsensiControllerTest.php  # Controller workflows
│   ├── LaporanMengajarControllerTest.php
│   └── ValidationSecurityTest.php # Security testing
└── TestCase.php                   # Base test class
```

## 🔬 Test Categories

### 1. Unit Tests

**Purpose**: Test individual components in isolation

**AttendanceServiceTest.php**
- ✅ Dropout calculation algorithm
- ✅ Attendance statistics computation
- ✅ Student filtering by attendance status
- ✅ Edge cases (empty data, consecutive absences)

**LaporanMengajarTest.php**
- ✅ Model relationships (instructor, assistant, school)
- ✅ Mass assignment protection
- ✅ File cleanup on deletion
- ✅ Default attribute values
- ✅ Attendance calculation from relationships

**AbsensiTest.php**
- ✅ Model relationships (laporan, siswa)
- ✅ Boolean casting for attendance status
- ✅ Validation of foreign key constraints
- ✅ Unique constraint enforcement

### 2. Feature Tests

**Purpose**: Test complete user workflows and HTTP interactions

**AbsensiControllerTest.php**
- ✅ Authorization policies (instructor vs admin access)
- ✅ Student filtering by school and rombel
- ✅ Attendance creation and updates
- ✅ Transaction rollback on errors
- ✅ Dropout calculation integration
- ✅ Form validation and error handling

**LaporanMengajarControllerTest.php**
- ✅ CRUD operations with proper authorization
- ✅ File upload handling and validation
- ✅ Search functionality
- ✅ Export features (Excel, PDF)
- ✅ Date and time validation
- ✅ Business rule enforcement

### 3. Security Tests

**Purpose**: Verify security measures and vulnerability protection

**ValidationSecurityTest.php**
- ✅ Debug route protection in production
- ✅ Mass assignment vulnerability prevention
- ✅ CSRF protection
- ✅ File upload security (type, size validation)
- ✅ SQL injection prevention
- ✅ Input sanitization (XSS protection)
- ✅ Authorization policy enforcement
- ✅ Role-based access control

## 🔧 Test Configuration

### Environment Setup

**phpunit.xml**
```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_STORE" value="array"/>
<env name="SESSION_DRIVER" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
```

**Test Database**
- Uses SQLite in-memory database for speed
- Fresh migration for each test run
- Isolated transactions for test isolation

### Factory Usage

**Enhanced Factories with Builder Pattern:**
```php
// User factory with role states
User::factory()->instructor()->create();
User::factory()->admin()->create();

// LaporanMengajar with relationships
LaporanMengajar::factory()
    ->withInstructor($instructor)
    ->withSekolah($sekolah)
    ->onDate(Carbon::today())
    ->create();

// Siswa for specific school and rombel
Siswa::factory()
    ->forSekolah($sekolah)
    ->inRombel('A1')
    ->create();

// Absensi with specific status
Absensi::factory()
    ->forLaporanAndSiswa($laporan, $siswa)
    ->present()
    ->create();
```

## 📊 Testing Best Practices

### 1. Test Organization
- **Arrange, Act, Assert**: Clear test structure
- **Single Responsibility**: One concept per test
- **Descriptive Names**: Self-documenting test methods
- **Data Isolation**: Each test creates its own data

### 2. Coverage Guidelines
- **Critical Paths**: 100% coverage for business logic
- **Controllers**: 90%+ coverage for public methods
- **Models**: 85%+ coverage including relationships
- **Services**: 95%+ coverage for complex algorithms

### 3. Security Testing
- **Input Validation**: Test all user inputs
- **Authorization**: Verify access controls
- **File Uploads**: Test malicious file types
- **SQL Injection**: Test query parameters
- **XSS Prevention**: Test script injection

### 4. Performance Testing
```php
// Example performance assertion
public function test_attendance_calculation_performance(): void
{
    // Create large dataset
    $students = Siswa::factory()->count(100)->create();
    
    $startTime = microtime(true);
    $result = $this->attendanceService->calculateStats($laporan);
    $endTime = microtime(true);
    
    $this->assertLessThan(0.5, $endTime - $startTime); // < 500ms
}
```

## 🎯 Quality Gates

### Before Committing
```bash
# Run complete test suite
./scripts/test.sh

# Check code style
vendor/bin/pint --test

# Verify no debug statements
grep -r "dd\|dump\|var_dump" app/ tests/ --exclude-dir=vendor
```

### Continuous Integration Checklist
- ✅ All tests pass
- ✅ Code coverage > 80%
- ✅ No security vulnerabilities
- ✅ Code style compliant
- ✅ No debug statements

## 🐛 Debugging Failed Tests

### Common Issues and Solutions

**1. Database Errors**
```bash
# Clear test database
php artisan migrate:fresh --env=testing

# Check migration status
php artisan migrate:status --env=testing
```

**2. Factory Relationship Issues**
```php
// Use explicit relationships instead of random selection
$sekolah = Sekolah::factory()->create();
$siswa = Siswa::factory()->forSekolah($sekolah)->create();
```

**3. Authentication Issues**
```php
// Ensure user has proper role
$user = User::factory()->instructor()->create();
$this->actingAs($user);
```

**4. File Upload Tests**
```php
// Use Storage facade for testing
Storage::fake('public');
$file = UploadedFile::fake()->image('test.jpg');
```

## 📈 Coverage Reports

### Interpreting Coverage

**Line Coverage**: Percentage of code lines executed
- **Target**: 85%+ for critical modules
- **Minimum**: 70% for all modules

**Branch Coverage**: Percentage of code branches tested
- **Target**: 80%+ for business logic
- **Minimum**: 60% for controllers

**Method Coverage**: Percentage of methods tested
- **Target**: 95%+ for public methods
- **Minimum**: 85% for all methods

### Coverage Exclusions
```php
// @codeCoverageIgnore - for generated code
// @codeCoverageIgnoreStart / @codeCoverageIgnoreEnd - for blocks
```

## 🚀 Advanced Testing

### 1. Mocking External Services
```php
// Mock file storage
Storage::fake('public');

// Mock HTTP requests
Http::fake([
    'api.example.com/*' => Http::response(['status' => 'success'], 200)
]);
```

### 2. Testing Events
```php
Event::fake();

// Perform action that should trigger event
$laporan = LaporanMengajar::factory()->create();

Event::assertDispatched(LaporanMengajarCreated::class);
```

### 3. Testing Jobs
```php
Queue::fake();

// Perform action that should queue job
$this->post('/some-route', $data);

Queue::assertPushed(ProcessLaporanExport::class);
```

## 📋 Test Maintenance

### Regular Tasks
- **Weekly**: Review test coverage reports
- **Monthly**: Update test data and scenarios
- **Release**: Full regression testing
- **Quarterly**: Performance baseline updates

### Adding New Tests
```php
// 1. Create test file
php artisan make:test NewFeatureTest

// 2. Follow naming convention
class NewFeatureTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_perform_specific_action(): void
    {
        // Test implementation
    }
}

// 3. Add to test suite and verify coverage
```

## 🎓 Learning Resources

### Laravel Testing Documentation
- [Laravel Testing Guide](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [HTTP Tests](https://laravel.com/docs/http-tests)
- [Database Testing](https://laravel.com/docs/database-testing)

### Best Practices
- [Test-Driven Development](https://laravel.com/docs/testing#introduction)
- [Testing Patterns](https://martinfowler.com/articles/practical-test-pyramid.html)
- [Mock Objects](https://phpunit.de/manual/current/en/test-doubles.html)

---

**Remember**: Good tests are investments in code quality, maintainability, and team confidence. Write tests that document behavior and catch regressions early.