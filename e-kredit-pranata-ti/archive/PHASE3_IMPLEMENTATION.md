# Phase 3 Implementation Summary - WhatsApp Flows
**Date**: 2025-11-13
**Status**: ✅ Complete (Pending Meta Business Setup)

---

## 🎯 Overview

Phase 3 implements **WhatsApp Flows** - interactive forms that allow users to submit activities directly through WhatsApp with a native, app-like experience. This eliminates the need to switch to a web browser.

---

## ✅ Features Implemented

### 1. **WhatsApp Flow Service** ✅
**File**: `backend/app/Services/WhatsApp/FlowService.php`

**Capabilities**:
- Generate Flow JSON schema
- Create Flow via WhatsApp Cloud API
- Update Flow JSON content
- Publish Flow to production
- Send Flow messages to users
- Generate secure flow tokens
- Fetch dynamic category data based on user's jenjang jabatan

**Key Methods**:
```php
getActivitySubmissionFlowJson()   // Returns Flow JSON schema
createFlow($name, $category)      // Creates Flow via API
updateFlowJson($flowId, $json)   // Updates Flow content
publishFlow($flowId)              // Publishes Flow
sendFlowMessage($to, $flowId...) // Sends Flow to user
getCategoriesData($jenjang)       // Gets filtered credit schemas
```

---

### 2. **Flow Data Controller** ✅
**File**: `backend/app/Http/Controllers/API/FlowDataController.php`

**Endpoints**:
1. **POST `/api/whatsapp/flow/data-exchange`**
   - Handles Flow data requests from WhatsApp
   - Actions: `INIT` (first load), `data_exchange` (user interactions)
   - Returns dynamic data (categories, schema details)
   - Validates request signatures for security

2. **POST `/api/whatsapp/flow/response`**
   - Handles Flow completion (form submission)
   - Creates activity in database
   - Triggers notification event
   - Validates and stores activity data

**Security**:
- X-WhatsApp-Signature validation
- HMAC-SHA256 signature verification
- Flow token validation

---

### 3. **Flow JSON Schema** ✅

**Structure**:
```json
{
  "version": "5.0",
  "data_api_version": "3.0",
  "screens": [
    {
      "id": "SUBMIT_ACTIVITY",
      "title": "Ajukan Aktivitas",
      "terminal": true,
      "layout": {
        "type": "SingleColumnLayout",
        "children": [
          {
            "type": "Form",
            "name": "activity_form",
            "children": [
              // Dropdown for credit schema selection
              {
                "type": "Dropdown",
                "label": "Pilih Skema Kredit",
                "name": "schema_id",
                "data-source": "${data.categories}"
              },
              // Text input for title
              {
                "type": "TextInput",
                "label": "Judul Aktivitas",
                "name": "title"
              },
              // Text area for description
              {
                "type": "TextArea",
                "label": "Deskripsi",
                "name": "description"
              },
              // Number input for quantity (optional)
              {
                "type": "TextInput",
                "label": "Jumlah/Volume",
                "name": "quantity",
                "input-type": "number"
              }
            ]
          }
        ]
      }
    }
  ]
}
```

**Form Fields**:
1. **Schema Selection** (Dropdown)
   - Dynamically populated based on user's jenjang jabatan
   - Shows: Activity Name - Category - Credit Points
   - Example: "Aplikasi Web Kompleks - Pengembangan Sistem - 5.000 AK"

2. **Activity Title** (Text Input)
   - Required field
   - Helper text: "Berikan judul deskriptif untuk aktivitas"

3. **Description** (Text Area)
   - Required field
   - Helper text: "Jelaskan detail aktivitas yang dilakukan"

4. **Quantity/Volume** (Number Input)
   - Optional field
   - Helper text: "Opsional: jumlah output yang dihasilkan"

---

### 4. **Enhanced `/submit` Command** ✅
**File**: `backend/app/Services/WhatsApp/WebhookHandler.php:478-526`

**Workflow**:
1. Check if user is registered
2. Fetch published Flow from database
3. Generate secure flow token
4. Get filtered credit schemas for user's jenjang
5. Send Flow message with dynamic data
6. Handle errors gracefully

**Error Handling**:
- User not registered → Prompt registration
- No published Flow → User-friendly message
- No schemas available → Inform user
- API failure → Error message with retry suggestion

---

### 5. **API Routes** ✅
**File**: `backend/routes/api.php`

**New Routes**:
```php
// Flow data exchange endpoint (called by WhatsApp)
POST /api/whatsapp/flow/data-exchange

// Flow response endpoint (form submission)
POST /api/whatsapp/flow/response
```

**Security**: Both routes use signature validation (no auth:sanctum needed)

---

## 📋 Flow User Journey

### Step-by-Step Experience:

1. **User types `/submit`** in WhatsApp

2. **Bot responds** with interactive Flow button:
   ```
   📝 Ajukan Aktivitas

   Silakan isi form berikut untuk mengajukan aktivitas Anda.

   [Mulai] ← Button
   ```

3. **User taps "Mulai"** → Flow opens in WhatsApp

4. **Form Screen** appears with:
   - Dropdown showing available credit schemas
   - Title input field
   - Description text area
   - Optional quantity field

5. **User selects schema** from dropdown:
   - Example: "Aplikasi Web Kompleks - Pengembangan Sistem - 5.000 AK"

6. **User fills** title and description

7. **User taps "Kirim"** button

8. **System processes**:
   - Validates data
   - Creates activity in database
   - Sets status to "pending"
   - Marks as submitted via WhatsApp

9. **User receives confirmation**:
   ```
   ✅ Aktivitas Berhasil Diajukan

   Aktivitas: [title]
   Angka Kredit: [credits]
   Tanggal: [date]

   Aktivitas Anda sedang menunggu verifikasi.

   Anda akan menerima notifikasi setelah aktivitas diverifikasi.

   Ketik /activities untuk melihat status aktivitas Anda.
   ```

---

## 📁 Files Created/Modified

### New Files:
1. `backend/app/Services/WhatsApp/FlowService.php` (310 lines)
2. `backend/app/Http/Controllers/API/FlowDataController.php` (213 lines)
3. `PHASE3_IMPLEMENTATION.md` (this file)

### Modified Files:
1. `backend/app/Services/WhatsApp/WebhookHandler.php`
   - Line 8: Added `WhatsAppFlow` model import
   - Line 14-20: Added `FlowService` dependency injection
   - Line 478-526: Implemented `initiateActivitySubmission()` with Flow

2. `backend/routes/api.php`
   - Line 11: Added `FlowDataController` import
   - Line 28-29: Added Flow endpoints

---

## 🔧 Setup Instructions

### 1. Create Flow in Meta Business Manager

Once Meta Business account is ready, run these commands:

```bash
# Create Flow via artisan command (you'll need to create this)
php artisan whatsapp:create-flow "e-Kredit Activity Submission"
```

Or via API call:

```bash
curl -X POST "https://graph.facebook.com/v21.0/{BUSINESS_ACCOUNT_ID}/flows" \
  -H "Authorization: Bearer {ACCESS_TOKEN}" \
  -d '{
    "name": "e-Kredit Activity Submission",
    "categories": ["ACTIVITY_SUBMISSION"]
  }'
```

### 2. Update Flow JSON

```bash
# Upload Flow JSON
curl -X POST "https://graph.facebook.com/v21.0/{FLOW_ID}/assets" \
  -H "Authorization: Bearer {ACCESS_TOKEN}" \
  -d '{
    "name": "flow.json",
    "asset_type": "FLOW_JSON",
    "body": "{JSON_CONTENT_HERE}"
  }'
```

### 3. Publish Flow

```bash
curl -X POST "https://graph.facebook.com/v21.0/{FLOW_ID}/publish" \
  -H "Authorization: Bearer {ACCESS_TOKEN}"
```

### 4. Store Flow in Database

```sql
INSERT INTO whatsapp_flows (flow_id, name, status, category, created_at, updated_at)
VALUES ('{FLOW_ID}', 'e-Kredit Activity Submission', 'published', 'ACTIVITY_SUBMISSION', NOW(), NOW());
```

---

## 🧪 Testing

### Test Flow Data Exchange (INIT):

```bash
curl -X POST http://localhost/api/whatsapp/flow/data-exchange \
  -H "Content-Type: application/json" \
  -H "X-Whatsapp-Signature: {SIGNATURE}" \
  -d '{
    "version": "3.0",
    "action": "INIT",
    "flow_token": "1_1699999999",
    "screen": "SUBMIT_ACTIVITY",
    "data": {}
  }'
```

**Expected Response**:
```json
{
  "version": "3.0",
  "screen": "SUBMIT_ACTIVITY",
  "data": {
    "categories": [
      {
        "id": "1",
        "title": "Aplikasi Web Kompleks",
        "description": "Pengembangan Sistem - 5.000 AK"
      }
    ]
  }
}
```

### Test Flow Response (Submission):

```bash
curl -X POST http://localhost/api/whatsapp/flow/response \
  -H "Content-Type: application/json" \
  -H "X-Whatsapp-Signature: {SIGNATURE}" \
  -d '{
    "flow_token": "1_1699999999",
    "response": {
      "schema_id": "1",
      "title": "Membuat Sistem Perpustakaan",
      "description": "Aplikasi web untuk manajemen perpustakaan dengan fitur peminjaman",
      "quantity": "1"
    }
  }'
```

**Expected Response**:
```json
{
  "success": true,
  "activity_id": 123,
  "message": "Activity submitted successfully"
}
```

### Test /submit Command:

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
            "text": {"body": "/submit"}
          }]
        }
      }]
    }]
  }'
```

---

## 🔐 Security Features

### 1. **Signature Validation**
All Flow requests must include `X-Whatsapp-Signature` header:
```php
$signature = hash_hmac('sha256', $payload, config('whatsapp.webhook_secret'));
```

### 2. **Flow Token**
- Format: `{user_id}_{timestamp}`
- Used to identify user making the request
- Should be upgraded to JWT in production

### 3. **Input Validation**
- Required fields check
- Schema ID validation
- User authorization check
- Jenjang jabatan filtering

---

## 📊 Database Impact

### Tables Used:
- `whatsapp_flows` - Stores Flow metadata
- `activities` - Stores submitted activities
- `credit_schema` - Source for dropdown options
- `users` - User jenjang jabatan for filtering

### New Field in Activities:
- `submitted_via` = 'whatsapp' (distinguishes WhatsApp submissions)

---

## 🚀 Next Steps

### Immediate (After Meta Setup):
1. ✅ Create Flow in Meta Business Manager
2. ✅ Upload Flow JSON
3. ✅ Publish Flow
4. ✅ Store Flow ID in database
5. ✅ Test end-to-end submission

### Phase 4 - Already Implemented!:
✅ Notifications system is ready (from Phase 2)
✅ Event-driven architecture in place
✅ Users will automatically receive notifications when activities are approved/rejected

### Future Enhancements:
1. **File Upload Support**
   - Add media upload field to Flow
   - Handle file download from WhatsApp
   - Store in Laravel storage
   - Generate thumbnails

2. **Multi-step Flow**
   - Split into multiple screens
   - Category → Activity Type → Details → Proof → Confirm

3. **Form Validation**
   - Real-time validation in Flow
   - Check batasan penilaian limits
   - Prevent duplicate submissions

4. **Flow Analytics**
   - Track completion rate
   - Monitor drop-off points
   - Optimize user experience

---

## ⚠️ Important Notes

### Flow Token Security
Current implementation uses simple `user_id_timestamp` format. For production:

**Upgrade to JWT**:
```bash
composer require firebase/php-jwt
```

```php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Generate
$payload = [
    'user_id' => $userId,
    'exp' => time() + 3600, // 1 hour expiry
];
$token = JWT::encode($payload, config('app.key'), 'HS256');

// Decode
$decoded = JWT::decode($token, new Key(config('app.key'), 'HS256'));
```

### Flow JSON Updates
When updating Flow JSON:
1. Flow must be in `draft` status
2. After update, must republish
3. Changes take effect immediately after publish
4. Keep backup of working JSON

### Rate Limits
WhatsApp Cloud API limits:
- 250 messages/second (per phone number)
- 1000 messages/day (free tier)
- Flow API: 100 requests/second

---

## 📈 Feature Comparison

| Feature | Before Phase 3 | After Phase 3 |
|---------|----------------|---------------|
| Activity Submission | ❌ Web only | ✅ WhatsApp + Web |
| User Experience | Desktop required | Mobile-native |
| Form Interface | HTML form | Native WhatsApp Flow |
| Data Entry | Manual typing | Dropdowns + validation |
| Credit Schema Selection | Browse web | Filtered dropdown |
| Real-time Feedback | Page reload | Instant in-app |
| Accessibility | Limited | High (WhatsApp users) |

---

## ✅ Phase 3 Completion Checklist

- [x] Flow JSON schema designed
- [x] FlowService created with all methods
- [x] Flow data exchange endpoint
- [x] Flow response handler
- [x] Activity creation from Flow
- [x] `/submit` command updated
- [x] Routes added
- [x] Security (signature validation)
- [x] Error handling
- [x] Jenjang jabatan filtering
- [x] Documentation created

### Pending (Requires Meta Business):
- [ ] Create Flow in Meta Business Manager
- [ ] Upload Flow JSON
- [ ] Publish Flow
- [ ] Test end-to-end submission
- [ ] User acceptance testing

---

## 🎉 Summary

**Phase 3 is complete!** WhatsApp Flows implementation is ready:

- ✅ Complete Flow architecture
- ✅ Dynamic form generation
- ✅ Secure data exchange
- ✅ Activity submission via WhatsApp
- ✅ User-filtered credit schemas
- ✅ Error handling & validation
- ✅ Integration with existing notification system

**Lines of Code Added**: ~550+ lines
**Files Created**: 2 major services + controller
**Files Modified**: 2 files

**Ready for**: Meta Business setup to create and test actual Flow!

---

**Last Updated**: 2025-11-13
**Next Session**: Meta Business setup OR Phase 4 enhancements
