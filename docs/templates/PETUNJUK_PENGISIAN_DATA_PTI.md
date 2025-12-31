# Petunjuk Pengisian Data Pegawai Pranata TI

**Untuk**: Bagian SDM/Kepegawaian
**Perihal**: Pengumpulan Data Pegawai Pranata Teknologi Informasi
**Sistem**: e-Kredit Pranata TI
**Tanggal**: November 2025

---

## 📋 Daftar Isi

1. [Pengantar](#pengantar)
2. [File Template yang Tersedia](#file-template-yang-tersedia)
3. [Petunjuk Pengisian](#petunjuk-pengisian)
4. [Validasi Data](#validasi-data)
5. [FAQ](#faq)
6. [Kontak](#kontak)

---

## Pengantar

Dokumen ini berisi petunjuk lengkap untuk pengisian data pegawai Pranata Teknologi Informasi (PTI) yang akan digunakan dalam sistem **e-Kredit Pranata TI**.

Sistem ini akan mengelola:
- ✅ Pencatatan aktivitas dan angka kredit PTI
- ✅ Pengajuan dan persetujuan kegiatan
- ✅ Tracking progress menuju kenaikan jenjang
- ✅ Validasi compliance sesuai PR No. 3 Tahun 2025

---

## File Template yang Tersedia

### 1️⃣ **1_Master_Pegawai.csv** ⭐ WAJIB
Template data utama pegawai PTI.

**Kolom yang tersedia:**
1. `NIP` - Nomor Induk Pegawai (18 digit)
2. `Nama Lengkap` - Nama lengkap dengan gelar
3. `Email` - Email institusi (untuk login sistem)
4. `No HP` - Nomor HP aktif (untuk notifikasi WhatsApp)
5. `Jenjang Jabatan` - Sesuai SK penetapan
6. `Golongan` - Golongan ruang saat ini
7. `Unit Kerja` - Unit/departemen tempat bertugas
8. `Pendidikan Terakhir` - Jenjang dan jurusan
9. `Tahun Lulus` - Tahun kelulusan
10. `TMT Jabatan` - Terhitung Mulai Tanggal jabatan fungsional
11. `Status Kepegawaian` - PNS / PPPK
12. `Tempat Lahir` - Tempat lahir
13. `Tanggal Lahir` - Format: YYYY-MM-DD
14. `Jenis Kelamin` - L / P
15. `Alamat` - Alamat tempat tinggal

### 2️⃣ **2_Angka_Kredit_Existing.csv** ⭐ SANGAT PENTING
Data angka kredit yang sudah dimiliki pegawai (dari PAK terakhir).

**Kolom yang tersedia:**
1. `NIP` - Harus sama dengan Sheet 1
2. `Periode Penetapan` - Contoh: 2024-I, 2023-II
3. `Total AK Unsur Utama` - Total angka kredit unsur utama
4. `Total AK Unsur Penunjang` - Total angka kredit penunjang
5. `Total AK Keseluruhan` - Total semua AK (auto-calculate)
6. `Tanggal Penetapan` - Tanggal SK PAK
7. `Nomor SK PAK` - Nomor surat keputusan
8. `Keterangan` - Catatan tambahan

### 3️⃣ **3_Riwayat_Aktivitas.csv** 🟢 OPTIONAL
Data aktivitas yang sudah pernah diajukan dan disetujui.

**Kolom yang tersedia:**
1. `NIP` - Harus sama dengan Sheet 1
2. `Nama Aktivitas` - Nama kegiatan
3. `Kategori` - Tugas Pokok / Pengembangan Profesi / dll
4. `Sub Kategori` - Detail kategori
5. `Tahun Pelaksanaan` - Tahun kegiatan
6. `Angka Kredit` - Nilai AK yang didapat
7. `Unsur` - Utama / Penunjang
8. `Tanggal Mulai` - Format: YYYY-MM-DD
9. `Tanggal Selesai` - Format: YYYY-MM-DD
10. `Bukti Dokumen` - Jenis dokumen pendukung
11. `Status` - Selesai / Dalam Proses
12. `Keterangan` - Catatan

### 4️⃣ **4_Referensi.csv** 📚 PANDUAN
Berisi daftar nilai yang valid untuk setiap field.

---

## Petunjuk Pengisian

### ✅ Langkah-langkah:

#### 1. **Buka File CSV di Microsoft Excel**
   - Klik kanan file `.csv`
   - Pilih "Open with Microsoft Excel"
   - Atau buka Excel, pilih File > Open > pilih file CSV

#### 2. **Mulai dari Sheet 1_Master_Pegawai**
   - Row 1 adalah header (jangan diubah)
   - Row 2 adalah contoh data (bisa dihapus)
   - Mulai isi data dari row 3

#### 3. **Gunakan Sheet 4_Referensi untuk Validasi**
   - Cek jenjang jabatan yang valid
   - Cek golongan yang sesuai dengan jenjang
   - Lihat mapping jenjang-golongan

#### 4. **Isi Sheet 2_Angka_Kredit_Existing**
   - Pastikan NIP sama dengan Sheet 1
   - Isi data dari PAK terakhir yang sudah ditetapkan
   - **PENTING**: Data ini akan jadi baseline sistem

#### 5. **Isi Sheet 3_Riwayat_Aktivitas (Optional)**
   - Jika punya data aktivitas historis
   - Membantu tracking yang lebih lengkap

---

## Detail Kolom - Sheet 1: Master Pegawai

### 1. **NIP** ⭐ WAJIB
- **Format**: 18 digit angka
- **Contoh**: `198501012010011001`
- **Validasi**: Harus 18 digit, unik per pegawai

### 2. **Nama Lengkap** ⭐ WAJIB
- **Format**: Teks, boleh dengan gelar
- **Contoh**: `Ahmad Sudrajat, S.Kom`, `Dr. Siti Aminah, M.T.`
- **Validasi**: Minimal 3 karakter

### 3. **Email** ⭐ WAJIB
- **Format**: Email institusi valid
- **Contoh**: `ahmad.sudrajat@uii.ac.id`
- **Validasi**: Harus format email, unique
- **Catatan**: Akan digunakan untuk login sistem

### 4. **No HP** 🟡 SANGAT DIREKOMENDASIKAN
- **Format**: Nomor telepon Indonesia
- **Contoh**: `081234567890`, `+6281234567890`
- **Validasi**: Diawali 08 atau +62
- **Catatan**: Untuk notifikasi WhatsApp

### 5. **Jenjang Jabatan** ⭐ WAJIB
- **Format**: Pilih dari daftar yang valid
- **Pilihan Jalur Terampil**:
  - `Pelaksana`
  - `Pelaksana Lanjutan`
  - `Penyelia`
- **Pilihan Jalur Ahli**:
  - `Pranata TI Pertama` (atau `Ahli Pertama`)
  - `Pranata TI Muda` (atau `Ahli Muda`)
  - `Pranata TI Madya` (atau `Ahli Madya`)
  - `Pranata TI Utama` (atau `Ahli Utama`)
- **Validasi**: Harus sesuai SK penetapan jenjang
- **Catatan**: Gunakan nama yang konsisten

### 6. **Golongan** ⭐ WAJIB
- **Format**: Angka/huruf (contoh: III/b)
- **Range Jalur Terampil**: II/b - III/d
- **Range Jalur Ahli**: III/a - IV/e
- **Validasi**: Harus sesuai dengan jenjang jabatan
- **Contoh Mapping**:
  - Pelaksana: II/b, II/c, II/d
  - Pranata TI Pertama: III/a, III/b, III/c, III/d
  - Pranata TI Muda: III/b, III/c, III/d, IV/a
  - Pranata TI Madya: IV/a, IV/b, IV/c
  - Pranata TI Utama: IV/c, IV/d, IV/e

### 7. **Unit Kerja** ⭐ WAJIB
- **Format**: Teks
- **Contoh**: `Direktorat TI`, `Pusat Data`, `Bagian Sistem Informasi`
- **Validasi**: Minimal 3 karakter

### 8. **Pendidikan Terakhir** 🟡 DIREKOMENDASIKAN
- **Format**: Jenjang + Jurusan
- **Contoh**:
  - `S1 Teknik Informatika`
  - `S2 Sistem Informasi`
  - `D3 Manajemen Informatika`
- **Validasi**: -

### 9. **Tahun Lulus** 🟢 OPTIONAL
- **Format**: 4 digit tahun
- **Contoh**: `2010`, `2015`
- **Validasi**: 1980 - tahun saat ini

### 10. **TMT Jabatan** 🟡 DIREKOMENDASIKAN
- **Format**: Tanggal (YYYY-MM-DD)
- **Contoh**: `2020-01-15`
- **Validasi**: Tanggal valid
- **Catatan**: TMT sebagai jabatan fungsional PTI

### 11. **Status Kepegawaian** 🟡 DIREKOMENDASIKAN
- **Format**: Pilihan
- **Pilihan**: `PNS` atau `PPPK`
- **Validasi**: Hanya 2 pilihan tersebut

### 12. **Tempat Lahir** 🟢 OPTIONAL
- **Format**: Teks
- **Contoh**: `Yogyakarta`, `Jakarta`

### 13. **Tanggal Lahir** 🟢 OPTIONAL
- **Format**: YYYY-MM-DD
- **Contoh**: `1985-01-01`

### 14. **Jenis Kelamin** 🟢 OPTIONAL
- **Format**: Single character
- **Pilihan**: `L` (Laki-laki) atau `P` (Perempuan)

### 15. **Alamat** 🟢 OPTIONAL
- **Format**: Teks
- **Contoh**: `Jl. Kaliurang Km 14.5, Sleman`

---

## Detail Kolom - Sheet 2: Angka Kredit Existing

### 1. **NIP** ⭐ WAJIB
- **Format**: 18 digit, sama dengan Sheet 1
- **Validasi**: Harus ada di Sheet 1

### 2. **Periode Penetapan** ⭐ WAJIB
- **Format**: YYYY-I atau YYYY-II
- **Contoh**: `2024-I`, `2023-II`
- **Keterangan**:
  - I = Periode Januari-Juni
  - II = Periode Juli-Desember

### 3. **Total AK Unsur Utama** ⭐ WAJIB
- **Format**: Angka desimal
- **Contoh**: `45.50`, `68.00`
- **Validasi**: ≥ 0
- **Catatan**: Dari PAK terakhir yang sudah ditetapkan

### 4. **Total AK Unsur Penunjang** ⭐ WAJIB
- **Format**: Angka desimal
- **Contoh**: `8.25`, `12.00`
- **Validasi**: ≥ 0

### 5. **Total AK Keseluruhan** ⭐ WAJIB
- **Format**: Angka desimal
- **Rumus**: Unsur Utama + Unsur Penunjang
- **Validasi**: Harus = kolom 3 + kolom 4

### 6. **Tanggal Penetapan** 🟡 DIREKOMENDASIKAN
- **Format**: YYYY-MM-DD
- **Contoh**: `2024-06-15`
- **Catatan**: Tanggal SK PAK

### 7. **Nomor SK PAK** 🟢 OPTIONAL
- **Format**: Teks
- **Contoh**: `123/SK/BKN/2024`

### 8. **Keterangan** 🟢 OPTIONAL
- **Format**: Teks bebas
- **Contoh**: `PAK terakhir periode I 2024`

---

## Validasi Data

### ✅ Validasi Otomatis yang Akan Dilakukan Sistem:

1. **Format NIP**: Harus 18 digit angka
2. **Format Email**: Harus valid email format
3. **Uniqueness**: NIP dan Email harus unik
4. **Mapping Jenjang-Golongan**: Sesuai regulasi
5. **Compliance 80/20**:
   - Unsur Utama ≥ 80% dari total
   - Unsur Penunjang ≤ 20% dari total
6. **Data Relationship**: NIP di Sheet 2 & 3 harus ada di Sheet 1

### ⚠️ Warning yang Akan Muncul:

- Email bukan email institusi
- No HP tidak valid
- Golongan tidak sesuai jenjang
- AK tidak comply 80/20 rule
- Tanggal tidak valid

---

## FAQ

### ❓ Berapa minimal data yang harus dikumpulkan?

**Minimal untuk MVP**:
- ✅ Sheet 1: Master_Pegawai (kolom wajib saja)
- ✅ Sheet 2: Angka_Kredit_Existing (minimal Total AK)

Sheet 3 (Riwayat Aktivitas) optional, tapi sangat membantu.

### ❓ Bagaimana jika pegawai belum punya PAK?

Untuk pegawai baru:
- Isi Total AK = 0
- Periode Penetapan = `-` (kosong)
- System akan mulai tracking dari 0

### ❓ Format tanggal yang benar?

Gunakan format **YYYY-MM-DD**:
- ✅ Benar: `2024-06-15`
- ❌ Salah: `15/06/2024`, `15-06-2024`

### ❓ Bagaimana dengan pegawai yang pindah unit?

Isi dengan unit kerja **saat ini** (yang aktif sekarang).

### ❓ Jenjang jabatan menggunakan nama yang mana?

Gunakan salah satu secara konsisten:
- **Opsi 1**: Pranata TI Pertama, Pranata TI Muda, dst
- **Opsi 2**: Ahli Pertama, Ahli Muda, dst

Sistem akan recognize keduanya.

### ❓ Bagaimana jika ada data yang tidak tersedia?

- **Kolom WAJIB** (⭐): Harus diisi
- **Kolom DIREKOMENDASIKAN** (🟡): Usahakan diisi
- **Kolom OPTIONAL** (🟢): Boleh dikosongkan

### ❓ Apakah bisa update data setelah import?

Ya, data bisa diupdate melalui:
1. Admin dashboard
2. Re-import dengan data baru (akan update)
3. User bisa update sebagian data profil sendiri

### ❓ Data apa yang sensitif dan perlu proteksi?

Data yang akan diproteksi sistem:
- Password (di-hash)
- Data pribadi (alamat, tanggal lahir) - hanya visible untuk admin
- NIP bisa dilihat semua user

### ❓ Berapa lama proses import data?

Estimasi:
- < 50 pegawai: 5-10 menit
- 50-100 pegawai: 10-20 menit
- > 100 pegawai: 20-30 menit

Includes validation dan error checking.

---

## Checklist Sebelum Submit

Sebelum mengirim data ke Tim IT, pastikan:

- [ ] Semua kolom WAJIB (⭐) sudah terisi
- [ ] Format NIP benar (18 digit)
- [ ] Format email valid
- [ ] Golongan sesuai dengan jenjang jabatan
- [ ] Data AK ada untuk pegawai yang sudah punya PAK
- [ ] Tidak ada data duplikat (NIP sama)
- [ ] Format tanggal konsisten (YYYY-MM-DD)
- [ ] File sudah di-save dengan nama yang jelas
- [ ] Sudah koordinasi dengan atasan untuk validasi data

---

## Kontak

### 📞 Jika Ada Pertanyaan:

**Tim IT / Pengembang Sistem**:
- Email: [email_tim_it@uii.ac.id]
- WhatsApp: [nomor_whatsapp]
- Extension: [nomor_extension]

**Koordinator Project**:
- Nama: [nama_koordinator]
- Email: [email]
- HP: [nomor_hp]

### 📅 Timeline Pengumpulan Data:

- **Deadline**: [tentukan tanggal]
- **Format Pengiriman**: Email file Excel ke [email]
- **Subject Email**: `[Data PTI] Nama Unit Kerja`

---

## Lampiran

### Template Email Pengiriman:

```
Kepada: [email_tim_it@uii.ac.id]
Subject: [Data PTI] [Nama Unit Kerja Anda]

Yth. Tim Pengembang e-Kredit Pranata TI,

Terlampir data pegawai Pranata TI dari [nama unit kerja]:

- Jumlah pegawai: [X] orang
- File:
  - 1_Master_Pegawai.csv
  - 2_Angka_Kredit_Existing.csv
  - 3_Riwayat_Aktivitas.csv (jika ada)

Kelengkapan data:
- Data master: [lengkap/sebagian]
- Data AK existing: [ada/tidak ada]
- Data historis: [ada/tidak ada]

Catatan khusus: [jika ada]

Demikian kami sampaikan. Terima kasih.

Hormat kami,
[Nama]
[Jabatan]
[Unit Kerja]
```

---

**Terima kasih atas kerjasamanya!**

*Dokumen ini dibuat pada November 2025*
*Version 1.0*
