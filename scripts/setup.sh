#!/bin/bash

###############################################################################
# Edison Tech Laravel 11 - Initial Setup Script
#
# This script performs initial setup of the application on a fresh server.
#
# Usage:
#   ./scripts/setup.sh [environment]
#
# Example:
#   ./scripts/setup.sh development
#   ./scripts/setup.sh production
###############################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
ENVIRONMENT=${1:-development}

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

log_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Welcome message
echo -e "${GREEN}"
echo "╔════════════════════════════════════════════════════╗"
echo "║   Edison Tech Laravel 11 - Setup Script           ║"
echo "║   Environment: $ENVIRONMENT                        ║"
echo "╚════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Step 1: Check prerequisites
log_step "1/12: Checking prerequisites..."

# Check PHP version
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
if [ "$(printf '%s\n' "8.2" "$PHP_VERSION" | sort -V | head -n1)" != "8.2" ]; then
    log_error "PHP 8.2+ is required. Current version: $PHP_VERSION"
    exit 1
fi
log_info "PHP version: $PHP_VERSION ✓"

# Check Composer
if ! command -v composer &> /dev/null; then
    log_error "Composer is not installed"
    exit 1
fi
log_info "Composer installed ✓"

# Check Node.js
if ! command -v node &> /dev/null; then
    log_error "Node.js is not installed"
    exit 1
fi
log_info "Node.js version: $(node --version) ✓"

# Step 2: Install Composer dependencies
log_step "2/12: Installing Composer dependencies..."
if [ "$ENVIRONMENT" == "production" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
else
    composer install --optimize-autoloader --no-interaction
fi

# Step 3: Install NPM dependencies
log_step "3/12: Installing NPM dependencies..."
npm ci

# Step 4: Setup environment file
log_step "4/12: Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    log_info "Created .env file from .env.example"
else
    log_warn ".env file already exists, skipping"
fi

# Step 5: Generate application key
log_step "5/12: Generating application key..."
php artisan key:generate

# Step 6: Create storage directories
log_step "6/12: Creating storage directories..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p storage/app/public
log_info "Storage directories created ✓"

# Step 7: Set permissions
log_step "7/12: Setting proper permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
log_info "Permissions set ✓"

# Step 8: Create symbolic link for storage
log_step "8/12: Creating storage symbolic link..."
php artisan storage:link

# Step 9: Run database migrations
log_step "9/12: Running database migrations..."
read -p "Do you want to run migrations now? (yes/no): " RUN_MIGRATIONS

if [ "$RUN_MIGRATIONS" == "yes" ]; then
    php artisan migrate --force
    log_info "Migrations completed ✓"

    # Ask about seeders
    read -p "Do you want to run database seeders? (yes/no): " RUN_SEEDERS

    if [ "$RUN_SEEDERS" == "yes" ]; then
        php artisan db:seed --force
        log_info "Seeders completed ✓"
    fi
else
    log_warn "Skipping migrations"
fi

# Step 10: Build frontend assets
log_step "10/12: Building frontend assets..."
if [ "$ENVIRONMENT" == "production" ]; then
    npm run build
else
    npm run dev
fi
log_info "Assets built ✓"

# Step 11: Cache configuration (production only)
if [ "$ENVIRONMENT" == "production" ]; then
    log_step "11/12: Caching configuration..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    log_info "Configuration cached ✓"
else
    log_step "11/12: Clearing caches (development mode)..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    log_info "Caches cleared ✓"
fi

# Step 12: Setup complete
log_step "12/12: Final checks..."

# Create audit log migration if needed
if [ ! -f database/migrations/*_create_audit_logs_table.php ]; then
    log_warn "Audit logs migration not found. You may need to run: php artisan migrate"
fi

# Display setup summary
echo ""
echo -e "${GREEN}"
echo "╔════════════════════════════════════════════════════╗"
echo "║              Setup Complete! ✓                     ║"
echo "╚════════════════════════════════════════════════════╝"
echo -e "${NC}"

log_info "Application is ready to use!"
log_info "Environment: $ENVIRONMENT"

# Next steps
echo ""
echo -e "${BLUE}Next Steps:${NC}"
echo "  1. Configure your .env file with proper database credentials"
echo "  2. Run migrations if you skipped them: php artisan migrate"
echo "  3. Start development server: php artisan serve"
echo "  4. Or use Docker: docker-compose up -d"
echo ""
echo "For production deployment:"
echo "  - Configure your web server (Nginx/Apache)"
echo "  - Setup SSL certificates"
echo "  - Configure queue workers: php artisan queue:work"
echo "  - Setup scheduler: * * * * * php artisan schedule:run >> /dev/null 2>&1"
echo ""

exit 0
