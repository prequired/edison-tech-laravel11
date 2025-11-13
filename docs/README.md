# Edison Tech Documentation

Complete documentation for the Edison Tech Project Management System.

## 📚 Documentation Index

### Getting Started

- [Project README](../README.md) - Project overview, features, and quick start
- [Installation Guide](../README.md#installation) - Detailed installation instructions
- [Configuration](../README.md#configuration) - Environment configuration

### API Documentation

- [API Overview](api/README.md) - API documentation guide
- [OpenAPI Specification](api/openapi.yaml) - Complete API specification (OpenAPI 3.0)
- [Authentication](api/README.md#authentication) - API authentication guide
- [Rate Limiting](api/README.md#rate-limiting) - Rate limiting details
- [Error Handling](api/README.md#error-responses) - Error response formats

### Deployment

- [Production Checklist](deployment/PRODUCTION-CHECKLIST.md) - Complete pre-deployment checklist
- [Backup & Recovery](deployment/BACKUP-RECOVERY.md) - Backup strategy and recovery procedures
- [Docker Deployment](../docker-compose.yml) - Container deployment configuration
- [CI/CD Pipeline](../.github/workflows/) - Automated deployment workflows

### Testing

- [Load Testing Guide](../tests/Load/README.md) - Performance and load testing
- [Test Coverage Report](PRODUCTION-READINESS-REPORT.md#test-coverage-statistics) - Current test coverage
- [Performance Tests](../tests/Performance/) - Database performance tests
- [Integration Tests](../tests/Integration/) - End-to-end integration tests

### Operations

- [Production Readiness Report](PRODUCTION-READINESS-REPORT.md) - Complete production assessment
- [Health Checks](PRODUCTION-READINESS-REPORT.md#health-checks) - Monitoring endpoints
- [Performance Monitoring](PRODUCTION-READINESS-REPORT.md#performance-monitoring) - Monitoring setup
- [Error Tracking](PRODUCTION-READINESS-REPORT.md#error-tracking) - Sentry configuration

---

## 🏗️ Architecture

### Application Structure

```
edison-tech-laravel11/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Request handlers
│   │   ├── Middleware/       # Request/response middleware
│   │   └── Requests/         # Form request validation
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic layer
│   ├── Observers/            # Model observers
│   ├── Policies/             # Authorization policies
│   ├── Notifications/        # Multi-channel notifications
│   └── Rules/                # Custom validation rules
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── tests/
│   ├── Unit/                 # Unit tests
│   ├── Feature/              # Feature tests
│   ├── Integration/          # Integration tests
│   ├── Performance/          # Performance tests
│   └── Load/                 # Load testing configuration
├── docs/                     # Documentation
└── docker/                   # Docker configuration
```

### Key Components

#### Models

- **User** - User accounts with roles (admin, employee, client)
- **Company** - Client companies
- **Project** - Projects with status tracking
- **Task** - Project tasks with assignments
- **TimeEntry** - Time tracking
- **Invoice** - Invoicing with items
- **Payment** - Payment processing and tracking
- **Contract** - Contracts with signing workflow
- **Document** - File attachments (polymorphic)

#### Services

- **UserService** - User management
- **ProjectService** - Project operations
- **InvoiceService** - Invoice generation and management
- **PaymentService** - Payment processing
- **TimeTrackingService** - Time entry management
- **CacheService** - Centralized caching
- **AuditLogger** - Activity logging

#### Middleware

- **SecurityHeaders** - Security header injection
- **DatabaseQueryCache** - Query result caching
- **PerformanceMonitoring** - Request performance tracking

---

## 🔒 Security

### Security Features

- **Authentication:** Sanctum-based API authentication
- **Authorization:** Policy-based access control
- **Rate Limiting:** 8 configured rate limiters
- **Security Headers:** CSP, HSTS, X-Frame-Options, etc.
- **File Upload Security:** MIME validation with magic bytes
- **Audit Logging:** Complete activity tracking
- **CORS:** Configurable cross-origin policies

### Security Configuration

```env
# Security Settings
RATE_LIMIT_AUTH=5
RATE_LIMIT_PASSWORD_RESET=3
RATE_LIMIT_2FA=5
RATE_LIMIT_API=60
RATE_LIMIT_UPLOADS=10

MAX_FILE_UPLOAD_SIZE=10240  # KB
ALLOWED_FILE_TYPES=images,documents,archives

AUDIT_LOG_ENABLED=true
AUDIT_LOG_RETENTION_DAYS=365
```

---

## ⚡ Performance

### Caching Strategy

- **Redis Cache:** Primary cache store
- **Query Caching:** Automatic query result caching
- **Model Caching:** Per-model caching with auto-invalidation
- **Config/Route/View:** Laravel optimization caching

### Cache TTL Values

| Data Type | TTL | Use Case |
|-----------|-----|----------|
| Short | 5 minutes | Stats, invoices, tasks |
| Medium | 30 minutes | Users, projects |
| Long | 1 hour | Companies |
| Day | 24 hours | Rarely changing data |

### Performance Targets

- Health checks: < 50ms
- API (cached): < 200ms
- API (database): < 500ms
- Search queries: < 800ms
- Concurrent users: 500+

---

## 📊 Monitoring

### Health Check Endpoints

```bash
# Basic health check
GET /health

# Comprehensive check (all services)
GET /health/comprehensive

# Kubernetes readiness probe
GET /health/readiness

# Kubernetes liveness probe
GET /health/liveness
```

### Error Tracking

**Sentry Configuration:**
- Error capture and reporting
- Performance monitoring
- Release tracking
- User context
- Breadcrumb logging

### Performance Monitoring

**Request Headers:**
- `X-Response-Time`: Request processing time
- `X-Query-Count`: Database queries executed
- `X-Query-Time`: Total query execution time
- `X-Memory-Peak`: Peak memory usage
- `X-Cache`: Cache hit/miss status

---

## 🧪 Testing

### Test Coverage

```
Total Tests: 634
Coverage: 70%

Breakdown:
- Unit Tests: 303 (47.8%)
- Feature Tests: 268 (42.3%)
- Integration Tests: 33 (5.2%)
- Performance Tests: 15 (2.4%)
- Security Tests: 15 (2.4%)
```

### Running Tests

```bash
# All tests
php artisan test

# With coverage
php artisan test --coverage

# Specific suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Specific test
php artisan test tests/Unit/Models/UserTest.php

# Parallel execution
php artisan test --parallel

# Performance tests
php artisan test tests/Performance/

# Load tests
artillery run tests/Load/artillery-config.yml
```

---

## 🚀 Deployment

### Quick Deployment

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Build assets
npm ci --production && npm run build

# 4. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Migrate
php artisan migrate --force

# 6. Restart services
php artisan queue:restart
sudo systemctl restart php8.2-fpm
```

### Zero-Downtime Deployment

See [Deployment Checklist](deployment/PRODUCTION-CHECKLIST.md#zero-downtime-deployment)

### Docker Deployment

```bash
# Build and start
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down
```

---

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME="Edison Tech"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edison_tech
DB_USERNAME=your_user
DB_PASSWORD=your_password

# Cache
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

# Monitoring
SENTRY_LARAVEL_DSN=your_sentry_dsn
```

### Redis Configuration

```bash
# Start Redis
sudo systemctl start redis

# Enable on boot
sudo systemctl enable redis

# Check status
redis-cli ping
```

---

## 📖 Additional Resources

### External Links

- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Redis Documentation](https://redis.io/documentation)
- [Docker Documentation](https://docs.docker.com/)
- [Artillery Documentation](https://www.artillery.io/docs)

### Community

- [Laravel Community](https://laravel.com/community)
- [GitHub Discussions](https://github.com/laravel/framework/discussions)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)

---

## 🆘 Support

### Getting Help

1. Check documentation (you are here!)
2. Review [Production Readiness Report](PRODUCTION-READINESS-REPORT.md)
3. Check logs: `storage/logs/laravel.log`
4. Run health checks: `/health/comprehensive`
5. Contact development team

### Common Issues

#### Application Not Loading

```bash
# Check logs
tail -f storage/logs/laravel.log

# Verify permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
```

#### Database Connection Errors

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Run migrations
php artisan migrate

# Check credentials in .env
```

#### Performance Issues

```bash
# Check query count
# Look for X-Query-Count header in responses

# Enable query logging
php artisan db:monitor

# Run performance tests
php artisan test tests/Performance/
```

---

## 📝 Changelog

### Version 1.0.0 (2025-01-15)

- ✅ Initial production release
- ✅ Complete feature set implemented
- ✅ 70% test coverage achieved
- ✅ Full documentation
- ✅ Production hardening complete
- ✅ CI/CD pipeline configured
- ✅ Monitoring and logging configured

---

## 👥 Contributing

This is a private project. For internal contributions:

1. Create feature branch
2. Write tests
3. Update documentation
4. Submit pull request
5. Pass CI/CD checks
6. Get code review
7. Merge to main

---

## 📄 License

Proprietary - All rights reserved

---

**Last Updated:** 2025-01-15
**Documentation Version:** 1.0.0
**Application Version:** 1.0.0
