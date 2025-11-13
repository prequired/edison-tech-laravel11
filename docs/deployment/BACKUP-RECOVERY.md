# Backup and Recovery Procedures

Comprehensive backup and disaster recovery procedures for the Edison Tech Project Management System.

## Table of Contents

1. [Backup Strategy](#backup-strategy)
2. [Database Backups](#database-backups)
3. [File Backups](#file-backups)
4. [Recovery Procedures](#recovery-procedures)
5. [Disaster Recovery](#disaster-recovery)
6. [Automated Backup Scripts](#automated-backup-scripts)

## Backup Strategy

### Backup Schedule

| Type | Frequency | Retention | Storage Location |
|------|-----------|-----------|------------------|
| Database (Full) | Daily at 2 AM | 30 days | S3 + Local |
| Database (Incremental) | Every 6 hours | 7 days | Local |
| Files (Full) | Daily at 3 AM | 30 days | S3 + Local |
| Files (Incremental) | Every 12 hours | 7 days | Local |
| Configuration | On change | Forever | Git + S3 |

### Backup Requirements

- **RPO (Recovery Point Objective):** 6 hours
- **RTO (Recovery Time Objective):** 2 hours
- **Backup Locations:** Primary + Offsite
- **Encryption:** AES-256
- **Compression:** gzip
- **Verification:** Daily automated testing

## Database Backups

### Manual Database Backup

#### MySQL/MariaDB

```bash
#!/bin/bash
# Full database backup

# Configuration
DB_NAME="edison_tech"
DB_USER="db_user"
DB_PASS="db_password"
BACKUP_DIR="/var/backups/mysql"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="${DB_NAME}_${DATE}.sql.gz"

# Create backup directory
mkdir -p $BACKUP_DIR

# Perform backup
mysqldump \
  --user=$DB_USER \
  --password=$DB_PASS \
  --single-transaction \
  --quick \
  --lock-tables=false \
  --routines \
  --triggers \
  --events \
  $DB_NAME | gzip > "${BACKUP_DIR}/${FILENAME}"

# Verify backup
if [ $? -eq 0 ]; then
    echo "Backup successful: ${FILENAME}"
    ls -lh "${BACKUP_DIR}/${FILENAME}"
else
    echo "Backup failed!"
    exit 1
fi

# Upload to S3 (optional)
aws s3 cp "${BACKUP_DIR}/${FILENAME}" \
  s3://your-bucket/backups/database/ \
  --storage-class STANDARD_IA

# Remove backups older than 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete
```

#### SQLite

```bash
#!/bin/bash
# SQLite backup

DB_FILE="/var/www/database/database.sqlite"
BACKUP_DIR="/var/backups/sqlite"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="database_${DATE}.sqlite"

mkdir -p $BACKUP_DIR

# Using sqlite3 backup command
sqlite3 $DB_FILE ".backup '${BACKUP_DIR}/${FILENAME}'"

# Compress
gzip "${BACKUP_DIR}/${FILENAME}"

echo "Backup created: ${FILENAME}.gz"
```

### Incremental Database Backup

#### Binary Log Backup (MySQL)

```bash
#!/bin/bash
# Backup binary logs for point-in-time recovery

BINLOG_DIR="/var/log/mysql"
BACKUP_DIR="/var/backups/mysql/binlogs"
DATE=$(date +%Y%m%d)

# Flush logs to start new binary log
mysql -u root -p -e "FLUSH BINARY LOGS;"

# Copy old binary logs
mkdir -p "${BACKUP_DIR}/${DATE}"
find $BINLOG_DIR -name "mysql-bin.*" -mmin +60 -exec cp {} "${BACKUP_DIR}/${DATE}/" \;

# Compress
tar -czf "${BACKUP_DIR}/binlogs_${DATE}.tar.gz" "${BACKUP_DIR}/${DATE}"
rm -rf "${BACKUP_DIR}/${DATE}"
```

## File Backups

### Application Files Backup

```bash
#!/bin/bash
# Backup application files

APP_DIR="/var/www/edison-tech"
BACKUP_DIR="/var/backups/app"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="app_files_${DATE}.tar.gz"

mkdir -p $BACKUP_DIR

# Create backup excluding certain directories
tar -czf "${BACKUP_DIR}/${FILENAME}" \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='storage/logs/*.log' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='.git' \
  -C $(dirname $APP_DIR) \
  $(basename $APP_DIR)

if [ $? -eq 0 ]; then
    echo "Application backup successful: ${FILENAME}"
    ls -lh "${BACKUP_DIR}/${FILENAME}"

    # Upload to S3
    aws s3 cp "${BACKUP_DIR}/${FILENAME}" \
      s3://your-bucket/backups/app/

    # Remove local backups older than 7 days
    find $BACKUP_DIR -name "app_files_*.tar.gz" -mtime +7 -delete
else
    echo "Application backup failed!"
    exit 1
fi
```

### Storage Directory Backup

```bash
#!/bin/bash
# Backup user-uploaded files

STORAGE_DIR="/var/www/edison-tech/storage/app"
BACKUP_DIR="/var/backups/storage"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="storage_${DATE}.tar.gz"

mkdir -p $BACKUP_DIR

# Backup storage with rsync (incremental)
rsync -av \
  --delete \
  --link-dest="${BACKUP_DIR}/latest" \
  $STORAGE_DIR/ \
  "${BACKUP_DIR}/${DATE}/"

# Create tarball
tar -czf "${BACKUP_DIR}/${FILENAME}" \
  -C $BACKUP_DIR \
  $DATE

# Update latest symlink
ln -snf "${BACKUP_DIR}/${DATE}" "${BACKUP_DIR}/latest"

# Upload to S3
aws s3 sync "${BACKUP_DIR}/${DATE}/" \
  s3://your-bucket/backups/storage/${DATE}/

echo "Storage backup completed: ${FILENAME}"
```

## Recovery Procedures

### Database Recovery

#### Full Database Restore

```bash
#!/bin/bash
# Restore database from backup

BACKUP_FILE="/var/backups/mysql/edison_tech_20250115_020000.sql.gz"
DB_NAME="edison_tech"
DB_USER="db_user"
DB_PASS="db_password"

# Confirm before proceeding
read -p "This will OVERWRITE the current database. Continue? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "Restore cancelled."
    exit 1
fi

# Stop application
echo "Stopping application..."
php artisan down

# Drop and recreate database
mysql -u$DB_USER -p$DB_PASS -e "DROP DATABASE IF EXISTS $DB_NAME;"
mysql -u$DB_USER -p$DB_PASS -e "CREATE DATABASE $DB_NAME;"

# Restore from backup
echo "Restoring database..."
gunzip < $BACKUP_FILE | mysql -u$DB_USER -p$DB_PASS $DB_NAME

if [ $? -eq 0 ]; then
    echo "Database restored successfully!"

    # Restart application
    php artisan up
    php artisan cache:clear
    php artisan config:cache

    echo "Application is back online."
else
    echo "Database restore failed!"
    exit 1
fi
```

#### Point-in-Time Recovery

```bash
#!/bin/bash
# Restore to specific point in time using binary logs

FULL_BACKUP="/var/backups/mysql/edison_tech_20250115_020000.sql.gz"
BINLOG_DIR="/var/backups/mysql/binlogs"
STOP_DATETIME="2025-01-15 14:30:00"  # Point to restore to

# 1. Restore full backup
gunzip < $FULL_BACKUP | mysql -uroot -p edison_tech

# 2. Apply binary logs up to specified time
for binlog in $(ls $BINLOG_DIR/mysql-bin.*); do
    mysqlbinlog \
      --stop-datetime="$STOP_DATETIME" \
      $binlog | mysql -uroot -p edison_tech
done

echo "Point-in-time recovery completed to: $STOP_DATETIME"
```

### Application Files Recovery

```bash
#!/bin/bash
# Restore application files

BACKUP_FILE="/var/backups/app/app_files_20250115_030000.tar.gz"
APP_DIR="/var/www/edison-tech"
TEMP_DIR="/tmp/app_restore"

# Stop web server
sudo systemctl stop nginx

# Create temporary directory
mkdir -p $TEMP_DIR

# Extract backup
tar -xzf $BACKUP_FILE -C $TEMP_DIR

# Move current app to backup location
mv $APP_DIR "${APP_DIR}.old.$(date +%Y%m%d)"

# Restore application
mv $TEMP_DIR/edison-tech $APP_DIR

# Fix permissions
sudo chown -R www-data:www-data $APP_DIR
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/bootstrap/cache

# Clear caches
cd $APP_DIR
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart web server
sudo systemctl start nginx

echo "Application files restored successfully!"
```

### Storage Files Recovery

```bash
#!/bin/bash
# Restore uploaded files from S3

STORAGE_DIR="/var/www/edison-tech/storage/app"
S3_PATH="s3://your-bucket/backups/storage/20250115/"

# Stop application
php artisan down

# Sync from S3
aws s3 sync $S3_PATH $STORAGE_DIR/ \
  --delete

# Fix permissions
sudo chown -R www-data:www-data $STORAGE_DIR
sudo chmod -R 775 $STORAGE_DIR

# Start application
php artisan up

echo "Storage files restored successfully!"
```

## Disaster Recovery

### Complete System Restore

In case of complete server failure:

#### 1. Provision New Server

```bash
# Install required packages
sudo apt-get update
sudo apt-get install -y nginx php8.2-fpm php8.2-mysql php8.2-redis \
  mysql-server redis-server git composer nodejs npm

# Clone application
cd /var/www
git clone https://github.com/your-org/edison-tech.git
cd edison-tech

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build
```

#### 2. Restore Database

```bash
# Download latest backup from S3
aws s3 cp s3://your-bucket/backups/database/latest.sql.gz /tmp/

# Create database
mysql -uroot -p -e "CREATE DATABASE edison_tech;"

# Restore database
gunzip < /tmp/latest.sql.gz | mysql -uroot -p edison_tech
```

#### 3. Restore Files

```bash
# Restore storage files
aws s3 sync s3://your-bucket/backups/storage/latest/ \
  /var/www/edison-tech/storage/app/

# Restore .env file (from secure location)
aws s3 cp s3://your-bucket/config/.env \
  /var/www/edison-tech/.env
```

#### 4. Configure and Start

```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/edison-tech
sudo chmod -R 775 /var/www/edison-tech/storage
sudo chmod -R 775 /var/www/edison-tech/bootstrap/cache

# Generate app key (if needed)
php artisan key:generate

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
sudo systemctl restart redis-server

# Start queue workers
php artisan queue:work --daemon &

# Verify
curl http://localhost/health
```

## Automated Backup Scripts

### Master Backup Script

```bash
#!/bin/bash
# /usr/local/bin/backup-all.sh
# Master backup script - runs all backups

set -e

LOG_FILE="/var/log/backups/backup-$(date +%Y%m%d).log"
mkdir -p $(dirname $LOG_FILE)

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $LOG_FILE
}

log "Starting backup process..."

# 1. Database backup
log "Backing up database..."
/usr/local/bin/backup-database.sh >> $LOG_FILE 2>&1

# 2. Application files backup
log "Backing up application files..."
/usr/local/bin/backup-app-files.sh >> $LOG_FILE 2>&1

# 3. Storage files backup
log "Backing up storage files..."
/usr/local/bin/backup-storage.sh >> $LOG_FILE 2>&1

# 4. Verify backups
log "Verifying backups..."
/usr/local/bin/verify-backups.sh >> $LOG_FILE 2>&1

log "Backup process completed successfully!"

# Send notification
mail -s "Backup Completed - $(date +%Y-%m-%d)" \
  admin@example.com < $LOG_FILE
```

### Crontab Configuration

```bash
# /etc/cron.d/edison-tech-backups

# Full backup daily at 2 AM
0 2 * * * root /usr/local/bin/backup-all.sh

# Incremental database backup every 6 hours
0 */6 * * * root /usr/local/bin/backup-database-incremental.sh

# Storage files backup every 12 hours
0 */12 * * * root /usr/local/bin/backup-storage.sh

# Verify backups daily at 4 AM
0 4 * * * root /usr/local/bin/verify-backups.sh

# Clean old backups weekly on Sunday at 1 AM
0 1 * * 0 root /usr/local/bin/cleanup-old-backups.sh
```

### Backup Verification Script

```bash
#!/bin/bash
# /usr/local/bin/verify-backups.sh
# Verify backup integrity

BACKUP_DIR="/var/backups"
ERROR=0

# Check latest database backup
LATEST_DB=$(ls -t $BACKUP_DIR/mysql/*.sql.gz | head -1)
if [ -f "$LATEST_DB" ]; then
    echo "Testing database backup: $LATEST_DB"
    gunzip -t "$LATEST_DB" 2>&1
    if [ $? -ne 0 ]; then
        echo "ERROR: Database backup is corrupted!"
        ERROR=1
    else
        echo "Database backup OK"
    fi
else
    echo "ERROR: No database backup found!"
    ERROR=1
fi

# Check latest app backup
LATEST_APP=$(ls -t $BACKUP_DIR/app/*.tar.gz | head -1)
if [ -f "$LATEST_APP" ]; then
    echo "Testing app backup: $LATEST_APP"
    tar -tzf "$LATEST_APP" > /dev/null 2>&1
    if [ $? -ne 0 ]; then
        echo "ERROR: App backup is corrupted!"
        ERROR=1
    else
        echo "App backup OK"
    fi
else
    echo "ERROR: No app backup found!"
    ERROR=1
fi

if [ $ERROR -eq 0 ]; then
    echo "All backups verified successfully!"
    exit 0
else
    echo "Backup verification failed!"
    # Send alert
    mail -s "ALERT: Backup Verification Failed" \
      admin@example.com <<< "Backup verification failed. Check logs immediately."
    exit 1
fi
```

## Backup Testing

### Monthly Recovery Test

Perform full recovery test monthly:

1. **Clone production database to test server**
2. **Restore from latest backup**
3. **Verify data integrity**
4. **Test application functionality**
5. **Document recovery time**
6. **Update procedures as needed**

### Test Recovery Checklist

- [ ] Latest backups available
- [ ] Test server provisioned
- [ ] Database restored successfully
- [ ] Application files restored
- [ ] Storage files restored
- [ ] Application functional
- [ ] Data integrity verified
- [ ] Recovery time documented
- [ ] Issues documented
- [ ] Procedures updated

## Emergency Contacts

- **DevOps Lead:** +1-XXX-XXX-XXXX
- **Database Admin:** +1-XXX-XXX-XXXX
- **Backup Service:** support@backupservice.com
- **AWS Support:** aws-support@amazon.com

## Important Notes

1. **Always test backups** - Untested backups are useless
2. **Automate everything** - Manual processes fail
3. **Monitor backup jobs** - Alert on failures
4. **Offsite storage** - Protect against site-wide disasters
5. **Encrypt backups** - Protect sensitive data
6. **Document procedures** - Keep this document current
7. **Practice recovery** - Regular drills are essential

---

**Last Updated:** 2025-01-15
**Next Review:** 2025-02-15
**Document Owner:** DevOps Team
