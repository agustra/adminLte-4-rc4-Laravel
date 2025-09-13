# Troubleshooting Guide

## 🚨 Common Issues & Solutions

### Installation Issues

#### 1. Composer Install Fails
**Problem**: `composer install` fails with memory errors
```bash
Fatal error: Allowed memory size exhausted
```

**Solution**:
```bash
# Increase PHP memory limit
php -d memory_limit=2G composer install

# Or set in php.ini
memory_limit = 2G
```

#### 2. NPM Install Fails
**Problem**: Node modules installation fails
```bash
npm ERR! peer dep missing
```

**Solution**:
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

#### 3. Permission Denied Errors
**Problem**: Laravel can't write to storage/cache directories
```bash
file_put_contents(): failed to open stream: Permission denied
```

**Solution**:
```bash
# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# For development (macOS/Linux)
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Database Issues

#### 1. Migration Fails
**Problem**: Migration fails with foreign key constraints
```bash
SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint
```

**Solution**:
```bash
# Check database engine
SHOW TABLE STATUS;

# Ensure all tables use InnoDB
ALTER TABLE table_name ENGINE=InnoDB;

# Run migrations in order
php artisan migrate:fresh --seed
```

#### 2. Connection Refused
**Problem**: Can't connect to database
```bash
SQLSTATE[HY000] [2002] Connection refused
```

**Solution**:
```bash
# Check MySQL service
sudo systemctl status mysql
sudo systemctl start mysql

# Verify connection settings in .env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

#### 3. Character Set Issues
**Problem**: Unicode characters not displaying correctly
```bash
Incorrect string value: '\xF0\x9F\x98\x80' for column 'name'
```

**Solution**:
```sql
-- Set database charset
ALTER DATABASE your_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Set table charset
ALTER TABLE your_table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Authentication Issues

#### 1. Login Redirects to Login
**Problem**: User gets redirected to login after successful authentication
```bash
Redirected to login page after login
```

**Solution**:
```php
// Check middleware in routes
Route::middleware(['auth'])->group(function () {
    // Your routes
});

// Clear auth cache
php artisan auth:clear-resets
php artisan cache:clear
```

#### 2. CSRF Token Mismatch
**Problem**: Forms fail with CSRF token mismatch
```bash
419 Page Expired - CSRF token mismatch
```

**Solution**:
```blade
{{-- Ensure CSRF token in forms --}}
<form method="POST">
    @csrf
    <!-- form fields -->
</form>

{{-- For AJAX requests --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>
```

#### 3. Session Issues
**Problem**: User sessions not persisting
```bash
Session data not saved
```

**Solution**:
```bash
# Check session configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Create sessions table
php artisan session:table
php artisan migrate

# Clear sessions
php artisan session:flush
```

### API Issues

#### 1. Passport Installation Fails
**Problem**: Laravel Passport installation issues
```bash
Passport keys not found
```

**Solution**:
```bash
# Install Passport
php artisan passport:install --force

# Generate keys manually
php artisan passport:keys --force

# Create personal access client
php artisan passport:client --personal
```

#### 2. API Authentication Fails
**Problem**: API requests return 401 Unauthorized
```bash
{"message": "Unauthenticated."}
```

**Solution**:
```php
// Check API routes
Route::middleware('auth:api')->group(function () {
    // API routes
});

// Verify token in request headers
Authorization: Bearer your-access-token

// Check token validity
php artisan tinker
$token = PersonalAccessToken::findToken('your-token');
$token->tokenable; // Should return user
```

#### 3. CORS Issues
**Problem**: Cross-origin requests blocked
```bash
Access to XMLHttpRequest blocked by CORS policy
```

**Solution**:
```php
// Install CORS package
composer require fruitcake/laravel-cors

// Publish config
php artisan vendor:publish --tag="cors"

// Configure cors.php
'allowed_origins' => ['http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Frontend Issues

#### 1. Vite Build Fails
**Problem**: Vite compilation errors
```bash
✘ [ERROR] Could not resolve "bootstrap"
```

**Solution**:
```bash
# Install missing dependencies
npm install bootstrap @popperjs/core

# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

#### 2. Assets Not Loading
**Problem**: CSS/JS files return 404
```bash
GET http://localhost/css/app.css 404 (Not Found)
```

**Solution**:
```bash
# Build assets
npm run build

# For development
npm run dev

# Check asset paths in blade
{{ Vite::asset('resources/css/app.css') }}
{{ Vite::asset('resources/js/app.js') }}
```

#### 3. JavaScript Errors
**Problem**: JavaScript console errors
```javascript
Uncaught ReferenceError: $ is not defined
```

**Solution**:
```javascript
// Ensure jQuery is loaded
import $ from 'jquery';
window.$ = window.jQuery = $;

// Or use CDN in blade template
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
```

### Performance Issues

#### 1. Slow Page Load
**Problem**: Pages loading slowly
```bash
Page load time > 3 seconds
```

**Solution**:
```bash
# Enable caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize database queries
# Use eager loading
$users = User::with(['roles', 'permissions'])->get();

# Add database indexes
Schema::table('users', function (Blueprint $table) {
    $table->index('email');
});
```

#### 2. Memory Limit Exceeded
**Problem**: PHP memory limit exceeded
```bash
Fatal error: Allowed memory size of 134217728 bytes exhausted
```

**Solution**:
```bash
# Increase memory limit in php.ini
memory_limit = 512M

# Or in specific script
ini_set('memory_limit', '512M');

# Optimize queries to use less memory
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

#### 3. Queue Jobs Not Processing
**Problem**: Background jobs stuck in queue
```bash
Jobs remain in 'pending' status
```

**Solution**:
```bash
# Start queue worker
php artisan queue:work

# Restart queue worker
php artisan queue:restart

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### File Upload Issues

#### 1. File Upload Fails
**Problem**: Large files can't be uploaded
```bash
413 Request Entity Too Large
```

**Solution**:
```php
// Increase PHP limits in php.ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300

// Nginx configuration
client_max_body_size 100M;

// Laravel validation
'file' => 'required|file|max:102400', // 100MB
```

#### 2. Storage Link Issues
**Problem**: Uploaded files not accessible
```bash
404 Not Found for storage files
```

**Solution**:
```bash
# Create storage link
php artisan storage:link

# Verify link exists
ls -la public/storage

# Manual link creation
ln -s ../storage/app/public public/storage
```

### Email Issues

#### 1. Emails Not Sending
**Problem**: Email notifications not delivered
```bash
Swift_TransportException: Connection could not be established
```

**Solution**:
```bash
# Check mail configuration in .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# Test email
php artisan tinker
Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')->subject('Test');
});
```

#### 2. Queue Email Issues
**Problem**: Queued emails not sending
```bash
Emails stuck in queue
```

**Solution**:
```bash
# Process email queue
php artisan queue:work --queue=emails

# Check queue configuration
QUEUE_CONNECTION=database

# Create jobs table
php artisan queue:table
php artisan migrate
```

## 🔧 Debugging Tools

### Laravel Debugbar
```bash
# Install debugbar
composer require barryvdh/laravel-debugbar --dev

# Publish config
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

### Log Monitoring
```bash
# Real-time log monitoring
php artisan pail

# Traditional log viewing
tail -f storage/logs/laravel.log

# Clear logs
> storage/logs/laravel.log
```

### Database Debugging
```php
// Enable query logging
DB::enableQueryLog();

// Your database operations
$users = User::all();

// Get executed queries
$queries = DB::getQueryLog();
dd($queries);
```

### Performance Profiling
```bash
# Install Clockwork
composer require itsgoingd/clockwork --dev

# View profiling data at
http://localhost/clockwork
```

## 🆘 Emergency Procedures

### Application Down
```bash
# Put application in maintenance mode
php artisan down --message="Maintenance in progress"

# Bring application back up
php artisan up
```

### Database Recovery
```bash
# Backup current database
mysqldump -u username -p database_name > backup.sql

# Restore from backup
mysql -u username -p database_name < backup.sql

# Reset migrations
php artisan migrate:fresh --seed
```

### Cache Issues
```bash
# Clear all caches
php artisan optimize:clear

# Individual cache clearing
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### File Permission Reset
```bash
# Reset all permissions
sudo chown -R www-data:www-data /path/to/laravel
sudo chmod -R 755 /path/to/laravel
sudo chmod -R 775 storage bootstrap/cache
```

## 📞 Getting Help

### Log Analysis
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server logs: `/var/log/nginx/error.log`
3. Check PHP-FPM logs: `/var/log/php8.4-fpm.log`
4. Check system logs: `journalctl -f`

### Debug Information
```php
// Add to any controller method for debugging
dd([
    'PHP Version' => phpversion(),
    'Laravel Version' => app()->version(),
    'Environment' => app()->environment(),
    'Debug Mode' => config('app.debug'),
    'Database' => config('database.default'),
    'Cache Driver' => config('cache.default'),
    'Session Driver' => config('session.driver'),
]);
```

### Health Check
```bash
# Laravel health check
php artisan about

# System health check
php artisan inspire # Just to test if artisan works
```

### Community Resources
- **Laravel Documentation**: https://laravel.com/docs
- **Laravel Forums**: https://laracasts.com/discuss
- **Stack Overflow**: Tag your questions with `laravel`
- **GitHub Issues**: Check repository issues
- **Discord/Slack**: Laravel community channels