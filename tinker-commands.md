# Laravel Tinker Commands - Database Operations

## User Management

### Lihat Semua Users
```bash
php artisan tinker --execute="User::all(['id', 'name', 'email'])->each(function(\$u) { echo \$u->id . ' - ' . \$u->name . ' (' . \$u->email . ')' . PHP_EOL; })"
```

### Lihat User Tertentu
```bash
php artisan tinker --execute="\$user = User::find(3); echo \$user->id . ' - ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL;"
```

### Lihat User dengan Role
```bash
php artisan tinker --execute="\$user = User::with('roles')->find(3); echo \$user->name . ' - Roles: ' . \$user->roles->pluck('name')->join(', ') . PHP_EOL;"
```

### Lihat 10 User Pertama
```bash
php artisan tinker --execute="User::take(10)->get(['id', 'name', 'email'])->each(function(\$u) { echo \$u->id . ' - ' . \$u->name . ' (' . \$u->email . ')' . PHP_EOL; })"
```

### Cari User by Email
```bash
php artisan tinker --execute="\$user = User::where('email', 'admin@mail.com')->first(); echo \$user->id . ' - ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL;"
```

### Count Total Users
```bash
php artisan tinker --execute="echo 'Total Users: ' . User::count()"
```

## Role & Permission Management

### Lihat Semua Roles
```bash
php artisan tinker --execute="Spatie\Permission\Models\Role::all(['id', 'name'])->each(function(\$r) { echo \$r->id . ' - ' . \$r->name . PHP_EOL; })"
```

### Lihat Semua Permissions
```bash
php artisan tinker --execute="Spatie\Permission\Models\Permission::all(['id', 'name'])->each(function(\$p) { echo \$p->id . ' - ' . \$p->name . PHP_EOL; })"
```

### Lihat User dengan Roles & Permissions
```bash
php artisan tinker --execute="\$user = User::with(['roles', 'permissions'])->find(3); echo \$user->name . ' - Roles: ' . \$user->roles->pluck('name')->join(', ') . ' - Permissions: ' . \$user->permissions->pluck('name')->join(', ') . PHP_EOL;"
```

## Menu Management

### Lihat Semua Menus
```bash
php artisan tinker --execute="App\Models\Menu::all(['id', 'name', 'url', 'parent_id'])->each(function(\$m) { echo \$m->id . ' - ' . \$m->name . ' (' . \$m->url . ')' . PHP_EOL; })"
```

### Lihat Menu Hierarchy
```bash
php artisan tinker --execute="App\Models\Menu::whereNull('parent_id')->with('children')->get()->each(function(\$menu) { echo \$menu->name . ' (ID: ' . \$menu->id . ')' . PHP_EOL; \$menu->children->each(function(\$child) { echo '  └─ ' . \$child->name . ' (ID: ' . \$child->id . ')' . PHP_EOL; }); })"
```

## Database Info

### Lihat Semua Tables
```bash
php artisan tinker --execute="collect(DB::select('SHOW TABLES'))->each(function(\$table) { echo array_values((array)\$table)[0] . PHP_EOL; })"
```

### Count Records per Table
```bash
php artisan tinker --execute="
echo 'Users: ' . User::count() . PHP_EOL;
echo 'Roles: ' . Spatie\Permission\Models\Role::count() . PHP_EOL;
echo 'Permissions: ' . Spatie\Permission\Models\Permission::count() . PHP_EOL;
echo 'Menus: ' . App\Models\Menu::count() . PHP_EOL;
"
```

## Create Operations

### Buat User Baru
```bash
php artisan tinker --execute="\$user = User::create(['name' => 'Test User New', 'email' => 'testnew@example.com', 'password' => Hash::make('password')]); echo 'User created: ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL;"
```

### Assign Role ke User
```bash
php artisan tinker --execute="\$user = User::find(1); \$user->assignRole('admin'); echo 'Role assigned to: ' . \$user->name . PHP_EOL;"
```

## Update Operations

### Update User
```bash
php artisan tinker --execute="\$user = User::find(1); \$user->update(['name' => 'Updated Name']); echo 'User updated: ' . \$user->fresh()->name . PHP_EOL;"
```

### Reset Password User
```bash
php artisan tinker --execute="\$user = User::find(1); \$user->update(['password' => Hash::make('newpassword')]); echo 'Password reset for: ' . \$user->name . PHP_EOL;"
```

## Delete Operations

### Hapus User (Soft Delete jika ada)
```bash
php artisan tinker --execute="User::find(1)->delete()"
```

### Force Delete User
```bash
php artisan tinker --execute="User::find(1)->forceDelete()"
```

## Advanced Queries

### Users dengan Role Tertentu
```bash
php artisan tinker --execute="User::role('admin')->get(['id', 'name', 'email'])->each(function(\$u) { echo \$u->id . ' - ' . \$u->name . ' (' . \$u->email . ')' . PHP_EOL; })"
```

### Users dengan Permission Tertentu
```bash
php artisan tinker --execute="User::permission('manage users')->get(['id', 'name', 'email'])->each(function(\$u) { echo \$u->id . ' - ' . \$u->name . ' (' . \$u->email . ')' . PHP_EOL; })"
```

### Latest 5 Users
```bash
php artisan tinker --execute="User::latest()->take(5)->get(['id', 'name', 'email', 'created_at'])->each(function(\$u) { echo \$u->id . ' - ' . \$u->name . ' (' . \$u->email . ') - ' . \$u->created_at . PHP_EOL; })"
```

## Debugging & Testing

### Test Database Connection
```bash
php artisan tinker --execute="try { \$pdo = DB::connection()->getPdo(); echo 'Database Connected Successfully!' . PHP_EOL; echo 'Driver: ' . \$pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . PHP_EOL; echo 'Server Version: ' . \$pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . PHP_EOL; } catch (Exception \$e) { echo 'Connection Failed: ' . \$e->getMessage() . PHP_EOL; }"
```

### Check Laravel Version
```bash
php artisan tinker --execute="echo app()->version()"
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Interactive Tinker Session

Untuk session interaktif (tanpa --execute):
```bash
php artisan tinker
```

Kemudian jalankan command di dalam session:

### Basic User Operations
```php
// Lihat semua users (hanya 5 pertama)
User::take(5)->get(['id', 'name', 'email'])

// Cari user tertentu
User::find(1)
User::where('email', 'admin@mail.com')->first()

// Simpan user ke variable
$user = User::first()
$admin = User::find(3)

// Lihat roles user
$user->roles
$user->roles->pluck('name')

// Lihat permissions user
$user->permissions
$user->getAllPermissions()
```

### Database Exploration
```php
// Count records
User::count()
Spatie\Permission\Models\Role::count()

// Lihat struktur tabel
DB::select('DESCRIBE users')
DB::select('SHOW TABLES')

// Raw queries
DB::select('SELECT COUNT(*) as total FROM users')
DB::table('users')->where('id', '>', 100)->count()
```

### Model Relationships
```php
// User dengan roles
$user = User::with('roles')->find(3)
$user->roles->pluck('name')

// Role dengan permissions
$role = Spatie\Permission\Models\Role::with('permissions')->first()
$role->permissions->pluck('name')

// Menu hierarchy
$menus = App\Models\Menu::whereNull('parent_id')->with('children')->get()
$menus->first()->children
```

### Testing & Debugging
```php
// Debug dengan dd()
dd(User::first())

// Lihat SQL query
User::where('id', 1)->toSql()
DB::enableQueryLog()
User::all()
DB::getQueryLog()

// Test relationships
$user = User::first()
$user->hasRole('admin')
$user->can('manage users')
```

### Create/Update Operations
```php
// Buat user baru
$newUser = User::create([
    'name' => 'Interactive User',
    'email' => 'interactive@test.com',
    'password' => Hash::make('password')
])

// Update user
$user = User::find(1)
$user->update(['name' => 'Updated via Tinker'])

// Assign role
$user->assignRole('admin')
$user->givePermissionTo('manage users')
```

### Keluar dari Tinker
```php
exit
// atau
quit
// atau tekan Ctrl+C
```

## Quick Reference Commands

### Cek Status Aplikasi
```bash
# Laravel version
php artisan tinker --execute="echo 'Laravel: ' . app()->version() . PHP_EOL;"

# PHP version
php artisan tinker --execute="echo 'PHP: ' . phpversion() . PHP_EOL;"

# Environment
php artisan tinker --execute="echo 'Environment: ' . app()->environment() . PHP_EOL;"

# Database info
php artisan tinker --execute="echo 'Database: ' . config('database.connections.mysql.database') . PHP_EOL;"
```

### Model Factories (jika ada)
```bash
# Generate fake users
php artisan tinker --execute="User::factory()->count(5)->create()->each(function(\$u) { echo 'Created: ' . \$u->name . PHP_EOL; })"
```

### Cache Operations
```bash
# Clear specific cache
php artisan tinker --execute="Cache::flush(); echo 'Cache cleared!' . PHP_EOL;"

# Set/Get cache
php artisan tinker --execute="Cache::put('test', 'value', 60); echo 'Cache set: ' . Cache::get('test') . PHP_EOL;"
```

## Tips & Best Practices

1. **Gunakan --execute** untuk command satu baris
2. **Gunakan session interaktif** untuk eksplorasi data kompleks
3. **Selalu backup database** sebelum operasi delete/update
4. **Gunakan dd()** untuk debug: `dd(User::first())`
5. **Gunakan toSql()** untuk lihat query: `User::where('id', 1)->toSql()`
6. **Gunakan take()** untuk limit hasil: `User::take(10)->get()`
7. **Gunakan with()** untuk eager loading: `User::with('roles')->get()`
8. **Gunakan pluck()** untuk ambil kolom tertentu: `User::pluck('name')`
9. **Gunakan each()** untuk loop hasil: `User::all()->each(function($u) { ... })`
10. **Gunakan try-catch** untuk error handling dalam command panjang

## Keyboard Shortcuts (Interactive Mode)

- **Tab**: Auto-complete
- **↑/↓**: History command
- **Ctrl+C**: Keluar dari Tinker
- **Ctrl+L**: Clear screen
- **Ctrl+A**: Ke awal baris
- **Ctrl+E**: Ke akhir baris