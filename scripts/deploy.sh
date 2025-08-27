#!/bin/bash

# Deployment script for Web Apperlass
# Usage: ./scripts/deploy.sh [environment]

set -e

ENVIRONMENT=${1:-production}
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/webapperlass"

echo "🚀 Starting deployment to $ENVIRONMENT environment..."

# Create backup directory
mkdir -p $BACKUP_DIR

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Function to create database backup
backup_database() {
    log "Creating database backup..."
    docker-compose exec -T mysql mysqldump \
        -u root -p$DB_ROOT_PASSWORD \
        $DB_DATABASE > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
    log "Database backup created: $BACKUP_DIR/db_backup_$TIMESTAMP.sql"
}

# Function to backup uploaded files
backup_files() {
    log "Creating files backup..."
    tar -czf "$BACKUP_DIR/files_backup_$TIMESTAMP.tar.gz" \
        storage/app/public/
    log "Files backup created: $BACKUP_DIR/files_backup_$TIMESTAMP.tar.gz"
}

# Function to deploy application
deploy_application() {
    log "Pulling latest Docker images..."
    docker-compose pull

    log "Putting application in maintenance mode..."
    docker-compose exec -T app php artisan down || true

    log "Starting updated containers..."
    docker-compose up -d

    log "Running database migrations..."
    docker-compose exec -T app php artisan migrate --force

    log "Clearing and caching configuration..."
    docker-compose exec -T app php artisan config:clear
    docker-compose exec -T app php artisan config:cache
    docker-compose exec -T app php artisan route:cache
    docker-compose exec -T app php artisan view:cache

    log "Restarting queue workers..."
    docker-compose exec -T app php artisan queue:restart

    log "Taking application out of maintenance mode..."
    docker-compose exec -T app php artisan up
}

# Function to verify deployment
verify_deployment() {
    log "Verifying deployment..."
    
    # Wait for application to be ready
    sleep 30
    
    # Health check
    if curl -f http://localhost/health > /dev/null 2>&1; then
        log "✅ Health check passed"
    else
        log "❌ Health check failed"
        exit 1
    fi
    
    # Database connectivity check
    if docker-compose exec -T app php artisan migrate:status > /dev/null; then
        log "✅ Database connection successful"
    else
        log "❌ Database connection failed"
        exit 1
    fi
}

# Function to cleanup old backups
cleanup_old_backups() {
    log "Cleaning up old backups (keeping last 7 days)..."
    find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
    find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
}

# Main deployment flow
main() {
    case $ENVIRONMENT in
        production)
            log "🔥 Production deployment"
            backup_database
            backup_files
            deploy_application
            verify_deployment
            cleanup_old_backups
            ;;
        staging)
            log "🧪 Staging deployment"
            deploy_application
            verify_deployment
            ;;
        *)
            log "❌ Unknown environment: $ENVIRONMENT"
            log "Available environments: production, staging"
            exit 1
            ;;
    esac
    
    log "🎉 Deployment completed successfully!"
}

# Load environment variables
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi

# Run main deployment
main