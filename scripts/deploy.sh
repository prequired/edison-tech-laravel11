#!/bin/bash

###############################################################################
# Edison Tech Laravel 11 - Deployment Script
#
# This script handles zero-downtime deployment of the Laravel application.
# It supports multiple environments (staging, production).
#
# Usage:
#   ./scripts/deploy.sh [environment]
#
# Example:
#   ./scripts/deploy.sh staging
#   ./scripts/deploy.sh production
#
# Prerequisites:
#   - PHP 8.2+ installed
#   - Composer installed
#   - Node.js 18+ and NPM installed
#   - Database credentials configured in .env
#   - Proper file permissions set
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
APP_DIR=$(pwd)
RELEASE_DIR="/var/www/releases/$(date +%Y%m%d%H%M%S)"
CURRENT_DIR="/var/www/current"
SHARED_DIR="/var/www/shared"

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Validate environment
if [[ ! "$ENVIRONMENT" =~ ^(staging|production)$ ]]; then
    log_error "Invalid environment. Use 'staging' or 'production'"
    exit 1
fi

log_info "Starting deployment to $ENVIRONMENT environment..."

# Step 1: Create release directory
log_info "Creating release directory: $RELEASE_DIR"
mkdir -p "$RELEASE_DIR"

# Step 2: Copy application files
log_info "Copying application files..."
rsync -az --exclude='.git' \
          --exclude='node_modules' \
          --exclude='vendor' \
          --exclude='storage' \
          --exclude='.env' \
          "$APP_DIR/" "$RELEASE_DIR/"

# Step 3: Create symlinks to shared directories
log_info "Creating symlinks to shared resources..."
mkdir -p "$SHARED_DIR/storage"
mkdir -p "$SHARED_DIR/storage/logs"
mkdir -p "$SHARED_DIR/storage/framework/cache"
mkdir -p "$SHARED_DIR/storage/framework/sessions"
mkdir -p "$SHARED_DIR/storage/framework/views"
mkdir -p "$SHARED_DIR/storage/app/public"

ln -nfs "$SHARED_DIR/storage" "$RELEASE_DIR/storage"
ln -nfs "$SHARED_DIR/.env" "$RELEASE_DIR/.env"

# Step 4: Install Composer dependencies
log_info "Installing Composer dependencies..."
cd "$RELEASE_DIR"
composer install --no-interaction --no-dev --optimize-autoloader --prefer-dist

# Step 5: Install NPM dependencies and build assets
log_info "Building frontend assets..."
npm ci --production
npm run build

# Step 6: Run migrations
log_info "Running database migrations..."
php artisan migrate --force

# Step 7: Clear and cache configuration
log_info "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Step 8: Put application in maintenance mode
if [ -L "$CURRENT_DIR" ]; then
    log_info "Enabling maintenance mode..."
    php "$CURRENT_DIR/artisan" down --retry=60
fi

# Step 9: Switch symlink to new release
log_info "Switching to new release..."
ln -nfs "$RELEASE_DIR" "$CURRENT_DIR"

# Step 10: Restart services
log_info "Restarting services..."
sudo systemctl reload php8.2-fpm
php artisan queue:restart

# Step 11: Disable maintenance mode
log_info "Disabling maintenance mode..."
php artisan up

# Step 12: Health check
log_info "Performing health check..."
sleep 5
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "${APP_URL}/up")

if [ "$HTTP_STATUS" == "200" ]; then
    log_info "Health check passed! ✓"
else
    log_error "Health check failed with status: $HTTP_STATUS"
    log_error "Rolling back to previous release..."

    # Rollback
    PREVIOUS=$(ls -t /var/www/releases | head -n 2 | tail -n 1)
    if [ -n "$PREVIOUS" ]; then
        ln -nfs "/var/www/releases/$PREVIOUS" "$CURRENT_DIR"
        php artisan up
        log_info "Rolled back to previous release: $PREVIOUS"
    fi

    exit 1
fi

# Step 13: Cleanup old releases (keep last 5)
log_info "Cleaning up old releases..."
cd /var/www/releases
ls -t | tail -n +6 | xargs -r rm -rf
log_info "Kept last 5 releases"

# Step 14: Set proper permissions
log_info "Setting permissions..."
chown -R www-data:www-data "$RELEASE_DIR"
chmod -R 755 "$RELEASE_DIR"
chmod -R 775 "$RELEASE_DIR/storage"
chmod -R 775 "$RELEASE_DIR/bootstrap/cache"

log_info "Deployment completed successfully! ✓"
log_info "Release: $RELEASE_DIR"
log_info "Environment: $ENVIRONMENT"
log_info "Timestamp: $(date)"

exit 0
