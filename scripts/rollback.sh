#!/bin/bash

################################################################################
# FTI Multi-App Rollback Script
# Safely rollback deployments to previous versions
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
ROLLBACK_LOG="$LOG_DIR/rollback-$(date +%Y%m%d-%H%M%S).log"

mkdir -p "$LOG_DIR"

# ============================================================================
# UTILITY FUNCTIONS
# ============================================================================

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$ROLLBACK_LOG"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$ROLLBACK_LOG"
}

log_error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$ROLLBACK_LOG"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$ROLLBACK_LOG"
}

print_usage() {
    cat << EOF
Usage: $0 <app_name> [environment]

Description:
  Rollback an application deployment to the previous version

Arguments:
  app_name      - Application name (e.g., e-kredit-pranata-ti, exam-scheduler-app)
  environment   - Target environment (staging | production, default: staging)

Examples:
  $0 e-kredit-pranata-ti staging
  $0 exam-scheduler-app production

EOF
    exit 1
}

# ============================================================================
# ROLLBACK FUNCTIONS
# ============================================================================

perform_rollback() {
    local app=$1
    local env=$2
    
    log "═══════════════════════════════════════════════════════════════"
    log "  INITIATING ROLLBACK"
    log "═══════════════════════════════════════════════════════════════"
    
    log_warning "Rolling back $app in $env environment"
    
    # Read backup file
    local backup_file="$LOG_DIR/backup-${app}.txt"
    if [[ ! -f "$backup_file" ]]; then
        log_error "Backup file not found: $backup_file"
        log_error "Cannot determine previous version for rollback"
        return 1
    fi
    
    local previous_image=$(grep "PREVIOUS_IMAGE:" "$backup_file" | cut -d' ' -f2-)
    log "Previous image: $previous_image"
    
    # Request confirmation
    read -p "Are you sure you want to rollback to $previous_image? (yes/no): " -r confirmation
    if [[ ! "$confirmation" =~ ^[Yy][Ee][Ss]$ ]]; then
        log_error "Rollback cancelled by user"
        return 1
    fi
    
    # Stop current container
    log "Stopping current container..."
    if docker stop "${app}-${env}" 2>/dev/null; then
        log_success "Container stopped"
    else
        log_warning "Container was not running"
    fi
    
    # Remove current container
    log "Removing current container..."
    docker rm "${app}-${env}" 2>/dev/null || log_warning "No container to remove"
    
    # Start previous version
    log "Starting previous version: $previous_image"
    docker run -d \
        --name "${app}-${env}" \
        --network fti-network \
        --env-file "${PROJECT_ROOT}/.env.${env}" \
        --restart unless-stopped \
        "$previous_image"
    
    log_success "Previous version started"
    
    # Verify rollback
    log "Verifying rollback..."
    sleep 3
    
    if curl -s -f "http://localhost:8001/health" > /dev/null 2>&1; then
        log_success "Application is responding to health checks after rollback"
        return 0
    else
        log_error "Application health check failed after rollback"
        return 1
    fi
}

# ============================================================================
# MAIN
# ============================================================================

main() {
    if [[ $# -lt 1 ]]; then
        print_usage
    fi
    
    local app=$1
    local env=${2:-staging}
    
    log "═══════════════════════════════════════════════════════════════"
    log "  FTI Multi-App Rollback Script"
    log "═══════════════════════════════════════════════════════════════"
    log "Rollback log: $ROLLBACK_LOG"
    
    if perform_rollback "$app" "$env"; then
        log_success "Rollback completed successfully!"
    else
        log_error "Rollback failed!"
        exit 1
    fi
}

main "$@"
