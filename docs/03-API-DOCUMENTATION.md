# 📚 API Documentation - e-Kredit Pranata TI

**Base URL**: `http://localhost/api`
**Authentication**: Bearer Token (Laravel Sanctum)

---

## 🔐 Authentication Endpoints

### 1. Register User
**POST** `/register`

**Request Body**:
```json
{
  "nip": "199901012020011001",
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "position": "Pranata TI Ahli Muda",
  "unit_kerja": "Bidang Aplikasi"
}
```

**Response** (201):
```json
{
  "message": "User registered successfully",
  "user": { ... },
  "token": "1|xxxxxxxxxxxxx"
}
```

---

### 2. Login
**POST** `/login`

**Request Body**:
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response** (200):
```json
{
  "message": "Login successful",
  "user": {
    "id": 3,
    "nip": "199003032020013003",
    "name": "User Biasa",
    "email": "user@example.com",
    "role": "user",
    "position": "Pranata TI Ahli Muda",
    "unit_kerja": "Sub Bidang Aplikasi"
  },
  "token": "1|xxxxxxxxxxxxx"
}
```

---

### 3. Logout
**POST** `/logout` 🔒

**Headers**: `Authorization: Bearer {token}`

**Response** (200):
```json
{
  "message": "Logged out successfully"
}
```

---

### 4. Get Current User
**GET** `/me` 🔒

**Headers**: `Authorization: Bearer {token}`

**Response** (200):
```json
{
  "user": { ... }
}
```

---

## 📋 Credit Schema Endpoints

### 5. List All Credit Schemas
**GET** `/credit-schema`

**Query Parameters**:
- `category` (optional): Filter by category
- `subcategory` (optional): Filter by subcategory
- `paginate=false` (optional): Get all without pagination

**Response** (200):
```json
[
  {
    "id": 1,
    "category": "Pendidikan",
    "subcategory": "Pendidikan Formal",
    "activity_name": "Sarjana (S1) bidang TI",
    "credit_points": "100.00",
    "description": "Gelar S1 di bidang Teknologi Informasi"
  },
  ...
]
```

---

### 6. Get Credit Schema Categories
**GET** `/credit-schema/categories`

**Response** (200):
```json
["Pelatihan", "Pendidikan", "Pengembangan Profesi", "Penunjang", "Tugas Pokok"]
```

---

### 7. Get Single Credit Schema
**GET** `/credit-schema/{id}`

**Response** (200):
```json
{
  "id": 1,
  "category": "Pendidikan",
  "subcategory": "Pendidikan Formal",
  "activity_name": "Sarjana (S1) bidang TI",
  "credit_points": "100.00",
  "description": "Gelar S1 di bidang Teknologi Informasi"
}
```

---

## 📝 Activity Endpoints (Authenticated)

### 8. List User's Activities
**GET** `/activities` 🔒

**Headers**: `Authorization: Bearer {token}`

**Response** (200):
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 3,
      "schema_id": 5,
      "title": "Mengikuti Pelatihan Laravel",
      "description": "Pelatihan Laravel 12 selama 3 hari",
      "proof_file": "proofs/abc123.pdf",
      "status": "pending",
      "submitted_at": "2025-11-11T10:00:00.000000Z",
      "credit_schema": { ... },
      "latest_approval": null
    }
  ],
  "current_page": 1,
  "total": 10
}
```

---

### 9. Submit New Activity
**POST** `/activities` 🔒

**Headers**:
- `Authorization: Bearer {token}`
- `Content-Type: multipart/form-data`

**Request Body** (Form Data):
```
schema_id: 5
title: Mengikuti Pelatihan Laravel
description: Pelatihan Laravel 12 selama 3 hari
proof_file: [PDF/JPG/PNG file, max 5MB]
```

**Response** (201):
```json
{
  "message": "Activity submitted successfully",
  "activity": { ... }
}
```

---

### 10. Get Single Activity
**GET** `/activities/{id}` 🔒

**Headers**: `Authorization: Bearer {token}`

**Response** (200):
```json
{
  "id": 1,
  "title": "Mengikuti Pelatihan Laravel",
  ...
  "approvals": []
}
```

---

### 11. Update Activity
**PUT/PATCH** `/activities/{id}` 🔒

**Headers**: `Authorization: Bearer {token}`

**Note**: Only pending activities can be updated

**Request Body**:
```json
{
  "title": "Updated title",
  "description": "Updated description"
}
```

**Response** (200):
```json
{
  "message": "Activity updated successfully",
  "activity": { ... }
}
```

---

### 12. Delete Activity
**DELETE** `/activities/{id}` 🔒

**Headers**: `Authorization: Bearer {token}`

**Note**: Only pending activities can be deleted

**Response** (200):
```json
{
  "message": "Activity deleted successfully"
}
```

---

## 📊 Dashboard Endpoints (Authenticated)

### 13. Get User Statistics
**GET** `/dashboard/stats` 🔒

**Headers**: `Authorization: Bearer {token}`

**Response** (200):
```json
{
  "total_activities": 10,
  "pending": 3,
  "approved": 6,
  "rejected": 1,
  "total_points": 45.50
}
```

---

### 14. Get Summary by Category
**GET** `/dashboard/summary` 🔒

**Headers**: `Authorization: Bearer {token}`

**Response** (200):
```json
[
  {
    "category": "Pelatihan",
    "total_activities": 5,
    "approved_count": 4,
    "earned_points": 30.00
  },
  {
    "category": "Pengembangan Profesi",
    "total_activities": 3,
    "approved_count": 2,
    "earned_points": 15.50
  }
]
```

---

## ✅ Approval Endpoints (Verifier/Admin Only)

### 15. Get Pending Activities
**GET** `/approvals/pending` 🔒 👑

**Headers**: `Authorization: Bearer {token}`

**Note**: Only accessible by users with role `verifier` or `admin`

**Response** (200):
```json
{
  "data": [
    {
      "id": 1,
      "title": "Mengikuti Pelatihan Laravel",
      "status": "pending",
      "submitted_at": "2025-11-11T10:00:00.000000Z",
      "user": {
        "id": 3,
        "name": "User Biasa",
        "nip": "199003032020013003"
      },
      "credit_schema": {
        "activity_name": "Pelatihan 30-80 jam",
        "credit_points": "3.00"
      }
    }
  ]
}
```

---

### 16. Approve Activity
**POST** `/approvals/{id}/approve` 🔒 👑

**Headers**: `Authorization: Bearer {token}`

**Note**: Only accessible by users with role `verifier` or `admin`

**Request Body**:
```json
{
  "comments": "Approved. Documentation is complete."
}
```

**Response** (200):
```json
{
  "message": "Activity approved successfully",
  "activity": { ... },
  "approval": {
    "id": 1,
    "activity_id": 1,
    "verifier_id": 2,
    "status": "approved",
    "comments": "Approved. Documentation is complete.",
    "approved_at": "2025-11-11T14:30:00.000000Z"
  }
}
```

---

### 17. Reject Activity
**POST** `/approvals/{id}/reject` 🔒 👑

**Headers**: `Authorization: Bearer {token}`

**Note**: Only accessible by users with role `verifier` or `admin`

**Request Body**:
```json
{
  "comments": "Rejected. Proof file is not clear."
}
```

**Response** (200):
```json
{
  "message": "Activity rejected",
  "activity": { ... },
  "approval": { ... }
}
```

---

## 🔑 Test Users

| Email | Password | Role | NIP |
|-------|----------|------|-----|
| admin@example.com | password | admin | 199001012020011001 |
| verifier@example.com | password | verifier | 199002022020012002 |
| user@example.com | password | user | 199003032020013003 |

---

## 📌 Notes

### Authentication
- All endpoints marked with 🔒 require authentication
- Use Bearer token in Authorization header: `Authorization: Bearer {token}`
- Token is returned after successful login/register

### Roles & Permissions
- **user**: Can submit, view, edit, delete own activities
- **verifier**: Can approve/reject activities + all user permissions
- **admin**: Full access to all endpoints

### File Uploads
- Max file size: 5MB
- Accepted formats: PDF, JPG, JPEG, PNG
- Files stored in `storage/app/public/proofs/`

### Pagination
- Default: 10 items per page
- Use `?page=2` for pagination
- Use `?paginate=false` to get all items (credit schema only)

---

## 🧪 Test with cURL

```bash
# 1. Login
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# 2. Get Dashboard Stats (replace TOKEN)
curl http://localhost/api/dashboard/stats \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# 3. List Credit Schemas
curl http://localhost/api/credit-schema?paginate=false

# 4. Submit Activity (replace TOKEN)
curl -X POST http://localhost/api/activities \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -F "schema_id=1" \
  -F "title=My Activity" \
  -F "description=Activity description" \
  -F "proof_file=@/path/to/file.pdf"
```

---

**Generated**: 2025-11-11
**API Version**: 1.0
**Laravel Version**: 12.23.1
