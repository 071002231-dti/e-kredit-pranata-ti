<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CreditSchema;

class CreditSchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creditData = [
            // Pendidikan
            ['category' => 'pendidikan', 'subcategory' => 's1', 'description' => 'S1 Teknik Informatika/sejenisnya', 'credit_value' => 100],
            ['category' => 'pendidikan', 'subcategory' => 's2', 'description' => 'S2 Teknik Informatika/sejenisnya', 'credit_value' => 150],
            ['category' => 'pendidikan', 'subcategory' => 's3', 'description' => 'S3 Teknik Informatika/sejenisnya', 'credit_value' => 200],
            ['category' => 'pendidikan', 'subcategory' => 'sertifikasi', 'description' => 'Sertifikasi Profesi TI', 'credit_value' => 25],
            
            // Pelatihan
            ['category' => 'pelatihan', 'subcategory' => 'struktural', 'description' => 'Pelatihan Kepemimpinan', 'credit_value' => 15],
            ['category' => 'pelatihan', 'subcategory' => 'fungsional', 'description' => 'Pelatihan Fungsional', 'credit_value' => 20],
            ['category' => 'pelatihan', 'subcategory' => 'teknis', 'description' => 'Pelatihan Teknis TI', 'credit_value' => 10],
            ['category' => 'pelatihan', 'subcategory' => 'seminar', 'description' => 'Seminar/Workshop TI', 'credit_value' => 5],
            
            // Tugas Pokok
            ['category' => 'tugas_pokok', 'subcategory' => 'analisis_sistem', 'description' => 'Melakukan Analisis Sistem', 'credit_value' => 12.5],
            ['category' => 'tugas_pokok', 'subcategory' => 'desain_sistem', 'description' => 'Merancang Sistem Informasi', 'credit_value' => 15],
            ['category' => 'tugas_pokok', 'subcategory' => 'implementasi', 'description' => 'Mengimplementasikan Sistem', 'credit_value' => 20],
            ['category' => 'tugas_pokok', 'subcategory' => 'maintenance', 'description' => 'Pemeliharaan Sistem', 'credit_value' => 10],
            ['category' => 'tugas_pokok', 'subcategory' => 'evaluasi', 'description' => 'Evaluasi Sistem Informasi', 'credit_value' => 12.5],
            
            // Pengembangan Profesi
            ['category' => 'pengembangan_profesi', 'subcategory' => 'penelitian', 'description' => 'Melakukan Penelitian TI', 'credit_value' => 25],
            ['category' => 'pengembangan_profesi', 'subcategory' => 'karya_tulis', 'description' => 'Membuat Karya Tulis TI', 'credit_value' => 15],
            ['category' => 'pengembangan_profesi', 'subcategory' => 'presentasi', 'description' => 'Presentasi Ilmiah', 'credit_value' => 10],
            ['category' => 'pengembangan_profesi', 'subcategory' => 'mentoring', 'description' => 'Membimbing Junior', 'credit_value' => 5],
            
            // Penunjang
            ['category' => 'penunjang', 'subcategory' => 'organisasi', 'description' => 'Keanggotaan Organisasi Profesi', 'credit_value' => 5],
            ['category' => 'penunjang', 'subcategory' => 'penghargaan', 'description' => 'Memperoleh Penghargaan', 'credit_value' => 10],
            ['category' => 'penunjang', 'subcategory' => 'publikasi', 'description' => 'Publikasi di Media', 'credit_value' => 8]
        ];

        foreach ($creditData as $data) {
            CreditSchema::create($data);
        }
        //
    }
}
