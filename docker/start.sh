#!/bin/sh
set -e

cd /var/www/html

# Cache Laravel configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Storage link
php artisan storage:link || true

# Run migrations (optional — remove if you prefer manual)
php artisan migrate --force || true

# Start supervisord (PHP-FPM + Nginx + Queue Worker)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
