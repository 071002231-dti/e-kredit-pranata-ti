# FTI Multi-App Ecosystem - Architecture Guide

**Last Updated:** January 22, 2026  
**Version:** 1.0 - Foundation Phase  
**Status:** 🔧 Implementation in Progress

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Applications Inventory](#applications-inventory)
4. [Subagent Roles & Responsibilities](#subagent-roles--responsibilities)
5. [Development Workflow](#development-workflow)
6. [Deployment Architecture](#deployment-architecture)
7. [Technology Stack](#technology-stack)
8. [Infrastructure Setup](#infrastructure-setup)
9. [CI/CD Pipeline](#cicd-pipeline)
10. [Troubleshooting Guide](#troubleshooting-guide)

---

## Project Overview

The **FTI Multi-App Ecosystem** is a coordinated collection of 16 independent applications serving the Faculty of Information Technology's various operational needs. Instead of managing each application in isolation, this architecture provides:

- **Centralized coordination** via Claude Code agents
- **Shared infrastructure** (MySQL, Redis, Nginx)
- **Automated CI/CD** with GitHub Actions
- **Clear separation of concerns** with 6 specialized subagents
- **Independent deployments** for flexibility and risk reduction
- **Scalable development** for team collaboration

### Key Principles

✅ **Modular**: Each app is independently deployable  
✅ **Observable**: Health checks and monitoring across all services  
✅ **Scalable**: Shared infrastructure supports growth  
✅ **Automated**: CI/CD eliminates manual errors  
✅ **Documented**: Architecture decisions recorded (ADRs)

---

## System Architecture

### High-Level Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                     Claude Code Orchestration Layer                      │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐  ┌─────────────┐  │
│  │ DevOps      │  │ Laravel      │  │ Code         │  │ React       │  │
│  │ Specialist  │  │ Expert       │  │ Reviewer     │  │ Frontend    │  │
│  └─────────────┘  └──────────────┘  └──────────────┘  └─────────────┘  │
│  ┌─────────────┐  ┌──────────────┐                                      │
│  │ Database    │  │ Documentation│                                      │
│  │ Admin       │  │ Writer       │                                      │
│  └─────────────┘  └──────────────┘                                      │
└─────────────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                    GitHub Actions CI/CD Layer                            │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐  ┌─────────────┐  │
│  │ test-all    │  │ build-images │  │ health-check │  │ code-quality│  │
│  └─────────────┘  └──────────────┘  └──────────────┘  └─────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                   Docker Compose Infrastructure                          │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │ Shared Services Network (fti-network)                            │   │
│  │ ├─ MySQL 8              (Database)                              │   │
│  │ ├─ Redis 7              (Cache & Queue)                         │   │
│  │ ├─ PostgreSQL 15        (Alternative DB)                        │   │
│  │ └─ Nginx                (Reverse Proxy)                         │   │
│  └──────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────┬──────────┬──────────┬──────────┬──────────┬──────────┬──────┐
│ Academic │ Finance  │  Forms   │   CMS    │ Utility  │ Archived │Local │
├──────────┼──────────┼──────────┼──────────┼──────────┼──────────┼──────┤
│ Laravel  │ E-Kredit │ WAForm   │ FTICMS   │ SimLab   │ BackupVPS│ Dev  │
│ Exam     │ Credit   │ WhatsForm│ Aset-FTI │ Exam     │ Legacy   │ Env  │
│ Scheduler│ Scorer   │          │          │ Scheduler│          │      │
│ SimLab   │ Homebase │          │          │          │          │      │
│ Labdoc   │          │          │          │          │          │      │
└──────────┴──────────┴──────────┴──────────┴──────────┴──────────┴──────┘
```

### Data Flow

```
Developer
   ↓
git push (main/develop branch)
   ↓
GitHub Actions Triggered (test-all.yml)
   ├─ Run all tests (Laravel, Node, React)
   ├─ Run code quality checks
   ├─ Generate coverage reports
   └─ Comment results on PR
   ↓
If tests pass:
   ├─ Trigger build-images.yml
   ├─ Build Docker images per app
   ├─ Push to ghcr.io registry
   └─ Deploy to staging environment
   ↓
Manual Approval (for production)
   ├─ DevOps Specialist reviews
   ├─ Health checks pass
   └─ Deployment script executes
   ↓
Deployment to Production
   ├─ Graceful container restart
   ├─ Database migrations (if applicable)
   ├─ Health verification
   └─ Notification sent
   ↓
Monitoring
   ├─ Continuous health checks (every 5min)
   ├─ Alert if issues detected
   └─ Automatic logging
```

---

## Applications Inventory

### Academic Group (4 apps)

| App | Tech | Purpose | DB | Status |
|-----|------|---------|----|----|
| **laravel** | Laravel 12 | Base template | MySQL | Template |
| **exam-scheduler-app** | Node + React | Exam scheduling & room allocation | MySQL | Production |
| **simlab** | Laravel + React | Lab simulation & testing | MySQL | Production |
| **labdocutrack** | Node.js | Document tracking system | MySQL | Development |

### Finance Group (3 apps)

| App | Tech | Purpose | DB | Status |
|-----|------|---------|----|----|
| **e-kredit-pranata-ti** | Laravel + React | IT Functionary credit system | MySQL | Production 🔴 CRITICAL |
| **CreditScorer** | Express.js | Credit scoring engine | MySQL | Development |
| **homebase-project** | Express.js | Base template | MySQL | Template |

### Forms Group (2 apps)

| App | Tech | Purpose | DB | Status |
|-----|------|---------|----|----|
| **waform** | Laravel 11 | Form builder (WhatsApp ready) | MySQL | Development |
| **whatsform** | Laravel 12 | Form system (WhatsApp integration) | MySQL | Development |

### CMS & Assets Group (2 apps)

| App | Tech | Purpose | DB | Status |
|-----|------|---------|----|----|
| **fticms** | React + Vite | Digital signage CMS | None (SPA) | Development |
| **aset-fti** | Next.js 15 | Asset management | PostgreSQL | Development |

### Other Apps

- **BackupVPS** - Legacy backup storage
- **credit_point** - Legacy credit point system
- **sinau** - Learning/test environment

---

## Subagent Roles & Responsibilities

### 1. 🚀 DevOps Specialist
**Priority: #1** | **Scope:** Infrastructure, CI/CD, Deployment

**Responsibilities:**
- Maintain `.github/workflows/` (test-all.yml, build-images.yml, health-check.yml)
- Orchestrate Docker Compose for local & staging environments
- Deploy applications to production (with approval)
- Monitor application health and uptime
- Implement backup and disaster recovery

**Tools:** Docker, Docker Compose, Bash, Git, ssh  
**Permissions:** Can deploy to staging (auto), production (requires approval)  
**Key Files:**
- `.github/workflows/*` - CI/CD pipelines
- `docker-compose.yml` - Infrastructure definition
- `scripts/deploy.sh` - Deployment orchestration
- `scripts/health-check.sh` - Service monitoring

**Claude Commands:** `/deploy`, `/health-check`, `/test-all`

---

### 2. 🔧 Laravel Expert
**Priority: #2** | **Scope:** Backend architecture, Services, DTOs, Migrations

**Responsibilities:**
- Design and implement Services layer across all Laravel apps
- Create and review database migrations
- Build API endpoints following REST principles
- Implement Repository/Service/DTO patterns
- Manage background jobs and queuing
- Event-driven architecture design

**Tools:** Composer, Artisan, PHP, MySQL  
**Permissions:** Can modify app/, routes/, database/, services/  
**Scope Apps:**
- e-kredit-pranata-ti/backend
- simlab/lab-pengujian-laravel
- exam-scheduler-app/backend
- waform, whatsform

**Key Files:**
- `app/Services/` - Business logic
- `app/Repositories/` - Data access
- `database/migrations/` - Schema changes
- `routes/api.php` - API definitions

---

### 3. 👁️ Code Reviewer
**Priority: #3** | **Scope:** Quality, Standards, Security

**Responsibilities:**
- Enforce Laravel Pint (PHP) and ESLint (JS) standards
- Run PHPStan static analysis
- Perform security vulnerability scanning
- Review code for architecture compliance
- Enforce test coverage thresholds (70% minimum)
- Approve/reject PRs based on quality

**Tools:** PHPStan, Pint, ESLint, Prettier, PHPUnit, Jest  
**Permissions:** Can request changes, approve PRs, run tests  
**Scope:** All applications

**Claude Commands:** `/code-review`

---

### 4. ⚛️ React Frontend Specialist
**Priority: #4** | **Scope:** UI, Components, Frontend architecture

**Responsibilities:**
- Build and maintain shared UI component library
- Implement Tailwind CSS design system
- Develop React hooks and utilities
- Optimize bundle size and performance
- Implement accessibility (WCAG) standards
- Code splitting and lazy loading strategies

**Tools:** npm, Vite, TypeScript, React, Tailwind CSS  
**Permissions:** Can modify src/, components/, shared/ui-components/  
**Scope Apps:**
- e-kredit-pranata-ti/web-client
- exam-scheduler-app/frontend
- simlab/lab-pengujian-react
- fticms, aset-fti

---

### 5. 💾 Database Administrator
**Priority: #5** | **Scope:** Schemas, Migrations, Optimization, Backups

**Responsibilities:**
- Design and review database schemas
- Create and validate database migrations
- Query optimization and performance tuning
- Implement backup and recovery procedures
- Monitor database replication and health
- Plan and execute version upgrades

**Tools:** MySQL, PostgreSQL, Redis, Artisan migrate  
**Permissions:** Can create migrations, backup, optimize (requires approval for production)  
**Key Files:**
- `database/migrations/` - Schema versioning
- `database/seeders/` - Development data
- `database/factories/` - Test data

---

### 6. 📚 Documentation Writer
**Priority: #6** | **Scope:** Architecture, API Docs, Guides, Runbooks

**Responsibilities:**
- Create and maintain ARCHITECTURE.md
- Document API contracts (OpenAPI/Swagger)
- Write deployment and setup guides
- Create runbooks for common operations
- Record Architecture Decision Records (ADRs)
- Maintain troubleshooting guides

**Tools:** Markdown, YAML, OpenAPI  
**Permissions:** Can modify docs/, *.md files  
**Key Files:**
- `ARCHITECTURE.md` (this file)
- `docs/` - Detailed documentation
- `.claude/workflows/` - Agent workflow definitions

---

## Development Workflow

### Local Development Setup

#### Prerequisites
```bash
# Required
- Docker & Docker Compose
- Git
- PHP 8.3+ (for local Laravel development)
- Node.js 20+ (for frontend development)
```

#### Initial Setup

```bash
# 1. Clone repository
git clone <repo-url>
cd /Users/4h3/myproject

# 2. Copy environment files
cp .env.example .env
cp .env.example .env.local
cp .env.example .env.docker

# 3. Start infrastructure (MySQL, Redis, Nginx)
docker-compose up -d

# 4. Install dependencies per app
cd e-kredit-pranata-ti/backend && composer install && cd ../..
cd e-kredit-pranata-ti/web-client && npm install && cd ../..

# 5. Run migrations
docker-compose exec e-kredit-backend php artisan migrate

# 6. Start development servers
docker-compose up -d e-kredit-backend e-kredit-frontend
```

#### Development Cycle

```
1. Create feature branch
   git checkout -b feature/payment-refund

2. Make code changes
   - Follow Laravel best practices (Services, DTOs, Enums)
   - Add/update tests alongside code
   - Follow code style (Pint, ESLint)

3. Test locally
   docker-compose exec e-kredit-backend php artisan test
   docker-compose exec e-kredit-frontend npm run test

4. Commit and push
   git add .
   git commit -m "feat: implement payment refund feature"
   git push origin feature/payment-refund

5. Create Pull Request
   - GitHub Actions automatically runs tests
   - Code Reviewer checks for quality
   - DevOps Specialist verifies CI/CD readiness

6. Get approval and merge
   - Green CI/CD checks required
   - 1 approval from Code Reviewer required
   - Automatic deployment to staging

7. Validate in staging
   - Smoke tests pass
   - Database migrations successful
   - No errors in logs

8. Deploy to production
   - DevOps Specialist triggers /deploy command
   - Manual approval required
   - Automatic rollback on failure
```

---

## Deployment Architecture

### Environments

#### 1. Local Development (Docker Compose)
```yaml
Purpose: Developer iteration
Refresh: On-demand
Services: All (optional profiles)
Database: MySQL + Redis
Access: http://localhost:8001, etc.
```

#### 2. Staging (Cloud Server)
```yaml
Purpose: Integration testing, QA validation
Refresh: On every merge to main branch
Services: All production services
Database: MySQL (staging replica)
Access: https://staging.fti.ac.id
Monitoring: Enabled
Backup: Daily
```

#### 3. Production (Live VPS)
```yaml
Purpose: Live user-facing services
Refresh: Manual deployment only
Services: Production applications only
Database: MySQL (replicated, backed up)
Access: https://app.fti.ac.id
Monitoring: Continuous (every 5 minutes)
Backup: Hourly snapshots
Disaster Recovery: Automated rollback on failure
```

### Deployment Process

```
Step 1: Pre-Deployment Checks (Automated)
├─ Application exists and is valid
├─ Docker image is available in registry
├─ All tests passed on CI
├─ Database migrations are compatible
├─ Health check endpoints are responsive
└─ Resource constraints are met

Step 2: Create Deployment Plan (Visible)
├─ Display target app and version
├─ Show estimated downtime
├─ List database changes (if any)
├─ Request backup confirmation
└─ List rollback plan

Step 3: Request Approval (For Production)
├─ Wait for DevOps Specialist approval
├─ Require 1 explicit approval
├─ Set timeout for approval (24 hours)
└─ Create GitHub issue if timeout expires

Step 4: Execute Deployment
├─ Backup current state (Docker image, version info)
├─ Pull new Docker image from registry
├─ Stop old container (graceful, 30s timeout)
├─ Start new container with new image
├─ Wait for health checks (up to 2 minutes)
├─ Run post-deployment tests
└─ Log deployment results

Step 5: Verification
├─ Application responding to /health endpoint
├─ Database migrations completed
├─ No error patterns in logs
├─ Response times within acceptable range
└─ All dependencies accessible

Step 6: Handle Failure
├─ Automatically stop failed container
├─ Restore previous Docker image
├─ Restart previous version
├─ Run verification tests
├─ Alert DevOps team
└─ Create incident GitHub issue
```

### Rollback Strategy

**Automatic Rollback Triggers:**
- Health check fails (3 consecutive failures)
- Database migration fails
- Container crashes within 5 minutes
- High error rate detected (>10% errors)

**Manual Rollback:**
```bash
scripts/rollback.sh e-kredit-pranata-ti production
```

**Rollback Procedure:**
1. Stop current (failed) container
2. Restore previous Docker image
3. Restart container with previous version
4. Run health verification
5. Alert team and create incident ticket

---

## Technology Stack

### Backend

| Tech | Version | Apps | Purpose |
|------|---------|------|---------|
| **Laravel** | 12 | e-kredit, simlab, waform, whatsform | Web framework |
| **Laravel Sanctum** | ^2.11 | All Laravel apps | API authentication |
| **Eloquent ORM** | - | All Laravel apps | Database abstraction |
| **Express.js** | 4.x | exam-scheduler, CreditScorer | Node web framework |
| **Drizzle ORM** | - | aset-fti | Type-safe DB queries |

### Frontend

| Tech | Version | Apps | Purpose |
|------|---------|------|---------|
| **React** | 19/18 | e-kredit-web, exam-scheduler, simlab, fticms | UI library |
| **Next.js** | 15 | aset-fti | React framework |
| **Vite** | 6+ | All modern apps | Build tool |
| **Tailwind CSS** | 4 | All modern apps | Utility CSS |
| **Radix UI** | Latest | e-kredit, homebase | Headless components |
| **Headless UI** | Latest | exam-scheduler | Component library |

### Database

| Tech | Version | Purpose |
|------|---------|---------|
| **MySQL** | 8 | Primary datastore |
| **PostgreSQL** | 15 | Alternative (aset-fti) |
| **Redis** | 7 | Cache & queuing |

### DevOps & CI/CD

| Tech | Purpose |
|------|---------|
| **Docker** | Containerization |
| **Docker Compose** | Local orchestration |
| **GitHub Actions** | CI/CD automation |
| **GHCR** | Container registry |

### Testing

| Tech | Purpose |
|------|---------|
| **PHPUnit** | PHP unit testing |
| **Pest** | Laravel testing (alternative) |
| **Jest** | JavaScript testing |
| **Vitest** | Vue/React testing (alternative) |

---

## Infrastructure Setup

### Docker Compose Layers

#### Layer 1: Shared Infrastructure
```yaml
Services:
  - mysql:8           # Primary database
  - redis:7           # Cache & queue
  - postgres:15       # Alternative DB (optional)
  - nginx:alpine      # Reverse proxy
```

#### Layer 2: Core Applications
```yaml
Profiles:
  - default           # e-kredit (always running)
  - simlab            # Optional - SimLab services
  - exam-scheduler    # Optional - Exam scheduler services
  - cms               # Optional - CMS services
```

#### Layer 3: Development Tools (Optional)
```yaml
Profiles:
  - tools             # phpmyadmin, redis-commander, mailhog
```

### Network Architecture

```
fti-network (Docker bridge network)
├─ MySQL              10.0.0.2:3306
├─ Redis              10.0.0.3:6379
├─ PostgreSQL         10.0.0.4:5432
├─ Nginx              10.0.0.5:80,443
├─ e-kredit-backend   10.0.0.10:8000
├─ e-kredit-frontend  10.0.0.11:5173
├─ simlab-backend     10.0.0.20:8000
├─ simlab-frontend    10.0.0.21:5173
└─ ... other services
```

---

## CI/CD Pipeline

### GitHub Actions Workflows

#### 1. test-all.yml (On every push)
```yaml
Trigger: push to main/develop, PR creation, manual
Jobs:
  - detect-changes      # Identify modified apps
  - test-laravel-*      # Unit tests for Laravel apps
  - test-node-*         # Unit tests for Node apps
  - test-react-*        # Unit tests for React apps
  - code-quality        # Static analysis, linting
  - test-summary        # Aggregate results
  
Coverage Threshold: 70%
Fail On: Low coverage, test failures, lint errors
Time: ~5 minutes
```

#### 2. build-images.yml (When code changes)
```yaml
Trigger: push to main/develop (code changes)
Jobs:
  - detect-changes      # Identify modified apps
  - build-<app>-*       # Build Docker images
  - push-registry       # Push to ghcr.io
  
Registry: ghcr.io/071002231-dti
Tags: branch-name, SHA, latest (for main)
Time: ~15 minutes
```

#### 3. health-check.yml (Periodic monitoring)
```yaml
Trigger: Every 5 minutes (prod), every hour (staging), manual
Jobs:
  - health-check-containers
  - health-check-database
  - health-check-registry
  - health-check-workflows
  - health-check-security
  - generate-report
  
Alerts: GitHub Issues created if failures detected
Time: ~2 minutes
```

---

## Troubleshooting Guide

### Common Issues & Solutions

#### Issue: Docker container won't start
```bash
# Check logs
docker logs <container-name>

# Verify image exists
docker images | grep <app-name>

# Verify network
docker network inspect fti-network

# Restart service
docker-compose restart <service-name>
```

#### Issue: Database connection refused
```bash
# Verify MySQL is running
docker ps | grep mysql

# Check MySQL logs
docker logs mysql-local

# Verify environment variables
env | grep DB_

# Test connection
docker exec mysql-local mysql -u${DB_USER} -p${DB_PASSWORD} -e "SELECT 1;"
```

#### Issue: Tests failing with "database does not exist"
```bash
# Create test database
docker-compose exec mysql-local mysql -uroot -proot -e "CREATE DATABASE test;"

# Run migrations for test
docker-compose exec e-kredit-backend php artisan migrate --env=testing

# Re-run tests
docker-compose exec e-kredit-backend php artisan test
```

#### Issue: Port already in use
```bash
# Find process using port
lsof -i :8001

# Kill process
kill -9 <PID>

# Or change port in docker-compose.yml
```

#### Issue: Redis connection timeout
```bash
# Verify Redis is running
docker ps | grep redis

# Check Redis logs
docker logs redis-local

# Test connection
docker exec redis-local redis-cli PING

# Verify Redis password
redis-cli -a ${REDIS_PASSWORD} PING
```

---

## Quick Reference

### Useful Commands

```bash
# Start entire stack
docker-compose up -d

# Start specific application
docker-compose up -d e-kredit-backend

# View logs
docker-compose logs -f e-kredit-backend

# Run migrations
docker-compose exec e-kredit-backend php artisan migrate

# Run tests
docker-compose exec e-kredit-backend php artisan test

# Deploy application
./scripts/deploy.sh e-kredit-pranata-ti staging

# Check health
./scripts/health-check.sh

# Rollback deployment
./scripts/rollback.sh e-kredit-pranata-ti staging

# Stop all services
docker-compose down
```

### Important Files

- **`.claude/agents.yaml`** - Subagent definitions
- **`.claude/settings.json`** - Global configuration
- **`.github/workflows/`** - CI/CD pipelines
- **`docker-compose.yml`** - Infrastructure definition
- **`scripts/deploy.sh`** - Deployment orchestration
- **`scripts/health-check.sh`** - Service monitoring
- **`ARCHITECTURE.md`** - This file

---

## Next Steps (Week 2-4)

- [ ] Implement Laravel best practices (Services, DTOs, Enums) for all apps
- [ ] Create shared UI component library in `shared/ui-components/`
- [ ] Add proper health check endpoints to all applications
- [ ] Create detailed API documentation (OpenAPI/Swagger)
- [ ] Setup monitoring and alerting (Prometheus, Grafana)
- [ ] Implement proper logging strategy (ELK stack or similar)
- [ ] Create disaster recovery runbooks
- [ ] Setup Slack/Discord notifications for deployments
- [ ] Document all Architecture Decision Records (ADRs)

---

## References

- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Best Practices](https://laravel.com/docs/)
- [React Best Practices](https://react.dev/)

---

**For questions or updates to this architecture, contact the DevOps Specialist or Documentation Writer subagents.**
