# 🛠️ Code Quality Tools

Proyek ini menggunakan tools modern untuk memastikan kode PHP selalu menggunakan standar terbaru dan berkualitas tinggi.

## 📋 Tools yang Tersedia

### 1. **Laravel Pint** - Code Style Fixer
Memperbaiki style kode sesuai standar Laravel/PSR-12.

```bash
# Fix code style
composer run pint

# Check code style (dry-run)
composer run pint-test
```

### 2. **PHPStan** - Static Analysis
Analisis statis untuk mendeteksi bug dan type errors.

```bash
# Run static analysis
composer run phpstan
```

**Level**: 5 (dari 0-8, semakin tinggi semakin strict)

### 3. **Rector** - Automated PHP Upgrades
Upgrade kode PHP ke versi terbaru secara otomatis.

```bash
# Preview changes (dry-run)
composer run rector-dry

# Apply changes
composer run rector
```

**Target**: PHP 8.2 dengan code quality improvements

## 🚀 Quick Commands

### Run All Quality Checks
```bash
# Check semua tanpa mengubah kode
composer run code-quality
```

### Fix All Issues
```bash
# Fix style + upgrade code
composer run code-fix
```

## 📊 Current Status

### PHPStan Results
- **Level**: 5/8
- **Files Analyzed**: 98
- **Issues Found**: 159
- **Status**: ⚠️ Needs attention

### Rector Results
- **Files to Upgrade**: 63
- **Improvements**: Type declarations, modern syntax, code quality
- **Status**: ✅ Ready to apply

### Laravel Pint Results
- **Files Checked**: 146
- **Style Issues**: 106
- **Status**: ⚠️ Needs fixing

## 🔧 Configuration Files

### PHPStan (`phpstan.neon`)
```neon
includes:
    - ./vendor/larastan/larastan/extension.neon

parameters:
    level: 5
    paths:
        - app
        - config
        - routes
```

### Rector (`rector.php`)
```php
$rectorConfig->sets([
    LevelSetList::UP_TO_PHP_82,
    SetList::CODE_QUALITY,
    SetList::DEAD_CODE,
    SetList::EARLY_RETURN,
    SetList::TYPE_DECLARATION,
]);
```

### Laravel Pint
Menggunakan konfigurasi default Laravel (PSR-12).

## 📈 Workflow Recommendations

### Daily Development
```bash
# Before commit
composer run pint          # Fix style
composer run phpstan       # Check types
```

### Weekly Maintenance
```bash
# Check for upgrades
composer run rector-dry

# Apply safe upgrades
composer run rector
```

### CI/CD Integration
```bash
# In your CI pipeline
composer run code-quality  # Fail if issues found
```

## 🎯 Goals

- ✅ **Consistent Code Style**: Laravel/PSR-12 standards
- ✅ **Type Safety**: PHPStan level 8 (target)
- ✅ **Modern PHP**: PHP 8.2+ features
- ✅ **Code Quality**: Dead code removal, optimizations
- ✅ **Automated**: Minimal manual intervention

## 🔍 Issue Categories

### PHPStan Issues
- Property access on resources
- Missing type declarations
- Undefined methods/properties
- Laravel-specific patterns

### Rector Improvements
- Arrow functions
- Type declarations
- Modern syntax
- Code simplification

### Pint Fixes
- Spacing and formatting
- Import ordering
- Trailing commas
- Blank lines

## 📚 Learn More

- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Rector Documentation](https://getrector.org/documentation)
- [Laravel Pint Documentation](https://laravel.com/docs/pint)

---

**Next Steps**: Run `composer run code-fix` to start improving code quality!