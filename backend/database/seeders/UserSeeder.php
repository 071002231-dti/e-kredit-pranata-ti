<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin - PTI Utama
        \App\Models\User::create([
            'nip' => '199001012020011001',
            'name' => 'Admin System',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'position' => 'Kepala Bidang IT',
            'unit_kerja' => 'Bidang IT',
            'jenjang_jabatan' => 'Pranata TI Utama',
            'golongan' => 'IV/e',
            'target_angka_kredit' => 1050.00,
            'angka_kredit_minimal' => 840.00, // 80% dari 1050
        ]);

        // Create Verifier - PTI Madya
        \App\Models\User::create([
            'nip' => '199002022020012002',
            'name' => 'Verifikator Satu',
            'email' => 'verifier@example.com',
            'password' => bcrypt('password'),
            'role' => 'verifier',
            'position' => 'Pranata TI Ahli Madya',
            'unit_kerja' => 'Sub Bidang Infrastruktur',
            'jenjang_jabatan' => 'Pranata TI Madya',
            'golongan' => 'IV/c',
            'target_angka_kredit' => 700.00,
            'angka_kredit_minimal' => 560.00, // 80% dari 700
        ]);

        // Create Regular User - PTI Muda
        \App\Models\User::create([
            'nip' => '199003032020013003',
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'position' => 'Pranata TI Ahli Muda',
            'unit_kerja' => 'Sub Bidang Aplikasi',
            'jenjang_jabatan' => 'Pranata TI Muda',
            'golongan' => 'III/d',
            'target_angka_kredit' => 300.00,
            'angka_kredit_minimal' => 240.00, // 80% dari 300
        ]);

        // Create additional test users for different jenjang
        \App\Models\User::create([
            'nip' => '199004042020014004',
            'name' => 'PTI Pertama',
            'email' => 'pertama@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'position' => 'Pranata TI Ahli Pertama',
            'unit_kerja' => 'Sub Bidang Aplikasi',
            'jenjang_jabatan' => 'Pranata TI Pertama',
            'golongan' => 'III/a',
            'target_angka_kredit' => 100.00,
            'angka_kredit_minimal' => 80.00, // 80% dari 100
        ]);

        \App\Models\User::create([
            'nip' => '199005052020015005',
            'name' => 'PTI Pelaksana',
            'email' => 'pelaksana@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'position' => 'Pranata TI Pelaksana',
            'unit_kerja' => 'Sub Bidang Infrastruktur',
            'jenjang_jabatan' => 'Pranata TI Pelaksana',
            'golongan' => 'II/b',
            'target_angka_kredit' => 40.00,
            'angka_kredit_minimal' => 32.00, // 80% dari 40
        ]);
    }
}
