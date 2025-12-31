# WhatsApp Integration - Complete Guide
## e-Kredit Pranata TI System

**Version**: 1.0.0
**Status**: ✅ Phases 1-3 Complete (Pending Meta Business Setup)
**Last Updated**: 2025-11-13

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Features Implemented](#features-implemented)
4. [Phase 1: Infrastructure Setup](#phase-1-infrastructure-setup)
5. [Phase 2: Enhanced Messaging](#phase-2-enhanced-messaging)
6. [Phase 3: WhatsApp Flows](#phase-3-whatsapp-flows)
7. [Meta Business Setup](#meta-business-setup)
8. [Testing Guide](#testing-guide)
9. [Deployment](#deployment)
10. [Troubleshooting](#troubleshooting)

---

## Overview

This guide documents the complete WhatsApp integration for the e-Kredit Pranata TI system. The integration enables Pranata TI professionals to manage their activity credits entirely through WhatsApp, providing a mobile-first, accessible platform.

### What Users Can Do

- 📝 **Register** via WhatsApp (`/register`)
- 📊 **View Statistics** with compliance checking (`/stats`)
- 📋 **Browse Activities** with pagination (`/activities`)
- 📄 **View Details** of specific activities (`/detail`)
- ✍️ **Submit Activities** via WhatsApp Flows (`/submit`)
- 🔔 **Receive Notifications** when activities are approved/rejected
- ❓ **Get Help** anytime (`/help`)

### Implementation Timeline

- **Phase 1** (Week 1-2): Infrastructure Setup ✅
- **Phase 2** (Week 3): Enhanced Messaging ✅
- **Phase 3** (Week 4-5): WhatsApp Flows ✅
- **Pending**: Meta Business Account setup

### Technology Stack

- **Backend**: Laravel 12 + WhatsApp Cloud API
- **Database**: MySQL (4 new tables)
- **Queue**: Laravel Queue (async notifications)
- **Tunnel**: LocalTunnel (development) / ngrok (production)

---

## Architecture

### System Flow

```
┌─────────────────┐
│  WhatsApp User  │
└────────┬────────┘
         │
         ├─ Send Message (/stats, /submit, etc.)
         │
         ▼
┌─────────────────────────────────────┐
│   WhatsApp Cloud API (Meta)         │
│   - Receives messages                │
│   - Sends webhook to our server      │
└────────┬────────────────────────────┘
         │
         ▼ POST /api/whatsapp/webhook
┌─────────────────────────────────────┐
│   Laravel Backend                    │
│   ┌─────────────────────────────┐   │
│   │ WhatsAppWebhookController   │   │
│   │  ├─ Verify signature        │   │
│   │  └─ Dispatch to handler     │   │
│   └────────┬────────────────────┘   │
│            │                         │
│   ┌────────▼────────────────────┐   │
│   │ WebhookHandler              │   │
│   │  ├─ Parse message           │   │
│   │  ├─ Route commands          │   │
│   │  └─ Execute actions         │   │
│   └────────┬────────────────────┘   │
│            │                         │
│   ┌────────▼────────────────────┐   │
│   │ Services & Models           │   │
│   │  ├─ WhatsAppApiService      │   │
│   │  ├─ FlowService             │   │
│   │  ├─ Activity, User models   │   │
│   │  └─ Event system            │   │
│   └─────────────────────────────┘   │
└─────────────────────────────────────┘
         │
         ├─ Send Response
         ▼
┌─────────────────────────────────────┐
│   WhatsApp Cloud API                │
│   - Delivers to user                 │
└────────┬────────────────────────────┘
         │
         ▼
┌─────────────────┐
│  WhatsApp User  │
│  (Receives msg) │
└─────────────────┘
```

### Database Schema

**New Tables**:
1. `whatsapp_messages` - Message logging
2. `whatsapp_users` - Phone-to-user mapping
3. `whatsapp_flows` - Flow metadata
4. `whatsapp_sessions` - Conversation state

**Modified Tables**:
- `activities` - Added `submitted_via`, `whatsapp_message_id`

---

## Features Implemented

### Phase 1: Infrastructure ✅

**Completed**: 2025-11-12

#### Components Created
- ✅ Webhook endpoints (GET/POST)
- ✅ WhatsApp API service wrapper
- ✅ Message builder for formatting
- ✅ Webhook handler for processing
- ✅ 4 database tables
- ✅ 4 Eloquent models
- ✅ Queue job for async processing
- ✅ User registration system

#### Commands Available
- `/register <email> <NIP>` - Link WhatsApp to account
- `/menu` or `/start` - Show main menu
- `/status` - View account info

**Files**: `backend/app/Services/WhatsApp/*`, `backend/app/Http/Controllers/API/WhatsAppWebhookController.php`

---

### Phase 2: Enhanced Messaging ✅

**Completed**: 2025-11-13

#### Features Added

**1. Real Statistics (`/stats`)**
- Total activities count
- Status breakdown (pending/approved/rejected)
- Credit calculations (Unsur Utama vs Penunjang)
- Compliance checking (≥80% Utama, ≤20% Penunjang)
- PR No. 3 Tahun 2025 validation

**Output Example**:
```
📊 Statistik Anda

Total Aktivitas: 15
Menunggu Verifikasi: 3
Disetujui: 10
Ditolak: 2

Total Kredit Disetujui: 45.500 angka kredit
Kredit Unsur Utama: 38.250 (84.1%)
Kredit Penunjang: 7.250 (15.9%)

✅ Status: Sesuai Ketentuan
```

**2. Activity History (`/activities [page]`)**
- Paginated list (5 per page)
- Status icons (✅ approved, ❌ rejected, ⏳ pending)
- Activity titles, credits, dates
- Quick links to `/detail` command

**3. Activity Detail (`/detail <ID>`)**
- Complete activity information
- Credit schema details
- Submission date/time
- Verifier information
- Approval/rejection comments

**4. Enhanced Help (`/help`)**
- All commands with descriptions
- Usage examples
- Parameter explanations
- Compliance rules

**5. Notification System 🔔**
- Event-driven architecture (Laravel Events & Listeners)
- Queued async processing
- Three notification types:
  - ✅ **Submission Confirmation**
  - 🎉 **Approval Notification**
  - ❌ **Rejection with Reason**

**Files Modified**:
- `WebhookHandler.php` - Added statistics, activity history, detail view
- `MessageBuilder.php` - Enhanced help message
- **New**: `ActivityStatusChanged.php` (Event)
- **New**: `SendWhatsAppNotification.php` (Listener)

---

### Phase 3: WhatsApp Flows ✅

**Completed**: 2025-11-13

#### Flow Architecture

WhatsApp Flows provide native, app-like forms directly in WhatsApp without opening a browser.

**Flow Structure**:
```json
{
  "version": "5.0",
  "screens": [{
    "id": "SUBMIT_ACTIVITY",
    "layout": {
      "type": "Form",
      "children": [
        {"type": "Dropdown", "name": "schema_id"},
        {"type": "TextInput", "name": "title"},
        {"type": "TextArea", "name": "description"},
        {"type": "TextInput", "name": "quantity"}
      ]
    }
  }]
}
```

**Form Fields**:
1. **Credit Schema Dropdown** - Filtered by user's jenjang jabatan
2. **Activity Title** - Required text input
3. **Description** - Required text area
4. **Quantity/Volume** - Optional number input

#### Components Created

**1. FlowService** (`FlowService.php`)
- Generate Flow JSON schema
- Create Flows via API
- Update & publish Flows
- Send Flow messages
- Dynamic data generation

**2. FlowDataController** (`FlowDataController.php`)
- Handle data exchange requests
- Manage Flow responses
- Validate submissions
- Create activities from Flow data

**API Endpoints**:
- `POST /api/whatsapp/flow/data-exchange` - Flow data requests
- `POST /api/whatsapp/flow/response` - Form submissions

#### User Journey

1. User types `/submit`
2. Bot sends Flow button
3. User taps "Mulai"
4. Native form opens in WhatsApp
5. User selects schema, fills title & description
6. User taps "Kirim"
7. System creates activity
8. User receives confirmation notification

**Files Created**:
- `backend/app/Services/WhatsApp/FlowService.php`
- `backend/app/Http/Controllers/API/FlowDataController.php`

---

## Meta Business Setup

### Prerequisites

- Facebook Business Account
- WhatsApp Business API access
- HTTPS endpoint for webhook

### Step-by-Step Guide

#### 1. Create Meta Business Account

1. Go to [Meta Business Suite](https://business.facebook.com/)
2. Click "Create Account" or select existing
3. Complete business verification (optional for testing)

#### 2. Create WhatsApp App

1. Go to [Meta for Developers](https://developers.facebook.com/)
2. Click "My Apps" → "Create App"
3. Select "Business" as app type
4. Enter app name: "e-Kredit Pranata TI"
5. Add WhatsApp product to app

#### 3. Get Test Phone Number

1. In WhatsApp → API Setup
2. Meta provides a test number instantly
3. Copy the test number (format: +1 555-XXX-XXXX)

**Note**: Test number is for development only, no verification needed!

#### 4. Configure Webhook

1. In WhatsApp → Configuration → Webhook
2. Click "Configure" or "Edit"
3. Enter webhook details:
   - **Callback URL**: `https://your-domain.com/api/whatsapp/webhook`
   - **Verify Token**: Your token from `.env`
4. Click "Verify and Save"
5. Subscribe to webhook field: `messages`

#### 5. Get Credentials

Copy these values to `.env`:

**Phone Number ID**:
- Location: WhatsApp → API Setup → "Phone number ID"
- Copy the long number

**Business Account ID**:
- Location: WhatsApp → API Setup → "WhatsApp Business Account ID"
- Copy the ID

**Access Token** (Temporary for testing):
- Location: WhatsApp → API Setup → "Temporary access token"
- Click "Copy"
- Valid for 24 hours

**App Secret**:
- Location: Settings → Basic → "App Secret"
- Click "Show" and copy

#### 6. Update Configuration

Edit `backend/.env`:

```env
WHATSAPP_API_VERSION=v21.0
WHATSAPP_API_BASE_URL=https://graph.facebook.com
WHATSAPP_BUSINESS_ACCOUNT_ID=your_account_id_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_here
WHATSAPP_API_TOKEN=your_access_token_here
WHATSAPP_WEBHOOK_VERIFY_TOKEN=my-secret-verify-token-12345
WHATSAPP_WEBHOOK_SECRET=your_app_secret_here
```

#### 7. Create & Publish Flow

**Create Flow**:
```bash
curl -X POST "https://graph.facebook.com/v21.0/{ACCOUNT_ID}/flows" \
  -H "Authorization: Bearer {TOKEN}" \
  -d '{
    "name": "e-Kredit Activity Submission",
    "categories": ["ACTIVITY_SUBMISSION"]
  }'
```

**Upload Flow JSON**:
```bash
curl -X POST "https://graph.facebook.com/v21.0/{FLOW_ID}/assets" \
  -H "Authorization: Bearer {TOKEN}" \
  -d '{
    "name": "flow.json",
    "asset_type": "FLOW_JSON",
    "body": "{JSON_CONTENT}"
  }'
```

**Publish Flow**:
```bash
curl -X POST "https://graph.facebook.com/v21.0/{FLOW_ID}/publish" \
  -H "Authorization: Bearer {TOKEN}"
```

**Store in Database**:
```sql
INSERT INTO whatsapp_flows (flow_id, name, status, category)
VALUES ('{FLOW_ID}', 'e-Kredit Activity Submission', 'published', 'ACTIVITY_SUBMISSION');
```

---

## Testing Guide

### Local Testing (Mock Webhook)

**Test User Registration**:
```bash
curl -X POST http://localhost/api/whatsapp/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "entry": [{
      "changes": [{
        "field": "messages",
        "value": {
          "messages": [{
            "from": "6281234567890",
            "id": "wamid.test123",
            "timestamp": "1699999999",
            "type": "text",
            "text": {"body": "/register john@pranata.id 198501012010011001"}
          }]
        }
      }]
    }]
  }'
```

**Test Statistics Command**:
```bash
curl -X POST http://localhost/api/whatsapp/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "entry": [{
      "changes": [{
        "field": "messages",
        "value": {
          "messages": [{
            "from": "6281234567890",
            "id": "wamid.test124",
            "timestamp": "1699999999",
            "type": "text",
            "text": {"body": "/stats"}
          }]
        }
      }]
    }]
  }'
```

### End-to-End Testing (After Meta Setup)

1. **Test Registration**:
   - Send `/register` from WhatsApp
   - Verify welcome message received
   - Check database entry

2. **Test Commands**:
   - `/status` - Check account status
   - `/stats` - View statistics
   - `/activities` - Browse activities
   - `/detail 1` - View activity detail
   - `/help` - View help

3. **Test Flow Submission**:
   - Send `/submit`
   - Verify Flow button appears
   - Fill form and submit
   - Check activity created in database
   - Verify confirmation notification

4. **Test Notifications**:
   - Approve an activity via web dashboard
   - Verify WhatsApp notification received
   - Check notification content

---

## Deployment

### Environment Setup

**Required Environment Variables**:
```env
# WhatsApp Cloud API
WHATSAPP_API_VERSION=v21.0
WHATSAPP_API_BASE_URL=https://graph.facebook.com
WHATSAPP_BUSINESS_ACCOUNT_ID=
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_API_TOKEN=
WHATSAPP_WEBHOOK_VERIFY_TOKEN=
WHATSAPP_WEBHOOK_SECRET=
WHATSAPP_TIMEOUT=20
```

### Queue Worker

Notifications are queued for async processing. Run queue worker:

```bash
# Development
php artisan queue:work

# Production (with Supervisor)
[program:laravel-queue-worker]
command=php /path/to/artisan queue:work --tries=3
autostart=true
autorestart=true
user=www-data
```

### HTTPS Setup

**Option 1: LocalTunnel (Development)**
```bash
npm install -g localtunnel
lt --port 80
```

**Option 2: ngrok (Recommended)**
```bash
brew install ngrok
ngrok http 80
```

**Option 3: Production Server**
```bash
sudo certbot --nginx -d yourdomain.com
```

---

## Troubleshooting

### Webhook Verification Fails

**Symptoms**: Green checkmark doesn't appear in Meta Business Manager

**Solutions**:
1. Verify `WHATSAPP_WEBHOOK_VERIFY_TOKEN` matches exactly
2. Check HTTPS URL is publicly accessible
3. Check Laravel logs: `tail -f storage/logs/laravel.log`
4. Test manually:
   ```bash
   curl "https://your-url.com/api/whatsapp/webhook?hub.mode=subscribe&hub.verify_token=your-token&hub.challenge=test"
   ```

### Messages Not Received

**Solutions**:
1. Verify webhook subscribed to `messages` field
2. Check signature validation in logs
3. Ensure queue worker is running
4. Check Meta webhook delivery logs

### Flow Not Appearing

**Solutions**:
1. Verify Flow status is "published"
2. Check Flow ID stored in database
3. Verify user has available credit schemas
4. Check logs for Flow service errors

### Notifications Not Sending

**Solutions**:
1. Ensure queue worker is running
2. Check user has WhatsApp registered
3. Verify `notifications_enabled` is true
4. Check logs for API errors

---

## Security Considerations

### Signature Validation

All webhooks validate signatures:
```php
$signature = hash_hmac('sha256', $payload, config('whatsapp.webhook_secret'));
```

### Flow Tokens

Current implementation uses `{user_id}_{timestamp}`. For production, upgrade to JWT:

```bash
composer require firebase/php-jwt
```

### Input Validation

- All form inputs validated
- Required field checks
- Schema ID validation
- User authorization checks

---

## Performance & Scalability

### Optimizations Implemented
- ✅ Eager loading for relationships
- ✅ Queued notification processing
- ✅ Efficient database queries
- ✅ Message logging for audit trail

### Rate Limits
- **WhatsApp Cloud API**: 250 messages/second
- **Free Tier**: 1,000 conversations/month
- **Conversation**: 24-hour window with user

---

## Complete Command Reference

| Command | Description | Example |
|---------|-------------|---------|
| `/register <email> <NIP>` | Link WhatsApp to account | `/register john@example.com 123456` |
| `/menu` or `/start` | Show main menu | `/menu` |
| `/status` | View account status | `/status` |
| `/stats` | View complete statistics | `/stats` |
| `/activities [page]` | Browse activity history | `/activities 2` |
| `/detail <ID>` | View activity details | `/detail 123` |
| `/submit` | Submit new activity | `/submit` |
| `/help` | Show help message | `/help` |

---

## File Reference

### Core Services
- `app/Services/WhatsApp/WhatsAppApiService.php` - API wrapper
- `app/Services/WhatsApp/MessageBuilder.php` - Message formatting
- `app/Services/WhatsApp/WebhookHandler.php` - Command processing
- `app/Services/WhatsApp/FlowService.php` - Flow management

### Controllers
- `app/Http/Controllers/API/WhatsAppWebhookController.php` - Webhook endpoint
- `app/Http/Controllers/API/FlowDataController.php` - Flow endpoints

### Models
- `app/Models/WhatsAppMessage.php` - Message logging
- `app/Models/WhatsAppUser.php` - User mapping
- `app/Models/WhatsAppFlow.php` - Flow metadata
- `app/Models/WhatsAppSession.php` - Session state

### Events & Listeners
- `app/Events/ActivityStatusChanged.php` - Status change event
- `app/Listeners/SendWhatsAppNotification.php` - Notification sender

---

## What's Next

### Immediate (After Meta Setup)
1. Complete Meta Business Account setup
2. Create and publish Flow
3. Test end-to-end with real WhatsApp
4. User acceptance testing

### Future Enhancements
1. **File Upload Support** - Media handling in Flows
2. **Verifier Commands** - Approve/reject via WhatsApp
3. **Multi-language** - Indonesian + English
4. **Analytics Dashboard** - Usage statistics

---

## Support

### Documentation
- API Reference: `docs/03-API-DOCUMENTATION.md`
- Database Schema: `docs/04-DATABASE-SCHEMA.md`
- Compliance Guide: `docs/05-COMPLIANCE-GUIDE.md`

### External Resources
- [WhatsApp Cloud API Docs](https://developers.facebook.com/docs/whatsapp/cloud-api)
- [WhatsApp Flows Documentation](https://developers.facebook.com/docs/whatsapp/flows)
- [Laravel Events Documentation](https://laravel.com/docs/events)

---

**Status**: ✅ Complete (Phases 1-3)
**Ready for**: Meta Business setup → Production deployment
**Last Updated**: 2025-11-13
**Version**: 1.0.0
