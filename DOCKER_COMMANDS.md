# 🐳 Docker Quick Reference Commands

**Project**: e-Kredit Pranata TI
**Last Updated**: 2025-11-06

---

## 🚀 Essential Commands

### Start Containers
```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
./vendor/bin/sail up -d
```
**Output**: Containers start in background (detached mode)

### Stop Containers
```bash
./vendor/bin/sail down
```
**Output**: Stops and removes containers (data persists in volumes)

### Restart Containers
```bash
./vendor/bin/sail restart
```

### Check Container Status
```bash
docker ps
# or
./vendor/bin/sail ps
```

### View Container Logs
```bash
# All containers
./vendor/bin/sail logs

# Follow logs (real-time)
./vendor/bin/sail logs -f

# Specific service
./vendor/bin/sail logs laravel.test
./vendor/bin/sail logs mysql
```

---

## 🔧 Laravel Artisan Commands

### Access Container Shell
```bash
./vendor/bin/sail shell
```
**Use case**: Run commands inside container

### Run Artisan Commands (from host)
```bash
./vendor/bin/sail artisan [command]

# Examples:
./vendor/bin/sail artisan --version
./vendor/bin/sail artisan route:list
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan optimize
```

### Create Files
```bash
# Controller
./vendor/bin/sail artisan make:controller API/ActivityController --api

# Model
./vendor/bin/sail artisan make:model Activity -m

# Migration
./vendor/bin/sail artisan make:migration create_activities_table

# Seeder
./vendor/bin/sail artisan make:seeder ActivitySeeder

# Request
./vendor/bin/sail artisan make:request StoreActivityRequest
```

---

## 🗄️ Database Commands

### Access MySQL CLI
```bash
./vendor/bin/sail mysql

# Or with credentials
./vendor/bin/sail mysql -u sail -ppassword ekredit
```

### Run Migrations
```bash
# Run all pending migrations
./vendor/bin/sail artisan migrate

# Rollback last migration
./vendor/bin/sail artisan migrate:rollback

# Fresh migration (drop all tables & re-run)
./vendor/bin/sail artisan migrate:fresh

# Fresh with seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Check migration status
./vendor/bin/sail artisan migrate:status
```

### Run Seeders
```bash
# Run all seeders
./vendor/bin/sail artisan db:seed

# Run specific seeder
./vendor/bin/sail artisan db:seed --class=UserSeeder
```

### Database Backup & Restore
```bash
# Backup
docker exec backend-mysql-1 mysqldump -u sail -ppassword ekredit > backup.sql

# Restore
docker exec -i backend-mysql-1 mysql -u sail -ppassword ekredit < backup.sql
```

---

## 📦 Composer Commands

```bash
# Install dependencies
./vendor/bin/sail composer install

# Update dependencies
./vendor/bin/sail composer update

# Add package
./vendor/bin/sail composer require vendor/package

# Remove package
./vendor/bin/sail composer remove vendor/package

# Dump autoload
./vendor/bin/sail composer dump-autoload
```

---

## 🎨 NPM Commands (for Vite/Assets)

```bash
# Install dependencies
./vendor/bin/sail npm install

# Run dev server
./vendor/bin/sail npm run dev

# Build for production
./vendor/bin/sail npm run build

# Add package
./vendor/bin/sail npm install package-name
```

---

## 🔍 Debugging & Troubleshooting

### Check Container Logs
```bash
# Laravel app logs
./vendor/bin/sail logs laravel.test

# MySQL logs
./vendor/bin/sail logs mysql

# Follow logs in real-time
./vendor/bin/sail logs -f
```

### Restart Specific Service
```bash
docker restart backend-laravel.test-1
docker restart backend-mysql-1
```

### Rebuild Containers
```bash
# Stop containers
./vendor/bin/sail down

# Rebuild and start
./vendor/bin/sail up --build -d
```

### Clear All Laravel Caches
```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan optimize:clear
```

### Fix Permissions
```bash
# Inside container
./vendor/bin/sail shell
chmod -R 775 storage bootstrap/cache
chown -R sail:sail storage bootstrap/cache
exit
```

---

## 🧹 Cleanup Commands

### Remove Stopped Containers
```bash
docker container prune
```

### Remove Unused Images
```bash
docker image prune -a
```

### Remove Unused Volumes (⚠️ Will delete database data!)
```bash
# List volumes
docker volume ls

# Remove specific volume
docker volume rm backend_sail-mysql

# Remove all unused volumes
docker volume prune
```

### Nuclear Option - Remove Everything
```bash
./vendor/bin/sail down -v
docker system prune -a --volumes
```
**⚠️ WARNING**: This will delete ALL data including database!

---

## 📊 Monitoring & Information

### Container Resource Usage
```bash
docker stats
```

### Inspect Container
```bash
docker inspect backend-laravel.test-1
docker inspect backend-mysql-1
```

### Check Network
```bash
docker network ls
docker network inspect backend_sail
```

### Check Volumes
```bash
docker volume ls
docker volume inspect backend_sail-mysql
```

---

## 🔗 Port Mappings

| Service | Container Port | Host Port | URL |
|---------|---------------|-----------|-----|
| Laravel | 80 | 80 | http://localhost |
| Vite Dev | 5173 | 5173 | http://localhost:5173 |
| MySQL | 3306 | 3306 | localhost:3306 |

---

## 🚨 Common Issues & Solutions

### Issue: Port 80 already in use
```bash
# Find what's using port 80
sudo lsof -i :80

# Kill the process
sudo kill -9 [PID]

# Or use different port (edit docker-compose.yml)
# Change: '${APP_PORT:-80}:80' to '${APP_PORT:-8000}:80'
```

### Issue: MySQL container keeps restarting
```bash
# Check logs
docker logs backend-mysql-1

# Usually fixed by:
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### Issue: Permission denied errors
```bash
# Fix ownership
./vendor/bin/sail shell
chown -R sail:sail /var/www/html
exit
```

### Issue: Cannot connect to database
```bash
# Verify MySQL is running
docker ps | grep mysql

# Check database exists
./vendor/bin/sail mysql -e "SHOW DATABASES;"

# Restart containers
./vendor/bin/sail restart
```

---

## 💡 Pro Tips

### Create Bash Alias
Add to your `~/.zshrc` or `~/.bashrc`:
```bash
alias sail='./vendor/bin/sail'
```

Then you can use:
```bash
sail up -d
sail artisan migrate
sail composer install
```

### Run Multiple Commands
```bash
./vendor/bin/sail artisan migrate && \
./vendor/bin/sail artisan db:seed && \
./vendor/bin/sail artisan optimize
```

### Background Process Check
```bash
# Check if containers are running
if docker ps | grep -q "backend-laravel"; then
    echo "✅ Containers are running"
else
    echo "❌ Containers are not running"
    cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
    ./vendor/bin/sail up -d
fi
```

---

## 📋 Daily Workflow

### Morning (Start Work)
```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
./vendor/bin/sail up -d
docker ps  # Verify containers running
```

### During Development
```bash
# Run migrations after creating new ones
./vendor/bin/sail artisan migrate

# Clear cache after config changes
./vendor/bin/sail artisan config:clear

# Check logs for errors
./vendor/bin/sail logs -f
```

### Evening (End Work)
```bash
# Stop containers to free resources
./vendor/bin/sail down

# Or keep them running if continuing tomorrow
# (just close laptop, Docker will auto-resume)
```

---

**Quick Access**: Keep this file open in your editor for fast command reference! 🚀
