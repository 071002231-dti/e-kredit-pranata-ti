# 🎨 Web Client Guide - e-Kredit Pranata TI

**Tech Stack**: React 19 + Vite + TypeScript + Tailwind CSS
**Dev Server**: http://localhost:5173
**API Backend**: http://localhost/api
**Location**: `web-client/` folder in project root

---

## 📁 Project Structure

```
web-client/
├── src/
│   ├── components/
│   │   ├── Layout.tsx              # Main layout with navigation
│   │   └── ui/                     # UI components (Button, Card, Input, etc.)
│   ├── contexts/
│   │   └── AuthContext.tsx         # Authentication context & hooks
│   ├── hooks/
│   │   └── useAuth.ts              # Custom auth hook
│   ├── lib/
│   │   ├── api.ts                  # Axios instance with interceptors
│   │   └── utils.ts                # Utility functions
│   ├── pages/
│   │   ├── LoginPage.tsx           # Login page
│   │   ├── RegisterPage.tsx        # Registration page
│   │   ├── DashboardPage.tsx       # Main dashboard with stats & charts
│   │   ├── ActivitiesPage.tsx      # Activity list (table/card view)
│   │   └── ActivityFormPage.tsx    # Add/Edit activity form
│   ├── services/
│   │   └── api.ts                  # API service functions
│   ├── types/
│   │   └── index.ts                # TypeScript interfaces
│   ├── App.tsx                     # Main app with routing
│   ├── index.css                   # Tailwind imports
│   └── main.tsx                    # Entry point (Vite)
├── public/
├── .env                            # Environment variables
├── vite.config.ts                  # Vite configuration
├── tailwind.config.js              # Tailwind configuration
└── package.json
```

---

## 🚀 Quick Start

### 1. Start Backend (if not running)
```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
./vendor/bin/sail up -d
```

### 2. Start Web Client
```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/web-client
npm run dev
```

Web client akan running di: **http://localhost:5173**

---

## 🔑 Test Login

Gunakan salah satu test users:

| Email | Password | Role |
|-------|----------|------|
| user@example.com | password | user |
| verifier@example.com | password | verifier |
| admin@example.com | password | admin |

---

## 📄 Available Pages

### 1. **Login Page** (`/login`)
- Email & password login
- Display test users
- Auto redirect to dashboard after login
- Error handling

### 2. **Dashboard** (`/dashboard`)
- Stats cards: Total, Pending, Approved, Rejected, Total Points
- Category summary table
- Quick action buttons
- User info display
- Logout button

### 3. **Activities List** (`/activities`)
- Table view of all user activities
- Status badges (pending/approved/rejected)
- View and delete actions
- "New Activity" button
- Filters by status

---

## 🔧 Key Features Implemented

### Authentication
- JWT token-based auth (Laravel Sanctum)
- Token stored in localStorage
- Auto-redirect on 401 Unauthorized
- Protected routes with ProtectedRoute component
- useAuth() hook for accessing user & auth functions

### API Integration
- Axios instance with base URL configuration
- Request interceptor: Auto-add Bearer token
- Response interceptor: Handle 401 errors
- TypeScript interfaces for all API responses

### State Management
- React Context API for auth state
- Local state with useState for pages
- useEffect for data fetching

### Routing
- React Router v6
- Protected routes
- Auto redirect to dashboard when authenticated
- Redirect to login when not authenticated

---

## 🛠️ Services API

### AuthService
```typescript
import { authService } from './services/authService';

// Login
const { user, token } = await authService.login({ email, password });

// Get current user
const user = await authService.getCurrentUser();

// Logout
await authService.logout();

// Check if authenticated
const isAuth = authService.isAuthenticated();
```

### ActivityService
```typescript
import { activityService } from './services/activityService';

// Get activities (paginated)
const response = await activityService.getActivities(1);

// Create activity
const formData = new FormData();
formData.append('schema_id', '1');
formData.append('title', 'My Activity');
formData.append('description', 'Description');
formData.append('proof_file', file);
await activityService.createActivity(formData);

// Delete activity
await activityService.deleteActivity(id);
```

### DashboardService
```typescript
import { dashboardService } from './services/dashboardService';

// Get stats
const stats = await dashboardService.getStats();
// { total_activities, pending, approved, rejected, total_points }

// Get category summary
const summary = await dashboardService.getSummary();
// [{ category, total_activities, approved_count, earned_points }]
```

---

## 🎯 useAuth Hook

```typescript
import { useAuth } from './contexts/AuthContext';

function MyComponent() {
  const { user, loading, login, logout, isAuthenticated } = useAuth();

  // user: Current logged-in user object (null if not logged in)
  // loading: Boolean indicating if auth is being checked
  // login: Function to login (email, password)
  // logout: Function to logout
  // isAuthenticated: Boolean indicating if user is logged in
}
```

---

## 🎨 Styling

Current implementation menggunakan **inline styles** untuk simplicity.

**Untuk production**, disarankan menggunakan:
- Tailwind CSS
- Material-UI (MUI)
- Ant Design
- Styled Components
- CSS Modules

---

## 🔄 Data Flow

```
User Action
    ↓
Component/Page
    ↓
Service Layer (authService, activityService, etc.)
    ↓
Axios API Call (config/api.ts)
    ↓
Backend API (Laravel)
    ↓
Response
    ↓
Update State
    ↓
Re-render UI
```

---

## 📦 Dependencies

```json
{
  "react": "^19.1.1",
  "react-dom": "^19.1.1",
  "react-router-dom": "^6.x",
  "axios": "^1.x",
  "typescript": "^4.9.5"
}
```

---

## ⚠️ Important Notes

### CORS
- Backend sudah di-configure untuk accept requests dari localhost:3000
- Jika ada CORS errors, check `config/cors.php` di backend

### Environment Variables
Create `.env` file di frontend root:
```
REACT_APP_API_URL=http://localhost/api
```

### Token Expiration
- Tokens dari Sanctum tidak expire by default
- Untuk production, set token expiration di `config/sanctum.php`

### TypeScript
- All API responses typed with interfaces
- Type-safe service calls
- Auto-completion in IDE

---

## 🐛 Troubleshooting

### "Network Error" saat login
- Check backend is running: `docker ps`
- Check API URL correct: `http://localhost/api`
- Open browser console untuk detail error

### Token tidak saved
- Check browser localStorage
- Check console untuk errors
- Clear localStorage: `localStorage.clear()`

### Page tidak protected
- Check ProtectedRoute wrapping route
- Check useAuth hook returning correct values

### CORS errors
```bash
# Restart backend
cd backend
./vendor/bin/sail down
./vendor/bin/sail up -d
```

---

## 🚧 TODO / Next Steps

### Features to Add:
- [ ] Register page implementation
- [ ] Activity detail page
- [ ] Create/Edit activity form with file upload
- [ ] Approval page for verifiers
- [ ] Search & filter activities
- [ ] Pagination controls
- [ ] Loading states & skeletons
- [ ] Toast notifications
- [ ] Form validation
- [ ] Responsive mobile design

### Improvements:
- [ ] Add proper styling (Tailwind/MUI)
- [ ] Add unit tests (Jest + React Testing Library)
- [ ] Add E2E tests (Cypress/Playwright)
- [ ] Add error boundaries
- [ ] Add retry logic for failed requests
- [ ] Add offline support
- [ ] Optimize bundle size
- [ ] Add PWA support

---

## 📊 Current Implementation Status

| Feature | Status |
|---------|--------|
| Authentication | ✅ Complete |
| Protected Routes | ✅ Complete |
| Login Page | ✅ Complete |
| Dashboard | ✅ Complete |
| Activities List | ✅ Complete |
| API Integration | ✅ Complete |
| TypeScript Types | ✅ Complete |
| Service Layer | ✅ Complete |
| Register Page | ⏳ Pending |
| Activity Forms | ⏳ Pending |
| File Upload UI | ⏳ Pending |
| Approval UI | ⏳ Pending |

---

## 🎉 Testing the App

1. Open http://localhost:3000
2. You'll be redirected to /login
3. Login with `user@example.com` / `password`
4. You'll see dashboard with stats (all zeros since no activities yet)
5. Click "View My Activities" to see empty list
6. Backend API is ready for creating activities!

---

**Generated**: 2025-11-11
**React Version**: 19.1.1
**TypeScript**: Yes
