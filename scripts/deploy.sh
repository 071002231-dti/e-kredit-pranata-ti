#!/bin/bash

################################################################################
# FTI Multi-App Deployment Script
# Orchestrates deployment of individual applications to specified environment
################################################################################

set -euo pipefail

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
REGISTRY="ghcr.io"
IMAGE_REGISTRY_OWNER="071002231-dti"
LOG_DIR="$PROJECT_ROOT/logs"
DEPLOYMENT_LOG="$LOG_DIR/deployment-$(date +%Y%m%d-%H%M%S).log"

# Create logs directory
mkdir -p "$LOG_DIR"

# ============================================================================
# UTILITY FUNCTIONS
# ============================================================================

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$DEPLOYMENT_LOG"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$DEPLOYMENT_LOG"
}

log_error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$DEPLOYMENT_LOG"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$DEPLOYMENT_LOG"
}

print_usage() {
    cat << EOF
Usage: $0 <app_name> [environment] [version]

Description:
  Deploy an FTI application to a specified environment

Arguments:
  app_name      - Application name (e.g., e-kredit-pranata-ti, exam-scheduler-app, simlab)
  environment   - Target environment (staging | production, default: staging)
  version       - Docker image version/tag (default: latest)

Examples:
  $0 e-kredit-pranata-ti staging
  $0 exam-scheduler-app production v1.8.1
  $0 simlab staging latest

Available Applications:
  - e-kredit-pranata-ti       (Production critical - E-Kredit system)
  - exam-scheduler-app        (Production critical - Exam scheduling)
  - simlab                    (SimLab - Lab testing system)
  - waform                    (Form builder with WhatsApp)
  - whatsform                 (Form system with WhatsApp)
  - fticms                    (Digital signage CMS)
  - aset-fti                  (Asset management)

EOF
    exit 1
}

# ============================================================================
# VALIDATION FUNCTIONS
# ============================================================================

validate_app_name() {
    local app=$1
    local valid_apps=("e-kredit-pranata-ti" "exam-scheduler-app" "simlab" "waform" "whatsform" "fticms" "aset-fti")
    
    for valid_app in "${valid_apps[@]}"; do
        if [[ "$app" == "$valid_app" ]]; then
            return 0
        fi
    done
    
    return 1
}

validate_environment() {
    local env=$1
    if [[ "$env" != "staging" && "$env" != "production" ]]; then
        log_error "Invalid environment: $env. Must be 'staging' or 'production'"
        return 1
    fi
    return 0
}

# ============================================================================
# PRE-DEPLOYMENT CHECKS
# ============================================================================

check_deployment_readiness() {
    local app=$1
    local env=$2
    local version=$3
    
    log "Running pre-deployment checks..."
    
    # Check if Docker is available
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed or not in PATH"
        return 1
    fi
    
    # Check if Docker daemon is running
    if ! docker ps &> /dev/null; then
        log_error "Docker daemon is not running"
        return 1
    fi
    
    log_success "Docker is available and running"
    
    # Check image availability in registry
    local image_name="${REGISTRY}/${IMAGE_REGISTRY_OWNER}/${app}"
    log "Checking if image exists: $image_name:$version"
    
    if docker pull "$image_name:$version" &> /dev/null; then
        log_success "Docker image is available in registry"
    else
        log_warning "Could not pull image from registry - will build locally"
    fi
    
    # Check environment configuration
    local env_file="${PROJECT_ROOT}/.env.${env}"
    if [[ ! -f "$env_file" ]]; then
        log_warning "Environment file not found: $env_file"
        log "Using default .env"
    else
        log_success "Environment configuration found: $env_file"
    fi
    
    return 0
}

# ============================================================================
# DEPLOYMENT PLAN
# ============================================================================

create_deployment_plan() {
    local app=$1
    local env=$2
    local version=$3
    
    log "Creating deployment plan..."
    cat << EOF | tee -a "$DEPLOYMENT_LOG"

═══════════════════════════════════════════════════════════════════════
                    DEPLOYMENT PLAN
═══════════════════════════════════════════════════════════════════════
Application:      $app
Environment:      $env
Image Version:    $version
Timestamp:        $(date -u)
Registry:         $REGISTRY
Image:            ${REGISTRY}/${IMAGE_REGISTRY_OWNER}/${app}:${version}
Target URL:       https://${app}.${env}.local

═══════════════════════════════════════════════════════════════════════
                    PRE-DEPLOYMENT STEPS
═══════════════════════════════════════════════════════════════════════
1. Backup current configuration
2. Pull latest Docker image from registry
3. Stop current application container
4. Start new application container
5. Run database migrations (if applicable)
6. Run health checks
7. Verify application is responsive
8. Update load balancer / DNS (if applicable)

═══════════════════════════════════════════════════════════════════════
EOF
}

# ============================================================================
# APPROVAL STEP
# ============================================================================

request_approval() {
    local env=$1
    
    if [[ "$env" == "production" ]]; then
        log_warning "Deploying to PRODUCTION - requires approval"
        read -p "Are you sure you want to deploy to production? (yes/no): " -r approval
        
        if [[ ! "$approval" =~ ^[Yy][Ee][Ss]$ ]]; then
            log_error "Deployment cancelled by user"
            return 1
        fi
        
        log_success "Deployment approved for production"
    else
        log "Deployment to staging - automatic approval"
    fi
    
    return 0
}

# ============================================================================
# DEPLOYMENT EXECUTION
# ============================================================================

backup_current_state() {
    local app=$1
    local env=$2
    
    log "Backing up current state..."
    
    # Get current image ID
    local current_image_id=$(docker ps -a -f "name=${app}" --format "{{.Image}}" 2>/dev/null || echo "")
    
    if [[ -n "$current_image_id" ]]; then
        log "Current image: $current_image_id"
        # Save backup info to file for potential rollback
        echo "APP: $app" > "$LOG_DIR/backup-${app}.txt"
        echo "ENVIRONMENT: $env" >> "$LOG_DIR/backup-${app}.txt"
        echo "PREVIOUS_IMAGE: $current_image_id" >> "$LOG_DIR/backup-${app}.txt"
        echo "BACKUP_TIMESTAMP: $(date -u)" >> "$LOG_DIR/backup-${app}.txt"
        log_success "Backup information saved to $LOG_DIR/backup-${app}.txt"
    fi
}

deploy_application() {
    local app=$1
    local env=$2
    local version=$3
    
    log "Starting deployment of $app to $env environment..."
    
    # Pull latest image
    log "Pulling Docker image: ${REGISTRY}/${IMAGE_REGISTRY_OWNER}/${app}:${version}"
    docker pull "${REGISTRY}/${IMAGE_REGISTRY_OWNER}/${app}:${version}" || {
        log_error "Failed to pull Docker image"
        return 1
    }
    
    log_success "Docker image pulled successfully"
    
    # Stop current container gracefully
    log "Stopping current container (graceful shutdown - 30s timeout)..."
    docker stop "${app}-${env}" 2>/dev/null || log "No container to stop"
    
    # Remove old container
    docker rm "${app}-${env}" 2>/dev/null || log "No old container to remove"
    
    # Start new container
    log "Starting new container with image: ${REGISTRY}/${IMAGE_REGISTRY_OWNER}/${app}:${version}"
    docker run -d \
        --name "${app}-${env}" \
        --network fti-network \
        --env-file "${PROJECT_ROOT}/.env.${env}" \
        --restart unless-stopped \
        -p "8001:8000" \
        "${REGISTRY}/${IMAGE_REGISTRY_OWNER}/${app}:${version}" || {
        log_error "Failed to start new container"
        return 1
    }
    
    log_success "New container started successfully"
    
    return 0
}

# ============================================================================
# POST-DEPLOYMENT VERIFICATION
# ============================================================================

verify_deployment() {
    local app=$1
    local max_retries=10
    local retry_count=0
    
    log "Verifying deployment (checking health endpoint)..."
    
    while [[ $retry_count -lt $max_retries ]]; do
        if curl -s -f "http://localhost:8001/health" > /dev/null 2>&1; then
            log_success "Application is responding to health checks"
            return 0
        fi
        
        retry_count=$((retry_count + 1))
        log "Health check attempt $retry_count/$max_retries..."
        sleep 3
    done
    
    log_error "Application health check failed after $max_retries attempts"
    return 1
}

run_post_deployment_tests() {
    local app=$1
    
    log "Running post-deployment verification tests..."
    
    # Check container logs for errors
    local errors=$(docker logs "${app}-staging" 2>&1 | grep -i "error" | head -5 || echo "")
    
    if [[ -n "$errors" ]]; then
        log_warning "Potential errors found in container logs:"
        echo "$errors" | tee -a "$DEPLOYMENT_LOG"
    else
        log_success "No errors detected in container logs"
    fi
    
    # Get container stats
    local stats=$(docker stats "${app}-staging" --no-stream --format "{{.CPUPerc}} | {{.MemUsage}}" 2>/dev/null || echo "N/A")
    log "Container resource usage: $stats"
}

# ============================================================================
# ROLLBACK
# ============================================================================

rollback_deployment() {
    local app=$1
    local env=$2
    
    log_error "Deployment failed - initiating rollback..."
    
    # Read backup info
    if [[ -f "$LOG_DIR/backup-${app}.txt" ]]; then
        log "Restoring from backup: $LOG_DIR/backup-${app}.txt"
        local previous_image=$(grep "PREVIOUS_IMAGE:" "$LOG_DIR/backup-${app}.txt" | cut -d' ' -f2-)
        
        if [[ -n "$previous_image" ]]; then
            log "Stopping failed container..."
            docker stop "${app}-${env}" 2>/dev/null || true
            docker rm "${app}-${env}" 2>/dev/null || true
            
            log "Starting previous version: $previous_image"
            docker run -d \
                --name "${app}-${env}" \
                --network fti-network \
                --env-file "${PROJECT_ROOT}/.env.${env}" \
                --restart unless-stopped \
                "$previous_image"
            
            log_success "Rollback completed - previous version restored"
            return 0
        fi
    fi
    
    log_error "Could not find backup information for rollback"
    return 1
}

# ============================================================================
# NOTIFICATION
# ============================================================================

send_notification() {
    local app=$1
    local env=$2
    local status=$3
    local message=$4
    
    log "Sending notification..."
    
    if [[ "$status" == "success" ]]; then
        echo "✅ Deployment successful: $app to $env"
    else
        echo "❌ Deployment failed: $app to $env - $message"
    fi
    
    # Could integrate with Slack, GitHub, etc. here
    # Example: curl -X POST $SLACK_WEBHOOK -d "text=..."
}

# ============================================================================
# MAIN EXECUTION
# ============================================================================

main() {
    if [[ $# -lt 1 ]]; then
        print_usage
    fi
    
    local app=$1
    local env=${2:-staging}
    local version=${3:-latest}
    
    log "═══════════════════════════════════════════════════════════════════"
    log "  FTI Multi-App Deployment Script"
    log "═══════════════════════════════════════════════════════════════════"
    
    # Validate inputs
    if ! validate_app_name "$app"; then
        log_error "Unknown application: $app"
        print_usage
    fi
    
    if ! validate_environment "$env"; then
        print_usage
    fi
    
    # Pre-deployment checks
    if ! check_deployment_readiness "$app" "$env" "$version"; then
        log_error "Pre-deployment checks failed"
        exit 1
    fi
    
    # Create deployment plan
    create_deployment_plan "$app" "$env" "$version"
    
    # Request approval for production
    if ! request_approval "$env"; then
        exit 1
    fi
    
    # Backup current state
    backup_current_state "$app" "$env"
    
    # Execute deployment
    if deploy_application "$app" "$env" "$version"; then
        log_success "Application deployed successfully"
        
        # Verify deployment
        if verify_deployment "$app"; then
            run_post_deployment_tests "$app"
            log_success "Deployment verification passed"
            send_notification "$app" "$env" "success"
        else
            log_error "Deployment verification failed"
            rollback_deployment "$app" "$env"
            send_notification "$app" "$env" "failed" "Health check failed"
            exit 1
        fi
    else
        log_error "Deployment failed"
        rollback_deployment "$app" "$env"
        send_notification "$app" "$env" "failed" "Deployment execution failed"
        exit 1
    fi
    
    log "═══════════════════════════════════════════════════════════════════"
    log_success "Deployment completed successfully!"
    log "Deployment log: $DEPLOYMENT_LOG"
    log "═══════════════════════════════════════════════════════════════════"
}

main "$@"
