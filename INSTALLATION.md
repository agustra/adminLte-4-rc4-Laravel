## ini test sync untuk memastikan saja


# Installation Guide

## Quick Installation Steps

Follow these steps after cloning the repository:

### 1. Clone Repository
```bash
git clone https://github.com/agustra/adminLte-4-rc4-Laravel.git
cd adminLte-4-rc4-Laravel
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration
Edit `.env` file with your database configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Migration & Seeding
```bash
# Run migrations
php artisan migrate

# Seed database with initial data
php artisan db:seed

# Generate Passport keys
php artisan passport:install
```

### 6. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server
```bash
# Using composer script (recommended)
composer run dev

# Or manually
php artisan serve
```

## Troubleshooting

### If you get any errors:

1. **Clear and reinstall:**
```bash
composer clear-cache
rm -rf vendor composer.lock
composer install
```

3. **If permission issues on macOS/Linux:**
```bash
chmod -R 755 vendor
rm -rf vendor composer.lock
composer install --no-scripts
composer dump-autoload
```

### Common Issues:

- **Vite Manifest Error:** Run `npm run build`
- **Permission Denied:** Run `chmod -R 755 storage bootstrap/cache`
- **Database Connection:** Check `.env` configuration and ensure MySQL is running

## Default Login Credentials

After seeding:
```
Admin:
Email: admin@mail.com
Password: password

User:
Email: user@example.com
Password: password
```

## System Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Git

## Production Deployment

For production, run these additional commands:
```bash
# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```