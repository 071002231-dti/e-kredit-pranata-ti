# e-Kredit Pranata TI - Web Client

Frontend web application untuk manajemen angka kredit Jabatan Fungsional Pranata Teknologi Informasi.

> **Note**: Ini adalah bagian dari project e-Kredit Pranata TI monorepo. Backend API dan WhatsApp integration berada di folder `../backend/`

## 🚀 Quick Start

### 1. Start Backend (dari root project)

```bash
cd ../backend
./vendor/bin/sail up -d
```

Backend akan running di: `http://localhost/api`

### 2. Start Web Client (dari folder ini)

```bash
npm run dev
```

### 3. Akses Aplikasi

Buka browser: **http://localhost:5173/**

## 🔑 Test Users

| Role | Email | Password |
|------|-------|----------|
| **User** | user@example.com | password |
| **Verifier** | verifier@example.com | password |
| **Admin** | admin@example.com | password |

## ✅ Checklist Testing

### 1. Login & Dashboard
- [ ] Buka http://localhost:5173
- [ ] Login dengan: user@example.com / password
- [ ] Lihat dashboard dengan statistik
- [ ] Cek compliance status (Unsur Utama & Penunjang)

### 2. Tambah Aktivitas
- [ ] Klik "Tambah Aktivitas"
- [ ] Pilih jenis aktivitas dari dropdown
- [ ] Isi judul dan deskripsi
- [ ] Upload file (opsional)
- [ ] Submit

### 3. List Aktivitas
- [ ] Lihat daftar aktivitas
- [ ] Test filter by status
- [ ] Test search
- [ ] Delete aktivitas pending

### 4. Mobile Responsive
- [ ] Resize browser ke mobile
- [ ] Test hamburger menu
- [ ] Test navigation

## 🎨 Features Ready

✅ Login & Register
✅ Dashboard dengan stats & compliance
✅ Submit aktivitas dengan file upload
✅ List & filter aktivitas
✅ Responsive mobile design
✅ Protected routes
✅ Real-time data sync dengan WhatsApp

## 📁 Environment

File `.env`:
```
VITE_API_URL=http://localhost/api
VITE_APP_NAME=e-Kredit Pranata TI
```

---

**Version**: 1.0.0 | **Tech**: React 19 + TypeScript + Tailwind CSS + Laravel API
