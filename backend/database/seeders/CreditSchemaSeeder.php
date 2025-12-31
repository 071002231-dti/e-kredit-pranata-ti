<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditSchema;

class CreditSchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schemas = [
            // PENDIDIKAN
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Pendidikan Formal',
                'activity_name' => 'Sarjana (S1) bidang TI',
                'credit_points' => 100.00,
                'description' => 'Gelar S1 di bidang Teknologi Informasi',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Pendidikan Formal',
                'activity_name' => 'Magister (S2) bidang TI',
                'credit_points' => 150.00,
                'description' => 'Gelar S2 di bidang Teknologi Informasi',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Pendidikan Formal',
                'activity_name' => 'Doktor (S3) bidang TI',
                'credit_points' => 200.00,
                'description' => 'Gelar S3 di bidang Teknologi Informasi',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Sertifikasi',
                'activity_name' => 'Sertifikasi Profesional Internasional',
                'credit_points' => 15.00,
                'description' => 'Misal: AWS, Azure, Oracle, Cisco, ITIL',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Sertifikasi',
                'activity_name' => 'Sertifikasi Profesional Nasional',
                'credit_points' => 10.00,
                'description' => 'Sertifikasi dari lembaga nasional',
            ],

            // PELATIHAN
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan lebih dari 960 jam',
                'credit_points' => 15.00,
                'description' => 'Pelatihan teknis dengan durasi sangat lama',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 641-960 jam',
                'credit_points' => 12.00,
                'description' => 'Pelatihan teknis dengan durasi sedang',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 161-640 jam',
                'credit_points' => 9.00,
                'description' => 'Pelatihan teknis dengan durasi pendek',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 81-160 jam',
                'credit_points' => 6.00,
                'description' => 'Pelatihan teknis dengan durasi singkat',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 30-80 jam',
                'credit_points' => 3.00,
                'description' => 'Pelatihan teknis dengan durasi sangat singkat',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Fungsional',
                'activity_name' => 'Pelatihan Pranata TI',
                'credit_points' => 10.00,
                'description' => 'Pelatihan khusus jabatan fungsional Pranata TI',
            ],

            // TUGAS POKOK DAN FUNGSI
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Analisis Kebutuhan Sistem Besar',
                'credit_points' => 25.00,
                'description' => 'Analisis sistem kompleks multi-user dan multi-modul',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Analisis Kebutuhan Sistem Sedang',
                'credit_points' => 15.00,
                'description' => 'Analisis sistem dengan kompleksitas menengah',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Analisis Kebutuhan Sistem Kecil',
                'credit_points' => 8.00,
                'description' => 'Analisis sistem sederhana dengan user terbatas',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Desain Sistem',
                'activity_name' => 'Desain Database Kompleks',
                'credit_points' => 20.00,
                'description' => 'Perancangan database dengan banyak tabel dan relasi',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Desain Sistem',
                'activity_name' => 'Desain Database Sederhana',
                'credit_points' => 10.00,
                'description' => 'Perancangan database dengan tabel terbatas',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Desain Sistem',
                'activity_name' => 'Desain UI/UX Aplikasi',
                'credit_points' => 15.00,
                'description' => 'Perancangan antarmuka dan pengalaman pengguna',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Pembuatan Aplikasi Web Kompleks',
                'credit_points' => 30.00,
                'description' => 'Implementasi aplikasi web dengan fitur lengkap',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Pembuatan Aplikasi Web Sederhana',
                'credit_points' => 15.00,
                'description' => 'Implementasi aplikasi web dengan fitur dasar',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Pembuatan Aplikasi Mobile',
                'credit_points' => 25.00,
                'description' => 'Implementasi aplikasi mobile (Android/iOS)',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Administrasi Sistem',
                'activity_name' => 'Administrasi Server',
                'credit_points' => 12.00,
                'description' => 'Pengelolaan dan maintenance server',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Administrasi Sistem',
                'activity_name' => 'Administrasi Jaringan',
                'credit_points' => 12.00,
                'description' => 'Pengelolaan infrastruktur jaringan',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Administrasi Sistem',
                'activity_name' => 'Administrasi Database',
                'credit_points' => 10.00,
                'description' => 'Maintenance dan optimasi database',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Pemeliharaan Sistem',
                'activity_name' => 'Update dan Patch Sistem',
                'credit_points' => 8.00,
                'description' => 'Pembaruan sistem aplikasi/infrastruktur',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Pemeliharaan Sistem',
                'activity_name' => 'Troubleshooting dan Bug Fixing',
                'credit_points' => 6.00,
                'description' => 'Perbaikan error dan masalah sistem',
            ],

            // PENGEMBANGAN PROFESI
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Karya Ilmiah',
                'activity_name' => 'Makalah Internasional',
                'credit_points' => 25.00,
                'description' => 'Publikasi makalah di jurnal/seminar internasional',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Karya Ilmiah',
                'activity_name' => 'Makalah Nasional',
                'credit_points' => 15.00,
                'description' => 'Publikasi makalah di jurnal/seminar nasional',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Karya Ilmiah',
                'activity_name' => 'Artikel Populer',
                'credit_points' => 5.00,
                'description' => 'Artikel di media massa/blog teknis',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Terjemahan',
                'activity_name' => 'Terjemahan Buku Teknologi',
                'credit_points' => 10.00,
                'description' => 'Menerjemahkan buku/dokumentasi teknis',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Presentasi',
                'activity_name' => 'Narasumber Seminar Internasional',
                'credit_points' => 15.00,
                'description' => 'Pembicara di seminar/workshop internasional',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Presentasi',
                'activity_name' => 'Narasumber Seminar Nasional',
                'credit_points' => 10.00,
                'description' => 'Pembicara di seminar/workshop nasional',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Presentasi',
                'activity_name' => 'Narasumber Seminar Internal',
                'credit_points' => 5.00,
                'description' => 'Pembicara di seminar internal instansi',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Penelitian',
                'activity_name' => 'Penelitian Mandiri',
                'credit_points' => 20.00,
                'description' => 'Melakukan penelitian di bidang TI',
            ],

            // PENUNJANG TUGAS
            [
                'category' => 'Penunjang',
                'subcategory' => 'Mengajar/Melatih',
                'activity_name' => 'Mengajar di Perguruan Tinggi',
                'credit_points' => 8.00,
                'description' => 'Mengajar mata kuliah di universitas',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Mengajar/Melatih',
                'activity_name' => 'Instruktur Pelatihan',
                'credit_points' => 5.00,
                'description' => 'Menjadi instruktur di pelatihan teknis',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Organisasi Profesi',
                'activity_name' => 'Pengurus Organisasi Profesi',
                'credit_points' => 5.00,
                'description' => 'Menjadi pengurus di organisasi profesi TI',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Organisasi Profesi',
                'activity_name' => 'Anggota Aktif Organisasi Profesi',
                'credit_points' => 2.00,
                'description' => 'Keanggotaan aktif di organisasi profesi TI',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Penghargaan',
                'activity_name' => 'Penghargaan Tingkat Internasional',
                'credit_points' => 20.00,
                'description' => 'Penghargaan dari organisasi internasional',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Penghargaan',
                'activity_name' => 'Penghargaan Tingkat Nasional',
                'credit_points' => 15.00,
                'description' => 'Penghargaan dari pemerintah pusat',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Penghargaan',
                'activity_name' => 'Penghargaan Tingkat Provinsi',
                'credit_points' => 10.00,
                'description' => 'Penghargaan dari pemerintah provinsi',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Penghargaan',
                'activity_name' => 'Penghargaan Tingkat Instansi',
                'credit_points' => 5.00,
                'description' => 'Penghargaan dari instansi sendiri',
            ],
        ];

        foreach ($schemas as $schema) {
            CreditSchema::create($schema);
        }
    }
}
