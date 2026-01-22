#!/bin/bash

################################################################################
# FTI Multi-App Health Check Script
# Monitors health of all applications and infrastructure
################################################################################

set -euo pipefail

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
LOG_DIR="$PROJECT_ROOT/logs"
HEALTH_REPORT="$LOG_DIR/health-report-$(date +%Y%m%d-%H%M%S).log"

# Create logs directory
mkdir -p "$LOG_DIR"

# Health check configuration
TIMEOUT=10
RETRIES=3
SERVICES=(
    "mysql:mysql-local:3306"
    "redis:redis-local:6379"
    "nginx:nginx-local:80"
)

# Application endpoints
declare -A APP_ENDPOINTS=(
    ["e-kredit-backend"]="http://localhost:8001/health"
    ["simlab-backend"]="http://localhost:8002/health"
    ["exam-scheduler-backend"]="http://localhost:8003/health"
)

# ============================================================================
# UTILITY FUNCTIONS
# ============================================================================

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$HEALTH_REPORT"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$HEALTH_REPORT"
}

log_error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$HEALTH_REPORT"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$HEALTH_REPORT"
}

# ============================================================================
# HEALTH CHECK FUNCTIONS
# ============================================================================

check_docker_daemon() {
    log "Checking Docker daemon..."
    
    if docker ps &> /dev/null; then
        log_success "Docker daemon is running"
        return 0
    else
        log_error "Docker daemon is not running"
        return 1
    fi
}

check_container_running() {
    local container=$1
    
    if docker ps --filter "name=$container" --format "{{.Names}}" | grep -q "$container"; then
        log_success "Container running: $container"
        return 0
    else
        log_error "Container not running: $container"
        return 1
    fi
}

check_all_containers() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "CONTAINER STATUS"
    log "═══════════════════════════════════════════════════════════════"
    
    local all_healthy=true
    
    # Check critical services
    for service_info in "${SERVICES[@]}"; do
        IFS=':' read -r name container port <<< "$service_info"
        
        if check_container_running "$container"; then
            # Check if port is responding
            if nc -z -w 2 localhost "$port" 2>/dev/null; then
                log_success "$name ($container:$port) is responding"
            else
                log_warning "$name ($container:$port) is running but not responding on port"
                all_healthy=false
            fi
        else
            log_error "$name ($container) is NOT running"
            all_healthy=false
        fi
    done
    
    return $([ "$all_healthy" = true ] && echo 0 || echo 1)
}

check_database_connectivity() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "DATABASE CONNECTIVITY"
    log "═══════════════════════════════════════════════════════════════"
    
    local all_healthy=true
    
    # Check MySQL
    if docker exec mysql-local mysql -uroot -proot -e "SELECT 1;" &> /dev/null; then
        log_success "MySQL is accessible"
    else
        log_error "MySQL is NOT accessible"
        all_healthy=false
    fi
    
    # Check Redis
    if docker exec redis-local redis-cli ping | grep -q "PONG"; then
        log_success "Redis is accessible"
    else
        log_error "Redis is NOT accessible"
        all_healthy=false
    fi
    
    return $([ "$all_healthy" = true ] && echo 0 || echo 1)
}

check_application_health() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "APPLICATION HEALTH"
    log "═══════════════════════════════════════════════════════════════"
    
    local all_healthy=true
    
    for app in "${!APP_ENDPOINTS[@]}"; do
        local endpoint="${APP_ENDPOINTS[$app]}"
        local retry=0
        local success=false
        
        while [[ $retry -lt $RETRIES ]]; do
            if curl -s -f --connect-timeout $TIMEOUT "$endpoint" > /dev/null 2>&1; then
                log_success "$app is responding ($endpoint)"
                success=true
                break
            fi
            retry=$((retry + 1))
            [[ $retry -lt $RETRIES ]] && sleep 2
        done
        
        if [[ $success == false ]]; then
            log_error "$app is NOT responding ($endpoint)"
            all_healthy=false
        fi
    done
    
    return $([ "$all_healthy" = true ] && echo 0 || echo 1)
}

check_system_resources() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "SYSTEM RESOURCES"
    log "═══════════════════════════════════════════════════════════════"
    
    # Disk usage
    local disk_usage=$(df -h "$PROJECT_ROOT" | awk 'NR==2 {print $5}' | sed 's/%//')
    if [[ $disk_usage -lt 80 ]]; then
        log_success "Disk usage: ${disk_usage}%"
    elif [[ $disk_usage -lt 90 ]]; then
        log_warning "Disk usage: ${disk_usage}% (above 80%)"
    else
        log_error "Disk usage: ${disk_usage}% (CRITICAL - above 90%)"
    fi
    
    # Memory usage
    local memory_usage=$(free | awk 'NR==2 {printf("%.0f", $3/$2 * 100)}')
    if [[ $memory_usage -lt 80 ]]; then
        log_success "Memory usage: ${memory_usage}%"
    elif [[ $memory_usage -lt 90 ]]; then
        log_warning "Memory usage: ${memory_usage}% (above 80%)"
    else
        log_error "Memory usage: ${memory_usage}% (CRITICAL - above 90%)"
    fi
    
    # CPU load
    local cpu_load=$(uptime | awk -F'average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
    log "CPU load average (1m): $cpu_load"
}

check_docker_volumes() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "DOCKER VOLUMES"
    log "═══════════════════════════════════════════════════════════════"
    
    docker volume ls --filter "label=com.docker.compose.project=myproject" --format "table {{.Name}}\t{{.Size}}" | tee -a "$HEALTH_REPORT" || log "No volumes found"
}

check_docker_logs() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "RECENT CONTAINER LOGS (Errors Only)"
    log "═══════════════════════════════════════════════════════════════"
    
    for service_info in "${SERVICES[@]}"; do
        IFS=':' read -r name container port <<< "$service_info"
        
        local errors=$(docker logs --tail 10 "$container" 2>&1 | grep -i "error" || echo "No errors")
        if [[ "$errors" != "No errors" ]]; then
            log_warning "Errors in $container:"
            echo "$errors" | tee -a "$HEALTH_REPORT"
        fi
    done
}

# ============================================================================
# REPORTING
# ============================================================================

generate_health_report() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "HEALTH CHECK REPORT"
    log "═══════════════════════════════════════════════════════════════"
    log "Timestamp: $(date -u)"
    log "Project: FTI Multi-App Ecosystem"
    log "Report file: $HEALTH_REPORT"
}

send_alert_if_unhealthy() {
    local overall_status=$1
    
    if [[ $overall_status -eq 1 ]]; then
        log_error "Health check detected issues!"
        
        # Could integrate with alerting system here
        # Example: send to Slack, create GitHub issue, etc.
        
        # Create GitHub issue (requires gh CLI)
        if command -v gh &> /dev/null; then
            gh issue create \
                --title "🚨 Health Check Failed - Investigation Required" \
                --body "Health check detected issues at $(date -u). Check logs at $HEALTH_REPORT" \
                --label "health-check,incident" || true
        fi
    fi
}

# ============================================================================
# MAIN EXECUTION
# ============================================================================

main() {
    log ""
    log "═══════════════════════════════════════════════════════════════"
    log "  FTI Multi-App Health Check"
    log "═══════════════════════════════════════════════════════════════"
    
    local all_checks_passed=true
    
    # Run checks
    check_docker_daemon || all_checks_passed=false
    check_all_containers || all_checks_passed=false
    check_database_connectivity || all_checks_passed=false
    check_application_health || all_checks_passed=false
    check_system_resources || true  # Resource checks are informational
    check_docker_volumes || true
    check_docker_logs || true
    
    # Generate report
    generate_health_report
    
    log ""
    if [[ $all_checks_passed == true ]]; then
        log_success "All health checks passed! ✨"
        exit 0
    else
        log_error "Some health checks failed. Please review the report above."
        send_alert_if_unhealthy 1
        exit 1
    fi
}

main "$@"
