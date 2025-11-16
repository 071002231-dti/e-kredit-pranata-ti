<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditSchema;

/**
 * Comprehensive Credit Schema Seeder
 * Based on PR No. 3 Tahun 2025 - Lampiran I (Halaman 10-16)
 * This seeder contains detailed credit schemas with proper angka kredit values,
 * satuan hasil, batasan penilaian, pelaksana, and unsur_type
 */
class ComprehensiveCreditSchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing data (disable foreign key checks temporarily)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CreditSchema::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $schemas = array_merge(
            $this->getPendidikanSchemas(),
            $this->getPelatihanSchemas(),
            $this->getOperasiTISchemas(),
            $this->getImplementasiSchemas(),
            $this->getAnalisisSchemas(),
            $this->getPengembanganProfesiSchemas(),
            $this->getPenunjangSchemas(),
            $this->getDakwahIslamiyahSchemas()
        );

        foreach ($schemas as $schema) {
            CreditSchema::create($schema);
        }

        $this->command->info('Comprehensive Credit Schemas seeded: ' . count($schemas) . ' items');
    }

    /**
     * I. PENDIDIKAN (Lampiran I halaman 10)
     */
    private function getPendidikanSchemas(): array
    {
        return [
            // Pendidikan Formal
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Pendidikan Formal',
                'activity_name' => 'Doktor (S3) bidang TI',
                'credit_points' => 200.000,
                'satuan_hasil' => 'Ijazah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi ijazah S3 yang telah dilegalisir',
                'unsur_type' => 'utama',
                'description' => 'Pendidikan Doktor (S3) di bidang Teknologi Informasi',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Pendidikan Formal',
                'activity_name' => 'Magister (S2) bidang TI',
                'credit_points' => 150.000,
                'satuan_hasil' => 'Ijazah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi ijazah S2 yang telah dilegalisir',
                'unsur_type' => 'utama',
                'description' => 'Pendidikan Magister (S2) di bidang Teknologi Informasi',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Pendidikan Formal',
                'activity_name' => 'Sarjana (S1) bidang TI',
                'credit_points' => 100.000,
                'satuan_hasil' => 'Ijazah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi ijazah S1 yang telah dilegalisir',
                'unsur_type' => 'utama',
                'description' => 'Pendidikan Sarjana (S1) di bidang Teknologi Informasi',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Pendidikan Formal',
                'activity_name' => 'Diploma III bidang TI',
                'credit_points' => 60.000,
                'satuan_hasil' => 'Ijazah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi ijazah D3 yang telah dilegalisir',
                'unsur_type' => 'utama',
                'description' => 'Pendidikan Diploma III di bidang Teknologi Informasi',
            ],

            // Sertifikasi
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Sertifikasi',
                'activity_name' => 'Sertifikasi Profesional Internasional',
                'credit_points' => 15.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi sertifikat internasional (AWS, Azure, Oracle, Cisco, ITIL, dll)',
                'unsur_type' => 'utama',
                'description' => 'Sertifikasi profesional tingkat internasional',
            ],
            [
                'category' => 'Pendidikan',
                'subcategory' => 'Sertifikasi',
                'activity_name' => 'Sertifikasi Profesional Nasional',
                'credit_points' => 10.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi sertifikat nasional',
                'unsur_type' => 'utama',
                'description' => 'Sertifikasi profesional dari lembaga nasional',
            ],
        ];
    }

    /**
     * II. PELATIHAN (Lampiran I halaman 10)
     */
    private function getPelatihanSchemas(): array
    {
        return [
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan lebih dari 960 jam',
                'credit_points' => 15.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat pelatihan, dokumentasi kegiatan',
                'unsur_type' => 'utama',
                'description' => 'Pelatihan teknis dengan durasi lebih dari 960 jam',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 641-960 jam',
                'credit_points' => 12.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat pelatihan, dokumentasi kegiatan',
                'unsur_type' => 'utama',
                'description' => 'Pelatihan teknis dengan durasi 641-960 jam',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 161-640 jam',
                'credit_points' => 9.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat pelatihan, dokumentasi kegiatan',
                'unsur_type' => 'utama',
                'description' => 'Pelatihan teknis dengan durasi 161-640 jam',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 81-160 jam',
                'credit_points' => 6.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat pelatihan, dokumentasi kegiatan',
                'unsur_type' => 'utama',
                'description' => 'Pelatihan teknis dengan durasi 81-160 jam',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Teknis',
                'activity_name' => 'Pelatihan 30-80 jam',
                'credit_points' => 3.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat pelatihan, dokumentasi kegiatan',
                'unsur_type' => 'utama',
                'description' => 'Pelatihan teknis dengan durasi 30-80 jam',
            ],
            [
                'category' => 'Pelatihan',
                'subcategory' => 'Pelatihan Fungsional',
                'activity_name' => 'Pelatihan Pranata TI',
                'credit_points' => 10.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat pelatihan fungsional',
                'unsur_type' => 'utama',
                'description' => 'Pelatihan khusus jabatan fungsional Pranata TI',
            ],
        ];
    }

    /**
     * III. OPERASI DAN ADMINISTRASI TI (Lampiran I halaman 11-12)
     */
    private function getOperasiTISchemas(): array
    {
        return [
            // Mengelola komponen sistem komputer
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Mengelola spesifikasi teknis komponen sistem komputer',
                'credit_points' => 0.147,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi spesifikasi teknis',
                'unsur_type' => 'utama',
                'description' => 'Melakukan pengelolaan spesifikasi teknis komponen sistem komputer',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Melakukan instalasi dan meningkatkan sistem',
                'credit_points' => 0.435,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi instalasi dan upgrade',
                'unsur_type' => 'utama',
                'description' => 'Instalasi dan peningkatan sistem komputer',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Melakukan troubleshooting perangkat keras',
                'credit_points' => 0.290,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Laporan troubleshooting',
                'unsur_type' => 'utama',
                'description' => 'Menangani masalah pada perangkat keras komputer',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Melakukan troubleshooting perangkat lunak',
                'credit_points' => 0.290,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Laporan troubleshooting',
                'unsur_type' => 'utama',
                'description' => 'Menangani masalah pada perangkat lunak',
            ],

            // Administrasi Jaringan
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Administrasi jaringan sederhana',
                'credit_points' => 0.435,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => '12 kali/tahun',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Laporan administrasi jaringan',
                'unsur_type' => 'utama',
                'description' => 'Melakukan administrasi jaringan komputer sederhana',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Administrasi jaringan kompleks',
                'credit_points' => 0.870,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => '12 kali/tahun',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Laporan administrasi jaringan kompleks',
                'unsur_type' => 'utama',
                'description' => 'Melakukan administrasi jaringan komputer yang kompleks',
            ],

            // Backup dan Recovery
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Melakukan backup data',
                'credit_points' => 0.145,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Laporan backup',
                'unsur_type' => 'utama',
                'description' => 'Melakukan backup data sistem',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Melakukan recovery data',
                'credit_points' => 0.435,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Laporan recovery',
                'unsur_type' => 'utama',
                'description' => 'Melakukan pemulihan data dari backup',
            ],

            // Database Administration
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Administrasi database sederhana',
                'credit_points' => 0.580,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => '12 kali/tahun',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Laporan administrasi database',
                'unsur_type' => 'utama',
                'description' => 'Melakukan administrasi database sederhana',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Operasi TI',
                'activity_name' => 'Administrasi database kompleks',
                'credit_points' => 1.160,
                'satuan_hasil' => 'Kali',
                'batasan_penilaian' => '12 kali/tahun',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Laporan administrasi database kompleks',
                'unsur_type' => 'utama',
                'description' => 'Melakukan administrasi database yang kompleks',
            ],
        ];
    }

    /**
     * IV. IMPLEMENTASI DAN PENGEMBANGAN (Lampiran I halaman 12-13)
     */
    private function getImplementasiSchemas(): array
    {
        return [
            // Implementasi Aplikasi
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Implementasi aplikasi web sederhana',
                'credit_points' => 1.000,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Source code, dokumentasi aplikasi',
                'unsur_type' => 'utama',
                'description' => 'Mengimplementasikan aplikasi web sederhana',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Implementasi aplikasi web kompleks',
                'credit_points' => 2.500,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => '25 program/tahun',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Source code, dokumentasi aplikasi, laporan testing',
                'unsur_type' => 'utama',
                'description' => 'Mengimplementasikan aplikasi web yang kompleks',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Implementasi aplikasi mobile',
                'credit_points' => 2.000,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => '25 program/tahun',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Source code, APK/IPA, dokumentasi',
                'unsur_type' => 'utama',
                'description' => 'Mengimplementasikan aplikasi mobile (Android/iOS)',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Implementasi backend API',
                'credit_points' => 1.500,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Source code API, dokumentasi endpoint',
                'unsur_type' => 'utama',
                'description' => 'Mengimplementasikan backend API',
            ],

            // Pengembangan Paket Teknologi
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Paket teknologi internet advanced',
                'credit_points' => 0.580,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => '25 program/tahun',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Dokumentasi paket teknologi',
                'unsur_type' => 'utama',
                'description' => 'Mengembangkan paket teknologi internet tingkat lanjut',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Paket untuk pengguna internasional',
                'credit_points' => 2.319,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => '10 program/tahun',
                'pelaksana' => 'PTI Madya',
                'bukti_fisik' => 'Dokumentasi paket, deployment internasional',
                'unsur_type' => 'utama',
                'description' => 'Mengembangkan paket untuk pengguna internasional',
            ],

            // Integration & Testing
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Integrasi sistem',
                'credit_points' => 1.740,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Dokumentasi integrasi',
                'unsur_type' => 'utama',
                'description' => 'Melakukan integrasi antar sistem',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Implementasi',
                'activity_name' => 'Testing dan QA aplikasi',
                'credit_points' => 0.870,
                'satuan_hasil' => 'Program',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Test report, bug report',
                'unsur_type' => 'utama',
                'description' => 'Melakukan testing dan quality assurance',
            ],
        ];
    }

    /**
     * V. ANALISIS DAN DESAIN (Lampiran I halaman 13-14)
     */
    private function getAnalisisSchemas(): array
    {
        return [
            // Analisis Sistem
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Analisis kebutuhan sistem sederhana',
                'credit_points' => 1.160,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Pertama',
                'bukti_fisik' => 'Dokumen analisis kebutuhan',
                'unsur_type' => 'utama',
                'description' => 'Melakukan analisis kebutuhan sistem sederhana',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Analisis kebutuhan sistem kompleks',
                'credit_points' => 2.320,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => '12 dokumen/tahun',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Dokumen analisis kebutuhan lengkap',
                'unsur_type' => 'utama',
                'description' => 'Melakukan analisis kebutuhan sistem yang kompleks',
            ],

            // Desain Sistem
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Merancang arsitektur sistem',
                'credit_points' => 2.320,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => '12 dokumen/tahun',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Dokumen arsitektur sistem',
                'unsur_type' => 'utama',
                'description' => 'Merancang arsitektur sistem informasi',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Merancang database',
                'credit_points' => 1.160,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Pertama',
                'bukti_fisik' => 'ERD, dokumentasi database',
                'unsur_type' => 'utama',
                'description' => 'Merancang struktur database',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Merancang user interface',
                'credit_points' => 0.870,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Mockup, wireframe, prototype',
                'unsur_type' => 'utama',
                'description' => 'Merancang antarmuka pengguna',
            ],

            // Dokumentasi Teknis
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Membuat dokumentasi teknis',
                'credit_points' => 0.580,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi teknis lengkap',
                'unsur_type' => 'utama',
                'description' => 'Membuat dokumentasi teknis sistem',
            ],
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Membuat user manual',
                'credit_points' => 0.435,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Buku manual pengguna',
                'unsur_type' => 'utama',
                'description' => 'Membuat panduan penggunaan untuk pengguna',
            ],

            // Security & Compliance
            [
                'category' => 'Tugas Pokok',
                'subcategory' => 'Analisis Sistem',
                'activity_name' => 'Audit keamanan sistem',
                'credit_points' => 1.740,
                'satuan_hasil' => 'Dokumen',
                'batasan_penilaian' => '6 dokumen/tahun',
                'pelaksana' => 'PTI Madya',
                'bukti_fisik' => 'Laporan audit keamanan',
                'unsur_type' => 'utama',
                'description' => 'Melakukan audit keamanan sistem informasi',
            ],
        ];
    }

    /**
     * VI. PENGEMBANGAN PROFESI (Lampiran I halaman 14-15)
     */
    private function getPengembanganProfesiSchemas(): array
    {
        return [
            // Karya Tulis Ilmiah
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Karya Tulis',
                'activity_name' => 'Membuat karya tulis ilmiah di jurnal internasional bereputasi',
                'credit_points' => 12.500,
                'satuan_hasil' => 'Naskah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Madya',
                'bukti_fisik' => 'Fotokopi artikel dan bukti publikasi',
                'unsur_type' => 'utama',
                'description' => 'Membuat karya tulis ilmiah yang dipublikasikan di jurnal internasional bereputasi',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Karya Tulis',
                'activity_name' => 'Membuat karya tulis ilmiah di jurnal nasional terakreditasi',
                'credit_points' => 6.000,
                'satuan_hasil' => 'Naskah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Fotokopi artikel dan bukti publikasi',
                'unsur_type' => 'utama',
                'description' => 'Membuat karya tulis ilmiah yang dipublikasikan di jurnal nasional terakreditasi',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Karya Tulis',
                'activity_name' => 'Membuat buku di bidang TI (ISBN)',
                'credit_points' => 10.000,
                'satuan_hasil' => 'Buku',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Buku ber-ISBN',
                'unsur_type' => 'utama',
                'description' => 'Menulis buku di bidang teknologi informasi dengan ISBN',
            ],

            // Presentasi dan Seminar
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Presentasi',
                'activity_name' => 'Presenter di seminar internasional',
                'credit_points' => 3.000,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Sertifikat presenter, materi presentasi',
                'unsur_type' => 'utama',
                'description' => 'Menjadi presenter di seminar/konferensi internasional',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Presentasi',
                'activity_name' => 'Presenter di seminar nasional',
                'credit_points' => 2.000,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Pertama',
                'bukti_fisik' => 'Sertifikat presenter, materi presentasi',
                'unsur_type' => 'utama',
                'description' => 'Menjadi presenter di seminar/konferensi nasional',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Presentasi',
                'activity_name' => 'Mengikuti seminar/workshop internasional',
                'credit_points' => 1.500,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat peserta',
                'unsur_type' => 'utama',
                'description' => 'Mengikuti seminar/workshop tingkat internasional',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'Presentasi',
                'activity_name' => 'Mengikuti seminar/workshop nasional',
                'credit_points' => 1.000,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Sertifikat peserta',
                'unsur_type' => 'utama',
                'description' => 'Mengikuti seminar/workshop tingkat nasional',
            ],

            // Hak Kekayaan Intelektual
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'HKI',
                'activity_name' => 'Mendapatkan hak cipta software',
                'credit_points' => 5.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'Sertifikat hak cipta dari Kemenkumham',
                'unsur_type' => 'utama',
                'description' => 'Mendapatkan hak cipta untuk perangkat lunak',
            ],
            [
                'category' => 'Pengembangan Profesi',
                'subcategory' => 'HKI',
                'activity_name' => 'Mendapatkan paten teknologi',
                'credit_points' => 15.000,
                'satuan_hasil' => 'Sertifikat',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'PTI Madya',
                'bukti_fisik' => 'Sertifikat paten dari Kemenkumham',
                'unsur_type' => 'utama',
                'description' => 'Mendapatkan paten untuk teknologi/inovasi',
            ],
        ];
    }

    /**
     * VII. PENUNJANG (Lampiran I halaman 15-16)
     */
    private function getPenunjangSchemas(): array
    {
        return [
            // Pengajaran dan Pelatihan
            [
                'category' => 'Penunjang',
                'subcategory' => 'Pengajaran',
                'activity_name' => 'Mengajar di perguruan tinggi',
                'credit_points' => 1.000,
                'satuan_hasil' => 'SKS/semester',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'SK mengajar, berita acara perkuliahan',
                'unsur_type' => 'penunjang',
                'description' => 'Mengajar mata kuliah di perguruan tinggi',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Pengajaran',
                'activity_name' => 'Menjadi instruktur pelatihan',
                'credit_points' => 0.500,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'SK instruktur, dokumentasi pelatihan',
                'unsur_type' => 'penunjang',
                'description' => 'Menjadi instruktur dalam kegiatan pelatihan',
            ],

            // Organisasi Profesi
            [
                'category' => 'Penunjang',
                'subcategory' => 'Organisasi',
                'activity_name' => 'Pengurus organisasi profesi tingkat nasional',
                'credit_points' => 2.000,
                'satuan_hasil' => 'Periode',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'PTI Muda',
                'bukti_fisik' => 'SK kepengurusan',
                'unsur_type' => 'penunjang',
                'description' => 'Menjadi pengurus organisasi profesi tingkat nasional',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Organisasi',
                'activity_name' => 'Anggota organisasi profesi',
                'credit_points' => 0.500,
                'satuan_hasil' => 'Tahun',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Kartu anggota organisasi profesi',
                'unsur_type' => 'penunjang',
                'description' => 'Menjadi anggota organisasi profesi TI',
            ],

            // Penghargaan
            [
                'category' => 'Penunjang',
                'subcategory' => 'Penghargaan',
                'activity_name' => 'Penghargaan/tanda jasa tingkat nasional',
                'credit_points' => 3.000,
                'satuan_hasil' => 'Penghargaan',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Piagam penghargaan',
                'unsur_type' => 'penunjang',
                'description' => 'Menerima penghargaan/tanda jasa tingkat nasional',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Penghargaan',
                'activity_name' => 'Penghargaan/tanda jasa tingkat provinsi',
                'credit_points' => 2.000,
                'satuan_hasil' => 'Penghargaan',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Piagam penghargaan',
                'unsur_type' => 'penunjang',
                'description' => 'Menerima penghargaan/tanda jasa tingkat provinsi',
            ],

            // Keanggotaan Tim
            [
                'category' => 'Penunjang',
                'subcategory' => 'Tim Penilai',
                'activity_name' => 'Menjadi tim penilai angka kredit',
                'credit_points' => 1.000,
                'satuan_hasil' => 'Tahun',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'PTI Madya',
                'bukti_fisik' => 'SK tim penilai',
                'unsur_type' => 'penunjang',
                'description' => 'Menjadi anggota tim penilai angka kredit',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Kepanitiaan',
                'activity_name' => 'Panitia kegiatan tingkat nasional',
                'credit_points' => 1.000,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'SK panitia, laporan kegiatan',
                'unsur_type' => 'penunjang',
                'description' => 'Menjadi panitia kegiatan tingkat nasional',
            ],
            [
                'category' => 'Penunjang',
                'subcategory' => 'Kepanitiaan',
                'activity_name' => 'Panitia kegiatan tingkat lokal',
                'credit_points' => 0.500,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => '20% dari total angka kredit',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'SK panitia, laporan kegiatan',
                'unsur_type' => 'penunjang',
                'description' => 'Menjadi panitia kegiatan tingkat lokal',
            ],
        ];
    }

    /**
     * VIII. DAKWAH ISLAMIYAH (Lampiran I halaman 15) - Specific for UII
     */
    private function getDakwahIslamiyahSchemas(): array
    {
        return [
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Hal',
                'activity_name' => 'Melaksanakan dakwah amal nyata (bil hal)',
                'credit_points' => 0.500,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi kegiatan dakwah',
                'unsur_type' => 'penunjang',
                'description' => 'Melaksanakan kegiatan dakwah amal nyata',
            ],
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Hal',
                'activity_name' => 'Menjadi panitia pembangunan masjid',
                'credit_points' => 0.500,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'SK panitia, dokumentasi',
                'unsur_type' => 'penunjang',
                'description' => 'Menjadi panitia dalam pembangunan masjid',
            ],
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Hal',
                'activity_name' => 'Melaksanakan kegiatan pembinaan agama/dakwah di masyarakat',
                'credit_points' => 0.500,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi pembinaan',
                'unsur_type' => 'penunjang',
                'description' => 'Melaksanakan pembinaan agama atau dakwah di masyarakat',
            ],

            // Dakwah Bil Lisan (Ceramah)
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Lisan',
                'activity_name' => 'Ceramah ke-Islam-an tingkat internasional',
                'credit_points' => 0.800,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi ceramah, undangan',
                'unsur_type' => 'penunjang',
                'description' => 'Memberikan ceramah/penyuluhan ke-Islam-an tingkat internasional',
            ],
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Lisan',
                'activity_name' => 'Ceramah ke-Islam-an tingkat nasional',
                'credit_points' => 0.600,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi ceramah, undangan',
                'unsur_type' => 'penunjang',
                'description' => 'Memberikan ceramah/penyuluhan ke-Islam-an tingkat nasional',
            ],
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Lisan',
                'activity_name' => 'Ceramah ke-Islam-an tingkat lokal',
                'credit_points' => 0.400,
                'satuan_hasil' => 'Kegiatan',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Dokumentasi ceramah, undangan',
                'unsur_type' => 'penunjang',
                'description' => 'Memberikan ceramah/penyuluhan ke-Islam-an tingkat lokal',
            ],

            // Dakwah Bil Kitabah (Menulis)
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Kitabah',
                'activity_name' => 'Karya tulis ke-Islam-an di koran',
                'credit_points' => 0.450,
                'satuan_hasil' => 'Naskah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Kliping artikel',
                'unsur_type' => 'penunjang',
                'description' => 'Menulis artikel ke-Islam-an di koran',
            ],
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Kitabah',
                'activity_name' => 'Karya tulis ke-Islam-an di majalah ber-ISSN',
                'credit_points' => 0.900,
                'satuan_hasil' => 'Naskah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi artikel dari majalah',
                'unsur_type' => 'penunjang',
                'description' => 'Menulis artikel ke-Islam-an di majalah ber-ISSN',
            ],
            [
                'category' => 'Dakwah Islamiyah',
                'subcategory' => 'Dakwah Bil Kitabah',
                'activity_name' => 'Karya tulis ke-Islam-an di buletin',
                'credit_points' => 0.450,
                'satuan_hasil' => 'Naskah',
                'batasan_penilaian' => 'tidak terbatas',
                'pelaksana' => 'Semua Jenjang',
                'bukti_fisik' => 'Fotokopi artikel dari buletin',
                'unsur_type' => 'penunjang',
                'description' => 'Menulis artikel ke-Islam-an di buletin',
            ],
        ];
    }
}
