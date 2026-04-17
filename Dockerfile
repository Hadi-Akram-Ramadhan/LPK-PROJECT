# --- Stage 1: Build Frontend Assets ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Stage 2: PHP Environment ---
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    icu-dev \
    autoconf \
    build-base \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy built assets from frontend-builder
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Setup configurations
COPY ./deployment/nginx.conf /etc/nginx/http.d/default.conf
COPY ./deployment/supervisor.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./deployment/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# File permissions & Storage Link
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
RUN php artisan storage:link --force

# Create log directories for supervisor
RUN mkdir -p /var/log/supervisor

# Expose ports: 80 (Web), 8080 (Reverb)
EXPOSE 80 8080

# Clean up
RUN rm -rf /root/.composer /tmp/*

# Start with supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
