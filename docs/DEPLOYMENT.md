# Deployment Guide - Web Apperlass

## Overview
Panduan deployment untuk sistem manajemen pendidikan Web Apperlass menggunakan Docker dan CI/CD pipeline.

## Prerequisites
- Docker & Docker Compose
- Git
- Make (optional, untuk automation)

## Environment Setup

### 1. Production Environment
```bash
# Clone repository
git clone https://github.com/your-org/webapperlass.git
cd webapperlass

# Setup environment
cp .env.production .env
# Edit .env dengan konfigurasi production yang sesuai

# Generate application key
docker-compose exec app php artisan key:generate
```

### 2. Environment Variables
Konfigurasi environment variables penting:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATED_KEY_HERE

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=webapperlass_prod
DB_USERNAME=webapperlass_user
DB_PASSWORD=SECURE_PASSWORD

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_PASSWORD=SECURE_REDIS_PASSWORD

# File Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=YOUR_AWS_KEY
AWS_SECRET_ACCESS_KEY=YOUR_AWS_SECRET
AWS_BUCKET=webapperlass-storage-prod
```

## Deployment Methods

### 1. Manual Deployment
```bash
# Build dan start containers
make build
make up

# Run migrations
make migrate

# Build caches
make cache-build

# Verify deployment
make health
```

### 2. Automated Deployment via CI/CD
CI/CD pipeline akan otomatis triggered pada:
- Push ke branch `main` → Production deployment
- Push ke branch `develop` → Staging deployment

### 3. Script-based Deployment
```bash
# Deploy to staging
./scripts/deploy.sh staging

# Deploy to production (with backup)
./scripts/deploy.sh production
```

## Database Management

### Migration Strategy
```bash
# Backup database sebelum migration
make backup

# Run migrations
make migrate

# Jika ada masalah, restore dari backup
docker-compose exec mysql mysql -u root -p < backup.sql
```

### Seeding Data
```bash
# Seed initial data
make db-seed

# Fresh install dengan seeding
make migrate-fresh
```

## File Storage Strategy

### Local Storage (Development)
- Files stored di `storage/app/public/`
- Symlink ke `public/storage/`

### Cloud Storage (Production)
- AWS S3 untuk file storage
- CDN untuk asset delivery
- Automatic backup ke S3

## Backup & Recovery

### Automated Backup
```bash
# Manual backup
make backup

# Scheduled backup (via cron)
0 2 * * * /path/to/webapperlass/scripts/backup.sh
```

### Recovery Process
```bash
# Restore database
docker-compose exec mysql mysql -u root -p webapperlass < backup.sql

# Restore files
tar -xzf storage_backup.tar.gz -C storage/
```

## Monitoring & Health Checks

### Health Endpoint
- URL: `https://yourdomain.com/health`
- Returns JSON dengan status aplikasi
- Checks: database, cache, storage, disk space, memory

### Log Monitoring
```bash
# Real-time logs
make monitor

# Specific service logs
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis
```

## Security Considerations

### 1. Environment Security
- Gunakan strong passwords
- Rotate keys secara berkala
- Simpan secrets di environment variables

### 2. Container Security
- Non-root user dalam container
- Minimal base image (Alpine)
- Regular security updates

### 3. Network Security
- Firewall configuration
- SSL/TLS termination
- Rate limiting

## Performance Optimization

### 1. Caching Strategy
- Redis untuk session & cache
- OPcache untuk PHP
- CDN untuk assets

### 2. Database Optimization
- Connection pooling
- Query optimization
- Regular maintenance

### 3. Asset Optimization
- Vite untuk bundling
- Gzip compression
- Browser caching

## Troubleshooting

### Common Issues

#### 1. Container Won't Start
```bash
# Check logs
docker-compose logs app

# Rebuild container
make build
make up
```

#### 2. Database Connection Issues
```bash
# Check database status
docker-compose ps mysql

# Reset database
docker-compose down
docker volume rm webapperlass_mysql_data
make up
make migrate
```

#### 3. File Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R appuser:appuser storage/
docker-compose exec app chmod -R 775 storage/
```

### Emergency Procedures

#### 1. Rollback Deployment
```bash
# Rollback to previous image
docker-compose down
docker-compose pull webapperlass:previous-tag
docker-compose up -d
```

#### 2. Emergency Maintenance
```bash
# Put application in maintenance mode
docker-compose exec app php artisan down

# Take application out of maintenance
docker-compose exec app php artisan up
```

## Best Practices

1. **Always backup before deployment**
2. **Test on staging first**
3. **Monitor logs during deployment**
4. **Have rollback plan ready**
5. **Use blue-green deployment for zero-downtime**
6. **Implement proper monitoring and alerting**
7. **Regular security updates**
8. **Document all procedures**

## Support

Untuk bantuan deployment, hubungi tim DevOps atau buat issue di repository.