# AdminLTE Laravel Application

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
    <strong>Modern Admin Dashboard with Laravel 12 & AdminLTE 4</strong>
</p>

## 📋 Description

A modern web application built with Laravel 12 and AdminLTE 4 template, providing a complete admin management system with:

- **User Management** - Manage users with roles & permissions
- **Role & Permission Management** - RBAC system using Spatie Permission
- **Menu Management** - Dynamic sidebar menu with badges and real-time updates
- **Media Library** - Complete file management system
- **Settings Management** - Application configuration
- **API Integration** - RESTful API with Laravel Passport

## 📸 Screenshots

### Dashboard Overview
![Dashboard](Jepretan%20Layar%202025-09-07%20pukul%2002.22.49.png)
*Modern dashboard with comprehensive application overview, statistics, and quick actions*

### User Management
![User Management](Jepretan%20Layar%202025-09-07%20pukul%2002.23.12.png)
*Complete user management with roles, permissions, and advanced filtering*

### Menu Management
![Menu Management](Jepretan%20Layar%202025-09-07%20pukul%2002.23.52.png)
*Dynamic menu builder with real-time sidebar updates and badge system*

### Settings Configuration
![Settings](Jepretan%20Layar%202025-09-07%20pukul%2002.24.18.png)
*Application settings with media picker integration and real-time validation*

## 🚀 Key Features

### 🔐 Authentication & Authorization
- Custom authentication system
- Laravel Passport for API authentication
- Role-based access control (RBAC)
- Permission management per module

### 👥 User Management
- CRUD operations for users
- Assign roles & permissions
- Avatar management with media library
- Bulk operations and export functions

### 📝 Menu Management
- Dynamic sidebar menu builder
- Hierarchical parent-child structure
- Bootstrap Icons integration
- Smart badge system with real-time updates
- Badge configuration management interface
- Permission-based menu display
- Real-time sidebar updates with intelligent caching

### 🌙 Dark Mode System
- Dropdown theme selector: Light/Dark/Auto
- Auto mode follows real time (6AM-6PM light, 6PM-6AM dark)
- localStorage persistence
- AdminLTE 4 native implementation

### 📁 Media Library System
- File upload with WebP conversion
- Folder management with hierarchical structure
- Drag & drop interface
- Context menu operations
- Image cropping and editing
- API integration for all operations

### ⚙️ Settings Management
- Single form application configuration
- Media picker integration
- Real-time form validation
- Loading states and success feedback

### 📊 Advanced Data Tables
- **ModernTable.js Integration** - Powerful DataTable library from CDN
- **Server-side Processing** - Efficient handling of large datasets
- **Column Search** - Individual column filtering capabilities
- **Bulk Operations** - Multi-select with bulk delete functionality
- **Export Features** - CSV, Excel, PDF export options
- **Responsive Design** - Mobile-friendly table layouts
- **Real-time Filtering** - Date range and advanced filters
- **State Persistence** - Remember user preferences

## 🛠️ Tech Stack

### Backend
- **Laravel 12.23** - PHP Framework
- **PHP 8.2+** - Programming Language
- **MySQL** - Database
- **Laravel Passport** - OAuth2 API Authentication
- **Spatie Permission** - Role & Permission management
- **Spatie Backup** - Database backup system
- **Spatie MediaLibrary** - Media management

### Frontend
- **AdminLTE 4** - Modern admin template
- **Bootstrap 5** - CSS Framework
- **Bootstrap Icons** - Icon library
- **ModernTable.js v1.0.6** - Advanced DataTable library (CDN)
- **Vanilla JavaScript** - ES6+ modules (no jQuery)
- **Vite** - Build tool and module bundler

### Development Tools
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Testing framework
- **Laravel Sail** - Docker development environment
- **Concurrently** - Multi-process development server

### External Dependencies (CDN)
- **ModernTable.js v1.0.6** - `https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js`
- **TomSelect CSS** - `https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css`
- **TomSelect JS** - `https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js`

## 📦 Installation

### System Requirements
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Git

### Installation Steps

1. **Clone Repository**
```bash
git clone <repository-url>
cd adminLte-Laravel
```

2. **Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

3. **Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

4. **Database Configuration**
```bash
# Edit .env file with your database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Database Migration & Seeding**
```bash
# Run migrations
php artisan migrate

# Seed database with initial data
php artisan db:seed

# Generate Passport keys
php artisan passport:install
```

6. **Build Assets**
```bash
# Development
npm run dev

# Production
npm run build
```

7. **Start Development Server**
```bash
# Using composer script (recommended)
composer run dev

# Or manually
php artisan serve
```

## 🔧 Configuration

### Environment Variables
```env
# Application
APP_NAME="AdminLTE Laravel"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=UTC

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=log
```

## 📁 Project Structure

```
adminLte-Laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/v1/          # Admin controllers
│   │   ├── Api/v1/            # API controllers
│   │   ├── Auth/              # Authentication controllers
│   │   └── Media/             # Media management controllers
│   ├── Models/                # Eloquent models
│   ├── Traits/                # Reusable traits
│   └── Helpers/               # Helper functions
├── resources/
│   ├── views/
│   │   ├── admin/             # Admin views
│   │   ├── layouts/           # Layout templates
│   │   └── media/             # Media library views
│   └── js/                    # JavaScript modules
├── routes/
│   ├── web.php               # Web routes
│   ├── api.php               # Public API routes
│   ├── api.admin.php         # Admin API routes
│   └── admin.php             # Admin web routes
└── database/
    ├── migrations/           # Database migrations
    └── seeders/              # Database seeders
```

## 🔐 Authentication

### Default Users
After seeding, use these credentials:

```
Admin:
Email: admin@mail.com
Password: password

User:
Email: user@example.com
Password: password
```

### API Authentication
```bash
# Login via API
POST /api/login
{
    "email": "admin@mail.com",
    "password": "password"
}

# Response
{
    "data": {...},
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "message": "Login berhasil"
}
```

## 📊 API Endpoints

**⚠️ IMPORTANT**: After major refactoring, API response structure has been updated. See [docs/API_UPDATED.md](docs/API_UPDATED.md) for complete details.

### 🚨 Breaking Changes:
- `currentPage` → `current_page` (snake_case)
- `api_version` moved to `meta` object
- `timestamp` moved to `meta` object
- Show routes (`GET /api/{resource}/{id}`) are **excluded**

### Authentication
```
POST   /api/login           # Login
POST   /api/logout          # Logout
POST   /api/register        # Register
```

### User Management
```
GET    /api/users           # List users (DataTable with pagination)
GET    /api/users/json      # List users (Simple JSON for dropdowns)
GET    /api/users/by-ids    # Get users by specific IDs
POST   /api/users           # Create user
PUT    /api/users/{id}      # Update user
DELETE /api/users/{id}      # Delete user
POST   /api/users/multiple/delete  # Bulk delete users
GET    /api/users/{id}/permissions/paginated  # Get user permissions
```

### Role Management
```
GET    /api/roles           # List roles (DataTable with pagination)
GET    /api/roles/json      # List roles (Simple JSON for dropdowns)
GET    /api/roles/by-ids    # Get roles by specific IDs
POST   /api/roles           # Create role
PUT    /api/roles/{id}      # Update role
DELETE /api/roles/{id}      # Delete role
POST   /api/roles/multiple/delete  # Bulk delete roles
GET    /api/roles/{id}/permissions/paginated  # Get role permissions
```

### Permission Management
```
GET    /api/permissions     # List permissions (DataTable with pagination)
GET    /api/permissions/json # List permissions (Simple JSON for dropdowns)
GET    /api/permissions/by-ids # Get permissions by specific IDs
POST   /api/permissions     # Create permission
PUT    /api/permissions/{id} # Update permission
DELETE /api/permissions/{id} # Delete permission
POST   /api/permissions/multiple/delete # Bulk delete permissions
```

### Badge Management
```
GET    /api/menu/badge-count        # Get badge count for specific menu
GET    /api/menu/all-badge-counts   # Get all badge counts
GET    /api/menu/active-urls        # Get active badge config URLs (for caching)
POST   /api/menu/clear-badge-cache  # Clear badge cache
GET    /api/badge-configs           # List badge configurations
POST   /api/badge-configs           # Create badge configuration
PUT    /api/badge-configs/{id}      # Update badge configuration
DELETE /api/badge-configs/{id}      # Delete badge configuration
```

### Media Library
```
GET    /api/media-management/json    # List media files
POST   /api/media/upload/file        # Upload file
POST   /api/media/folders            # Create folder
DELETE /api/media-management/{id}    # Delete media file
```

## 🌙 Dark Mode Usage

The application includes a sophisticated dark mode system:

1. **Theme Selector**: Dropdown in navbar with 3 options:
   - ☀️ Light - Force light theme
   - 🌙 Dark - Force dark theme  
   - 🔄 Auto - Follow real time (6AM-6PM light, 6PM-6AM dark)

2. **Persistent State**: Theme preference saved in localStorage

3. **Developer Integration**:
```javascript
// Listen for theme changes
document.addEventListener('themeChanged', function(event) {
    const mode = event.detail.mode;           // 'light', 'dark', or 'system'
    const actualTheme = event.detail.actualTheme; // 'light' or 'dark' (actual applied)
    const theme = event.detail.theme;        // same as actualTheme
    
    console.log('Theme mode:', mode);
    console.log('Actual theme applied:', actualTheme);
    
    // Use actualTheme for styling decisions
    if (actualTheme === 'dark') {
        // Apply dark mode styles
    } else {
        // Apply light mode styles
    }
});
```

## 🧪 Testing

```bash
# Run all tests
composer test

# Run specific test
vendor/bin/phpunit --filter UserTest

# Run with coverage
vendor/bin/phpunit --coverage-html coverage
```

## 🚀 Deployment

### Production Setup
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

### Production Environment
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use Redis for better performance
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 🏧 Frontend Architecture

### JavaScript Module System
The application uses modern ES6+ modules with a clean, organized structure:

```
resources/js/
├── admin/                    # Admin panel modules
│   ├── users/users.js         # User management
│   ├── roles/roles.js         # Role management  
│   ├── menus/menus.js         # Menu management
│   └── backup/backup.js       # Backup management
├── components/               # Reusable UI components
│   ├── ui/permissionsPopup.js # Permission popup
│   └── tables/ActionButton.js # Table action buttons
├── helpers/                  # Utility functions
│   ├── delete.js             # Delete operations
│   ├── bulkDelete.js         # Bulk delete handler
│   └── filterData.js         # Data filtering
├── tables/                   # Table-related modules
│   ├── apiConfig.js          # API configuration
│   ├── tableButtons.js       # Table button factory
│   └── handleSelectionChange.js # Selection handling
└── handlers/                 # Event handlers
    ├── modalHandler.js       # Modal operations
    └── fetchAxios.js         # HTTP requests
```

### ModernTable.js Integration
All data tables use ModernTable.js v1.0.6 loaded from CDN:

```javascript
import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";

// Consistent table configuration across all modules
const table = new ModernTable("#table-users", {
    // Server-side processing
    serverSide: true,
    ajax: {
        url: "/api/users",
        beforeSend: (params) => Object.assign(params, getApiParams()),
    },
    
    // Advanced features
    columnSearch: true,
    select: true,
    responsive: true,
    stateSave: true,
    
    // Consistent styling
    theme: "auto",
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
});
```

### Key Frontend Features
- **No jQuery Dependency** - Pure vanilla JavaScript
- **ES6+ Modules** - Modern import/export syntax
- **CDN Integration** - ModernTable.js loaded from jsDelivr
- **Consistent Architecture** - Standardized patterns across modules
- **Component-based** - Reusable UI components
- **State Management** - localStorage for user preferences
- **Error Handling** - Comprehensive error management
- **Performance Optimized** - Lazy loading and efficient rendering

## 🔧 Development

### Code Style
```bash
# Fix code style
vendor/bin/pint

# Check code style
vendor/bin/pint --test
```

### Useful Commands
```bash
# Clear all caches
php artisan optimize:clear

# Run queue worker
php artisan queue:work

# Run development server with all services
composer run dev
```

## 📝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards
- Use Laravel conventions
- Write tests for new features
- Update documentation

## 🐛 Troubleshooting

### Common Issues

**Composer Install Error (Class not found)**
If you encounter `Class "Illuminate\Foundation\Console\ConfigMakeCommand" not found` error:

```bash
# Method 1: Clear and reinstall
rm -rf vendor composer.lock
composer install

# Method 2: Update without scripts first
composer update --no-scripts
composer install

# Method 3: Ignore platform requirements (if needed)
composer install --ignore-platform-reqs --no-scripts
php artisan package:discover --ansi
```

**Vite Manifest Error**
```bash
npm run build
```

**Permission Denied**
```bash
chmod -R 755 storage bootstrap/cache
```

**Database Connection**
- Ensure MySQL service is running
- Check .env configuration
- Test connection: `php artisan migrate:status`

**CDN Dependencies Issues**
```bash
# If ModernTable.js fails to load from CDN
# Check network connection and CDN availability
# Alternative: Download and serve locally if needed
```

**JavaScript Module Errors**
- Ensure all import paths are correct
- Check browser console for module loading errors
- Verify Vite configuration for path aliases

**PHP Version Compatibility**
- This project requires PHP 8.2 or higher
- If you have multiple PHP versions, ensure you're using the correct one:
```bash
php -v  # Check current PHP version
composer --version  # Ensure Composer is using correct PHP
```

## 📄 License

This application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - PHP Framework
- [AdminLTE](https://adminlte.io) - Admin Template
- [Spatie](https://spatie.be) - Laravel Packages
- [Bootstrap](https://getbootstrap.com) - CSS Framework

---

**Built with ❤️ using Laravel & AdminLTE**