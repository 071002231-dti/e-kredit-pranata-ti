# 📊 Template Data Pegawai PTI

**Direktori ini berisi template untuk pengumpulan data pegawai Pranata Teknologi Informasi**

---

## 📁 Daftar File

### 1. **Template CSV (Ready to Use)** ✅

| File | Deskripsi | Status |
|------|-----------|--------|
| `1_Master_Pegawai.csv` | Template data utama pegawai | ⭐ WAJIB |
| `2_Angka_Kredit_Existing.csv` | Template data AK yang sudah ada | ⭐ WAJIB |
| `3_Riwayat_Aktivitas.csv` | Template data aktivitas historis | 🟢 Optional |
| `4_Referensi.csv` | Daftar nilai valid untuk validasi | 📚 Referensi |

### 2. **Dokumentasi** 📚

| File | Deskripsi |
|------|-----------|
| `PETUNJUK_PENGISIAN_DATA_PTI.md` | Panduan lengkap pengisian data |
| `SURAT_PERMINTAAN_DATA_SDM.md` | Template surat resmi ke SDM |
| `README.md` | File ini |

---

## 🚀 Quick Start

### Untuk SDM/Bagian Kepegawaian:

1. **Download semua file CSV**
   ```bash
   # File yang harus diisi:
   - 1_Master_Pegawai.csv
   - 2_Angka_Kredit_Existing.csv
   - 3_Riwayat_Aktivitas.csv (optional)
   ```

2. **Buka dengan Microsoft Excel**
   - Klik kanan file `.csv`
   - Pilih "Open with Microsoft Excel"
   - Atau import ke Excel: Data > From Text/CSV

3. **Isi Data**
   - Lihat file `4_Referensi.csv` untuk nilai yang valid
   - Baca `PETUNJUK_PENGISIAN_DATA_PTI.md` untuk detail
   - Gunakan contoh data di row 2 sebagai referensi

4. **Save dan Kirim**
   - Save as Excel format (.xlsx) atau tetap .csv
   - Kirim ke email yang ditentukan

### Untuk Tim IT/Pengembang:

1. **Kirim Template ke SDM**
   - Gunakan `SURAT_PERMINTAAN_DATA_SDM.md` sebagai draft surat resmi
   - Attach semua file CSV dan dokumentasi
   - Set deadline yang reasonable

2. **Terima Data dari SDM**
   - Validasi format file
   - Check kelengkapan data wajib
   - Run validation script (akan dibuat)

3. **Import ke Database**
   - Gunakan import service (akan dibuat)
   - Preview data sebelum commit
   - Generate error report jika ada

---

## 📊 Struktur Data

### Sheet 1: Master_Pegawai (15 kolom)

**Kolom Wajib** (⭐):
- NIP (18 digit)
- Nama Lengkap
- Email
- Jenjang Jabatan
- Golongan
- Unit Kerja

**Kolom Direkomendasikan** (🟡):
- No HP
- Pendidikan Terakhir
- TMT Jabatan
- Status Kepegawaian

**Kolom Optional** (🟢):
- Tempat Lahir
- Tanggal Lahir
- Jenis Kelamin
- Alamat
- Tahun Lulus

### Sheet 2: Angka_Kredit_Existing (8 kolom)

**Semua kolom penting untuk baseline AK**:
- NIP (FK to Sheet 1)
- Periode Penetapan
- Total AK Unsur Utama
- Total AK Unsur Penunjang
- Total AK Keseluruhan
- Tanggal Penetapan
- Nomor SK PAK
- Keterangan

### Sheet 3: Riwayat_Aktivitas (12 kolom)

**Optional tapi sangat membantu untuk tracking**:
- NIP
- Nama Aktivitas
- Kategori & Sub Kategori
- Tahun Pelaksanaan
- Angka Kredit
- Unsur (Utama/Penunjang)
- Tanggal Mulai & Selesai
- Bukti Dokumen
- Status
- Keterangan

---

## ✅ Validasi Data

Data akan divalidasi untuk:

### Format Check:
- ✅ NIP: Harus 18 digit angka
- ✅ Email: Format email valid
- ✅ Tanggal: Format YYYY-MM-DD
- ✅ No HP: Diawali 08 atau +62

### Business Rules:
- ✅ NIP unique (tidak duplikat)
- ✅ Email unique
- ✅ Golongan sesuai jenjang jabatan
- ✅ AK comply 80/20 rule (Unsur Utama ≥ 80%)

### Data Relationship:
- ✅ NIP di Sheet 2 & 3 harus exist di Sheet 1
- ✅ Total AK = Unsur Utama + Unsur Penunjang

---

## 📋 Contoh Data

### Contoh 1: PTI Ahli Pertama

```csv
NIP: 198501012010011001
Nama: Ahmad Sudrajat, S.Kom
Email: ahmad.sudrajat@uii.ac.id
Jenjang: Pranata TI Pertama
Golongan: III/b
AK Utama: 45.50
AK Penunjang: 8.25
Total AK: 53.75
Compliance: ✅ (84.6% unsur utama)
```

### Contoh 2: PTI Ahli Muda

```csv
NIP: 198601022011012002
Nama: Siti Aminah, S.T., M.T.
Email: siti.aminah@uii.ac.id
Jenjang: Pranata TI Muda
Golongan: III/d
AK Utama: 68.00
AK Penunjang: 12.00
Total AK: 80.00
Compliance: ✅ (85% unsur utama)
```

---

## 🎯 Mapping Jenjang - Golongan

Sesuai **PR No. 3 Tahun 2025**:

### Jalur Terampil:

| Jenjang | Golongan Valid | Target AK |
|---------|----------------|-----------|
| Pelaksana | II/b - II/d | 25 - 50 |
| Pelaksana Lanjutan | II/d - III/b | 50 - 100 |
| Penyelia | III/b - III/d | 100 - 150 |

### Jalur Ahli:

| Jenjang | Golongan Valid | Target AK |
|---------|----------------|-----------|
| Pranata TI Pertama | III/a - III/d | 50 - 100 |
| Pranata TI Muda | III/b - IV/a | 100 - 200 |
| Pranata TI Madya | IV/a - IV/c | 200 - 400 |
| Pranata TI Utama | IV/c - IV/e | 400 - 1050 |

---

## ⚠️ Common Issues & Solutions

### Issue 1: File tidak bisa dibuka di Excel
**Solusi**:
- Pastikan file extension `.csv`
- Buka Excel terlebih dahulu, lalu File > Open
- Atau klik kanan file > Open With > Excel

### Issue 2: Format tanggal berubah otomatis
**Solusi**:
- Gunakan format YYYY-MM-DD konsisten
- Di Excel, format cell as "Text" dulu sebelum input tanggal
- Atau tambahkan single quote di depan: `'2024-01-15`

### Issue 3: NIP hilang leading zero
**Solusi**:
- Format kolom NIP sebagai "Text"
- Atau tambahkan single quote: `'198501012010011001`

### Issue 4: Tidak tahu golongan yang valid untuk jenjang tertentu
**Solusi**:
- Lihat file `4_Referensi.csv`
- Atau lihat tabel mapping di atas
- Atau lihat `PETUNJUK_PENGISIAN_DATA_PTI.md`

---

## 💡 Tips

### Untuk Pengisian Efektif:

1. **Mulai dari yang Wajib**
   - Fokus isi kolom ⭐ dulu
   - Kolom 🟡 dan 🟢 bisa menyusul

2. **Gunakan Copy-Paste**
   - Untuk data yang repetitive (unit kerja sama, dll)
   - Hati-hati dengan NIP dan Email (harus unique)

3. **Validasi Berkala**
   - Check setiap 10-20 rows
   - Pastikan format konsisten
   - Cek tidak ada data kosong di kolom wajib

4. **Backup Data**
   - Save berkala saat mengisi
   - Buat backup file sebelum kirim

5. **Koordinasi dengan Tim**
   - Konfirmasi data AK dengan bagian terkait
   - Cross-check dengan SK penetapan

---

## 📞 Support

### Jika Ada Pertanyaan:

**Email**: [email_support@institusi.ac.id]
**WhatsApp**: [nomor_whatsapp]
**Extension**: [nomor_ext]

**Jam Operasional**:
- Senin - Jumat: 08.00 - 16.00 WIB
- Response time: < 24 jam

---

## 📝 Checklist Sebelum Submit

Sebelum mengirim data, pastikan:

- [ ] Semua kolom WAJIB (⭐) terisi
- [ ] Format NIP benar (18 digit, sebagai text)
- [ ] Format email valid (@institusi.ac.id)
- [ ] Tidak ada NIP atau email duplikat
- [ ] Golongan sesuai dengan jenjang jabatan
- [ ] Data AK ada untuk semua pegawai (atau 0 jika belum ada PAK)
- [ ] Format tanggal konsisten (YYYY-MM-DD)
- [ ] File sudah di-save dengan nama yang jelas
- [ ] Sudah koordinasi untuk validasi data

---

## 🔄 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Nov 2025 | Initial template release |

---

## 📄 License & Usage

Template ini dibuat untuk internal [Nama Institusi] dan dapat disesuaikan sesuai kebutuhan.

---

**Happy Data Collection! 📊**

*Jika menemukan issue atau punya saran improvement untuk template, silakan hubungi tim IT.*
