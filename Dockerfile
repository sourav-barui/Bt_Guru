# Bt_Guru Dokploy Dockerfile
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    mysql-client \
    bash \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev, optimized autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Copy application
# Cache bust: 2026-06-05 18:15 UTC
COPY . .

# Create production .env (overrides local .env)
RUN echo "APP_NAME=Bt_Guru" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_KEY=base64:4XUYkqQw2E6rSifjMPitm5VYnvB8VePOM2DMBQykpaE=" >> .env && \
    echo "APP_URL=https://btguru.tech" >> .env && \
    echo "CENTRAL_DOMAIN=btguru.tech" >> .env && \
    echo "BASE_DOMAIN=btguru.tech" >> .env && \
    echo "ADMIN_SUBDOMAIN=admin" >> .env && \
    echo "DB_CONNECTION=mysql" >> .env && \
    echo "DB_HOST=btguru-btgurudb-red7t9" >> .env && \
    echo "DB_PORT=3306" >> .env && \
    echo "DB_DATABASE=bt_guru" >> .env && \
    echo "DB_USERNAME=bt_guru" >> .env && \
    echo "DB_PASSWORD=jJpEplhZcgbMzVQDhXzN" >> .env && \
    echo "CACHE_STORE=database" >> .env && \
    echo "SESSION_DRIVER=database" >> .env && \
    echo "QUEUE_CONNECTION=database" >> .env && \
    echo "MAIL_MAILER=log" >> .env

# Install Node dependencies and build Vite assets
RUN npm install && npm run build

# Run composer scripts now that full app is present
RUN composer run-script post-autoload-dump --no-dev || true

# Copy PHP production configs
COPY docker/php.ini /usr/local/etc/php/conf.d/99-production.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/99-opcache.ini
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy supervisord config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# PHP-FPM + Nginx ports
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

CMD ["/start.sh"]
