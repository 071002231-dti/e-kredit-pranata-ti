# 🚀 Cara Penggunaan Template Data PTI

**Quick Start Guide untuk Mengumpulkan Data dari SDM**

---

## 📦 Apa yang Ada di Folder Ini?

Folder `docs/templates/` berisi **8 file** yang siap digunakan:

### ✅ File Template (Siap Dikirim ke SDM)
1. `1_Master_Pegawai.csv` - Template data pegawai utama
2. `2_Angka_Kredit_Existing.csv` - Template data angka kredit
3. `3_Riwayat_Aktivitas.csv` - Template aktivitas historis
4. `4_Referensi.csv` - Daftar nilai yang valid
5. `CONTOH_Data_Lengkap.csv` - Contoh data sudah terisi (10 pegawai)

### 📚 File Dokumentasi (Panduan untuk SDM)
6. `PETUNJUK_PENGISIAN_DATA_PTI.md` - Panduan detail pengisian
7. `SURAT_PERMINTAAN_DATA_SDM.md` - Template surat resmi
8. `README.md` - Overview lengkap
9. `00_CARA_PENGGUNAAN.md` - File ini

---

## 🎯 Langkah-Langkah Cepat

### Opsi 1: Kirim Email Langsung (Paling Mudah)

#### Step 1: Siapkan Email
```
To: [email_sdm@institusi.ac.id]
Subject: Permintaan Data Pegawai PTI untuk Sistem e-Kredit

Lampiran:
- 1_Master_Pegawai.csv
- 2_Angka_Kredit_Existing.csv
- 3_Riwayat_Aktivitas.csv (optional)
- 4_Referensi.csv
- PETUNJUK_PENGISIAN_DATA_PTI.md (PDF/Word)
- CONTOH_Data_Lengkap.csv
```

#### Step 2: Gunakan Template Email Ini
```
Yth. Bagian SDM/Kepegawaian,

Dalam rangka implementasi Sistem e-Kredit Pranata TI, kami memohon
bantuan untuk mengisi data pegawai PTI.

Terlampir:
1. File template CSV (4 file) - mohon diisi
2. Panduan pengisian lengkap
3. Contoh data terisi (sebagai referensi)

Data yang diperlukan:
✅ Data master pegawai PTI (WAJIB)
✅ Data angka kredit yang sudah ada (WAJIB)
🟢 Riwayat aktivitas (optional)

Deadline: [tentukan tanggal - misal 2 minggu dari sekarang]

Jika ada pertanyaan, silakan hubungi:
- Nama: [nama Anda]
- Email: [email]
- HP: [nomor HP]

Terima kasih atas kerjasamanya.

Hormat kami,
[Nama & Jabatan]
```

---

### Opsi 2: Kirim Surat Resmi (Lebih Formal)

#### Step 1: Customize Surat
1. Buka file `SURAT_PERMINTAAN_DATA_SDM.md`
2. Isi bagian yang masih kosong:
   - [Nomor Surat]
   - [Tanggal]
   - [Nama Penandatangan]
   - [Deadline]
   - [Kontak Person]
3. Convert ke Word/PDF jika perlu

#### Step 2: Lampirkan Template
- Zip semua file template CSV
- Atau copy ke USB/folder sharing

#### Step 3: Kirim Resmi
- Via surat internal
- Atau email dengan attachment surat resmi

---

## 📊 Apa yang Harus Diminta ke SDM?

### Data WAJIB (Minimum):
```
✅ NIP (18 digit)
✅ Nama lengkap
✅ Email institusi
✅ Jenjang jabatan saat ini
✅ Golongan ruang
✅ Unit kerja
✅ Total angka kredit (unsur utama + penunjang)
```

### Data SANGAT DIREKOMENDASIKAN:
```
🟡 No HP (untuk WhatsApp notification)
🟡 Pendidikan terakhir
🟡 TMT jabatan fungsional
🟡 Detail AK per periode penetapan
```

### Data NICE TO HAVE:
```
🟢 Riwayat aktivitas yang sudah dapat AK
🟢 Nomor SK PAK
🟢 Data pribadi (tempat/tanggal lahir, alamat)
```

---

## 💡 Tips untuk Koordinasi dengan SDM

### 1️⃣ Jelaskan Tujuan
SDM perlu tahu kenapa data ini penting:
- ✅ Untuk sistem tracking angka kredit
- ✅ Compliance dengan PR No. 3 Tahun 2025
- ✅ Memudahkan PTI kelola karir
- ✅ Transparansi progress kenaikan jenjang

### 2️⃣ Berikan Contoh Konkrit
- Kirim `CONTOH_Data_Lengkap.csv`
- Jelaskan format yang diharapkan
- Tunjukkan file `4_Referensi.csv` untuk nilai yang valid

### 3️⃣ Tawarkan Bantuan
- Siap membantu jika ada kesulitan pengisian
- Bisa bantu validasi data sebelum submit
- Bisa koordinasi langsung via telp/WA

### 4️⃣ Set Ekspektasi Realistis
- Deadline 2-3 minggu (bukan besok!)
- Boleh kirim bertahap (Master dulu, detail menyusul)
- Data bisa diupdate kemudian

### 5️⃣ Konfirmasi Penerimaan
- Setelah terima data, konfirmasi via email
- Informasikan jika ada data yang kurang
- Apresiasi kerjasamanya

---

## 🔍 Checklist Setelah Terima Data dari SDM

Saat data sudah diterima, lakukan pengecekan:

### Format Check:
- [ ] File format CSV atau Excel
- [ ] Encoding UTF-8 (tidak ada karakter aneh)
- [ ] Struktur kolom sesuai template
- [ ] Header ada di row 1

### Data Completeness:
- [ ] Semua kolom WAJIB terisi
- [ ] Jumlah pegawai sesuai ekspektasi
- [ ] Data AK ada (atau 0 untuk pegawai baru)
- [ ] Tidak ada row kosong di tengah

### Data Quality:
- [ ] NIP 18 digit dan valid
- [ ] Email format benar
- [ ] Jenjang-golongan sesuai mapping
- [ ] Tidak ada duplikasi (NIP/Email)
- [ ] Tanggal format YYYY-MM-DD

### Data Relationship:
- [ ] NIP di Sheet 2 exist di Sheet 1
- [ ] NIP di Sheet 3 exist di Sheet 1
- [ ] Total AK = Unsur Utama + Penunjang

---

## 🛠️ Import Data ke Sistem (Untuk Developer)

Setelah data valid, import ke database:

### Manual Import (Sementara):
```bash
# 1. Copy file ke server
scp *.csv user@server:/path/to/import/

# 2. Run import script (akan dibuat)
php artisan import:pegawai /path/to/1_Master_Pegawai.csv
php artisan import:credits /path/to/2_Angka_Kredit_Existing.csv
php artisan import:activities /path/to/3_Riwayat_Aktivitas.csv
```

### Automated Import (Future):
- Web interface untuk upload Excel
- Preview data sebelum commit
- Validation dan error reporting
- Rollback jika ada error

---

## ⚠️ Troubleshooting Umum

### Problem 1: SDM Tidak Punya Data AK Detail
**Solusi**:
- Minta minimal total AK saja
- Breakdown per unsur bisa ditambahkan nanti
- Atau set AK = 0 untuk pegawai yang belum punya PAK

### Problem 2: Format Data Berbeda dari Template
**Solusi**:
- Minta sesuai template
- Atau develop script konversi khusus
- Atau manual adjust di Excel

### Problem 3: Data Tidak Lengkap
**Solusi**:
- Import yang ada dulu
- Field kosong bisa diisi manual via admin dashboard
- Atau minta update incremental

### Problem 4: SDM Tidak Familiar dengan CSV
**Solusi**:
- Ajarkan cara buka di Excel
- Atau terima dalam format Excel (.xlsx)
- Convert Excel → CSV saat import

### Problem 5: Data Sensitif (Privacy Concern)
**Solusi**:
- Jelaskan security measures sistem
- Tanda tangan perjanjian kerahasiaan data
- Hapus field yang terlalu sensitif (gaji, dll)

---

## 📞 FAQ untuk SDM

### Q: Berapa lama proses pengisian?
**A**: Tergantung jumlah pegawai:
- 1-20 pegawai: ~2 jam
- 21-50 pegawai: ~4 jam
- 51-100 pegawai: ~1 hari
- >100 pegawai: ~2-3 hari

### Q: Apakah data bisa diupdate nanti?
**A**: Ya! Data bisa:
- Update via admin dashboard
- Re-import dengan data baru
- Edit manual per pegawai

### Q: Bagaimana dengan pegawai baru?
**A**: Pegawai baru bisa:
- Ditambahkan kemudian via dashboard
- Included di batch import berikutnya
- Self-register via sistem (jika diaktifkan)

### Q: Data apa yang paling penting?
**A**: Prioritas:
1. NIP, Nama, Email (untuk login)
2. Jenjang & Golongan (untuk validasi)
3. Total AK (untuk baseline tracking)
4. Sisanya boleh menyusul

### Q: Apakah ada bantuan pengisian?
**A**: Ya! Tim IT bisa:
- Video call untuk demo
- On-site support
- Review data sebelum submit

---

## 📈 Next Steps Setelah Data Terkumpul

1. **Validasi Data** (1-2 hari)
   - Check format dan kelengkapan
   - Report error ke SDM untuk perbaikan

2. **Import ke Database** (1 hari)
   - Bulk import semua data
   - Generate user accounts
   - Assign passwords (kirim via email)

3. **Testing** (2-3 hari)
   - Login test untuk sample users
   - Check data display di dashboard
   - Verify calculations

4. **User Training** (1 minggu)
   - Demo sistem ke PTI
   - Panduan penggunaan
   - Q&A session

5. **Go Live!** 🚀
   - Announcement ke semua PTI
   - Monitor usage
   - Collect feedback

---

## 📝 Template Komunikasi

### Email Follow-up (1 minggu setelah request):
```
Subject: Follow-up: Permintaan Data PTI

Yth. Bapak/Ibu [Nama],

Menindaklanjuti permintaan data pegawai PTI minggu lalu,
kami ingin mengkonfirmasi:

1. Apakah ada kesulitan dalam pengisian template?
2. Apakah perlu bantuan dari tim IT?
3. Estimasi kapan data bisa diserahkan?

Kami siap membantu jika ada kendala.

Terima kasih,
[Nama]
```

### Email Terima Kasih (setelah terima data):
```
Subject: Terima Kasih - Data PTI Diterima

Yth. Bapak/Ibu [Nama],

Data pegawai PTI telah kami terima dengan baik.

Summary:
- Jumlah pegawai: [X] orang
- Kelengkapan: [%]
- Status: [Lengkap/Perlu Perbaikan]

[Jika perlu perbaikan:]
Mohon melengkapi data berikut: [list]

Langkah selanjutnya:
- Import data: [tanggal]
- Testing: [tanggal]
- Go live: [tanggal]

Terima kasih atas kerjasamanya!

Hormat kami,
[Nama]
```

---

## ✅ Summary Checklist

**Untuk mengirim request ke SDM:**

- [ ] Customize surat atau email template
- [ ] Attach semua file CSV template
- [ ] Include panduan pengisian
- [ ] Include contoh data
- [ ] Set deadline yang realistis
- [ ] Berikan kontak person
- [ ] Send!

**Untuk follow-up:**

- [ ] Tawarkan bantuan pengisian
- [ ] Reminder 1 minggu sebelum deadline
- [ ] Koordinasi jika ada kesulitan
- [ ] Terima dan validasi data
- [ ] Konfirmasi penerimaan
- [ ] Import ke sistem

---

## 🎯 Target Timeline

```
Week 1: Kirim request ke SDM
Week 2-3: SDM mengisi data
Week 3: Review & validasi data
Week 4: Import & testing
Week 5: User training
Week 6: Go live!
```

---

**Sukses untuk pengumpulan datanya! 🚀**

Jika ada pertanyaan atau butuh bantuan, jangan ragu untuk kontak tim IT.

---

*Dibuat: November 2025*
*Version: 1.0*
