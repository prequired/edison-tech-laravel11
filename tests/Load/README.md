# Load Testing Guide

This directory contains load testing configurations and scripts for the Edison Tech Project Management System.

## Prerequisites

### Install Artillery

```bash
# Using npm
npm install -g artillery

# Using yarn
yarn global add artillery

# Verify installation
artillery --version
```

### Alternative: Apache Bench

```bash
# Ubuntu/Debian
sudo apt-get install apache2-utils

# macOS
brew install httpie
```

## Running Load Tests

### Artillery Load Tests

**Basic test:**
```bash
artillery run artillery-config.yml
```

**Quick test (shorter duration):**
```bash
artillery quick --duration 60 --rate 10 http://localhost/health
```

**Test specific endpoint:**
```bash
artillery quick \
  --duration 120 \
  --rate 20 \
  http://localhost/api/projects
```

**Generate HTML report:**
```bash
artillery run artillery-config.yml --output report.json
artillery report report.json --output report.html
```

## Load Testing Scenarios

### 1. Health Check Load Test

Test basic application availability:

```bash
artillery quick \
  --duration 300 \
  --rate 100 \
  http://localhost/health
```

**Expected results:**
- Response time: < 50ms (p95)
- Success rate: 100%
- Throughput: 100+ req/s

### 2. API Endpoint Test

Test authenticated API endpoints:

```bash
# First, get auth token
TOKEN=$(curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' \
  | jq -r '.token')

# Then load test
artillery quick \
  --duration 180 \
  --rate 50 \
  -H "Authorization: Bearer $TOKEN" \
  http://localhost/api/projects
```

**Expected results:**
- Response time: < 200ms (p95)
- Success rate: > 99%
- Throughput: 50+ req/s

### 3. Database Query Test

Test database-heavy endpoints:

```bash
artillery run artillery-config.yml --target http://localhost
```

**Expected results:**
- Response time: < 500ms (p95)
- Success rate: > 95%
- No database connection errors

### 4. Spike Test

Test system behavior under sudden load:

```bash
# Sustained load then spike
artillery quick \
  --duration 60 \
  --rate 10 \
  http://localhost/api/projects

# Immediate spike
artillery quick \
  --duration 30 \
  --rate 200 \
  http://localhost/api/projects
```

**Expected results:**
- System remains stable
- Response time degrades gracefully
- No 5xx errors

## Performance Benchmarks

### Target Metrics

| Endpoint Type | p50 | p95 | p99 | Success Rate |
|--------------|-----|-----|-----|--------------|
| Health checks | < 20ms | < 50ms | < 100ms | 100% |
| Static pages | < 50ms | < 100ms | < 200ms | 100% |
| API (cached) | < 100ms | < 200ms | < 500ms | > 99% |
| API (database) | < 200ms | < 500ms | < 1s | > 95% |
| Search queries | < 300ms | < 800ms | < 1.5s | > 95% |

### Load Capacity

- **Concurrent users:** 500+
- **Requests per second:** 1000+
- **Sustained load duration:** 1 hour+

## Using Apache Bench

### Simple GET request test

```bash
ab -n 10000 -c 100 http://localhost/health
```

Parameters:
- `-n 10000`: Total number of requests
- `-c 100`: Concurrent requests
- `-t 60`: Time limit (60 seconds)
- `-k`: Keep-alive enabled

### POST request test

```bash
ab -n 1000 -c 50 -p login.json -T application/json \
  http://localhost/api/login
```

Create `login.json`:
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

### Authenticated API test

```bash
# Get token
TOKEN=$(curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' \
  | jq -r '.token')

# Load test with auth
ab -n 5000 -c 50 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  http://localhost/api/projects
```

## Monitoring During Load Tests

### 1. Application Logs

```bash
# Watch application logs
tail -f storage/logs/laravel.log

# Watch web server logs
tail -f /var/log/nginx/access.log
```

### 2. Database Performance

```bash
# MySQL
mysql -u root -p -e "SHOW PROCESSLIST;"
mysql -u root -p -e "SHOW STATUS LIKE 'Threads%';"

# PostgreSQL
psql -U postgres -c "SELECT * FROM pg_stat_activity;"
```

### 3. System Resources

```bash
# CPU and memory
htop

# Disk I/O
iotop

# Network
iftop

# All-in-one
docker stats  # If using Docker
```

### 4. Redis Cache

```bash
# Connect to Redis
redis-cli

# Monitor in real-time
redis-cli MONITOR

# Check stats
redis-cli INFO stats
redis-cli INFO memory
```

## Analyzing Results

### Key Metrics

1. **Response Time**
   - p50 (median): Half of requests faster
   - p95: 95% of requests faster
   - p99: 99% of requests faster

2. **Throughput**
   - Requests per second
   - Should remain stable under load

3. **Error Rate**
   - Target: < 1%
   - 5xx errors indicate server issues
   - 429 errors indicate rate limiting

4. **Concurrency**
   - Max concurrent users supported
   - Should handle 500+ users

### Artillery Report Analysis

```bash
# Generate detailed report
artillery run artillery-config.yml --output results.json
artillery report results.json

# View in browser
open report.html
```

Look for:
- Response time distribution
- Error rates by endpoint
- Throughput over time
- Virtual user behavior

## Troubleshooting

### High Response Times

1. **Check database queries**
   ```bash
   # Enable query logging
   php artisan db:monitor
   ```

2. **Review cache hit rates**
   ```bash
   redis-cli INFO stats | grep hit
   ```

3. **Check for N+1 queries**
   ```bash
   php artisan test tests/Performance/DatabasePerformanceTest.php
   ```

### High Error Rates

1. **Check rate limiting**
   - Adjust rate limits in `.env`
   - Scale up if legitimate traffic

2. **Review error logs**
   ```bash
   tail -100 storage/logs/laravel.log | grep ERROR
   ```

3. **Check database connections**
   ```bash
   mysql -e "SHOW STATUS LIKE 'Threads_connected';"
   ```

### Low Throughput

1. **Scale web servers**
   - Increase PHP-FPM workers
   - Add more application instances

2. **Optimize database**
   - Add missing indexes
   - Enable query cache
   - Use read replicas

3. **Enable caching**
   - Redis for sessions
   - Redis for cache
   - CDN for static assets

## Best Practices

1. **Always test in staging first**
2. **Start with low load and ramp up**
3. **Monitor system resources**
4. **Test during off-peak hours**
5. **Have rollback plan ready**
6. **Document baseline metrics**
7. **Test regularly (weekly/monthly)**
8. **Automate load tests in CI/CD**

## CI/CD Integration

Add to `.github/workflows/load-test.yml`:

```yaml
name: Load Test

on:
  schedule:
    - cron: '0 2 * * 0'  # Weekly on Sunday at 2 AM
  workflow_dispatch:

jobs:
  load-test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Install Artillery
        run: npm install -g artillery

      - name: Run load tests
        run: artillery run tests/Load/artillery-config.yml

      - name: Upload results
        uses: actions/upload-artifact@v3
        with:
          name: load-test-results
          path: report.json
```

## References

- [Artillery Documentation](https://www.artillery.io/docs)
- [Apache Bench Manual](https://httpd.apache.org/docs/2.4/programs/ab.html)
- [Load Testing Best Practices](https://grafana.com/blog/2020/03/03/load-testing-best-practices/)
