<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatedCreditSchemaSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('credit_schema')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $schemas = $this->getSchemas();

        foreach ($schemas as $schema) {
            DB::table('credit_schema')->insert(array_merge($schema, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Credit schema updated successfully with ' . count($schemas) . ' activities!');
    }

    private function getSchemas(): array
    {
        return [
            // I. PENDIDIKAN - Pendidikan Formal
            ['category' => 'Pendidikan', 'subcategory' => 'Pendidikan Formal', 'activity_name' => 'Doktor (S3) bidang TI', 'credit_points' => '200.000', 'satuan_hasil' => 'Ijazah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi ijazah yang telah disahkan', 'unsur_type' => 'utama', 'description' => 'Pendidikan Doktor'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pendidikan Formal', 'activity_name' => 'Magister (S2) bidang TI', 'credit_points' => '150.000', 'satuan_hasil' => 'Ijazah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi ijazah yang telah disahkan', 'unsur_type' => 'utama', 'description' => 'Pendidikan Magister'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pendidikan Formal', 'activity_name' => 'Sarjana (S1) bidang TI', 'credit_points' => '100.000', 'satuan_hasil' => 'Ijazah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi ijazah yang telah disahkan', 'unsur_type' => 'utama', 'description' => 'Pendidikan Sarjana'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pendidikan Formal', 'activity_name' => 'Diploma III bidang TI', 'credit_points' => '60.000', 'satuan_hasil' => 'Ijazah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi ijazah yang telah disahkan', 'unsur_type' => 'utama', 'description' => 'Pendidikan Diploma III'],
            
            // Pelatihan Fungsional
            ['category' => 'Pendidikan', 'subcategory' => 'Pelatihan Fungsional', 'activity_name' => 'Pelatihan lebih dari 960 jam', 'credit_points' => '15.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Lamanya lebih dari 960 jam'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pelatihan Fungsional', 'activity_name' => 'Pelatihan 641-960 jam', 'credit_points' => '9.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Lamanya 641-960 jam'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pelatihan Fungsional', 'activity_name' => 'Pelatihan 481-640 jam', 'credit_points' => '6.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Lamanya 481-640 jam'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pelatihan Fungsional', 'activity_name' => 'Pelatihan 161-480 jam', 'credit_points' => '3.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Lamanya 161-480 jam'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pelatihan Fungsional', 'activity_name' => 'Pelatihan 81-160 jam', 'credit_points' => '2.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Lamanya 81-160 jam'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pelatihan Fungsional', 'activity_name' => 'Pelatihan 30-80 jam', 'credit_points' => '1.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Lamanya 30-80 jam'],
            ['category' => 'Pendidikan', 'subcategory' => 'Pelatihan Fungsional', 'activity_name' => 'Pelatihan 10-29 jam', 'credit_points' => '0.500', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Lamanya 10-29 jam'],

            // Sertifikasi
            ['category' => 'Pendidikan', 'subcategory' => 'Sertifikasi', 'activity_name' => 'Sertifikasi Profesional Internasional', 'credit_points' => '15.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Bersikala Internasional tanpa kursus'],
            ['category' => 'Pendidikan', 'subcategory' => 'Sertifikasi', 'activity_name' => 'Sertifikasi Profesional Nasional', 'credit_points' => '10.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Bersikala Nasional tanpa kursus'],
            ['category' => 'Pendidikan', 'subcategory' => 'Sertifikasi', 'activity_name' => 'Sertifikasi Profesional Lokal', 'credit_points' => '5.000', 'satuan_hasil' => 'Sertifikat', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Sertifikat', 'unsur_type' => 'utama', 'description' => 'Bersikala Lokal/internal'],

            // II. IMPLEMENTASI SISTEM INFORMASI
            // Implementasi Komputer dan Program Paket
            ['category' => 'Implementasi Sistem Informasi', 'subcategory' => 'Implementasi Komputer dan Program Paket', 'activity_name' => 'Membuat program paket internasional', 'credit_points' => '2.319', 'satuan_hasil' => 'Program', 'batasan_penilaian' => '25 program/tahun', 'pelaksana' => 'PTI Pertama', 'bukti_fisik' => 'Spesifikasi, Demo/lis program', 'unsur_type' => 'utama', 'description' => 'Untuk penggunaan internasional'],
            ['category' => 'Implementasi Sistem Informasi', 'subcategory' => 'Implementasi Komputer dan Program Paket', 'activity_name' => 'Membuat program paket nasional', 'credit_points' => '1.160', 'satuan_hasil' => 'Program', 'batasan_penilaian' => '25 program/tahun', 'pelaksana' => 'PTI Pertama', 'bukti_fisik' => 'Spesifikasi, Demo/lis program', 'unsur_type' => 'utama', 'description' => 'Untuk penggunaan nasional'],
            ['category' => 'Implementasi Sistem Informasi', 'subcategory' => 'Implementasi Komputer dan Program Paket', 'activity_name' => 'Membuat program paket instansi/lembaga', 'credit_points' => '0.580', 'satuan_hasil' => 'Program', 'batasan_penilaian' => '25 program/tahun', 'pelaksana' => 'PTI Pertama', 'bukti_fisik' => 'Spesifikasi, Demo/lis program', 'unsur_type' => 'utama', 'description' => 'Untuk antar instansi/lembaga pemerintah'],

            // Implementasi Basisdata
            ['category' => 'Implementasi Sistem Informasi', 'subcategory' => 'Implementasi Basisdata', 'activity_name' => 'Mengimplementasikan rancangan basisdata', 'credit_points' => '0.625', 'satuan_hasil' => 'Rancangan', 'batasan_penilaian' => '25 rancangan/tahun', 'pelaksana' => 'PTI Pertama', 'bukti_fisik' => 'Dokumentasi', 'unsur_type' => 'utama', 'description' => 'Mengimplementasikan rancangan basisdata'],
            ['category' => 'Implementasi Sistem Informasi', 'subcategory' => 'Implementasi Basisdata', 'activity_name' => 'Mengatur alokasi area basisdata', 'credit_points' => '0.347', 'satuan_hasil' => 'Kali', 'batasan_penilaian' => '25 kali/tahun', 'pelaksana' => 'PTI Pertama', 'bukti_fisik' => 'Dokumentasi', 'unsur_type' => 'utama', 'description' => 'Mengatur alokasi area basisdata dan media komputer'],

            // Implementasi Sistem Jaringan Komputer
            ['category' => 'Implementasi Sistem Informasi', 'subcategory' => 'Implementasi Sistem Jaringan Komputer', 'activity_name' => 'Menerapkan sistem penanganan kembali basisdata', 'credit_points' => '0.154', 'satuan_hasil' => 'Kali', 'batasan_penilaian' => '52 kali/tahun', 'pelaksana' => 'PTI Pertama', 'bukti_fisik' => 'Dokumentasi', 'unsur_type' => 'utama', 'description' => 'Menerapkan sistem penanganan kembali basisdata'],

            // III. ANALISIS DAN PERANCANGAN SISTEM INFORMASI
            ['category' => 'Analisis dan Perancangan Sistem Informasi', 'subcategory' => 'Analisis Sistem dan Teknologi Informasi', 'activity_name' => 'Menyusun rencana studi kelayakan pengolahan data', 'credit_points' => '0.666', 'satuan_hasil' => 'Proposal', 'batasan_penilaian' => 'tidak terbatas', 'pelaksana' => 'PTI Muda', 'bukti_fisik' => 'Proposal', 'unsur_type' => 'utama', 'description' => 'Menyusun rencana studi kelayakan'],
            ['category' => 'Analisis dan Perancangan Sistem Informasi', 'subcategory' => 'Analisis Sistem dan Teknologi Informasi', 'activity_name' => 'Melakukan studi kelayakan', 'credit_points' => '0.462', 'satuan_hasil' => 'Laporan', 'batasan_penilaian' => 'Min 20 hal, A4 spasi 1.5', 'pelaksana' => 'PTI Muda', 'bukti_fisik' => 'Laporan', 'unsur_type' => 'utama', 'description' => 'Melaksanakan studi kelayakan pendahuluan pengolahan data'],

            // IV. PENYUSUNAN KEBIJAKAN SISTEM INFORMASI
            ['category' => 'Penyusunan Kebijakan Sistem Informasi', 'subcategory' => 'Perencanaan dan Pengembangan Sistem Informasi', 'activity_name' => 'Melakukan diskusi dalam rangka integrasi sistem informasi basisdata', 'credit_points' => '0.96', 'satuan_hasil' => 'Kali', 'batasan_penilaian' => '25 kali/tahun', 'pelaksana' => 'PTI Madya', 'bukti_fisik' => 'Dokumentasi', 'unsur_type' => 'utama', 'description' => 'Melakukan diskusi dalam rangka integrasi sistem'],

            // V. PENGEMBANGAN PROFESI
            ['category' => 'Pengembangan Profesi', 'subcategory' => 'Membuat Karya Tulis/Ilmiah', 'activity_name' => 'Karya tulis/ilmiah dalam buku yang diterbitkan dan diedarkan secara nasional', 'credit_points' => '12.5', 'satuan_hasil' => 'Buku', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Naskah & buku yang diterbitkan', 'unsur_type' => 'utama', 'description' => 'Dalam bentuk buku yang diterbitkan'],
            ['category' => 'Pengembangan Profesi', 'subcategory' => 'Membuat Karya Tulis/Ilmiah', 'activity_name' => 'Karya tulis/ilmiah dalam majalah ilmiah yang diakui oleh instansi yang berwenang', 'credit_points' => '6.000', 'satuan_hasil' => 'Naskah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Naskah artikel & artikel di majalah', 'unsur_type' => 'utama', 'description' => 'Dalam majalah ilmiah yang diakui oleh LIPI'],

            // Menerjemahkan/menyadur buku
            ['category' => 'Pengembangan Profesi', 'subcategory' => 'Menerjemahkan/Menyadur Buku', 'activity_name' => 'Menerjemah/saduran buku di bidang TI yang dipublikasikan dalam bentuk buku yang diterbitkan', 'credit_points' => '7.000', 'satuan_hasil' => 'Buku', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Buku', 'unsur_type' => 'utama', 'description' => 'Dalam bentuk buku yang diterbitkan dan diedarkan secara nasional/internasional'],

            // VI. DAKWAH ISLAMIYAH
            ['category' => 'Dakwah Islamiyah', 'subcategory' => 'Dakwah Bil Hal', 'activity_name' => 'Melaksanakan dakwah dalam bentuk amal nyata (bil hal)', 'credit_points' => '0.5', 'satuan_hasil' => 'Kegiatan', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Surat Keterangan', 'unsur_type' => 'penunjang', 'description' => 'Melaksanakan kegiatan dakwah ke-Islam-an yang dapat dimanfaatkan'],
            ['category' => 'Dakwah Islamiyah', 'subcategory' => 'Dakwah Bil Hal', 'activity_name' => 'Menjadi panitia pembangunan masjid', 'credit_points' => '0.5', 'satuan_hasil' => 'Kegiatan', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Surat Keterangan', 'unsur_type' => 'penunjang', 'description' => 'Menjadi panitia pembangunan masjid'],

            // Dakwah Bil Lisan
            ['category' => 'Dakwah Islamiyah', 'subcategory' => 'Dakwah Bil Lisan', 'activity_name' => 'Memberi latihan/penyuluhan/penataran/ceramah keislaman pada masyarakat (bil lisan) - Internasional', 'credit_points' => '0.80', 'satuan_hasil' => 'Kegiatan', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Jadwal, surat permintaan, surat tugas', 'unsur_type' => 'penunjang', 'description' => 'Tersendiri, tiap program - Tingkat Internasional'],
            ['category' => 'Dakwah Islamiyah', 'subcategory' => 'Dakwah Bil Lisan', 'activity_name' => 'Memberi latihan/penyuluhan/penataran/ceramah keislaman pada masyarakat (bil lisan) - Nasional', 'credit_points' => '0.60', 'satuan_hasil' => 'Kegiatan', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Jadwal, surat permintaan, surat tugas', 'unsur_type' => 'penunjang', 'description' => 'Tersendiri, tiap program - Tingkat Nasional'],
            ['category' => 'Dakwah Islamiyah', 'subcategory' => 'Dakwah Bil Lisan', 'activity_name' => 'Memberi latihan/penyuluhan/penataran/ceramah keislaman pada masyarakat (bil lisan) - Lokal', 'credit_points' => '0.40', 'satuan_hasil' => 'Kegiatan', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Jadwal, surat permintaan, surat tugas', 'unsur_type' => 'penunjang', 'description' => 'Tersendiri, tiap program - Tingkat Lokal'],

            // Dakwah Bil Kitabah
            ['category' => 'Dakwah Islamiyah', 'subcategory' => 'Dakwah Bil Kitabah', 'activity_name' => 'Membuat/menulis karya tulis keislaman (bil kitabah) yang dipublikasikan - Koran', 'credit_points' => '0.45', 'satuan_hasil' => 'Naskah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Artikel di koran', 'unsur_type' => 'penunjang', 'description' => 'Membuat/menulis karya tulis keislaman di koran'],
            ['category' => 'Dakwah Islamiyah', 'subcategory' => 'Dakwah Bil Kitabah', 'activity_name' => 'Membuat/menulis karya tulis keislaman (bil kitabah) yang dipublikasikan - Majalah ber-ISSN', 'credit_points' => '0.90', 'satuan_hasil' => 'Naskah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Artikel di majalah', 'unsur_type' => 'penunjang', 'description' => 'Membuat/menulis karya tulis keislaman di majalah ber-ISSN'],

            // VII. PENUNJANG KEGIATAN
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Mengajar/Melatih', 'activity_name' => 'Mengajar/melatih di bidang teknologi informasi pada unit atau lembaga muka', 'credit_points' => '0.030', 'satuan_hasil' => 'Jam tatap muka', 'batasan_penilaian' => '2 jam tatap muka', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Surat tugas atau keterangan', 'unsur_type' => 'penunjang', 'description' => 'Mengajar/melatih di bidang teknologi informasi'],

            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Mengikuti Seminar/Lokakarya', 'activity_name' => 'Mengikuti seminar/lokakarya/konferensi di bidang TI - Sebagai Pemrasaran (Kali)', 'credit_points' => '3.000', 'satuan_hasil' => 'Kali', 'batasan_penilaian' => 'Max 2 kali/tahun', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Sertifikat', 'unsur_type' => 'penunjang', 'description' => 'Tingkat Nasional/Internasional sebagai pemrasaran'],
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Mengikuti Seminar/Lokakarya', 'activity_name' => 'Mengikuti seminar/lokakarya/konferensi di bidang TI - Sebagai Moderator/Pemberi Nara Sumber (Kali)', 'credit_points' => '2.000', 'satuan_hasil' => 'Kali', 'batasan_penilaian' => 'Max 2 kali/tahun', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Sertifikat', 'unsur_type' => 'penunjang', 'description' => 'Sebagai Moderator/Pemberi nara sumber'],
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Mengikuti Seminar/Lokakarya', 'activity_name' => 'Mengikuti seminar/lokakarya/konferensi di bidang TI - Sebagai Peserta (Kali)', 'credit_points' => '1.000', 'satuan_hasil' => 'Kali', 'batasan_penilaian' => 'Max 2 kali/tahun', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Sertifikat', 'unsur_type' => 'penunjang', 'description' => 'Sebagai Peserta'],

            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Peran serta dalam Tim Penilai', 'activity_name' => 'Peran serta dalam tim penilai angka kredit Jabatan Pranata Teknologi Informasi', 'credit_points' => '0.5', 'satuan_hasil' => 'Tahun', 'batasan_penilaian' => 'Per tahun masa keanggotaan', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Surat keputusan dan surat keterangan', 'unsur_type' => 'penunjang', 'description' => 'Keanggotaan dalam tim penilai angka kredit'],

            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Keanggotaan Organisasi Profesi', 'activity_name' => 'Menjadi anggota organisasi profesi kepranataan teknologi informasi - Pengurus aktif Nasional/Internasional', 'credit_points' => '1.000', 'satuan_hasil' => 'Tahun', 'batasan_penilaian' => 'Per tahun masa keanggotaan', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Surat keterangan kepegurusan/keanggotaan', 'unsur_type' => 'penunjang', 'description' => 'Tingkat Nasional/Internasional sebagai Pengurus aktif'],
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Keanggotaan Organisasi Profesi', 'activity_name' => 'Menjadi anggota organisasi profesi kepranataan teknologi informasi - Anggota aktif Nasional/Internasional', 'credit_points' => '0.5', 'satuan_hasil' => 'Tahun', 'batasan_penilaian' => 'Per tahun masa keanggotaan', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Surat keterangan kepegurusan/keanggotaan', 'unsur_type' => 'penunjang', 'description' => 'Tingkat Nasional/Internasional sebagai Anggota aktif'],

            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Memperoleh Penghargaan/Tanda Jasa', 'activity_name' => 'Memperoleh penghargaan/tanda jasa - Tanda kehormatan Karya Satya 30 (tiga puluh) tahun', 'credit_points' => '3.000', 'satuan_hasil' => 'Tanda jasa', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'SK atau surat keterangan', 'unsur_type' => 'penunjang', 'description' => '30 tahun'],
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Memperoleh Penghargaan/Tanda Jasa', 'activity_name' => 'Memperoleh penghargaan/tanda jasa - Tanda kehormatan Karya Satya 20 (dua puluh) tahun', 'credit_points' => '2.000', 'satuan_hasil' => 'Tanda jasa', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'SK atau surat keterangan', 'unsur_type' => 'penunjang', 'description' => '20 tahun'],
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Memperoleh Penghargaan/Tanda Jasa', 'activity_name' => 'Memperoleh penghargaan/tanda jasa - Tanda kehormatan Karya Satya 10 (sepuluh) tahun', 'credit_points' => '1.000', 'satuan_hasil' => 'Tanda jasa', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'SK atau surat keterangan', 'unsur_type' => 'penunjang', 'description' => '10 tahun'],

            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Memperoleh Gelar Kesarjanaan Lainnya', 'activity_name' => 'Memperoleh gelar kesarjanaan lainnya yang tidak sesuai dengan bidang tugas - Doktor (S3)', 'credit_points' => '15.000', 'satuan_hasil' => 'Ijazah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Ijazah yang telah disahkan', 'unsur_type' => 'penunjang', 'description' => 'Doktor'],
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Memperoleh Gelar Kesarjanaan Lainnya', 'activity_name' => 'Memperoleh gelar kesarjanaan lainnya yang tidak sesuai dengan bidang tugas - Magister (S2)', 'credit_points' => '10.000', 'satuan_hasil' => 'Ijazah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Ijazah yang telah disahkan', 'unsur_type' => 'penunjang', 'description' => 'Magister'],
            ['category' => 'Penunjang Kegiatan', 'subcategory' => 'Memperoleh Gelar Kesarjanaan Lainnya', 'activity_name' => 'Memperoleh gelar kesarjanaan lainnya yang tidak sesuai dengan bidang tugas - Sarjana/Diploma IV (S1)', 'credit_points' => '5.000', 'satuan_hasil' => 'Ijazah', 'batasan_penilaian' => 'Semua jenjang', 'pelaksana' => 'Semua Jenjang', 'bukti_fisik' => 'Fotokopi Ijazah yang telah disahkan', 'unsur_type' => 'penunjang', 'description' => 'Sarjana/Diploma IV'],
        ];
    }
}
