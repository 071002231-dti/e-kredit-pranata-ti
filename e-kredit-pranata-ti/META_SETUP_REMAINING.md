# Meta WhatsApp Setup - Remaining Steps

**Created**: 2025-11-15
**Status**: Blocked by Facebook Checkpoint
**Progress**: 85% Complete

---

## 🚧 Current Blocker

**Facebook Checkpoint**: Account hit security checkpoint at https://www.facebook.com/checkpoint/1501092823525282/

**Expected Resolution**: 24-48 hours
**Action**: Wait for checkpoint to clear, do NOT attempt to login repeatedly

---

## ✅ What's Already Complete

1. ✅ Meta Business Account created
2. ✅ WhatsApp Business App created ("e-Kredit Pranata TI")
3. ✅ Test phone number provisioned
4. ✅ Credentials obtained and configured in `.env`
5. ✅ Backend code complete and tested locally
6. ✅ Database tables created and verified
7. ✅ Webhook endpoints working
8. ✅ LocalTunnel setup and tested

---

## 📋 Remaining Steps (When Checkpoint Clears)

### Step 1: Access Meta Dashboard (2 minutes)

Once checkpoint clears, login to:
- **App Dashboard**: https://developers.facebook.com/apps/25060680903589338
- **WhatsApp Console**: https://developers.facebook.com/apps/25060680903589338/whatsapp-business/wa-dev-console/

---

### Step 2: Get App Secret (5 minutes)

**OPTIONAL** - Only needed if you want signature verification

1. Go to: **Settings** > **Basic** (left sidebar)
2. Find **App Secret**
3. Click **"Show"** and enter your Facebook password
4. Copy the App Secret
5. Update `backend/.env`:
   ```env
   WHATSAPP_WEBHOOK_SECRET=<paste_app_secret_here>
   ```
6. Set signature verification to true:
   ```env
   WHATSAPP_VERIFY_SIGNATURE=true
   ```

---

### Step 3: Configure Webhook in Meta (10 minutes)

1. **Navigate to Webhook Configuration**:
   - In WhatsApp Console, go to: **Configuration** > **Webhook**
   - Click **"Edit"** or **"Configure"**

2. **Check LocalTunnel Status**:
   ```bash
   # Check if LocalTunnel is still running
   ps aux | grep "lt --port" | grep -v grep

   # If not running, restart it
   lt --port 80
   ```

   **Current URL**: `https://blue-shoes-smell.loca.lt`

   ⚠️ **Important**: URL may change if LocalTunnel restarted

3. **Enter Webhook Details**:
   - **Callback URL**: `https://blue-shoes-smell.loca.lt/api/whatsapp/webhook`
   - **Verify Token**: `bJOsu1ZXLlm79BiYRw63eOme1VSUx3dKqvW9yRbujEQ=`

4. **Click "Verify and Save"**:
   - Meta will send GET request to your webhook
   - Should return success ✅

5. **Subscribe to Webhook Fields**:
   - Check ✅ **messages**
   - Save subscription

---

### Step 4: Add Test Recipient (5 minutes)

1. In WhatsApp Console, find **"To"** section
2. Click **"Manage phone number list"** or **"Add phone number"**
3. Enter your WhatsApp number (e.g., `+62812XXXXXXXX`)
4. Verify the number if prompted
5. Save

---

### Step 5: Test Basic Commands (10 minutes)

1. **Start Queue Worker** (in new terminal):
   ```bash
   cd backend
   docker exec -it backend-laravel.test-1 php artisan queue:work --queue=whatsapp
   ```

2. **Send WhatsApp Message to Test Number**:
   - From: `+1 555-XXX-XXXX` (Meta's test number)
   - Send message: `/help`

3. **You Should Receive**:
   - Help message with all available commands

4. **Test Registration**:
   - Send: `/register user@example.com 198501012010011001`
   - Check if registration succeeds

5. **Test Other Commands**:
   - `/stats` - View statistics
   - `/activities` - List activities
   - `/menu` - Main menu

---

### Step 6: Create & Publish WhatsApp Flow (15 minutes)

**OPTIONAL** - For activity submission via WhatsApp Flow

1. **Generate Flow JSON** (via artisan command):
   ```bash
   docker exec backend-laravel.test-1 php artisan whatsapp:create-flow
   ```

2. **Or Create Manually via API**:
   ```bash
   curl -X POST "https://graph.facebook.com/v22.0/25580086328282547/flows" \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
     -d '{
       "name": "e-Kredit Activity Submission",
       "categories": ["OTHER"]
     }'
   ```

3. **Upload Flow JSON**:
   ```bash
   curl -X POST "https://graph.facebook.com/v22.0/{FLOW_ID}/assets" \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
     -F "file=@/path/to/flow.json" \
     -F "asset_type=FLOW_JSON"
   ```

4. **Publish Flow**:
   ```bash
   curl -X POST "https://graph.facebook.com/v22.0/{FLOW_ID}/publish" \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
   ```

5. **Store Flow ID in Database**:
   ```bash
   docker exec backend-laravel.test-1 php artisan tinker --execute='
   DB::table("whatsapp_flows")->insert([
     "flow_id" => "YOUR_FLOW_ID",
     "name" => "e-Kredit Activity Submission",
     "status" => "published",
     "created_at" => now(),
     "updated_at" => now()
   ]);'
   ```

---

### Step 7: Test End-to-End (15 minutes)

1. **Test `/submit` Command**:
   - Send `/submit` via WhatsApp
   - Should receive Flow form

2. **Submit Activity via Flow**:
   - Fill in activity details
   - Submit form
   - Verify activity created in database

3. **Test Approval Notification**:
   ```bash
   # Via web dashboard, approve an activity
   # You should receive WhatsApp notification
   ```

4. **Verify Database**:
   ```bash
   docker exec backend-laravel.test-1 php artisan tinker --execute='
   echo "WhatsApp Users: " . DB::table("whatsapp_users")->count() . "\n";
   echo "WhatsApp Messages: " . DB::table("whatsapp_messages")->count() . "\n";
   echo "Activities: " . DB::table("activities")->count() . "\n";'
   ```

---

## 🔑 Quick Reference

### Current Credentials

```env
# App Info
APP_ID=25060680903589338
WHATSAPP_BUSINESS_ACCOUNT_ID=25580086328282547
PHONE_NUMBER_ID=793189970553956

# Access Token (expires Dec 2025)
WHATSAPP_API_TOKEN=EAFkIjncWfdoBPzuwNCfBJXThm2dOvJJqYFdCOa1Y82rVvN1cZA6nVz1KTtYoAo7ZBJHYnCwmJfLXcZCiRoPUn0FVOaXHQL6K3zfhhE8BDTKSFQymPYminKy2LqZCBZCWEdtPqwQKL59yhdSWKotPF6cZAEYPmZCeOvs3HwZBcZBhbC3dbmRcXtmoLZCfrwlrlgTHpj2IkVLfDiGaSNvZAmrmCGHZACCFIKtr7Q6IbugpRME1nZCU0mAZDZD

# Webhook
WEBHOOK_URL=https://blue-shoes-smell.loca.lt/api/whatsapp/webhook
VERIFY_TOKEN=bJOsu1ZXLlm79BiYRw63eOme1VSUx3dKqvW9yRbujEQ=
```

### Important URLs

- **App Dashboard**: https://developers.facebook.com/apps/25060680903589338
- **WhatsApp Console**: https://developers.facebook.com/apps/25060680903589338/whatsapp-business/wa-dev-console/
- **Local Backend**: http://localhost/api
- **Local Frontend**: http://localhost:3000
- **Webhook (LocalTunnel)**: https://blue-shoes-smell.loca.lt

### Available Commands

Once setup complete, users can send:
- `/register <email> <NIP>` - Link WhatsApp to account
- `/menu` or `/start` - Main menu
- `/help` - Get help
- `/status` - Check account status
- `/stats` - View statistics
- `/activities [page]` - Browse activities
- `/detail <ID>` - View activity details
- `/submit` - Submit new activity (via Flow)

---

## 🚨 Troubleshooting

### Facebook Checkpoint Won't Clear

**Options**:
1. Wait 48-72 hours
2. Try different Facebook account
3. Use colleague's Facebook account
4. Contact Facebook support (rarely helpful)

### LocalTunnel URL Changed

1. Get new URL:
   ```bash
   ps aux | grep "lt --port" | grep -v grep
   # or restart: lt --port 80
   ```

2. Update webhook in Meta Dashboard with new URL

### Webhook Verification Fails

1. Check LocalTunnel is running
2. Verify backend is running: `docker ps`
3. Test webhook locally:
   ```bash
   curl "http://localhost/api/whatsapp/webhook?hub.mode=subscribe&hub.challenge=TEST&hub.verify_token=bJOsu1ZXLlm79BiYRw63eOme1VSUx3dKqvW9yRbujEQ="
   ```
4. Check Laravel logs:
   ```bash
   docker exec backend-laravel.test-1 tail -50 storage/logs/laravel.log
   ```

### Messages Not Being Received

1. Check queue worker is running
2. Verify phone number added to test recipients
3. Check WhatsApp message logs:
   ```bash
   docker exec backend-laravel.test-1 php artisan tinker --execute='
   DB::table("whatsapp_messages")->latest()->take(5)->get();'
   ```

---

## 📊 Progress Tracker

- [x] Meta Business Account created
- [x] WhatsApp App created
- [x] Test phone number obtained
- [x] Credentials configured
- [x] Backend code complete
- [x] Database setup complete
- [x] Local testing complete
- [x] Model bugs fixed
- [ ] Facebook checkpoint cleared ⏳
- [ ] Webhook configured in Meta
- [ ] Test recipient added
- [ ] End-to-end testing complete
- [ ] WhatsApp Flow created (optional)
- [ ] Production ready

**Current**: 85% Complete
**Blocked By**: Facebook Checkpoint
**ETA**: 15-20 minutes after checkpoint clears

---

## 📝 Notes

- Access token expires Dec 2025 - will need to regenerate for long-term use
- LocalTunnel URL will change if process restarts
- For production, use ngrok or proper domain with SSL
- Queue worker should be running as daemon in production
- Consider setting up supervisor for queue worker

---

**Last Updated**: 2025-11-15
**Status**: Ready to continue once Facebook checkpoint clears
