# Development Guide

## Setup Development Environment

### Prerequisites
- PHP 8.4+
- Composer
- Node.js 18+
- MySQL 8.0+
- Git

### Quick Start
```bash
# Clone & setup
git clone <repository>
cd adminLte-Laravel
composer install
npm install
cp .env.example .env

# Database
php artisan key:generate
php artisan migrate --seed
php artisan passport:install

# Development
npm run dev
php artisan serve
```

## Architecture

### MVC Pattern
```
Controllers → Services → Models → Database
     ↓
   Views (Blade + Livewire)
```

### Directory Structure
```
app/
├── Http/Controllers/
│   ├── Admin/v1/          # Admin controllers
│   ├── Api/v1/            # API controllers
│   └── Auth/              # Authentication
├── Models/                # Eloquent models
├── Services/              # Business logic
└── Traits/                # Reusable code
```

## Coding Standards

### PHP (PSR-12)
```php
<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => []]);
    }
}
```

### JavaScript (ES6+)
```javascript
class DataTable {
    constructor(options) {
        this.options = options;
    }
    
    async fetchData() {
        const response = await fetch(this.options.apiEndpoint);
        return response.json();
    }
}
```

## Database

### Migrations
```bash
# Create migration
php artisan make:migration create_users_table

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback
```

### Models
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasRoles;
    
    protected $fillable = ['name', 'email'];
    
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```

## Frontend Development

### Blade Components
```php
// resources/views/components/button.blade.php
@props(['type' => 'button', 'variant' => 'primary'])

<button type="{{ $type }}" class="btn btn-{{ $variant }}" {{ $attributes }}>
    {{ $slot }}
</button>
```

### JavaScript Modules
```javascript
// public/js/components/DataTable.js
export class DataTable {
    constructor(config) {
        this.config = config;
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadData();
    }
}
```

### CSS/SCSS
```scss
// resources/scss/admin.scss
.admin-panel {
    .sidebar {
        background: var(--sidebar-bg);
        
        .nav-link {
            color: var(--nav-link-color);
            
            &:hover {
                background: var(--nav-hover-bg);
            }
        }
    }
}
```

## Testing

### Feature Tests
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    public function test_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        
        $response->assertStatus(200);
    }
}
```

### Unit Tests
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;

class UserServiceTest extends TestCase
{
    public function test_can_create_user(): void
    {
        $service = new UserService();
        $user = $service->create(['name' => 'John', 'email' => 'john@test.com']);
        
        $this->assertInstanceOf(User::class, $user);
    }
}
```

## API Development

### Controllers
```php
<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::paginate(10);
        return UserResource::collection($users);
    }
}
```

### Resources
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles->pluck('name'),
        ];
    }
}
```

## Security

### Authorization
```php
// In Controller
$this->authorize('read users');

// In Blade
@can('edit users')
    <button>Edit</button>
@endcan

// In Policy
public function view(User $user, User $model): bool
{
    return $user->hasPermissionTo('read users');
}
```

### Validation
```php
// Form Request
class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ];
    }
}
```

## Performance

### Database Optimization
```php
// Eager loading
$users = User::with(['roles', 'permissions'])->get();

// Query optimization
$users = User::select(['id', 'name', 'email'])
    ->where('active', true)
    ->limit(10)
    ->get();
```

### Caching
```php
// Cache data
Cache::remember('users.all', 3600, function () {
    return User::all();
});

// Cache views
php artisan view:cache

// Cache config
php artisan config:cache
```

## Debugging

### Laravel Debugbar
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Logging
```php
// In code
Log::info('User created', ['user_id' => $user->id]);
Log::error('Failed to create user', ['error' => $e->getMessage()]);

// View logs
php artisan pail
tail -f storage/logs/laravel.log
```

### Tinker
```bash
php artisan tinker

# In tinker
User::count()
User::factory()->create()
```

## Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper database
- [ ] Set up Redis for cache/sessions
- [ ] Configure mail settings
- [ ] Set up queue worker
- [ ] Configure file permissions
- [ ] Set up SSL certificate
- [ ] Configure backup strategy

### Optimization Commands
```bash
# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build assets
npm run build
```

## Git Workflow

### Branch Strategy
```
main (production)
├── develop (staging)
│   ├── feature/user-management
│   ├── feature/role-permissions
│   └── hotfix/login-bug
```

### Commit Messages
```
feat: add user management module
fix: resolve login authentication issue
docs: update API documentation
style: format code with Pint
refactor: optimize database queries
test: add user controller tests
```

## Tools & Extensions

### VS Code Extensions
- PHP Intelephense
- Laravel Extension Pack
- Blade Formatter
- GitLens
- Thunder Client

### Useful Commands
```bash
# Code formatting
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse

# Generate IDE helpers
php artisan ide-helper:generate

# Clear all caches
php artisan optimize:clear
```