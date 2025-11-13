# Production Deployment Checklist

Comprehensive checklist for deploying Edison Tech Project Management System to production.

## Pre-Deployment

### 1. Code Quality & Testing

- [ ] All tests passing (`php artisan test`)
- [ ] Test coverage ≥ 70%
- [ ] Code reviewed and approved
- [ ] No critical security vulnerabilities (run `composer audit`)
- [ ] PHPStan/Larastan analysis passed
- [ ] Code follows PSR-12 standards

### 2. Environment Configuration

- [ ] `.env` file configured for production
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_KEY` generated (`php artisan key:generate`)
  - [ ] Unique `APP_KEY` (not copied from development)
  - [ ] `APP_URL` set to production domain
  - [ ] `FRONTEND_URL` configured

### 3. Database Configuration

- [ ] Database credentials configured
- [ ] Database created
- [ ] Database user has appropriate permissions
- [ ] Database migrations reviewed
- [ ] Backup of existing database (if applicable)
- [ ] Connection tested
- [ ] Database timezone configured

### 4. Security Configuration

- [ ] Strong database passwords
- [ ] HTTPS/SSL certificate installed and valid
- [ ] Rate limiting configured (`.env`)
- [ ] CORS settings configured (`config/cors.php`)
- [ ] Security headers middleware enabled
- [ ] File upload limits configured
- [ ] Session configuration secure
  - [ ] `SESSION_SECURE_COOKIE=true` (if HTTPS)
  - [ ] `SESSION_HTTPONLY=true`
  - [ ] `SESSION_SAME_SITE=lax`

### 5. Cache & Performance

- [ ] Redis installed and configured
- [ ] Redis password set (if applicable)
- [ ] Cache driver set to Redis (`CACHE_STORE=redis`)
- [ ] Session driver configured (`SESSION_DRIVER=redis` or `database`)
- [ ] Queue driver configured (`QUEUE_CONNECTION=redis` or `database`)
- [ ] OpCache enabled in PHP
- [ ] Config cache cleared (`php artisan config:clear`)

### 6. Email Configuration

- [ ] Mail driver configured (`MAIL_MAILER`)
- [ ] SMTP settings configured
- [ ] Test email sent successfully
- [ ] `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME` set

### 7. Third-Party Services

- [ ] Sentry DSN configured (error tracking)
- [ ] Stripe keys configured (if using payments)
- [ ] AWS credentials configured (if using S3)
- [ ] Any other API keys configured

### 8. File Permissions

- [ ] Storage directory writable (`chmod -R 775 storage`)
- [ ] Bootstrap/cache directory writable (`chmod -R 775 bootstrap/cache`)
- [ ] Logs directory writable
- [ ] Uploads directory configured
- [ ] Correct ownership (`chown -R www-data:www-data`)

## Deployment Process

### 1. Pre-Deployment Backup

- [ ] Database backup created
- [ ] Application files backup created
- [ ] `.env` file backed up
- [ ] Backup tested and verified
- [ ] Rollback plan documented

### 2. Application Deployment

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Install node dependencies (if applicable)
npm ci --production

# 4. Build frontend assets
npm run build

# 5. Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Run database migrations
php artisan migrate --force

# 7. Restart queue workers
php artisan queue:restart

# 8. Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

- [ ] Code deployed successfully
- [ ] Dependencies installed
- [ ] Assets compiled
- [ ] Caches rebuilt
- [ ] Migrations executed
- [ ] No migration errors

### 3. Post-Deployment Verification

- [ ] Application loads successfully
- [ ] Health check endpoint responding (`/health`)
- [ ] Comprehensive health check passing (`/health/comprehensive`)
- [ ] Database connectivity confirmed
- [ ] Cache connectivity confirmed
- [ ] Queue connectivity confirmed
- [ ] Login functionality working
- [ ] Critical user flows tested
- [ ] API endpoints responding
- [ ] No JavaScript console errors
- [ ] No PHP errors in logs

## Post-Deployment

### 1. Monitoring Setup

- [ ] Sentry error tracking active
- [ ] Application logs being captured
- [ ] Server metrics monitoring configured
- [ ] Uptime monitoring configured
- [ ] SSL certificate monitoring
- [ ] Disk space monitoring
- [ ] Database performance monitoring

### 2. Performance Verification

- [ ] Response times acceptable (< 500ms)
- [ ] No memory leaks
- [ ] Database query performance good
- [ ] Cache hit rate > 80%
- [ ] No N+1 query issues

### 3. Security Verification

- [ ] HTTPS enforced
- [ ] Security headers present (check with securityheaders.com)
- [ ] No sensitive data in logs
- [ ] Rate limiting working
- [ ] CORS policy correct
- [ ] File upload restrictions working

### 4. Backup Verification

- [ ] Automated backups configured
- [ ] Backup schedule tested
- [ ] Backup restoration tested
- [ ] Offsite backup configured
- [ ] Backup retention policy set

### 5. Documentation

- [ ] Deployment documented
- [ ] Any issues documented
- [ ] Runbook updated
- [ ] Team notified
- [ ] Change log updated

## Rollback Procedure

If issues are encountered:

```bash
# 1. Revert code
git reset --hard <previous-commit>

# 2. Restore database backup (if migrations ran)
mysql -u user -p database < backup.sql

# 3. Restore .env file
cp .env.backup .env

# 4. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Restart services
php artisan queue:restart
sudo systemctl restart php8.2-fpm
```

- [ ] Rollback tested
- [ ] Rollback procedure documented
- [ ] Team notified of rollback

## Environment-Specific Checklist

### Staging Environment

- [ ] Deployed to staging first
- [ ] Smoke tests passed
- [ ] Performance tests passed
- [ ] Security tests passed
- [ ] Client/stakeholder approval

### Production Environment

- [ ] Maintenance mode enabled (if needed)
- [ ] Users notified of deployment
- [ ] Deployment window scheduled
- [ ] On-call engineer assigned
- [ ] Emergency contacts available

## Zero-Downtime Deployment

For zero-downtime deployments:

1. [ ] Deploy to new servers
2. [ ] Run migrations (safe migrations only)
3. [ ] Test new deployment
4. [ ] Switch load balancer to new servers
5. [ ] Monitor for issues
6. [ ] Keep old servers running temporarily
7. [ ] Shut down old servers after verification

## Health Checks

### Before Deployment

```bash
# Check current health
curl https://app.example.com/health/comprehensive
```

### After Deployment

```bash
# Basic health check
curl https://app.example.com/health
# Expected: {"status":"healthy","timestamp":"..."}

# Comprehensive health check
curl https://app.example.com/health/comprehensive
# Expected: {"status":"healthy","checks":{...}}

# Database check
php artisan tinker
>>> DB::connection()->getPdo();

# Cache check
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');

# Queue check
php artisan queue:work --once
```

## Critical Issues Response

If critical issues are found:

1. **Immediate Actions:**
   - [ ] Enable maintenance mode
   - [ ] Stop queue workers
   - [ ] Alert team
   - [ ] Assess impact

2. **Triage:**
   - [ ] Identify root cause
   - [ ] Determine severity
   - [ ] Decide: fix forward or rollback?

3. **Resolution:**
   - [ ] Implement fix or rollback
   - [ ] Test resolution
   - [ ] Re-deploy if needed
   - [ ] Verify resolution

4. **Follow-up:**
   - [ ] Post-mortem document
   - [ ] Root cause analysis
   - [ ] Preventive measures
   - [ ] Update procedures

## Sign-off

- [ ] Development team approval: ________________ Date: ________
- [ ] QA team approval: ________________ Date: ________
- [ ] Operations team approval: ________________ Date: ________
- [ ] Product owner approval: ________________ Date: ________

## Notes

Document any deviations from checklist or special considerations:

_______________________________________________________________
_______________________________________________________________
_______________________________________________________________
_______________________________________________________________

## Deployment Timeline

| Time | Action | Status |
|------|--------|--------|
| T-24h | Final code review |  |
| T-12h | Staging deployment |  |
| T-6h | Stakeholder approval |  |
| T-2h | Database backup |  |
| T-1h | Team briefing |  |
| T-0 | Production deployment |  |
| T+15min | Verification tests |  |
| T+1h | Monitoring review |  |
| T+24h | Post-deployment review |  |

## Emergency Contacts

- **On-Call Engineer:** ________________ Phone: __________
- **DBA:** ________________ Phone: __________
- **DevOps:** ________________ Phone: __________
- **Product Owner:** ________________ Phone: __________

---

**Deployment ID:** ________________
**Deployed By:** ________________
**Deployment Date:** ________________
**Deployment Time:** ________________
**Version/Tag:** ________________
