#!/bin/bash

# Backup .env if exists
if [ -f .env ]; then
    cp .env .env.backup
    echo "✅ .env backed up"
fi

# Your deployment commands here
# composer install --no-dev --optimize-autoloader
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# Restore .env if backup exists
if [ -f .env.backup ]; then
    cp .env.backup .env
    echo "✅ .env restored"
fi

echo "🚀 Deployment completed"