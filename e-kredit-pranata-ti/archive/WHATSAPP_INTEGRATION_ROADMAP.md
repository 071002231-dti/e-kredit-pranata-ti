# WhatsApp Integration Roadmap
## e-Kredit Pranata TI System

**Project Goal**: Integrate WhatsApp Cloud API + Flows to enable mobile-first activity submission while maintaining web-based admin/verification workflows.

---

## Table of Contents
1. [Current System Analysis](#current-system-analysis)
2. [Target Architecture](#target-architecture)
3. [Integration Phases](#integration-phases)
4. [Technical Requirements](#technical-requirements)
5. [Implementation Timeline](#implementation-timeline)
6. [Risk Assessment](#risk-assessment)

---

## Current System Analysis

### Existing Features ✅
- **Backend**: Laravel 12 REST API with Sanctum authentication
- **Frontend**: React 19.1.1 SPA for web dashboard
- **Database**: MySQL with complete schema for activities, users, credit schemas
- **Compliance Engine**: PR No. 3 Tahun 2025 validation (Unsur Utama ≥80%, Penunjang ≤20%)
- **User Management**: 5 jenjang jabatan levels (Pelaksana to Utama)
- **Credit Schemas**: 65 pre-defined activity types with detailed metadata
- **Approval Workflow**: Verifier and admin roles for activity approval/rejection

### Current Limitations ❌
- Web-only interface (not mobile-friendly for field workers)
- Requires desktop/laptop access for activity submission
- No push notifications for status updates
- Manual file uploads can be cumbersome on mobile browsers

### Current Tech Stack
```
Backend:
├── Laravel 12.x (PHP 8.3)
├── MySQL Database
├── Sanctum Authentication
├── File Storage (local/public disk)
└── RESTful API endpoints

Frontend:
├── React 19.1.1
├── TypeScript
├── React Router v6
├── Axios for API calls
└── No mobile app

Deployment:
├── Docker Compose (MySQL + PHP)
├── Port 80 (backend)
└── Port 3000 (frontend dev server)
```

---

## Target Architecture

### High-Level Overview
```
┌─────────────────────────────────────────────────────────────┐
│                     WhatsApp Cloud API                       │
│                  (Meta Business Platform)                    │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ Webhooks (HTTPS)
                         │
┌────────────────────────▼────────────────────────────────────┐
│                  Laravel Backend API                         │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  WhatsApp Integration Layer (NEW)                    │   │
│  │  ├── WebhookController (receive messages)           │   │
│  │  ├── WhatsAppService (send messages)                │   │
│  │  ├── FlowService (manage flows)                     │   │
│  │  └── NotificationService (status updates)           │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Existing API Layer (UNCHANGED)                      │   │
│  │  ├── ActivityController                              │   │
│  │  ├── ApprovalController                              │   │
│  │  ├── DashboardController                             │   │
│  │  └── CreditSchemaController                          │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────────────────┬────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         │                               │
┌────────▼────────┐            ┌────────▼────────┐
│  WhatsApp Users │            │  Web Dashboard  │
│  (Mobile Only)  │            │  (Admin/Verify) │
│                 │            │                 │
│ • Submit activity│            │ • Review queue  │
│ • Upload proof  │            │ • Approve/Reject│
│ • Check status  │            │ • View reports  │
│ • Get notifs    │            │ • Analytics     │
└─────────────────┘            └─────────────────┘
```

### User Journey Comparison

**Before (Web Only)**:
1. User logs in via browser
2. Navigates to "Submit Activity" page
3. Fills form, uploads file
4. Submits and waits
5. Manually checks back for approval status

**After (WhatsApp + Web)**:
1. User opens WhatsApp → business chat
2. Taps "Submit Activity" button
3. **WhatsApp Flow opens** (native form in WhatsApp)
4. Fills form, takes/uploads photo directly
5. Submits → **Instant confirmation**
6. **Auto notification** when approved/rejected
7. Admin reviews via web dashboard (unchanged)

---

## Integration Phases

### Phase 1: Infrastructure Setup (Week 1-2)
**Objective**: Prepare Meta Business account and Laravel backend for WhatsApp integration

#### Tasks:
1. **Meta Business Setup**
   - [ ] Create/verify Meta Business account
   - [ ] Set up WhatsApp Business API access
   - [ ] Generate permanent access token
   - [ ] Get Phone Number ID and WhatsApp Business Account ID
   - [ ] Configure webhook callback URL
   - [ ] Verify webhook with challenge/verify token

2. **Laravel Backend Preparation**
   - [ ] Create WhatsApp configuration file (`config/whatsapp.php`)
   - [ ] Add WhatsApp credentials to `.env`
   - [ ] Install Guzzle HTTP client (if not present)
   - [ ] Create database migrations for WhatsApp-related tables:
     ```sql
     - whatsapp_messages (log all incoming/outgoing)
     - whatsapp_users (map WhatsApp number to User ID)
     - whatsapp_flows (store flow definitions)
     - whatsapp_sessions (track conversation state)
     ```

3. **SSL/HTTPS Setup**
   - [ ] Ensure backend has valid SSL certificate (required for webhooks)
   - [ ] Use ngrok/CloudFlare Tunnel for local dev, or
   - [ ] Deploy to production-like staging environment
   - [ ] Test webhook connectivity

**Deliverables**:
- Meta Business account configured
- Laravel ready to receive webhooks
- HTTPS endpoint accessible by Meta

**Success Criteria**:
- Webhook verification successful
- Can send test message via Graph API
- Logs show incoming webhook calls

---

### Phase 2: Basic Messaging (Week 3)
**Objective**: Implement basic send/receive message functionality

#### Tasks:
1. **Create WhatsApp Service Classes**
   ```php
   app/Services/WhatsApp/
   ├── WhatsAppApiService.php      // Graph API wrapper
   ├── MessageBuilder.php           // Build message payloads
   ├── WebhookHandler.php          // Process incoming webhooks
   └── UserMapper.php              // Map WhatsApp # to User
   ```

2. **Webhook Controller**
   - [ ] Create `WhatsAppWebhookController`
   - [ ] Handle GET request (verification)
   - [ ] Handle POST request (incoming messages)
   - [ ] Parse webhook payload
   - [ ] Queue webhook processing (use Laravel Jobs)

3. **Basic Message Types**
   - [ ] Send text messages
   - [ ] Send template messages (for notifications)
   - [ ] Receive text messages
   - [ ] Handle message status updates (sent, delivered, read)

4. **User Authentication via WhatsApp**
   - [ ] Command: `/register <email> <NIP>` to link WhatsApp to account
   - [ ] Validate user credentials
   - [ ] Store `whatsapp_phone` in users table or separate mapping table
   - [ ] Command: `/status` to check current credits

**Deliverables**:
- Send text messages from Laravel
- Receive and log incoming messages
- User registration flow working

**Success Criteria**:
- Can send "Welcome" message to new users
- Users can register via WhatsApp
- Status command returns user data

---

### Phase 3: WhatsApp Flows Implementation (Week 4-5)
**Objective**: Create interactive forms for activity submission using WhatsApp Flows

#### Tasks:
1. **Flow Design & Creation**
   - [ ] Design Flow JSON for "Submit Activity" form
     - Screen 1: Select Category (dropdown)
     - Screen 2: Select Activity Type (filtered dropdown)
     - Screen 3: Activity Details (text inputs)
     - Screen 4: Upload Proof (media upload)
     - Screen 5: Confirmation

2. **Flow JSON Structure**
   ```json
   {
     "version": "5.0",
     "screens": [
       {
         "id": "CATEGORY_SCREEN",
         "title": "Select Category",
         "data": {},
         "layout": {
           "type": "SingleColumnLayout",
           "children": [
             {
               "type": "Dropdown",
               "name": "category",
               "label": "Activity Category",
               "data-source": []
             }
           ]
         }
       }
     ]
   }
   ```

3. **Flow Management**
   - [ ] Create Flow via API (Flows Manager API)
   - [ ] Store Flow ID in database
   - [ ] Create endpoint to update Flow JSON dynamically
   - [ ] Handle Flow data updates (category changes, new schemas)

4. **Flow Data Endpoint**
   - [ ] Create public API endpoint for Flow data requests
   - [ ] Return credit schemas as dropdown options
   - [ ] Filter by user's jenjang jabatan
   - [ ] Return schema details (satuan hasil, credit points, etc.)

5. **Flow Response Handler**
   - [ ] Create webhook handler for Flow responses
   - [ ] Parse submitted form data
   - [ ] Validate data against credit schema
   - [ ] Store activity in database
   - [ ] Send confirmation message

**Deliverables**:
- Working WhatsApp Flow for activity submission
- Backend endpoint handling Flow responses
- Activity creation via WhatsApp functional

**Success Criteria**:
- User can submit activity entirely within WhatsApp
- Form data correctly saved to database
- File uploads stored in Laravel storage

---

### Phase 4: Notification System (Week 6)
**Objective**: Implement real-time notifications for activity status changes

#### Tasks:
1. **Laravel Events & Listeners**
   - [ ] Create event: `ActivitySubmitted`
   - [ ] Create event: `ActivityApproved`
   - [ ] Create event: `ActivityRejected`
   - [ ] Create listener: `SendWhatsAppNotification`

2. **Notification Templates**
   - [ ] Create WhatsApp Message Templates in Meta Business Manager:
     - `activity_submitted` (confirmation)
     - `activity_approved` (success notification)
     - `activity_rejected` (rejection with reason)
   - [ ] Get template approval from Meta

3. **Notification Service**
   - [ ] Create `WhatsAppNotificationService`
   - [ ] Send template messages with variables
   - [ ] Handle template message failures
   - [ ] Log all notification attempts

4. **User Preferences**
   - [ ] Add `whatsapp_notifications` boolean to users table
   - [ ] Command: `/notifications on|off`
   - [ ] Respect user preferences

**Deliverables**:
- Automatic notifications on status changes
- User can enable/disable notifications
- Template messages approved by Meta

**Success Criteria**:
- Users receive instant notification when activity approved
- Notification includes activity details and credits earned
- Rejection notification includes verifier comments

---

### Phase 5: Advanced Features (Week 7-8)
**Objective**: Add convenience features and enhance user experience

#### Tasks:
1. **Menu System**
   - [ ] Create persistent menu (Reply Buttons)
   - [ ] Options:
     - 📝 Submit New Activity
     - 📊 My Statistics
     - 📋 Recent Activities
     - ✅ Pending Approvals
     - ℹ️ Help

2. **Rich Responses**
   - [ ] Statistics view with progress bar (text-based)
   - [ ] Activity list with status badges
   - [ ] Compliance status with percentage
   - [ ] Credit schema lookup

3. **Interactive Lists**
   - [ ] Use WhatsApp List Messages for category selection (alternative to Flow)
   - [ ] Quick submit for frequent activities

4. **File Handling Improvements**
   - [ ] Accept photos directly from WhatsApp camera
   - [ ] Accept documents from WhatsApp storage
   - [ ] Generate thumbnails for image proof files
   - [ ] Support multiple file formats (PDF, JPG, PNG)

5. **Bulk Operations (for verifiers)**
   - [ ] Verifiers can approve activities via WhatsApp
   - [ ] Quick approve/reject commands
   - [ ] Get pending queue via WhatsApp

**Deliverables**:
- Full menu system
- Rich, user-friendly responses
- Verifier commands working

**Success Criteria**:
- Users can check stats without opening web
- Verifiers can perform quick approvals
- All common tasks doable via WhatsApp

---

### Phase 6: Testing & Optimization (Week 9-10)
**Objective**: Comprehensive testing and performance optimization

#### Tasks:
1. **Testing**
   - [ ] Unit tests for all WhatsApp services
   - [ ] Integration tests for webhook handling
   - [ ] End-to-end tests for user flows
   - [ ] Load testing (simulate 100+ concurrent users)
   - [ ] Test all error scenarios

2. **Error Handling**
   - [ ] Graceful webhook failures
   - [ ] Retry logic for API calls
   - [ ] User-friendly error messages
   - [ ] Admin alerts for critical failures

3. **Performance Optimization**
   - [ ] Queue all webhook processing
   - [ ] Cache credit schemas
   - [ ] Optimize database queries
   - [ ] CDN for media files

4. **Security Audit**
   - [ ] Verify webhook signatures
   - [ ] Rate limiting on endpoints
   - [ ] Input validation and sanitization
   - [ ] XSS/SQL injection prevention
   - [ ] Secure file upload handling

5. **Documentation**
   - [ ] User guide (WhatsApp commands)
   - [ ] Admin guide (Meta Business setup)
   - [ ] Developer documentation (API)
   - [ ] Deployment guide

**Deliverables**:
- Full test coverage
- Security hardening complete
- Documentation published

**Success Criteria**:
- All tests passing
- No security vulnerabilities
- System handles peak load

---

### Phase 7: Deployment & Training (Week 11-12)
**Objective**: Production deployment and user onboarding

#### Tasks:
1. **Production Deployment**
   - [ ] Set up production environment
   - [ ] Configure production WhatsApp Business number
   - [ ] SSL certificate for production domain
   - [ ] Update webhook URL to production
   - [ ] Database migration and seeding
   - [ ] Environment variable configuration

2. **Monitoring Setup**
   - [ ] Set up application monitoring (Laravel Telescope)
   - [ ] Set up error tracking (Sentry/Bugsnag)
   - [ ] WhatsApp API usage monitoring
   - [ ] Database performance monitoring
   - [ ] Set up alerts for critical errors

3. **User Training**
   - [ ] Create video tutorials (WhatsApp usage)
   - [ ] Create PDF guides
   - [ ] Conduct training sessions for users
   - [ ] Conduct training for verifiers/admins
   - [ ] Create FAQ document

4. **Phased Rollout**
   - [ ] Week 1: Pilot with 10 test users
   - [ ] Week 2: Expand to 50 users
   - [ ] Week 3: Expand to all users
   - [ ] Monitor and fix issues at each phase

5. **Support System**
   - [ ] Create support channel (email/WhatsApp)
   - [ ] Document common issues and solutions
   - [ ] On-call schedule for technical issues

**Deliverables**:
- Production system live
- Users trained and onboarded
- Support system operational

**Success Criteria**:
- 90%+ user adoption rate
- Less than 5% error rate
- Positive user feedback

---

## Technical Requirements

### Meta Business Platform
| Requirement | Details |
|------------|---------|
| **Business Verification** | Meta Business Account must be verified |
| **WhatsApp Business API** | Access to Cloud API (free tier available) |
| **Phone Number** | Dedicated business phone number |
| **Webhook URL** | HTTPS endpoint with valid SSL |
| **Access Token** | Permanent access token (not temporary) |
| **Message Templates** | Pre-approved for notifications |

### Laravel Backend
| Component | Requirement |
|-----------|------------|
| **PHP Version** | ≥ 8.3 (current: ✅) |
| **Laravel Version** | ≥ 12.x (current: ✅) |
| **Database** | MySQL 8.0+ (current: ✅) |
| **Queue Driver** | Redis or Database (recommended: Redis) |
| **Cache Driver** | Redis (recommended) |
| **Storage** | Local or S3-compatible (for media files) |
| **HTTPS** | Valid SSL certificate (production) |
| **HTTP Client** | Guzzle 7.x |

### Infrastructure
| Resource | Specification |
|----------|--------------|
| **Server** | 2+ CPU cores, 4GB+ RAM |
| **SSL Certificate** | Free (Let's Encrypt) or paid |
| **Domain** | Custom domain with HTTPS |
| **Webhook Timeout** | 20 seconds (Meta requirement) |
| **Media Storage** | 10GB+ (for proof files) |
| **Database Backup** | Daily automated backups |

### Development Tools
- **Local HTTPS**: ngrok or CloudFlare Tunnel
- **API Testing**: Postman/Insomnia
- **WhatsApp Testing**: WhatsApp Business app (test number)
- **Git**: Version control
- **CI/CD**: GitHub Actions or GitLab CI (optional)

---

## Implementation Timeline

### Gantt Chart Overview
```
Phase 1: Infrastructure Setup        [Week 1-2]  ████████
Phase 2: Basic Messaging            [Week 3]    ████
Phase 3: WhatsApp Flows             [Week 4-5]  ████████
Phase 4: Notifications              [Week 6]    ████
Phase 5: Advanced Features          [Week 7-8]  ████████
Phase 6: Testing & Optimization     [Week 9-10] ████████
Phase 7: Deployment & Training      [Week 11-12]████████
                                    └─────────────────────┘
                                    12 weeks total (~3 months)
```

### Milestones
| Week | Milestone | Deliverable |
|------|-----------|------------|
| 2 | ✅ Infrastructure Ready | Webhook verified, SSL working |
| 3 | ✅ Basic Messaging Works | Send/receive messages functional |
| 5 | ✅ Flow Submission Ready | Users can submit via WhatsApp Flow |
| 6 | ✅ Notifications Live | Auto-notifications on status change |
| 8 | ✅ Feature Complete | All advanced features implemented |
| 10 | ✅ Production Ready | Testing complete, security hardened |
| 12 | ✅ System Live | Full deployment, users trained |

---

## Risk Assessment

### High Priority Risks

#### 1. Meta Business Verification Delay
- **Impact**: High (blocks entire project)
- **Likelihood**: Medium
- **Mitigation**:
  - Start verification process ASAP (can take 2-4 weeks)
  - Prepare all required documents upfront
  - Use test number for development in parallel
- **Contingency**: Use test environment with test phone number

#### 2. Webhook Connectivity Issues
- **Impact**: High (no message reception)
- **Likelihood**: Medium
- **Mitigation**:
  - Use reliable hosting with 99.9% uptime
  - Implement webhook queue with retries
  - Set up redundant webhook endpoints
- **Contingency**: Polling mechanism (not ideal, but works)

#### 3. WhatsApp API Rate Limits
- **Impact**: Medium (delays during peak usage)
- **Likelihood**: Low (free tier: 1000 conversations/month)
- **Mitigation**:
  - Monitor API usage closely
  - Implement message batching
  - Upgrade to paid tier if needed ($0.005-$0.09 per conversation)
- **Contingency**: Queue messages during rate limit periods

### Medium Priority Risks

#### 4. Template Message Rejection
- **Impact**: Medium (delays notification feature)
- **Likelihood**: Medium
- **Mitigation**:
  - Follow Meta's template guidelines strictly
  - Avoid promotional language
  - Submit templates early for review
- **Contingency**: Use alternative notification (email, SMS)

#### 5. User Adoption Resistance
- **Impact**: Medium (low usage)
- **Likelihood**: Low
- **Mitigation**:
  - Provide comprehensive training
  - Make interface very simple
  - Show clear benefits over web
- **Contingency**: Keep web interface fully functional

#### 6. File Upload Size Limits
- **Impact**: Low (user inconvenience)
- **Likelihood**: Medium (WhatsApp limit: 16MB)
- **Mitigation**:
  - Compress images automatically
  - Guide users on file size
  - Accept multiple files if needed
- **Contingency**: Provide web upload as alternative

### Low Priority Risks

#### 7. Localization Issues
- **Impact**: Low (minor UX issue)
- **Likelihood**: Low
- **Mitigation**:
  - Use Bahasa Indonesia consistently
  - Test with Indonesian users
- **Contingency**: Easy to update text

---

## Cost Estimate

### Development Costs
| Item | Cost | Notes |
|------|------|-------|
| Developer Time | 480 hours × rate | ~12 weeks × 40 hours |
| Testing | 80 hours × rate | QA and bug fixes |
| **Total Dev** | **560 hours** | |

### Infrastructure Costs (Monthly)
| Service | Cost | Notes |
|---------|------|-------|
| WhatsApp Cloud API | $0 - $50 | Free tier: 1000 conversations/mo |
| Server Hosting | $20 - $100 | VPS or cloud instance |
| SSL Certificate | $0 | Let's Encrypt |
| Domain | $10 - $20 | .com domain |
| Database Backup | $5 - $20 | Automated backups |
| Monitoring Tools | $0 - $50 | Free tier or paid |
| **Total Monthly** | **$35 - $240** | |

### One-Time Costs
| Item | Cost |
|------|------|
| Meta Business Verification | $0 (free, but requires documents) |
| Initial Setup | Included in dev time |
| Training Materials | Included in dev time |

---

## Success Metrics

### Key Performance Indicators (KPIs)

#### User Adoption
- **Target**: 80%+ of users submit at least one activity via WhatsApp within first month
- **Measure**: Count of unique WhatsApp users vs total active users

#### Activity Submission Rate
- **Target**: 50%+ of all activities submitted via WhatsApp after 3 months
- **Measure**: WhatsApp submissions / Total submissions

#### Response Time
- **Target**: 95% of notifications delivered within 30 seconds
- **Measure**: Timestamp delta between event and notification

#### System Reliability
- **Target**: 99.5% uptime
- **Measure**: Webhook availability monitoring

#### User Satisfaction
- **Target**: 4.0+ out of 5.0 rating
- **Measure**: Post-deployment user survey

#### Error Rate
- **Target**: <2% of webhook requests fail
- **Measure**: Failed webhooks / Total webhooks

---

## Next Steps (Immediate Actions)

### Week 1 - Getting Started

#### Day 1-2: Meta Business Setup
1. Go to [Meta Business Suite](https://business.facebook.com/)
2. Create or select Business Account
3. Navigate to WhatsApp > Getting Started
4. Add phone number or request new one
5. Start business verification process

#### Day 3-4: Laravel Preparation
```bash
# 1. Create configuration file
php artisan make:config whatsapp

# 2. Create migration for WhatsApp tables
php artisan make:migration create_whatsapp_messages_table
php artisan make:migration create_whatsapp_users_table

# 3. Create service classes
php artisan make:service WhatsApp/WhatsAppApiService
php artisan make:controller API/WhatsAppWebhookController

# 4. Update .env with placeholders
# WHATSAPP_API_TOKEN=
# WHATSAPP_PHONE_NUMBER_ID=
# WHATSAPP_BUSINESS_ACCOUNT_ID=
# WHATSAPP_WEBHOOK_VERIFY_TOKEN=
```

#### Day 5: Local Development Setup
1. Set up ngrok or CloudFlare Tunnel for local HTTPS
2. Configure webhook URL in Meta Business
3. Test webhook verification

---

## Appendix

### A. Useful Links
- [WhatsApp Cloud API Documentation](https://developers.facebook.com/docs/whatsapp/cloud-api)
- [WhatsApp Flows Documentation](https://developers.facebook.com/docs/whatsapp/flows)
- [Meta Business Manager](https://business.facebook.com/)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Guzzle HTTP Client](http://docs.guzzlephp.org/)

### B. WhatsApp API Endpoints
```
Base URL: https://graph.facebook.com/v21.0

Send Message:
POST /{phone-number-id}/messages

Upload Media:
POST /{phone-number-id}/media

Create Flow:
POST /{business-id}/flows

Send Flow Message:
POST /{phone-number-id}/messages
Body: { "type": "interactive", "interactive": { "type": "flow" } }
```

### C. Sample Webhook Payload
```json
{
  "object": "whatsapp_business_account",
  "entry": [{
    "id": "BUSINESS_ACCOUNT_ID",
    "changes": [{
      "value": {
        "messaging_product": "whatsapp",
        "metadata": {
          "display_phone_number": "15551234567",
          "phone_number_id": "PHONE_NUMBER_ID"
        },
        "messages": [{
          "from": "6281234567890",
          "id": "wamid.XXX",
          "timestamp": "1234567890",
          "type": "text",
          "text": {
            "body": "/status"
          }
        }]
      },
      "field": "messages"
    }]
  }]
}
```

### D. Database Schema Changes

#### New Tables
```sql
-- WhatsApp message log
CREATE TABLE whatsapp_messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    whatsapp_message_id VARCHAR(255) UNIQUE,
    user_id BIGINT UNSIGNED NULL,
    from_number VARCHAR(20),
    to_number VARCHAR(20),
    direction ENUM('inbound', 'outbound'),
    type VARCHAR(50),
    content TEXT,
    status VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- WhatsApp user mapping
CREATE TABLE whatsapp_users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED UNIQUE,
    whatsapp_phone VARCHAR(20) UNIQUE,
    verified BOOLEAN DEFAULT FALSE,
    notifications_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- WhatsApp flows
CREATE TABLE whatsapp_flows (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    flow_id VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    json_definition TEXT,
    status VARCHAR(50),
    version INT DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Modified Tables
```sql
-- Add to activities table
ALTER TABLE activities ADD COLUMN submitted_via ENUM('web', 'whatsapp') DEFAULT 'web';
ALTER TABLE activities ADD COLUMN whatsapp_message_id VARCHAR(255) NULL;

-- Add to users table (if not exists)
ALTER TABLE users ADD COLUMN whatsapp_phone VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN whatsapp_notifications BOOLEAN DEFAULT TRUE;
```

---

## Document Control

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2025-11-11 | Development Team | Initial roadmap created |

---

**End of Roadmap Document**

For questions or clarifications, please refer to the project documentation or contact the development team.
