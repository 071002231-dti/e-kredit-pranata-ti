# Panduan Cepat — E-Kredit Pranata TI

Aplikasi perhitungan angka kredit jabatan fungsional Pranata TI. Terdiri dari backend Laravel (SQLite) dan frontend React/Vite.

---

## Prasyarat

- PHP 8.2+ dengan ekstensi `pdo`, `pdo_sqlite`, `mbstring`, `openssl`, `xml`, `ctype`
- Composer 2+
- Node.js 20+ dan npm

Cek versi:
```bash
php -v && composer --version && node -v && npm -v
```

---

## 1. Jalankan Backend

```bash
cd e-kredit-pranata-ti/backend

# Install dependensi (hanya pertama kali atau setelah update)
composer install

# Salin konfigurasi environment
cp .env.example .env

# Generate app key
php artisan key:generate

# Buat database SQLite dan jalankan migrasi
touch database/database.sqlite
php artisan migrate

# Isi data awal (akun pengguna)
php artisan db:seed

# Jalankan server
php artisan serve
```

Server berjalan di **http://localhost:8000**

> Biarkan terminal ini tetap terbuka.

---

## 2. Jalankan Frontend

Buka terminal baru:

```bash
cd e-kredit-pranata-ti/web-client

# Install dependensi (hanya pertama kali)
npm install

# Jalankan dev server
npm run dev
```

Frontend berjalan di **http://localhost:5173**

> `VITE_API_URL` otomatis mengarah ke `http://localhost:8000/api` saat mode development — tidak perlu konfigurasi tambahan.

---

## 3. Buka di Browser

Buka: **http://localhost:5173**

---

## 4. Akun Demo

Semua akun dibuat oleh seeder dengan password default `password`:

| Email | Role | Jabatan |
|-------|------|---------|
| `admin@example.com` | admin | Kepala Bidang IT (Pranata TI Utama) |
| `verifier@example.com` | verifier | Pranata TI Ahli Madya |
| `user@example.com` | user | Pranata TI Ahli Muda |
| `pertama@example.com` | user | Pranata TI Ahli Pertama |
| `pelaksana@example.com` | user | Pranata TI Pelaksana |

Login dengan salah satu akun di atas, lalu masukkan password `password`.

---

## 5. Fitur Utama

Setelah login, jelajahi:

| Menu | Deskripsi |
|------|-----------|
| **Dashboard** | Ringkasan angka kredit dan status pengajuan |
| **Daftar Aktivitas** | Riwayat kegiatan yang telah diinput |
| **Input Aktivitas Baru** | Form pengisian butir kegiatan angka kredit |
| **Approval** *(login verifier/admin)* | Menyetujui atau menolak pengajuan |
| **Kelola Skema** *(login admin)* | Manajemen skema penilaian |
| **Kelola Pengguna** *(login admin)* | Manajemen akun pengguna |

Alur utama:
1. Login sebagai `user@example.com` → input aktivitas baru
2. Login sebagai `verifier@example.com` → approve aktivitas
3. Login sebagai `admin@example.com` → lihat ringkasan dan kelola skema

---

## Catatan

- Database SQLite disimpan di `e-kredit-pranata-ti/backend/database/database.sqlite`
- Untuk reset data: `php artisan migrate:fresh --seed`
- Log error ada di `e-kredit-pranata-ti/backend/storage/logs/laravel.log`
- Laporan angka kredit: [`credit_point/e_kredit/LAPORAN_RESMI_PENGAJUAN_ANGKA_KREDIT.md`](../../credit_point/e_kredit/LAPORAN_RESMI_PENGAJUAN_ANGKA_KREDIT.md)
