# Deployment Guide

## Production Deployment

### Server Requirements
- **OS**: Ubuntu 20.04+ / CentOS 8+
- **Web Server**: Nginx 1.18+ / Apache 2.4+
- **PHP**: 8.4+ with extensions:
  - BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Memory**: 2GB+ RAM
- **Storage**: 10GB+ SSD

### Server Setup (Ubuntu)

#### 1. Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### 2. Install PHP 8.4
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.4 php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-bcmath php8.4-gd
```

#### 3. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 4. Install Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs
```

#### 5. Install MySQL
```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

#### 6. Install Nginx
```bash
sudo apt install nginx
sudo systemctl enable nginx
```

### Application Deployment

#### 1. Clone Repository
```bash
cd /var/www
sudo git clone <repository-url> adminlte-laravel
sudo chown -R www-data:www-data adminlte-laravel
cd adminlte-laravel
```

#### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

#### 3. Environment Configuration
```bash
cp .env.example .env
nano .env
```

**Production .env:**
```env
APP_NAME="AdminLTE Laravel"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adminlte_prod
DB_USERNAME=adminlte_user
DB_PASSWORD=secure_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

#### 4. Generate Application Key
```bash
php artisan key:generate
```

#### 5. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE adminlte_prod;
CREATE USER 'adminlte_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON adminlte_prod.* TO 'adminlte_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force
php artisan db:seed --force
php artisan passport:install --force
```

#### 6. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

#### 7. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/adminlte-laravel
sudo chmod -R 755 /var/www/adminlte-laravel
sudo chmod -R 775 /var/www/adminlte-laravel/storage
sudo chmod -R 775 /var/www/adminlte-laravel/bootstrap/cache
```

### Nginx Configuration

#### Create Site Configuration
```bash
sudo nano /etc/nginx/sites-available/adminlte-laravel
```

**Nginx Config:**
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/adminlte-laravel/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/adminlte-laravel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Redis Setup

```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### Queue Worker Setup

#### Create Systemd Service
```bash
sudo nano /etc/systemd/system/adminlte-worker.service
```

**Service Config:**
```ini
[Unit]
Description=AdminLTE Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/adminlte-laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

#### Enable Service
```bash
sudo systemctl daemon-reload
sudo systemctl enable adminlte-worker
sudo systemctl start adminlte-worker
```

### Cron Jobs

```bash
sudo crontab -e
```

Add:
```cron
* * * * * cd /var/www/adminlte-laravel && php artisan schedule:run >> /dev/null 2>&1
```

## Docker Deployment

### Dockerfile
```dockerfile
FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . /var/www

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm ci && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

### Docker Compose
```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: adminlte-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - adminlte

  nginx:
    image: nginx:alpine
    container_name: adminlte-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - adminlte

  mysql:
    image: mysql:8.0
    container_name: adminlte-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: adminlte
      MYSQL_ROOT_PASSWORD: root
      MYSQL_PASSWORD: password
      MYSQL_USER: adminlte
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - adminlte

  redis:
    image: redis:alpine
    container_name: adminlte-redis
    restart: unless-stopped
    networks:
      - adminlte

networks:
  adminlte:
    driver: bridge

volumes:
  mysql_data:
```

## Monitoring & Maintenance

### Log Monitoring
```bash
# Laravel logs
tail -f /var/www/adminlte-laravel/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# System logs
journalctl -u adminlte-worker -f
```

### Performance Monitoring
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Monitor processes
htop
ps aux | grep php

# Monitor disk usage
df -h
du -sh /var/www/adminlte-laravel
```

### Backup Strategy

#### Database Backup
```bash
#!/bin/bash
# backup-db.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u adminlte_user -p adminlte_prod > /backups/db_backup_$DATE.sql
find /backups -name "db_backup_*.sql" -mtime +7 -delete
```

#### Application Backup
```bash
#!/bin/bash
# backup-app.sh
DATE=$(date +%Y%m%d_%H%M%S)
tar -czf /backups/app_backup_$DATE.tar.gz /var/www/adminlte-laravel
find /backups -name "app_backup_*.tar.gz" -mtime +7 -delete
```

#### Automated Backups
```cron
# Daily database backup at 2 AM
0 2 * * * /path/to/backup-db.sh

# Weekly application backup on Sunday at 3 AM
0 3 * * 0 /path/to/backup-app.sh
```

### Security Hardening

#### Firewall Setup
```bash
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow mysql
```

#### Fail2Ban
```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

#### Regular Updates
```bash
#!/bin/bash
# update-system.sh
sudo apt update
sudo apt upgrade -y
sudo apt autoremove -y
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm
```

### Troubleshooting

#### Common Issues

**1. Permission Errors**
```bash
sudo chown -R www-data:www-data /var/www/adminlte-laravel
sudo chmod -R 775 storage bootstrap/cache
```

**2. Cache Issues**
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**3. Queue Not Processing**
```bash
sudo systemctl restart adminlte-worker
sudo systemctl status adminlte-worker
```

**4. Database Connection**
```bash
# Test connection
php artisan tinker
DB::connection()->getPdo();
```

#### Health Check Script
```bash
#!/bin/bash
# health-check.sh

echo "=== System Health Check ==="

# Check services
systemctl is-active nginx
systemctl is-active php8.4-fpm
systemctl is-active mysql
systemctl is-active redis-server
systemctl is-active adminlte-worker

# Check disk space
df -h | grep -E "/$|/var"

# Check memory
free -h

# Check Laravel
cd /var/www/adminlte-laravel
php artisan about
```

### Scaling

#### Load Balancer Setup
```nginx
upstream adminlte_backend {
    server 192.168.1.10:80;
    server 192.168.1.11:80;
    server 192.168.1.12:80;
}

server {
    listen 80;
    server_name yourdomain.com;
    
    location / {
        proxy_pass http://adminlte_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

#### Database Replication
- Master-Slave setup for read/write splitting
- Connection pooling with ProxySQL
- Regular backup and monitoring

#### CDN Integration
- CloudFlare for static assets
- AWS S3 for file storage
- Redis cluster for session storage