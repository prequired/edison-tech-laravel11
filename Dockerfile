# Multi-stage Dockerfile for Laravel 11 Application
# Stage 1: Build dependencies
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (without dev dependencies for production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy application files
COPY . .

# Install npm dependencies and build assets
RUN npm ci && npm run build

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# ============================================================================
# Stage 2: Production image
FROM php:8.2-fpm-alpine

LABEL maintainer="Edison Tech <dev@edisontech.com>"
LABEL description="Edison Tech Laravel 11 Project Management System"

# Install runtime dependencies only
RUN apk add --no-cache \
    libpng \
    libzip \
    oniguruma \
    libxml2 \
    postgresql-libs \
    mysql-client \
    nginx \
    supervisor

# Install PHP extensions (runtime only)
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy Nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy Supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create application user
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Set working directory
WORKDIR /var/www/html

# Copy application from builder
COPY --from=builder --chown=www:www /var/www/html /var/www/html

# Set permissions
RUN chown -R www:www /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create necessary directories
RUN mkdir -p /var/www/html/storage/logs && \
    mkdir -p /var/www/html/storage/framework/{sessions,views,cache} && \
    chown -R www:www /var/www/html/storage

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
