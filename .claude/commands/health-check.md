# Claude Command: /health-check
## Description
Perform comprehensive health checks on all applications across all environments (local, staging, production) and report status.

## Usage
```
/health-check [environment] [--detailed] [--alert]
```

## Examples
```
/health-check staging
/health-check production --detailed
/health-check --alert
/health-check
```

## Parameters
- **environment** (optional, default: all): Target environment (local | staging | production | all)
- **--detailed**: Show detailed health metrics for each component
- **--alert**: Send alert notifications if unhealthy services detected

## Trigger
This command triggers the **DevOps Specialist** subagent to:

1. **Identify all services**
   - Application containers
   - Database servers
   - Cache (Redis) servers
   - Message queues (if applicable)
   - Load balancers (production)

2. **Perform health checks per service**

   **Application Endpoints:**
   ```
   GET /health
   GET /api/health
   GET /health/ping
   ```
   Expected: HTTP 200 with status JSON

   **Database:**
   ```
   SELECT 1;  -- MySQL connectivity
   ```
   Expected: Response within 500ms

   **Cache (Redis):**
   ```
   PING
   ```
   Expected: PONG response

   **Docker Containers:**
   ```
   docker ps --filter "status=running"
   ```
   Expected: All critical containers running

3. **Gather metrics**
   - Response times
   - Memory usage
   - CPU usage
   - Disk usage
   - Connection count
   - Active requests

4. **Compile comprehensive report**
   ```
   📊 HEALTH CHECK REPORT
   ════════════════════════════════════════════════════════
   Environment: production | Status: ✅ HEALTHY (15/15 services)
   Time: 2024-01-22 10:15:30 UTC | Duration: 2.3s
   
   ✅ APPLICATION SERVICES (7/7 healthy)
      ├─ e-kredit-pranata-ti/backend
      │  ├─ Status: ✅ RUNNING
      │  ├─ Response time: 45ms
      │  ├─ Memory: 245MB / 512MB (48%)
      │  ├─ CPU: 2.3%
      │  └─ Active requests: 12
      │
      ├─ exam-scheduler-app/backend
      │  ├─ Status: ✅ RUNNING
      │  ├─ Response time: 32ms
      │  ├─ Memory: 185MB / 512MB (36%)
      │  ├─ CPU: 1.1%
      │  └─ Active requests: 8
      │
      ├─ simlab/backend
      │  ├─ Status: ✅ RUNNING
      │  ├─ Response time: 28ms
      │  ├─ Memory: 312MB / 512MB (61%)
      │  ├─ CPU: 0.8%
      │  └─ Active requests: 5
      │
      └─ [... 4 more applications ...]
   
   ✅ DATABASES (3/3 healthy)
      ├─ MySQL (production)
      │  ├─ Status: ✅ CONNECTED
      │  ├─ Connections: 24 / 100
      │  ├─ Uptime: 45d 23h
      │  ├─ Replication lag: 0ms
      │  └─ Disk usage: 18GB / 100GB (18%)
      │
      ├─ MySQL (staging)
      │  ├─ Status: ✅ CONNECTED
      │  ├─ Connections: 8 / 50
      │  └─ Disk usage: 2.3GB / 50GB (5%)
      │
      └─ MySQL (local)
         ├─ Status: ✅ CONNECTED
         ├─ Connections: 3 / 50
         └─ Disk usage: 1.2GB / 100GB (1%)
   
   ✅ CACHE SERVICES (3/3 healthy)
      ├─ Redis (production)
      │  ├─ Status: ✅ RESPONDING
      │  ├─ Memory: 125MB / 256MB (49%)
      │  ├─ Keys: 2,345
      │  ├─ Evictions: 0
      │  └─ Response time: 2ms
      │
      ├─ Redis (staging)
      │  ├─ Status: ✅ RESPONDING
      │  └─ Memory: 45MB / 256MB (18%)
      │
      └─ Redis (local)
         ├─ Status: ✅ RESPONDING
         └─ Memory: 15MB / 256MB (6%)
   
   ⚠️  WARNINGS (2)
      ├─ MySQL (production) memory pressure
      │  └─ Available: 156MB / 512MB (30.5%)
      │     Recommendation: Monitor and consider upgrading
      │
      └─ e-kredit-paranata-ti/backend CPU spike
         └─ Current: 12.5% (was 2.3%)
            Recommendation: Investigate active requests
   
   ❌ ERRORS (0) - None
   
   ════════════════════════════════════════════════════════
   Overall Status: ✅ HEALTHY
   Timestamp: 2024-01-22T10:15:30Z
   Next check: 2024-01-22T10:30:30Z (15 minutes)
   ```

5. **Alert if unhealthy** (if --alert flag)
   - Send notifications to DevOps team
   - Create GitHub Issue for critical issues
   - Suggest remediation actions

6. **Generate historical report**
   - Track health metrics over time
   - Identify trends (e.g., growing memory usage)
   - Generate trend charts

## Health Check Details (with --detailed)

```
🔍 DETAILED HEALTH ANALYSIS

Application: e-kredit-pranata-ti/backend
────────────────────────────────────────
HTTP Health Check:
  GET /health
  Response: HTTP 200 OK
  Body: {"status": "ok", "timestamp": "2024-01-22T10:15:30Z"}
  Time: 45ms

Database Connectivity:
  Query: SELECT 1;
  Result: ✅ Connected
  Latency: 8ms
  Replication status: OK (lag: 0ms)

Cache Connectivity:
  Command: PING
  Result: ✅ PONG
  Latency: 2ms

Dependencies:
  ├─ MySQL: ✅ Reachable (10.0.0.5:3306)
  ├─ Redis: ✅ Reachable (10.0.0.6:6379)
  └─ WhatsApp API: ✅ Reachable

Memory Analysis:
  Physical: 245MB / 512MB (48%)
  Swap: 0MB / 512MB
  Trend: +5MB (over last hour)

CPU Analysis:
  Current: 2.3%
  Average (1h): 1.8%
  Peak (1h): 8.5%
  Trend: Stable

Network:
  Inbound: 2.3 Mbps
  Outbound: 1.2 Mbps
  Connections: 12 established, 0 time_wait
```

## Success Criteria
✅ All applications responding to health checks
✅ All databases connected and healthy
✅ All cache services (Redis) responding
✅ Response times < 500ms
✅ No critical resource constraints

## Failure Handling
If services are unhealthy:

1. **Critical services down** (e.g., production database)
   - Alert DevOps Specialist immediately
   - Create high-priority GitHub Issue
   - Attempt automatic restart
   - Escalate if restart fails

2. **Degraded performance** (e.g., slow response times)
   - Log detailed metrics
   - Create low-priority GitHub Issue
   - Monitor for escalation

3. **Resource warnings** (e.g., disk usage > 80%)
   - Alert team
   - Suggest cleanup or upgrade actions

## Auto-Scheduling
By default, health checks run:
- **Every 5 minutes** (production)
- **Every 15 minutes** (staging)
- **On-demand** (local)

Configure in `.claude/hooks/health-check-schedule.yaml`

## Integration
Health check results feed into:
- Monitoring dashboard (if configured)
- Alerting system (PagerDuty, OpsGenie)
- GitHub status page
- Team Slack channel (if configured)
