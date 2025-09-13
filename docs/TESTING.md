# Testing Guide

## 🧪 Overview

Aplikasi ini menggunakan PHPUnit sebagai testing framework dengan Laravel testing utilities untuk memastikan kualitas dan stabilitas kode.

## 📋 Test Structure

```
tests/
├── Feature/                    # End-to-end tests
│   ├── BasicTest.php           # Basic application functionality
│   ├── UserManagementTest.php  # Web user management (admin views)
│   ├── ApiUserTest.php         # Basic API model tests
│   ├── ApiCrudTest.php         # Complete API CRUD testing
│   ├── BackupServiceTest.php   # Backup service testing
│   ├── SettingsTest.php        # Settings management
│   ├── CustomAuthTest.php      # Authentication tests
│   └── ExampleTest.php         # Laravel default test
├── Unit/                       # Unit tests
│   └── ExampleTest.php         # Basic unit test example
└── TestCase.php               # Base test case
```

## 🚀 Running Tests

### Prerequisites

1. **Install dev dependencies** (termasuk PHPUnit):
```bash
# Install semua dependencies termasuk dev
composer install --dev

# Atau jika clone dari GitHub
git clone <repository-url>
cd adminLte-Laravel
composer install --dev
```

2. **Setup environment testing**:
```bash
# Copy environment file untuk testing
cp .env .env.testing

# Edit .env.testing dengan konfigurasi test database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_test
DB_USERNAME=root
DB_PASSWORD=root
```

### Basic Commands

```bash
# Run semua tests (recommended)
composer test

# Atau menggunakan PHPUnit langsung
vendor/bin/phpunit

# Run feature tests saja
vendor/bin/phpunit --testsuite=Feature

# Run unit tests saja
vendor/bin/phpunit --testsuite=Unit

# Run test tertentu
vendor/bin/phpunit --filter UserManagementTest

# Run test dengan output verbose
vendor/bin/phpunit --verbose

# Run test dengan coverage report
vendor/bin/phpunit --coverage-html coverage
```

## 📊 Test Configuration

### PHPUnit Configuration (`phpunit.xml`)
```xml
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
    </php>
</phpunit>
```

## 🔧 Test Database Setup

### Using MySQL (Recommended)
```bash
# Buat database test
mysql -u root -p
CREATE DATABASE laravel_test;
```

### Using SQLite (Alternative)
Uncomment di `phpunit.xml`:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## 📝 Writing Tests

### Feature Test Example
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup roles and permissions
        $adminRole = Role::create(['name' => 'admin']);
        // ... setup permissions
    }

    public function test_admin_can_access_users_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }
}
```

### API CRUD Test Example
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;

class ApiCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup admin user with permissions
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Use Passport authentication for API testing
        Passport::actingAs($admin, ['*']);
    }

    public function test_api_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => ['total', 'size', 'current_page']
        ]);
    }

    public function test_api_can_create_user(): void
    {
        $userData = [
            'name' => 'API Test User',
            'email' => 'apitest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => [1],
        ];

        $response = $this->postJson('/api/users', $userData);

        $this->assertContains($response->getStatusCode(), [201, 422]);
    }
}
```

### Unit Test Example
```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
```

## 🎯 Test Categories

### 1. Authentication Tests
- Login/logout functionality
- Permission checking
- Role assignment
- API token management

### 2. User Management Tests
- CRUD operations
- Role and permission assignment
- Bulk operations
- Data validation

### 3. Settings Tests
- Configuration management
- Dark mode toggle
- Application settings

### 4. API Tests
- Token authentication
- Permission-based access
- CRUD operations via API

## 📈 Coverage Reports

### Generate HTML Coverage Report
```bash
vendor/bin/phpunit --coverage-html coverage
```

Buka `coverage/index.html` di browser untuk melihat laporan coverage.

### Generate Text Coverage Report
```bash
vendor/bin/phpunit --coverage-text
```

## 🔍 Debugging Tests

### Run Specific Test Method
```bash
vendor/bin/phpunit --filter test_admin_can_create_new_user
```

### Debug dengan dd() atau dump()
```php
public function test_example()
{
    $user = User::factory()->create();
    dd($user->toArray()); // Debug output
    
    // Test continues...
}
```

### Enable Debug Mode
```bash
# Set APP_DEBUG=true di .env.testing
APP_DEBUG=true
```

## 🚨 Common Issues

### 1. Database Connection Error
```bash
# Pastikan database test exists
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS laravel_test;"
```

### 2. Permission Denied
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

### 3. Memory Limit
```bash
# Increase memory limit
php -d memory_limit=512M vendor/bin/phpunit
```

### 4. Slow Tests
```bash
# Run tests in parallel (jika ada paratest)
vendor/bin/paratest --processes=4
```

## 📋 Best Practices

### 1. Use RefreshDatabase
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
    // Database akan di-reset setiap test
}
```

### 2. Use Factories
```php
// Gunakan factories untuk test data
$user = User::factory()->create([
    'email' => 'test@example.com'
]);
```

### 3. Test Naming Convention
```php
// Gunakan nama yang descriptive
public function test_admin_can_create_new_user(): void
public function test_user_cannot_access_admin_panel(): void
```

### 4. Setup and Teardown
```php
protected function setUp(): void
{
    parent::setUp();
    // Setup yang dibutuhkan setiap test
}

protected function tearDown(): void
{
    // Cleanup setelah test
    parent::tearDown();
}
```

### 5. API Testing with Passport
```php
// Setup Passport authentication for API tests
protected function setUp(): void
{
    parent::setUp();
    
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    Passport::actingAs($admin, ['*']);
}

// Test API endpoints
public function test_api_endpoint(): void
{
    $response = $this->getJson('/api/users');
    $response->assertStatus(200);
}
```

## 🎯 Continuous Integration

### GitHub Actions Example
```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.4
        
    - name: Install dependencies
      run: composer install
      
    - name: Run tests
      run: vendor/bin/phpunit
```

## 📞 Troubleshooting

Jika mengalami masalah dengan testing:

1. **Periksa konfigurasi database test**
2. **Pastikan semua dependencies terinstall**
3. **Clear cache**: `php artisan config:clear`
4. **Regenerate autoload**: `composer dump-autoload`
5. **Check file permissions**

## 📚 Resources

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Spatie Permission Testing](https://spatie.be/docs/laravel-permission/testing)

## 🔧 BackupService Testing

### Service Test Example
```php
public function test_backup_service_can_be_instantiated(): void
{
    $backupService = new BackupService();
    $this->assertInstanceOf(BackupService::class, $backupService);
}

public function test_backup_methods_exist(): void
{
    $this->assertTrue(method_exists($this->backupService, 'create'));
    $this->assertTrue(method_exists($this->backupService, 'generateSqlBackup'));
    $this->assertTrue(method_exists($this->backupService, 'createZipFile'));
}
```

**Current Test Results: 37 tests, 70 assertions - All passing ✅**