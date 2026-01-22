# Claude Command: /deploy
## Description
Deploy a specific application to a target environment (staging or production).

## Usage
```
/deploy <app_name> [environment] [version]
```

## Examples
```
/deploy e-kredit-pranata-ti staging
/deploy exam-scheduler-app production v1.8.1
/deploy simlab staging latest
```

## Parameters
- **app_name** (required): Application name (e.g., e-kredit-pranata-ti, exam-scheduler-app, simlab)
- **environment** (optional, default: staging): Target environment (staging | production)
- **version** (optional, default: latest): Docker image version/tag to deploy

## Trigger
This command triggers the **DevOps Specialist** subagent to:

1. **Validate deployment readiness**
   - Verify application exists
   - Check if image is built and available in registry
   - Validate environment configuration

2. **Pre-deployment checks**
   - Run health checks on target environment
   - Verify database connectivity
   - Check available disk space

3. **Create deployment plan**
   ```
   Apps to deploy:    [list]
   Target environment: [staging|production]
   Image version:      [version]
   Estimated downtime: [X minutes]
   ```

4. **Get human approval** (for production)
   - If environment is production, wait for approval
   - Link to GitHub PR/deployment ticket

5. **Execute deployment**
   - Pull latest Docker image from registry
   - Stop old containers gracefully
   - Start new containers
   - Wait for health checks to pass
   - Update load balancer if applicable

6. **Post-deployment verification**
   - Run smoke tests
   - Check application logs for errors
   - Verify database migrations (if applicable)
   - Send notification to team

7. **Rollback plan** (if deployment fails)
   - Automatically stop new containers
   - Restart old containers
   - Verify rollback successful
   - Alert team to investigate

## Approval Requirements
- **Staging**: Automatic (no approval needed)
- **Production**: Requires 1 approval from DevOps Specialist

## Success Criteria
✅ Application is running and responding to health checks
✅ All dependent services (DB, Redis) are accessible
✅ No errors in application logs
✅ Database migrations completed successfully

## Failure Handling
If deployment fails:
1. Automatically roll back to previous version
2. Create GitHub Issue with error details
3. Notify DevOps Specialist
4. Pause further deployment attempts (requires investigation)

## Output
```
✅ Pre-deployment checks passed
   - Application: e-kredit-pranata-ti
   - Environment: staging
   - Version: v2.5.2
   - Estimated duration: 5 minutes

🔄 Deploying...
   [Progress bar: ████████░░] 80%

✅ Deployment successful!
   - New version deployed: v2.5.2
   - Health checks passed
   - Database migrations completed
   - All services responsive

📊 Deployment metrics:
   - Deployment time: 4m 23s
   - Downtime: 0.3s (graceful restart)
   - Container restarts: 1
   - Log errors: 0
```
