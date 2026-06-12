# Audit & Roadmap Modernisasi Portal FTI

## Context

Folder `/Users/4h3/myproject/BackupVPS/www/html` adalah backup VPS yang memuat **±25 aplikasi dalam satu portal** (mayoritas Laravel, ada CodeIgniter, satu app SPA Vue, dan komponen Go untuk cron). Tujuan user: menemukan **apa yang sebaiknya dikembangkan & di-update**, baik dari sisi **keamanan** maupun **tampilan/UI**.

Deliverable yang diminta: **dokumen audit + roadmap menyeluruh, semua app diperlakukan setara**. Dokumen ini **tidak mengubah kode** — hanya pemetaan temuan, prioritas, dan rencana modernisasi bertahap.

> Catatan penting: ini adalah **backup**, bukan repo kerja. Banyak folder hanya berisi artefak deploy (`vendor/`, `public/`, `storage/`) tanpa source code (`app/`, `composer.json`, `.env` sudah di-strip). Maka eksekusi nyata (upgrade, edit) harus dilakukan di **repo asli / server**, bukan di folder backup ini. Dokumen ini memetakan kondisi agar tim tahu di mana harus bekerja.

---

## 1. Inventaris Aplikasi & Versi Framework

| App | Stack | Versi | Status Dukungan | Kelengkapan di backup |
|-----|-------|-------|-----------------|------------------------|
| fti-gateway | Laravel | 6.20.45 | **EOL** (sejak 2022) | artefak deploy |
| fti-presensi | Laravel | 6.20.44 | **EOL** | artefak deploy |
| fti-presensi-ip | Laravel | 6.20.44 | **EOL** | artefak deploy |
| portodosen | Laravel | 6.18.3 | **EOL** | artefak deploy |
| fti-surat | Laravel | 7.7.1 | **EOL** (sejak 2021) | **source lengkap** (ada `app/`) |
| fti-kp | Laravel | 8.1.0 | **EOL** | artefak + **dump DB** |
| fti-kp-staging | Laravel | 8.1.0 | **EOL** | artefak + **dump DB** |
| fti-siso | Laravel | 8.83.27 | **EOL** | artefak deploy |
| fti-sk | Laravel | 8.83.27 | **EOL** | artefak deploy |
| fti-ta | Laravel | 8.83.23 | **EOL** | artefak deploy |
| fti-ta-staging | Laravel | 8.83.23 | **EOL** | artefak deploy |
| disertasi | Laravel | 9.52.16 | **EOL** (sejak Feb 2024) | source + node_modules |
| sekawan | Laravel | 9.52.16 | **EOL** | **source lengkap** |
| sekawan-staging | Laravel | 9.52.16 | **EOL** | artefak + node_modules |
| fti-kp-v2 | Laravel | 9.52.4 | **EOL** | artefak + node_modules |
| fti-kp-v2-staging | Laravel | 9.52.4 | **EOL** | artefak + node_modules |
| fti-nkmd-v2 | Laravel | 9.52.21 | **EOL** | artefak deploy |
| email-notification | Laravel | 10.48.4 | EOL (security s/d Feb 2025) | artefak deploy |
| email-notification-staging | Laravel | 10.48.4 | EOL | artefak deploy |
| fti-kinerja | Laravel | 10.48.12 | EOL | artefak deploy |
| fti-rapat | Laravel (BE) + **Vue 3 + Vite** (FE) | BE legacy (php ^7.1‖^8.0) | perlu cek | be/fe terpisah, FE node_modules |
| fti-nkmd | **CodeIgniter** (legacy MVC) | ~CI3 | **EOL berat** | hanya `application/` (tanpa `system/`) |
| fti-ruang | **CodeIgniter** (legacy MVC) | ~CI3 | **EOL berat** | hanya `application/` |
| fti-dashboard-dosen | Laravel (stub) | ? | — | stub deploy 52K |
| mcp-config | — | — | — | **kosong (0B)** |
| mcp-data | — | — | — | **kosong (0B)** |
| go/jobs | Go (cron worker) | — | — | hanya folder `logs/` |
| test | composer stub | — | — | stub |

**Kesimpulan inventaris:** **TIDAK ADA satu pun app pada versi Laravel yang masih didukung aktif** (11.x/12.x). Semua berada di rentang EOL 6–10. Dua app berbasis CodeIgniter legacy. Ini adalah temuan paling fundamental.

---

## 2. Temuan Keamanan (lintas app)

### Kritis
1. **Framework EOL menyeluruh** — semua app tidak lagi menerima patch keamanan. Laravel 6/7/8/9 sudah berhenti total; 10.x berhenti ~Feb 2025. CodeIgniter 3 EOL. Setiap CVE baru pada framework/dependency tidak akan tertambal.
2. **Dump database ter-commit di app root** — `laravel/fti-kp/db.sql`, `db2.sql`, dan `laravel/fti-kp-staging/db.sql`, `db2.sql`. Jika file ini ikut ter-deploy ke dokumen-root web atau ter-push ke repo, ini kebocoran data (skema + kemungkinan data/akun). **Harus dihapus dari source & web root, dan dirotasi kredensial bila pernah publik.**
3. **CodeIgniter tanpa folder `system/`** (`fti-nkmd`, `fti-ruang`) — versi core tidak terverifikasi; CI3 punya beberapa CVE (mis. SQLi/session). Perlu konfirmasi versi & patch di server asli.

### Tinggi / Menengah
4. **File backup tertinggal** — `fti-gateway/resources/views/home.blade.php.backup`. Bersihkan artefak `.backup`/`.bak`/`.old` agar tidak ter-serve sebagai source.
5. **Dependency lama** — composer.lock tiap app menunjuk paket pihak-ketiga lawas (mis. `fti-rapat/be` platform `php ^7.1‖^8.0`). Perlu audit `composer audit` / scanner CVE per app di repo asli.
6. **Konsistensi config keamanan belum terverifikasi** dari backup (`.env` di-strip): perlu cek di server — `APP_DEBUG=false` di produksi, `APP_KEY` unik per app, HTTPS-only cookie, header keamanan (CSP/HSTS), rate-limit login.

### Yang perlu diverifikasi di repo/server asli (tidak bisa dari backup)
- Paparan `.git/` di web root, permission `storage/`, validasi upload file, proteksi CSRF/XSS pada form lama, otorisasi antar-modul.

---

## 3. Temuan Tampilan / UI

- **Tidak ada design system bersama.** Tiap app kemungkinan punya tema/komponen sendiri (Blade + Bootstrap/jQuery untuk app lama). Akibatnya UX antar-app dalam satu portal tidak konsisten (navigasi, warna, tipografi, komponen form).
- **App lama (Laravel 6–8, CodeIgniter)** kemungkinan besar Blade/HTML server-rendered + Bootstrap 3/4 + jQuery — gaya lawas, belum responsif optimal, aksesibilitas rendah.
- **`fti-rapat`** sudah modern (Vue 3 + Vite) — bisa jadi **acuan/template** arah frontend ke depan.
- Beberapa app punya `node_modules` (sekawan, disertasi, kp-v2) → sudah pakai build tool (Vite/Mix), modal untuk standardisasi komponen.
- **Peluang:** satu **portal shell + design tokens bersama** (warna, logo FTI, header/nav, komponen form & tabel) yang dipakai ulang lintas app akan langsung menyeragamkan tampilan dan menurunkan biaya maintenance.

---

## 4. Roadmap Modernisasi (bertahap, semua app setara dulu lalu diprioritaskan oleh risiko)

### Fase 0 — Quick wins keamanan (segera, low effort)
- Hapus `db.sql`/`db2.sql` dari `fti-kp` & `fti-kp-staging` (source + web root); rotasi kredensial DB jika pernah ter-expose.
- Hapus file `*.backup`/`*.bak` (mis. `fti-gateway/.../home.blade.php.backup`).
- Pastikan dump DB & `.env` masuk `.gitignore` di semua repo.
- Verifikasi di server: `APP_DEBUG=false`, web root menunjuk ke `public/`, `.git/` tidak ter-serve.

### Fase 1 — Audit terukur per app (di repo asli)
- Jalankan `composer audit` + scanner CVE (mis. Enlightn / Roave SecurityAdvisories) tiap app.
- Inventaris dependency frontend (`npm audit`).
- Konfirmasi versi CodeIgniter `fti-nkmd`/`fti-ruang` dan kebutuhan migrasi.
- Hasil: matriks risiko (CVE × dampak × traffic) untuk menentukan urutan.

### Fase 2 — Upgrade framework bertahap (high effort, prioritas by risiko hasil Fase 1)
- Target jangka pendek: angkat tiap app minimal ke **versi LTS/aktif terdekat** lalu menuju **Laravel 11/12 + PHP 8.3**.
- Urutan disarankan by risiko: Laravel 6.x & CodeIgniter dulu (`fti-gateway`, `fti-presensi`, `portodosen`, `fti-nkmd`, `fti-ruang`), lalu 7/8, lalu 9/10.
- Gunakan Laravel Shift / upgrade guide bertahap; tulis test smoke per modul sebelum upgrade.
- Konsolidasikan app duplikat/`-staging` agar tidak menggandakan beban upgrade.

### Fase 3 — Standardisasi UI / design system
- Bangun **paket UI bersama** (design tokens + komponen Blade/Vue): header portal, nav, tema, komponen form & tabel, state kosong/loading.
- Jadikan `fti-rapat` (Vue 3 + Vite) acuan stack frontend modern.
- Terapkan bertahap mulai app dengan traffic tertinggi; sisanya menyusul saat di-upgrade di Fase 2.

### Fase 4 — Housekeeping portal
- Hapus/arsipkan folder kosong & stub (`mcp-config`, `mcp-data`, `test`, `fti-dashboard-dosen` jika tak terpakai).
- Dokumentasikan komponen Go (`go/jobs`) — apa yang dijalankan, jadwal, dan kepemilikan.
- Susun satu README portal: daftar app, versi, repo asli, pemilik, status.

---

## 5. Verifikasi / Cara Memakai Dokumen Ini

Karena ini backup read-only, "verifikasi" = mengonfirmasi temuan terhadap **repo/server asli**:
1. Untuk tiap app di tabel §1, buka repo aslinya dan cek versi (`composer show laravel/framework`) — pastikan cocok dengan backup.
2. Jalankan `composer audit` & `npm audit` untuk mengisi matriks CVE Fase 1.
3. Cek server: `APP_DEBUG`, web root, paparan `.git/`/`*.sql`/`*.backup`.
4. Konfirmasi mana app yang masih produksi aktif vs ditinggalkan (menentukan apakah di-upgrade atau di-decommission).

Output akhir yang diharapkan setelah dokumen ini dijalankan: **matriks risiko per app + urutan upgrade + rencana design system**, yang menjadi backlog kerja modernisasi.
