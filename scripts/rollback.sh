#!/bin/bash

###############################################################################
# Edison Tech Laravel 11 - Rollback Script
#
# This script performs emergency rollback to the previous release.
#
# Usage:
#   ./scripts/rollback.sh [release_number]
#
# Example:
#   ./scripts/rollback.sh              # Rollback to previous release
#   ./scripts/rollback.sh 20250115123000  # Rollback to specific release
###############################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
RELEASES_DIR="/var/www/releases"
CURRENT_DIR="/var/www/current"
TARGET_RELEASE=$1

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

# Check if releases directory exists
if [ ! -d "$RELEASES_DIR" ]; then
    log_error "Releases directory not found: $RELEASES_DIR"
    exit 1
fi

# Determine target release
if [ -z "$TARGET_RELEASE" ]; then
    # Get previous release (2nd most recent)
    CURRENT_RELEASE=$(readlink "$CURRENT_DIR" | xargs basename)
    TARGET_RELEASE=$(ls -t "$RELEASES_DIR" | grep -v "$CURRENT_RELEASE" | head -n 1)

    if [ -z "$TARGET_RELEASE" ]; then
        log_error "No previous release found for rollback"
        exit 1
    fi

    log_info "Rolling back from $CURRENT_RELEASE to $TARGET_RELEASE"
else
    log_info "Rolling back to specified release: $TARGET_RELEASE"
fi

TARGET_PATH="$RELEASES_DIR/$TARGET_RELEASE"

# Verify target release exists
if [ ! -d "$TARGET_PATH" ]; then
    log_error "Target release not found: $TARGET_PATH"
    log_info "Available releases:"
    ls -t "$RELEASES_DIR"
    exit 1
fi

# Confirm rollback
log_warn "⚠️  You are about to rollback to release: $TARGET_RELEASE"
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    log_info "Rollback cancelled"
    exit 0
fi

# Put application in maintenance mode
log_info "Enabling maintenance mode..."
php "$CURRENT_DIR/artisan" down || true

# Switch symlink to target release
log_info "Switching to release: $TARGET_RELEASE"
ln -nfs "$TARGET_PATH" "$CURRENT_DIR"

# Run migrations (if needed)
log_info "Running migrations..."
cd "$CURRENT_DIR"
php artisan migrate --force || log_warn "Migration failed (this may be expected for rollback)"

# Clear caches
log_info "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recache for performance
log_info "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
log_info "Restarting services..."
sudo systemctl reload php8.2-fpm
php artisan queue:restart

# Disable maintenance mode
log_info "Disabling maintenance mode..."
php artisan up

# Health check
log_info "Performing health check..."
sleep 3
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "${APP_URL:-http://localhost}/up")

if [ "$HTTP_STATUS" == "200" ]; then
    log_info "Rollback completed successfully! ✓"
    log_info "Current release: $TARGET_RELEASE"
else
    log_error "Health check failed with status: $HTTP_STATUS"
    log_error "Application may not be functioning correctly"
    exit 1
fi

exit 0
