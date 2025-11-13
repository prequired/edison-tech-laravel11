#!/bin/bash

#######################################################
# Edison Tech - Build Verification Script
#
# This script verifies that the application build is
# complete and production-ready without requiring
# a database connection.
#######################################################

# Don't exit on error - we want to run all checks
set +e

echo "=============================================="
echo "  Edison Tech - Build Verification"
echo "=============================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counters
PASSED=0
FAILED=0

# Function to check status
check_status() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ PASS${NC}: $2"
        ((PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC}: $2"
        ((FAILED++))
    fi
}

echo "1. Checking PHP Version..."
php -v | head -n 1
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
PHP_MAJOR=$(php -r 'echo PHP_MAJOR_VERSION;')
PHP_MINOR=$(php -r 'echo PHP_MINOR_VERSION;')
if [ "$PHP_MAJOR" -gt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -ge 2 ]); then
    check_status 0 "PHP version $PHP_VERSION is compatible (>= 8.2)"
else
    check_status 1 "PHP version $PHP_VERSION is not compatible (requires >= 8.2)"
fi
echo ""

echo "2. Checking Composer Dependencies..."
if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
    check_status 0 "Composer dependencies installed"
else
    check_status 1 "Composer dependencies not installed"
fi
echo ""

echo "3. Checking Laravel Installation..."
if php artisan --version > /dev/null 2>&1; then
    LARAVEL_VERSION=$(php artisan --version)
    check_status 0 "Laravel installed: $LARAVEL_VERSION"
else
    check_status 1 "Laravel not properly installed"
fi
echo ""

echo "4. Checking Environment Configuration..."
if [ -f ".env" ]; then
    check_status 0 ".env file exists"

    if grep -q "APP_KEY=base64:" .env; then
        check_status 0 "Application key is set"
    else
        check_status 1 "Application key not set"
    fi
else
    check_status 1 ".env file missing"
fi
echo ""

echo "5. Checking Directory Structure..."
REQUIRED_DIRS=(
    "app/Http/Controllers"
    "app/Models"
    "app/Services"
    "app/Policies"
    "app/Observers"
    "app/Http/Middleware"
    "database/migrations"
    "database/seeders"
    "database/factories"
    "tests/Unit"
    "tests/Feature"
    "tests/Integration"
    "tests/Performance"
    "docs"
)

for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        check_status 0 "Directory exists: $dir"
    else
        check_status 1 "Directory missing: $dir"
    fi
done
echo ""

echo "6. Checking Core Models..."
MODELS=(
    "User"
    "Company"
    "Project"
    "Task"
    "TimeEntry"
    "Invoice"
    "InvoiceItem"
    "Payment"
    "Contract"
    "Document"
)

for model in "${MODELS[@]}"; do
    if [ -f "app/Models/$model.php" ]; then
        check_status 0 "Model exists: $model"
    else
        check_status 1 "Model missing: $model"
    fi
done
echo ""

echo "7. Checking Services..."
SERVICES=(
    "UserService"
    "ProjectService"
    "InvoiceService"
    "PaymentService"
    "TimeTrackingService"
    "CacheService"
    "AuditLogger"
)

for service in "${SERVICES[@]}"; do
    if [ -f "app/Services/$service.php" ]; then
        check_status 0 "Service exists: $service"
    else
        check_status 1 "Service missing: $service"
    fi
done
echo ""

echo "8. Checking Middleware..."
MIDDLEWARE=(
    "SecurityHeaders"
    "DatabaseQueryCache"
    "PerformanceMonitoring"
)

for middleware in "${MIDDLEWARE[@]}"; do
    if [ -f "app/Http/Middleware/$middleware.php" ]; then
        check_status 0 "Middleware exists: $middleware"
    else
        check_status 1 "Middleware missing: $middleware"
    fi
done
echo ""

echo "9. Checking Test Files..."
TEST_CATEGORIES=(
    "Unit/Models"
    "Feature/Admin"
    "Feature/Security"
    "Integration"
    "Performance"
)

for category in "${TEST_CATEGORIES[@]}"; do
    if [ -d "tests/$category" ] && [ "$(ls -A tests/$category 2>/dev/null)" ]; then
        TEST_COUNT=$(find "tests/$category" -name "*Test.php" | wc -l)
        check_status 0 "Test category exists: $category ($TEST_COUNT test files)"
    else
        check_status 1 "Test category missing or empty: $category"
    fi
done
echo ""

echo "10. Checking Documentation..."
DOCS=(
    "docs/README.md"
    "docs/PRODUCTION-READINESS-REPORT.md"
    "docs/CTO-AUDIT-FINAL.md"
    "docs/api/README.md"
    "docs/api/openapi.yaml"
    "docs/deployment/PRODUCTION-CHECKLIST.md"
    "docs/deployment/BACKUP-RECOVERY.md"
)

for doc in "${DOCS[@]}"; do
    if [ -f "$doc" ]; then
        check_status 0 "Documentation exists: $doc"
    else
        check_status 1 "Documentation missing: $doc"
    fi
done
echo ""

echo "11. Checking DevOps Files..."
DEVOPS=(
    "docker-compose.yml"
    "Dockerfile"
    ".github/workflows/ci.yml"
    ".github/workflows/deploy.yml"
    "scripts/deploy.sh"
    "scripts/rollback.sh"
)

for file in "${DEVOPS[@]}"; do
    if [ -f "$file" ]; then
        check_status 0 "DevOps file exists: $file"
    else
        check_status 1 "DevOps file missing: $file"
    fi
done
echo ""

echo "12. Checking PHP Syntax..."
PHP_ERRORS=0
while IFS= read -r -d '' file; do
    if ! php -l "$file" > /dev/null 2>&1; then
        echo -e "${RED}Syntax error in: $file${NC}"
        ((PHP_ERRORS++))
    fi
done < <(find app -name "*.php" -print0)

if [ $PHP_ERRORS -eq 0 ]; then
    check_status 0 "All PHP files have valid syntax"
else
    check_status 1 "Found $PHP_ERRORS PHP syntax errors"
fi
echo ""

echo "13. Checking Autoload..."
if php artisan list > /dev/null 2>&1; then
    check_status 0 "Autoload is working correctly"
else
    check_status 1 "Autoload has issues"
fi
echo ""

echo "14. Checking Configuration Files..."
CONFIGS=(
    "config/app.php"
    "config/database.php"
    "config/cache.php"
    "config/queue.php"
    "config/cors.php"
    "config/sanctum.php"
)

for config in "${CONFIGS[@]}"; do
    if [ -f "$config" ]; then
        if php -l "$config" > /dev/null 2>&1; then
            check_status 0 "Config valid: $config"
        else
            check_status 1 "Config has syntax errors: $config"
        fi
    else
        check_status 1 "Config missing: $config"
    fi
done
echo ""

echo "15. Checking Routes..."
if php artisan route:list > /dev/null 2>&1; then
    ROUTE_COUNT=$(php artisan route:list --json 2>/dev/null | grep -c '"uri"' || echo "many")
    check_status 0 "Routes defined ($ROUTE_COUNT routes)"
else
    check_status 1 "Routes have errors"
fi
echo ""

echo "=============================================="
echo "  Build Verification Summary"
echo "=============================================="
echo -e "${GREEN}Passed:${NC} $PASSED"
echo -e "${RED}Failed:${NC} $FAILED"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ BUILD VERIFICATION SUCCESSFUL${NC}"
    echo ""
    echo "The Edison Tech application build is complete and ready for deployment."
    echo "Note: Full test suite requires a configured database environment."
    exit 0
else
    echo -e "${YELLOW}⚠ BUILD VERIFICATION COMPLETED WITH WARNINGS${NC}"
    echo ""
    echo "Some checks failed. Review the output above for details."
    exit 1
fi
