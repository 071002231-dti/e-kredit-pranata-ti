# Phase 1 Setup Guide - WhatsApp Integration
## e-Kredit Pranata TI System

This guide will help you complete Phase 1: Infrastructure Setup for WhatsApp integration.

---

## ✅ What We've Completed

1. **Laravel Configuration Files**
   - Created `config/whatsapp.php` with all necessary settings
   - Added WhatsApp environment variables to `.env.example`

2. **Database Migrations**
   - `whatsapp_messages` - Log all incoming/outgoing messages
   - `whatsapp_users` - Map WhatsApp numbers to users
   - `whatsapp_flows` - Store Flow definitions
   - `whatsapp_sessions` - Track conversation state
   - Added `submitted_via` and `whatsapp_message_id` to activities table

3. **Service Classes**
   - `WhatsAppApiService` - Wrapper for WhatsApp Graph API
   - `MessageBuilder` - Build message payloads and formats
   - `WebhookHandler` - Process incoming webhooks
   - `WhatsAppWebhookController` - Handle webhook HTTP requests
   - `ProcessWhatsAppWebhook` - Queue job for async processing

4. **Models**
   - `WhatsAppMessage` - Model for message logging
   - `WhatsAppUser` - Model for user-phone mapping
   - `WhatsAppFlow` - Model for Flow management
   - `WhatsAppSession` - Model for conversation sessions

5. **API Routes**
   - `GET /api/whatsapp/webhook` - Webhook verification
   - `POST /api/whatsapp/webhook` - Webhook message handling

---

## 📋 Next Steps to Complete Phase 1

### Step 1: Meta Business Setup (Required)

#### 1.1 Create/Verify Meta Business Account

1. Go to [Meta Business Suite](https://business.facebook.com/)
2. Click "Create Account" or select existing business account
3. Complete business verification:
   - Business name
   - Business address
   - Business phone number
   - Business documents (if required)
   - Tax ID (if applicable)

**Note**: Verification can take 2-4 weeks. You can use test mode while waiting.

#### 1.2 Set Up WhatsApp Business API

1. In Meta Business Suite, go to **WhatsApp** → **Getting Started**
2. Click "Create App" or select existing app
3. Add WhatsApp product to your app
4. Add phone number:
   - Option A: Use existing business phone number
   - Option B: Request test number from Meta (instant, for development)

#### 1.3 Get Required Credentials

1. **Phone Number ID**:
   - Go to WhatsApp → API Setup
   - Copy the "Phone number ID"

2. **WhatsApp Business Account ID**:
   - Go to WhatsApp → API Setup
   - Copy the "WhatsApp Business Account ID"

3. **Access Token**:
   - Go to WhatsApp → API Setup → Temporary Token (for testing)
   - For production: Create a System User and generate permanent token
   - Steps for permanent token:
     1. Go to Business Settings → Users → System Users
     2. Click "Add" to create new system user
     3. Assign WhatsApp permissions
     4. Generate token and save it securely

4. **Webhook Verify Token**:
   - Create a random string (e.g., `my-secret-verify-token-12345`)
   - You will use this when setting up the webhook

5. **Webhook Secret** (for signature verification):
   - Go to App Settings → Basic
   - Copy the "App Secret"

---

### Step 2: Configure Laravel Backend

#### 2.1 Copy Environment Variables

```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
cp .env.example .env
```

#### 2.2 Update `.env` File

Open `.env` and fill in the WhatsApp credentials:

```env
# WhatsApp Cloud API Configuration
WHATSAPP_API_VERSION=v21.0
WHATSAPP_API_BASE_URL=https://graph.facebook.com
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_API_TOKEN=your_access_token_here
WHATSAPP_WEBHOOK_VERIFY_TOKEN=my-secret-verify-token-12345
WHATSAPP_WEBHOOK_SECRET=your_app_secret_here
WHATSAPP_TIMEOUT=20
```

**Important**: Replace the placeholder values with your actual credentials from Meta Business Manager.

#### 2.3 Run Database Migrations

```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
php artisan migrate
```

This will create all the WhatsApp-related tables.

---

### Step 3: Set Up HTTPS for Webhook

Meta requires HTTPS endpoints for webhooks. Choose one option:

#### Option A: Local Development with ngrok (Recommended for Testing)

1. **Install ngrok**:
   ```bash
   brew install ngrok
   # OR download from https://ngrok.com/download
   ```

2. **Start Laravel server**:
   ```bash
   cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
   php artisan serve
   ```

3. **Start ngrok tunnel**:
   ```bash
   ngrok http 8000
   ```

4. **Copy the HTTPS URL**:
   - ngrok will display something like: `https://abc123.ngrok.io`
   - Your webhook URL will be: `https://abc123.ngrok.io/api/whatsapp/webhook`

**Note**: Free ngrok URLs change every time you restart. For persistent URLs, upgrade to ngrok paid plan or use CloudFlare Tunnel.

#### Option B: CloudFlare Tunnel (Free, Persistent URLs)

1. **Install cloudflared**:
   ```bash
   brew install cloudflare/cloudflare/cloudflared
   ```

2. **Authenticate**:
   ```bash
   cloudflared tunnel login
   ```

3. **Create tunnel**:
   ```bash
   cloudflared tunnel create whatsapp-tunnel
   ```

4. **Create config file** (`~/.cloudflared/config.yml`):
   ```yaml
   tunnel: <tunnel-id>
   credentials-file: /Users/<your-username>/.cloudflared/<tunnel-id>.json

   ingress:
     - hostname: whatsapp.yourdomain.com
       service: http://localhost:8000
     - service: http_status:404
   ```

5. **Start tunnel**:
   ```bash
   cloudflared tunnel run whatsapp-tunnel
   ```

6. **Your webhook URL**: `https://whatsapp.yourdomain.com/api/whatsapp/webhook`

#### Option C: Production Deployment (Digital Ocean, AWS, etc.)

For production, deploy to a server with SSL certificate:

1. **Deploy Laravel to server**
2. **Set up SSL certificate** (Let's Encrypt is free):
   ```bash
   sudo certbot --nginx -d yourdomain.com
   ```
3. **Webhook URL**: `https://yourdomain.com/api/whatsapp/webhook`

---

### Step 4: Configure Webhook in Meta Business Manager

1. Go to your app in Meta Business Manager
2. Navigate to **WhatsApp** → **Configuration**
3. Under "Webhook", click "Configure" or "Edit"
4. Enter your webhook details:
   - **Callback URL**: `https://your-domain.com/api/whatsapp/webhook`
   - **Verify Token**: The same token you set in `.env` (e.g., `my-secret-verify-token-12345`)
5. Click "Verify and Save"
6. If successful, you'll see a green checkmark ✅

7. **Subscribe to Webhook Fields**:
   - Check `messages` (required for receiving messages)
   - Check `message_echoes` (optional, for seeing your sent messages)
   - Click "Save"

---

### Step 5: Test Webhook Connection

#### 5.1 Test Webhook Verification

```bash
# Replace with your actual URL and verify token
curl "https://your-domain.com/api/whatsapp/webhook?hub.mode=subscribe&hub.verify_token=my-secret-verify-token-12345&hub.challenge=test123"

# Expected response: test123
```

#### 5.2 Send Test Message

1. Send a WhatsApp message to your business phone number from your personal phone
2. Check Laravel logs to see if webhook was received:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. You should see log entries like:
   ```
   [2025-11-12 10:00:00] local.INFO: Webhook verified successfully
   [2025-11-12 10:01:00] local.INFO: Webhook received {"payload":{...}}
   ```

#### 5.3 Test Registration Command

Send this message from WhatsApp to your business number:

```
/register your-email@example.com YOUR_NIP
```

Example:
```
/register john.doe@pranata.id 198501012010011001
```

Expected response:
```
👋 Selamat datang di e-Kredit Pranata TI!

Halo John Doe,

Anda sekarang dapat mengajukan aktivitas dan memantau angka kredit Anda langsung melalui WhatsApp.

Gunakan menu di bawah untuk memulai.
```

---

## 🔍 Troubleshooting

### Issue: Webhook verification fails

**Solution**:
- Ensure your `WHATSAPP_WEBHOOK_VERIFY_TOKEN` in `.env` matches exactly what you entered in Meta Business Manager
- Check that your HTTPS URL is accessible publicly
- Check Laravel logs for errors

### Issue: Messages not being received

**Solution**:
- Verify webhook is subscribed to `messages` field in Meta Business Manager
- Check that webhook signature verification is passing (check logs)
- Ensure queue worker is running if using queues:
  ```bash
  php artisan queue:work
  ```

### Issue: "Invalid signature" error

**Solution**:
- Verify `WHATSAPP_WEBHOOK_SECRET` matches your App Secret in Meta Business Manager
- Check that signature verification is enabled in config

### Issue: ngrok URL keeps changing

**Solution**:
- Upgrade to ngrok paid plan for persistent URLs
- Use CloudFlare Tunnel instead (free, persistent)
- Deploy to production server

---

## ✅ Phase 1 Completion Checklist

- [ ] Meta Business Account created and verified
- [ ] WhatsApp Business API enabled
- [ ] Phone number added (test or production)
- [ ] All credentials obtained (Phone ID, Business Account ID, Token, etc.)
- [ ] `.env` file configured with WhatsApp credentials
- [ ] Database migrations run successfully
- [ ] HTTPS endpoint set up (ngrok/CloudFlare/production)
- [ ] Webhook configured in Meta Business Manager
- [ ] Webhook verification successful (green checkmark in Meta)
- [ ] Webhook subscribed to `messages` field
- [ ] Test message received and logged
- [ ] Registration command working (`/register`)
- [ ] Main menu command working (`/menu`)

---

## 📚 Useful Commands

### Laravel Commands
```bash
# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear

# Start server
php artisan serve

# Start queue worker (if using queues)
php artisan queue:work

# View logs
tail -f storage/logs/laravel.log
```

### Testing Commands
```bash
# Test webhook verification
curl "https://your-url.com/api/whatsapp/webhook?hub.mode=subscribe&hub.verify_token=your-token&hub.challenge=test"

# Check database
php artisan tinker
>>> App\Models\WhatsAppMessage::count()
>>> App\Models\WhatsAppUser::all()
```

---

## 🎯 What's Next?

Once Phase 1 is complete, you can proceed to:

- **Phase 2**: Basic Messaging (Week 3)
  - Implement all command handlers
  - Add user statistics
  - Add recent activities list

- **Phase 3**: WhatsApp Flows (Week 4-5)
  - Design and create Flow JSON
  - Implement activity submission via Flow
  - Handle file uploads

Continue following the roadmap in `WHATSAPP_INTEGRATION_ROADMAP.md`.

---

## 📞 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Meta webhook delivery logs in Meta Business Manager
3. Review the [WhatsApp Cloud API Documentation](https://developers.facebook.com/docs/whatsapp/cloud-api)
4. Review the roadmap: `WHATSAPP_INTEGRATION_ROADMAP.md`

---

**Phase 1 Setup Complete!** 🎉

Once you've completed all the checklist items above, you'll have:
- ✅ WhatsApp Business API connected
- ✅ Webhook receiving messages
- ✅ User registration working
- ✅ Basic commands functional
- ✅ Foundation ready for Phase 2!
