#!/bin/bash

# Backup script for Web Apperlass
# Usage: ./scripts/backup.sh

set -e

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/webapperlass"
S3_BUCKET="webapperlass-backups"
RETENTION_DAYS=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Load environment variables
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi

log "🔄 Starting backup process..."

# Database backup
log "Creating database backup..."
docker-compose exec -T mysql mysqldump \
    --single-transaction \
    --routines \
    --triggers \
    -u root -p$DB_ROOT_PASSWORD \
    $DB_DATABASE | gzip > "$BACKUP_DIR/database_$TIMESTAMP.sql.gz"

# Files backup
log "Creating files backup..."
tar -czf "$BACKUP_DIR/storage_$TIMESTAMP.tar.gz" \
    storage/app/public/ \
    storage/logs/

# Application backup (code)
log "Creating application backup..."
tar -czf "$BACKUP_DIR/application_$TIMESTAMP.tar.gz" \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs' \
    --exclude='storage/framework/cache' \
    --exclude='storage/framework/sessions' \
    --exclude='storage/framework/views' \
    .

# Upload to S3 (if configured)
if [ ! -z "$AWS_ACCESS_KEY_ID" ] && [ ! -z "$AWS_SECRET_ACCESS_KEY" ]; then
    log "Uploading backups to S3..."
    
    aws s3 cp "$BACKUP_DIR/database_$TIMESTAMP.sql.gz" \
        "s3://$S3_BUCKET/database/" \
        --storage-class STANDARD_IA
    
    aws s3 cp "$BACKUP_DIR/storage_$TIMESTAMP.tar.gz" \
        "s3://$S3_BUCKET/storage/" \
        --storage-class STANDARD_IA
    
    aws s3 cp "$BACKUP_DIR/application_$TIMESTAMP.tar.gz" \
        "s3://$S3_BUCKET/application/" \
        --storage-class STANDARD_IA
    
    log "✅ Backups uploaded to S3"
else
    log "⚠️  S3 credentials not configured, skipping upload"
fi

# Cleanup old local backups
log "Cleaning up old local backups..."
find $BACKUP_DIR -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete

# Cleanup old S3 backups (if configured)
if [ ! -z "$AWS_ACCESS_KEY_ID" ] && [ ! -z "$AWS_SECRET_ACCESS_KEY" ]; then
    CUTOFF_DATE=$(date -d "$RETENTION_DAYS days ago" +%Y-%m-%d)
    
    aws s3api list-objects-v2 \
        --bucket $S3_BUCKET \
        --query "Contents[?LastModified<'$CUTOFF_DATE'].Key" \
        --output text | \
    xargs -r -n1 aws s3api delete-object --bucket $S3_BUCKET --key
fi

# Generate backup report
BACKUP_SIZE=$(du -sh $BACKUP_DIR | cut -f1)
log "📊 Backup completed successfully!"
log "📁 Backup location: $BACKUP_DIR"
log "📏 Total backup size: $BACKUP_SIZE"
log "🗓️  Retention period: $RETENTION_DAYS days"

# Send notification (optional)
if [ ! -z "$SLACK_WEBHOOK_URL" ]; then
    curl -X POST -H 'Content-type: application/json' \
        --data "{\"text\":\"✅ Web Apperlass backup completed successfully\nSize: $BACKUP_SIZE\nTimestamp: $TIMESTAMP\"}" \
        $SLACK_WEBHOOK_URL
fi