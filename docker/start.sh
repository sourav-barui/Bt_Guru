#!/bin/sh
set -e

cd /var/www/html

# Clear any stale caches
php artisan optimize:clear || true

# Cache Laravel configs (but NOT routes - conditional routing breaks route cache)
php artisan config:cache
php artisan view:cache
php artisan event:cache

# Storage link - force recreate to ensure symlink is valid after redeploy
rm -f public/storage
php artisan storage:link --force || true

# Run migrations (optional — remove if you prefer manual)
php artisan migrate --force || true

# Start supervisord (PHP-FPM + Nginx + Queue Worker)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
