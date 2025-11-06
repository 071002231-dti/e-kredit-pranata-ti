# 🗄️ Database Schema Reference

**Project**: e-Kredit Pranata TI
**Database**: MySQL 8.0
**Database Name**: ekredit

---

## 📊 Entity Relationship Diagram (ERD)

```
┌─────────────┐         ┌──────────────────┐         ┌────────────────┐
│   users     │         │   activities     │         │   approvals    │
├─────────────┤         ├──────────────────┤         ├────────────────┤
│ id (PK)     │────┬───>│ user_id (FK)     │────────>│ activity_id(FK)│
│ nip         │    │    │ schema_id (FK)   │         │ verifier_id(FK)│
│ name        │    │    │ title            │         │ status         │
│ email       │    │    │ description      │         │ comments       │
│ password    │    │    │ proof_file       │         │ approved_at    │
│ role        │    │    │ status           │         │ created_at     │
│ position    │    │    │ submitted_at     │         │ updated_at     │
│ unit_kerja  │    │    │ created_at       │         └────────────────┘
│ created_at  │    │    │ updated_at       │
│ updated_at  │    │    └──────────────────┘
└─────────────┘    │              ▲
                   │              │
                   │    ┌─────────────────┐
                   └───>│ credit_schema   │
                        ├─────────────────┤
                        │ id (PK)         │
                        │ category        │
                        │ subcategory     │
                        │ activity_name   │
                        │ credit_points   │
                        │ description     │
                        │ created_at      │
                        │ updated_at      │
                        └─────────────────┘
```

---

## 📋 Table: users

**Purpose**: Menyimpan data pegawai/user yang menggunakan sistem

### Columns

| Column | Type | Length | Nullable | Default | Constraint | Description |
|--------|------|--------|----------|---------|------------|-------------|
| id | BIGINT UNSIGNED | - | NO | AUTO_INCREMENT | PRIMARY KEY | ID user |
| nip | VARCHAR | 18 | NO | - | UNIQUE | Nomor Induk Pegawai |
| name | VARCHAR | 255 | NO | - | - | Nama lengkap |
| email | VARCHAR | 255 | NO | - | UNIQUE | Email (untuk login) |
| password | VARCHAR | 255 | NO | - | - | Hashed password |
| role | ENUM | - | NO | 'user' | - | user, verifier, admin |
| position | VARCHAR | 255 | YES | NULL | - | Jabatan pegawai |
| unit_kerja | VARCHAR | 255 | YES | NULL | - | Unit kerja/bidang |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu create |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu update |

### Indexes
- PRIMARY KEY (`id`)
- UNIQUE KEY (`nip`)
- UNIQUE KEY (`email`)
- INDEX (`role`)

### Sample Data
```sql
INSERT INTO users (nip, name, email, password, role, position, unit_kerja) VALUES
('199001012020011001', 'Admin System', 'admin@example.com', '$2y$12$...', 'admin', 'Kepala Bidang IT', 'Bidang IT'),
('199002022020012002', 'Verifikator Satu', 'verifier@example.com', '$2y$12$...', 'verifier', 'Pranata TI Ahli Madya', 'Sub Bidang Infrastruktur'),
('199003032020013003', 'User Biasa', 'user@example.com', '$2y$12$...', 'user', 'Pranata TI Ahli Muda', 'Sub Bidang Aplikasi');
```

---

## 📋 Table: credit_schema

**Purpose**: Menyimpan skema/struktur angka kredit Pranata TI sesuai PermenPAN RB

### Columns

| Column | Type | Length | Nullable | Default | Constraint | Description |
|--------|------|--------|----------|---------|------------|-------------|
| id | BIGINT UNSIGNED | - | NO | AUTO_INCREMENT | PRIMARY KEY | ID schema |
| category | VARCHAR | 100 | NO | - | INDEX | Kategori utama |
| subcategory | VARCHAR | 255 | YES | NULL | - | Sub kategori |
| activity_name | VARCHAR | 255 | NO | - | - | Nama aktivitas |
| credit_points | DECIMAL | 10,2 | NO | - | - | Jumlah angka kredit |
| description | TEXT | - | YES | NULL | - | Deskripsi detail |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu create |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu update |

### Indexes
- PRIMARY KEY (`id`)
- INDEX (`category`)
- INDEX (`category`, `subcategory`)

### Categories Structure

**1. Pendidikan**
- Pendidikan Formal (S1: 100, S2: 150, S3: 200)
- Pendidikan dan Pelatihan Fungsional
- Pelatihan Teknis

**2. Pelatihan**
- Pelatihan Kepemimpinan
- Pelatihan Fungsional
- Pelatihan Teknis

**3. Tugas Pokok dan Fungsi**
- Analisis dan Pengembangan Sistem
- Desain Sistem
- Implementasi Sistem
- Administrasi Sistem
- Pemeliharaan Sistem

**4. Pengembangan Profesi**
- Karya Tulis/Karya Ilmiah
- Terjemahan Buku
- Presentasi/Narasumber
- Penelitian

**5. Penunjang Tugas**
- Mengajar/Melatih
- Keanggotaan Organisasi Profesi
- Perolehan Penghargaan
- Perolehan Gelar Kehormatan

### Sample Data
```sql
INSERT INTO credit_schema (category, subcategory, activity_name, credit_points, description) VALUES
-- Pendidikan
('Pendidikan', 'Pendidikan Formal', 'Sarjana (S1) bidang TI', 100.00, 'Gelar S1 di bidang Teknologi Informasi'),
('Pendidikan', 'Pendidikan Formal', 'Magister (S2) bidang TI', 150.00, 'Gelar S2 di bidang Teknologi Informasi'),
('Pendidikan', 'Pendidikan Formal', 'Doktor (S3) bidang TI', 200.00, 'Gelar S3 di bidang Teknologi Informasi'),
('Pendidikan', 'Sertifikasi', 'Sertifikasi Profesional Internasional', 15.00, 'Misal: AWS, Azure, Oracle, Cisco'),

-- Pelatihan
('Pelatihan', 'Pelatihan Teknis', 'Pelatihan lebih dari 960 jam', 15.00, 'Pelatihan teknis lama'),
('Pelatihan', 'Pelatihan Teknis', 'Pelatihan 641-960 jam', 12.00, 'Pelatihan teknis sedang'),
('Pelatihan', 'Pelatihan Teknis', 'Pelatihan 161-640 jam', 9.00, 'Pelatihan teknis pendek'),

-- Tugas Pokok
('Tugas Pokok', 'Analisis Sistem', 'Analisis Kebutuhan Sistem Besar', 25.00, 'Analisis sistem kompleks multi-user'),
('Tugas Pokok', 'Desain Sistem', 'Desain Database', 15.00, 'Perancangan struktur database'),
('Tugas Pokok', 'Implementasi', 'Pembuatan Aplikasi Web', 20.00, 'Implementasi aplikasi berbasis web'),

-- Pengembangan Profesi
('Pengembangan Profesi', 'Karya Ilmiah', 'Makalah Nasional', 10.00, 'Publikasi makalah tingkat nasional'),
('Pengembangan Profesi', 'Karya Ilmiah', 'Makalah Internasional', 25.00, 'Publikasi makalah tingkat internasional'),
('Pengembangan Profesi', 'Presentasi', 'Narasumber Seminar', 5.00, 'Menjadi pembicara di seminar'),

-- Penunjang
('Penunjang', 'Organisasi Profesi', 'Anggota Aktif Organisasi', 2.00, 'Keanggotaan di organisasi profesi TI'),
('Penunjang', 'Penghargaan', 'Penghargaan Tingkat Instansi', 3.00, 'Penghargaan dari instansi'),
('Penunjang', 'Penghargaan', 'Penghargaan Tingkat Nasional', 10.00, 'Penghargaan dari pemerintah pusat');
```

---

## 📋 Table: activities

**Purpose**: Menyimpan aktivitas yang diajukan oleh user untuk mendapat angka kredit

### Columns

| Column | Type | Length | Nullable | Default | Constraint | Description |
|--------|------|--------|----------|---------|------------|-------------|
| id | BIGINT UNSIGNED | - | NO | AUTO_INCREMENT | PRIMARY KEY | ID aktivitas |
| user_id | BIGINT UNSIGNED | - | NO | - | FOREIGN KEY | ID user pengaju |
| schema_id | BIGINT UNSIGNED | - | NO | - | FOREIGN KEY | ID credit schema |
| title | VARCHAR | 255 | NO | - | - | Judul aktivitas |
| description | TEXT | - | NO | - | - | Deskripsi detail |
| proof_file | VARCHAR | 255 | YES | NULL | - | Path file bukti |
| status | ENUM | - | NO | 'pending' | - | pending,approved,rejected |
| submitted_at | TIMESTAMP | - | NO | CURRENT_TIMESTAMP | - | Waktu submit |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu create |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu update |

### Indexes
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
- FOREIGN KEY (`schema_id`) REFERENCES `credit_schema`(`id`) ON DELETE RESTRICT
- INDEX (`user_id`)
- INDEX (`status`)
- INDEX (`submitted_at`)

### Business Rules
- User hanya bisa edit/delete jika status = 'pending'
- Saat status berubah ke approved/rejected, otomatis create record di table approvals
- File bukti maksimal 5MB, format: PDF, JPG, PNG

---

## 📋 Table: approvals

**Purpose**: Menyimpan history approval/rejection dari verifier

### Columns

| Column | Type | Length | Nullable | Default | Constraint | Description |
|--------|------|--------|----------|---------|------------|-------------|
| id | BIGINT UNSIGNED | - | NO | AUTO_INCREMENT | PRIMARY KEY | ID approval |
| activity_id | BIGINT UNSIGNED | - | NO | - | FOREIGN KEY | ID aktivitas |
| verifier_id | BIGINT UNSIGNED | - | NO | - | FOREIGN KEY | ID verifikator |
| status | ENUM | - | NO | - | - | approved, rejected |
| comments | TEXT | - | YES | NULL | - | Catatan verifikator |
| approved_at | TIMESTAMP | - | NO | CURRENT_TIMESTAMP | - | Waktu approve/reject |
| created_at | TIMESTAMP | - | YES | NULL | - | Waktu create |
| updated_at | TIMESTAMP | - | YES | NULL | - | Waktu update |

### Indexes
- PRIMARY KEY (`id`)
- FOREIGN KEY (`activity_id`) REFERENCES `activities`(`id`) ON DELETE CASCADE
- FOREIGN KEY (`verifier_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
- INDEX (`activity_id`)
- INDEX (`verifier_id`)
- INDEX (`status`)

### Business Rules
- Hanya user dengan role 'verifier' atau 'admin' yang bisa approve/reject
- Satu activity bisa punya multiple approval records (untuk tracking history)
- Approval terbaru yang menentukan status final

---

## 🔗 Relationships

### users → activities (One to Many)
- Satu user bisa submit banyak activities
- `users.id` = `activities.user_id`

### credit_schema → activities (One to Many)
- Satu credit schema bisa digunakan di banyak activities
- `credit_schema.id` = `activities.schema_id`

### activities → approvals (One to Many)
- Satu activity bisa punya banyak approval history
- `activities.id` = `approvals.activity_id`

### users → approvals (One to Many)
- Satu verifier bisa approve banyak activities
- `users.id` = `approvals.verifier_id`

---

## 📊 Common Queries

### Get user's total approved credit points
```sql
SELECT
    u.name,
    SUM(cs.credit_points) as total_points
FROM users u
JOIN activities a ON u.id = a.user_id
JOIN credit_schema cs ON a.schema_id = cs.id
WHERE a.status = 'approved'
GROUP BY u.id, u.name;
```

### Get pending activities for verifier
```sql
SELECT
    a.id,
    a.title,
    u.name as user_name,
    cs.activity_name,
    cs.credit_points,
    a.submitted_at
FROM activities a
JOIN users u ON a.user_id = u.id
JOIN credit_schema cs ON a.schema_id = cs.id
WHERE a.status = 'pending'
ORDER BY a.submitted_at ASC;
```

### Get user's activities by category
```sql
SELECT
    cs.category,
    COUNT(*) as total_activities,
    SUM(CASE WHEN a.status = 'approved' THEN cs.credit_points ELSE 0 END) as earned_points
FROM activities a
JOIN credit_schema cs ON a.schema_id = cs.id
WHERE a.user_id = ?
GROUP BY cs.category;
```

### Get approval history for an activity
```sql
SELECT
    ap.status,
    ap.comments,
    u.name as verifier_name,
    ap.approved_at
FROM approvals ap
JOIN users u ON ap.verifier_id = u.id
WHERE ap.activity_id = ?
ORDER BY ap.approved_at DESC;
```

---

## 🚀 Migration Order

1. Create `users` table first (no dependencies)
2. Create `credit_schema` table (no dependencies)
3. Create `activities` table (depends on users & credit_schema)
4. Create `approvals` table (depends on activities & users)

---

## 📝 Notes

- Semua timestamps menggunakan timezone UTC
- Soft deletes tidak digunakan (hard delete)
- File uploads disimpan di storage/app/public/proofs/
- Indexing di kolom yang sering di-query (user_id, status, category)
- ENUM values untuk role & status sudah didefinisikan di migration

---

**Reference**: Lihat prototype di `e-kredit-pranata-ti.tsx` untuk detail lengkap struktur angka kredit
