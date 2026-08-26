# SSO Implementation - Quick Start Guide

## 🎯 Quick Test (5 Minutes)

### Prerequisites
- Docker Desktop installed and running
- Git repository up to date

### Step 1: Start Docker Environment
```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti
docker-compose up -d
```

### Step 2: Run Database Migration
```bash
# Execute migration in Laravel container
docker-compose exec laravel.test php artisan migrate

# Verify new SSO fields
docker-compose exec laravel.test php artisan tinker
>>> \Schema::hasColumn('users', 'nip_sso')  # Should return true
>>> \Schema::hasColumn('users', 'sso_uid')   # Should return true
>>> exit
```

### Step 3: Test Backend SSO with Mock Headers
```bash
# Test SSO login with faculty user (creates new user)
curl -X GET "http://localhost:8000/api/auth/sso/login?mock_sso=1&test_user=faculty" \
  -H "Accept: application/json" | jq

# Expected: 200 OK with token and user data
# {
#   "message": "SSO login successful",
#   "user": { "nip_sso": "123456789", ... },
#   "token": "1|abc123..."
# }
```

### Step 4: Test Different User Types
```bash
# Staff user (user role)
curl "http://localhost:8000/api/auth/sso/login?mock_sso=1&test_user=staff" -H "Accept: application/json" | jq

# Student user (user role)
curl "http://localhost:8000/api/auth/sso/login?mock_sso=1&test_user=student" -H "Accept: application/json" | jq

# Debug endpoint (shows all headers)
curl "http://localhost:8000/api/auth/sso/debug?mock_sso=1" -H "Accept: application/json" | jq
```

### Step 5: Build and Test Frontend
```bash
# Terminal 1: Start backend (if not already running)
cd /Users/4h3/myproject/e-kredit-pranata-ti
docker-compose up

# Terminal 2: Start frontend dev server
cd /Users/4h3/myproject/e-kredit-pranata-ti/e-kredit-pranata-ti/web-client
npm install
npm run dev
```

### Step 6: Test Frontend SSO Flow
1. Open browser: http://localhost:5173/login
2. Click "Login with UII SSO" (blue button)
3. Should redirect to `/api/auth/sso/login?mock_sso=1`
4. Backend creates user and returns token
5. Callback page processes token
6. Redirects to dashboard
7. Verify user data shows SSO fields

---

## 🧪 Detailed Testing Scenarios

### Scenario 1: New SSO User (Auto-Provisioning)
```bash
# Login as new faculty user
curl -X GET "http://localhost:8000/api/auth/sso/login?mock_sso=1&test_user=faculty" \
  -H "Accept: application/json" > response.json

# Extract token
TOKEN=$(cat response.json | jq -r '.token')

# Use token to access protected endpoint
curl -X GET "http://localhost:8000/api/me" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq

# Verify user created with SSO fields
# Expected: auth_method = "sso", nip_sso = "123456789"
```

### Scenario 2: Existing User Upgrade to Hybrid
```bash
# First, create user with local auth (via register or seeder)
docker-compose exec laravel.test php artisan db:seed --class=UserSeeder

# Then login with SSO using existing email
curl -X GET "http://localhost:8000/api/auth/sso/login?mock_sso=1&test_user=existing" \
  -H "Accept: application/json" | jq

# Verify user upgraded: auth_method = "hybrid"
```

### Scenario 3: SSO Then Local Login (Hybrid Auth)
```bash
# 1. Login with SSO first
SSO_TOKEN=$(curl -s "http://localhost:8000/api/auth/sso/login?mock_sso=1&test_user=faculty" | jq -r '.token')

# 2. Set password for the user (via tinker)
docker-compose exec laravel.test php artisan tinker
>>> $user = \App\Models\User::where('nip_sso', '123456789')->first()
>>> $user->password = bcrypt('password123')
>>> $user->save()
>>> exit

# 3. Login with email/password
curl -X POST "http://localhost:8000/api/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"123456789@uii.ac.id","password":"password123"}' | jq

# Expected: Same user, auth_method = "hybrid"
```

### Scenario 4: SSO Logout Flow
```bash
# Get SSO token
TOKEN=$(curl -s "http://localhost:8000/api/auth/sso/login?mock_sso=1" | jq -r '.token')

# Logout
curl -X POST "http://localhost:8000/api/auth/sso/logout" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq

# Expected: Returns shibboleth_logout_url
# {
#   "message": "Logged out successfully",
#   "shibboleth_logout_url": "http://localhost/Shibboleth.sso/Logout",
#   "redirect_url": "http://localhost/login"
# }
```

### Scenario 5: Error Handling - Missing Attributes
```bash
# Create custom mock headers without required fields
# (Requires modifying middleware or using manual curl with headers)
curl -X GET "http://localhost:8000/api/auth/sso/login" \
  -H "Accept: application/json" \
  -H "mail: incomplete@uii.ac.id" | jq
  # Note: Missing 'uid' header

# Expected: 401 Unauthorized with error_code: "MISSING_SAML_ATTRIBUTES"
```

---

## 🖥️ Frontend UI Testing

### Visual Checks
- [ ] Login page has blue "Login with UII SSO" button at top
- [ ] Divider with "Or continue with email" text
- [ ] Email/password form below with outline button
- [ ] Both buttons disabled during loading
- [ ] Error messages display correctly

### Functional Checks
- [ ] SSO button redirects to /api/auth/sso/login
- [ ] Mock mode activated automatically in development
- [ ] Callback page shows spinner while processing
- [ ] Dashboard loads with user data
- [ ] User menu shows name and email
- [ ] Logout redirects to login page

### Browser Console Checks
```javascript
// Check localStorage after SSO login
localStorage.getItem('auth_token')  // Should have token
localStorage.getItem('user')        // Should have user JSON

// Parse user data
JSON.parse(localStorage.getItem('user'))
// Should see: nip_sso, sso_uid, auth_method: "sso"
```

---

## 📊 Database Verification

### Check SSO Fields
```sql
-- Connect to database
docker-compose exec mysql mysql -u root -p ekredit

-- Show table structure
DESCRIBE users;

-- Check SSO users
SELECT id, nip, nip_sso, sso_uid, name, email, auth_method, sso_last_login
FROM users
WHERE auth_method IN ('sso', 'hybrid');

-- Check indexes
SHOW INDEX FROM users WHERE Key_name LIKE '%sso%';
```

### Expected Results
```
+----------+----------+-------------+-------+
| id       | nip_sso  | auth_method | email |
+----------+----------+-------------+-------+
| 1        | 123456789| sso         | 123456789@uii.ac.id |
| 2        | 987654321| sso         | 987654321@uii.ac.id |
+----------+----------+-------------+-------+
```

---

## 🔍 Debugging Tips

### Backend Not Responding?
```bash
# Check logs
docker-compose logs laravel.test --tail=100 -f

# Check specific error
docker-compose logs laravel.test | grep "SSO:"

# Restart container
docker-compose restart laravel.test
```

### Migration Failed?
```bash
# Check migration status
docker-compose exec laravel.test php artisan migrate:status

# Rollback and retry
docker-compose exec laravel.test php artisan migrate:rollback
docker-compose exec laravel.test php artisan migrate
```

### Frontend Not Connecting?
```bash
# Check API configuration
cd web-client
cat src/lib/api.ts | grep baseURL

# Check environment
cat .env | grep VITE_API_URL

# Restart dev server
npm run dev
```

### Mock Headers Not Working?
```bash
# Verify middleware is registered
grep -r "MockShibbolethHeaders" backend/routes/api.php

# Check app debug mode
docker-compose exec laravel.test php artisan tinker
>>> config('app.debug')  # Should be true for mock to work
>>> exit
```

---

## ✅ Success Criteria

### Backend Tests Pass When:
- ✅ Mock SSO login returns 200 with token
- ✅ User created with nip_sso and sso_uid populated
- ✅ auth_method set to 'sso'
- ✅ Faculty user has 'verifier' role
- ✅ Staff user has 'user' role
- ✅ sso_last_login timestamp recorded
- ✅ Token works for /api/me endpoint
- ✅ Debug endpoint shows injected headers

### Frontend Tests Pass When:
- ✅ Login page renders with SSO button (blue)
- ✅ SSO button triggers redirect
- ✅ Mock mode activates in development
- ✅ Callback page processes token
- ✅ Dashboard loads with user data
- ✅ User data includes SSO fields
- ✅ Logout clears token and redirects

### Database Tests Pass When:
- ✅ Migration creates all 5 new fields
- ✅ Indexes created on nip_sso, sso_uid, auth_method
- ✅ nip and password fields nullable
- ✅ SSO users have nip_sso populated
- ✅ auth_method enum works correctly

---

## 🚧 Common Issues & Solutions

### Issue 1: "Cannot connect to Docker daemon"
**Solution:**
```bash
# Start Docker Desktop
open -a Docker

# Wait for Docker to start (check menu bar icon)
# Then retry docker-compose up
```

### Issue 2: "Class 'SsoAuthController' not found"
**Solution:**
```bash
# Clear Laravel cache
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test composer dump-autoload
```

### Issue 3: "SQLSTATE[42S22]: Column not found"
**Solution:**
```bash
# Migration not run yet
docker-compose exec laravel.test php artisan migrate

# If already run, check migration status
docker-compose exec laravel.test php artisan migrate:status
```

### Issue 4: "401 Unauthorized" from SSO endpoint
**Solution:**
```bash
# Ensure mock_sso=1 parameter is present
curl "http://localhost:8000/api/auth/sso/login?mock_sso=1"

# Check debug mode is enabled
docker-compose exec laravel.test php artisan tinker
>>> config('app.debug')  # Must be true
```

### Issue 5: Frontend "Network Error"
**Solution:**
```bash
# Check CORS configuration
# Verify API_URL in frontend .env
cd web-client
cat .env

# Should have:
# VITE_API_URL=http://localhost:8000/api

# Restart both backend and frontend
docker-compose restart
npm run dev
```

---

## 📝 Test Checklist

Copy this checklist for each test run:

```
[ ] Docker started and containers running
[ ] Database migration executed successfully
[ ] Backend SSO login (faculty) returns 200 + token
[ ] Backend SSO login (staff) creates user with 'user' role
[ ] Backend SSO login (student) works
[ ] Backend debug endpoint shows mock headers
[ ] Token works for protected /api/me endpoint
[ ] Frontend login page shows SSO button (blue)
[ ] Frontend SSO button redirects correctly
[ ] Frontend callback page processes token
[ ] Frontend dashboard loads after SSO login
[ ] Frontend displays user name and SSO fields
[ ] Database shows user with nip_sso and sso_uid
[ ] Database auth_method = 'sso' for new users
[ ] Logout clears token and redirects to login
[ ] Error handling works (try without mock_sso parameter)
```

---

## 🎓 Next Steps After Testing

Once all tests pass:

1. **Create Infrastructure Files (Tasks #12-15)**
   - Docker Compose for Shibboleth SP
   - Shibboleth configuration (shibboleth2.xml)
   - SAML attribute mapping (attribute-map.xml)
   - Nginx FastCGI configuration

2. **Deploy to Staging**
   - Use staging domain (staging.ccp.uii.ac.id)
   - Configure test IdP
   - Register SP metadata with federation
   - Test with real SAML flow

3. **Production Preparation**
   - Generate production SP certificates
   - Obtain SSL certificates
   - Register with UII Federation (JAGGER)
   - Prepare user communication
   - Schedule deployment window

4. **Monitoring Setup**
   - Configure logging
   - Set up health checks
   - Create dashboards
   - Define alerts

---

**Ready to Test?** Start with Step 1 above! 🚀
