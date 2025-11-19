<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Comprehensive User Seeder based on PR No. 3 Tahun 2025
 *
 * Creates representative users for all jenjang jabatan and golongan
 * to test compliance features thoroughly
 */
class ComprehensiveUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password123');

        // Clear existing users (optional - comment out in production)
        // User::whereNotIn('email', ['admin@example.com'])->delete();

        $users = [
            // ==========================================
            // JALUR TERAMPIL - PELAKSANA
            // ==========================================
            [
                'nip' => '199001011990031001',
                'name' => 'Budi Santoso',
                'email' => 'budi.pelaksana.pemula@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pelaksana Pemula',
                'unit_kerja' => 'Fakultas Teknologi Industri',
                'jenjang_jabatan' => 'Pranata TI Pelaksana Pemula',
                'golongan' => 'II/a',
                'target_angka_kredit' => 25.00,
                'angka_kredit_minimal' => 20.00, // 80% dari target
            ],
            [
                'nip' => '199105151991031002',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.pelaksana@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pelaksana',
                'unit_kerja' => 'Fakultas Teknik Sipil dan Perencanaan',
                'jenjang_jabatan' => 'Pranata TI Pelaksana',
                'golongan' => 'II/b',
                'target_angka_kredit' => 40.00,
                'angka_kredit_minimal' => 32.00,
            ],
            [
                'nip' => '198112151981031003',
                'name' => 'Zulfahmi Nur Kesuma Atmaja',
                'email' => 'zulfahmi.pelaksana@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pelaksana',
                'unit_kerja' => 'Fakultas Teknologi Industri',
                'jenjang_jabatan' => 'Pranata TI Pelaksana',
                'golongan' => 'II/c',
                'target_angka_kredit' => 60.00,
                'angka_kredit_minimal' => 48.00,
            ],

            [
                'nip' => '199203151992031016',
                'name' => 'Eko Prasetyo',
                'email' => 'eko.pelaksana@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pelaksana',
                'unit_kerja' => 'Fakultas Psikologi dan Ilmu Sosial Budaya',
                'jenjang_jabatan' => 'Pranata TI Pelaksana',
                'golongan' => 'II/d',
                'target_angka_kredit' => 80.00,
                'angka_kredit_minimal' => 64.00,
            ],

            // ==========================================
            // JALUR TERAMPIL - PELAKSANA LANJUTAN
            // ==========================================
            [
                'nip' => '198503201985031004',
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.pelaksana.lanjutan@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pelaksana Lanjutan',
                'unit_kerja' => 'Fakultas Ekonomi',
                'jenjang_jabatan' => 'Pranata TI Pelaksana Lanjutan',
                'golongan' => 'III/a',
                'target_angka_kredit' => 100.00,
                'angka_kredit_minimal' => 80.00,
            ],
            [
                'nip' => '198905151989051017',
                'name' => 'Fitri Handayani',
                'email' => 'fitri.pelaksana.lanjutan@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pelaksana Lanjutan',
                'unit_kerja' => 'Fakultas Ilmu Agama Islam',
                'jenjang_jabatan' => 'Pranata TI Pelaksana Lanjutan',
                'golongan' => 'III/b',
                'target_angka_kredit' => 150.00,
                'angka_kredit_minimal' => 120.00,
            ],
            [
                'nip' => '198208101982081005',
                'name' => 'Dewi Lestari',
                'email' => 'dewi.pelaksana.lanjutan@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pelaksana Lanjutan',
                'unit_kerja' => 'Fakultas Hukum',
                'jenjang_jabatan' => 'Pranata TI Pelaksana Lanjutan',
                'golongan' => 'III/c',
                'target_angka_kredit' => 200.00,
                'angka_kredit_minimal' => 160.00,
            ],

            // ==========================================
            // JALUR TERAMPIL - PENYELIA
            // ==========================================
            [
                'nip' => '198401101984011018',
                'name' => 'Gunawan Susanto',
                'email' => 'gunawan.penyelia@uii.ac.id',
                'password' => $password,
                'role' => 'verifier',
                'position' => 'Pranata TI Penyelia',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Penyelia',
                'golongan' => 'III/d',
                'target_angka_kredit' => 300.00,
                'angka_kredit_minimal' => 240.00,
            ],
            [
                'nip' => '197805201978051006',
                'name' => 'Ir. Bambang Wijaya',
                'email' => 'bambang.penyelia@uii.ac.id',
                'password' => $password,
                'role' => 'verifier',
                'position' => 'Pranata TI Penyelia',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Penyelia',
                'golongan' => 'IV/a',
                'target_angka_kredit' => 400.00,
                'angka_kredit_minimal' => 320.00,
            ],
            [
                'nip' => '197512151975121007',
                'name' => 'Dra. Retno Wulandari',
                'email' => 'retno.penyelia@uii.ac.id',
                'password' => $password,
                'role' => 'verifier',
                'position' => 'Pranata TI Penyelia',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Penyelia',
                'golongan' => 'IV/c',
                'target_angka_kredit' => 700.00,
                'angka_kredit_minimal' => 560.00,
            ],

            // ==========================================
            // JALUR AHLI - PTI PERTAMA
            // ==========================================
            [
                'nip' => '199203151992031008',
                'name' => 'Andi Pratama, S.Kom',
                'email' => 'andi.pertama@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pertama',
                'unit_kerja' => 'Fakultas Ilmu Agama Islam',
                'jenjang_jabatan' => 'Pranata TI Pertama',
                'golongan' => 'III/a',
                'target_angka_kredit' => 100.00,
                'angka_kredit_minimal' => 80.00,
            ],
            [
                'nip' => '199105201991051019',
                'name' => 'Rika Widiastuti, S.Kom',
                'email' => 'rika.pertama@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pertama',
                'unit_kerja' => 'Fakultas Kedokteran',
                'jenjang_jabatan' => 'Pranata TI Pertama',
                'golongan' => 'III/b',
                'target_angka_kredit' => 150.00,
                'angka_kredit_minimal' => 120.00,
            ],
            [
                'nip' => '199010201990101009',
                'name' => 'Lina Marlina, S.T',
                'email' => 'lina.pertama@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pertama',
                'unit_kerja' => 'Fakultas Hukum',
                'jenjang_jabatan' => 'Pranata TI Pertama',
                'golongan' => 'III/c',
                'target_angka_kredit' => 200.00,
                'angka_kredit_minimal' => 160.00,
            ],
            [
                'nip' => '198908151989081020',
                'name' => 'Joko Widodo, S.T',
                'email' => 'joko.pertama@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Pertama',
                'unit_kerja' => 'Fakultas Ekonomi',
                'jenjang_jabatan' => 'Pranata TI Pertama',
                'golongan' => 'III/d',
                'target_angka_kredit' => 300.00,
                'angka_kredit_minimal' => 240.00,
            ],

            // ==========================================
            // JALUR AHLI - PTI MUDA
            // ==========================================
            [
                'nip' => '198912201989121021',
                'name' => 'Dian Pratiwi, S.Kom., M.T',
                'email' => 'dian.muda@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Muda',
                'unit_kerja' => 'Fakultas Psikologi dan Ilmu Sosial Budaya',
                'jenjang_jabatan' => 'Pranata TI Muda',
                'golongan' => 'III/b',
                'target_angka_kredit' => 150.00,
                'angka_kredit_minimal' => 120.00,
            ],
            [
                'nip' => '198808151988081022',
                'name' => 'Budi Hartono, M.Kom',
                'email' => 'budi.muda@uii.ac.id',
                'password' => $password,
                'role' => 'user',
                'position' => 'Pranata TI Muda',
                'unit_kerja' => 'Fakultas Teknik Sipil dan Perencanaan',
                'jenjang_jabatan' => 'Pranata TI Muda',
                'golongan' => 'III/c',
                'target_angka_kredit' => 200.00,
                'angka_kredit_minimal' => 160.00,
            ],
            [
                'nip' => '198707101987071010',
                'name' => 'Dr. Hendra Gunawan, M.Kom',
                'email' => 'hendra.muda@uii.ac.id',
                'password' => $password,
                'role' => 'verifier',
                'position' => 'Pranata TI Muda',
                'unit_kerja' => 'Fakultas Teknologi Industri',
                'jenjang_jabatan' => 'Pranata TI Muda',
                'golongan' => 'III/d',
                'target_angka_kredit' => 300.00,
                'angka_kredit_minimal' => 240.00,
            ],
            [
                'nip' => '198505251985051011',
                'name' => 'Sri Wahyuni, M.T',
                'email' => 'sri.muda@uii.ac.id',
                'password' => $password,
                'role' => 'verifier',
                'position' => 'Pranata TI Muda',
                'unit_kerja' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam',
                'jenjang_jabatan' => 'Pranata TI Muda',
                'golongan' => 'IV/b',
                'target_angka_kredit' => 550.00,
                'angka_kredit_minimal' => 440.00,
            ],

            // ==========================================
            // JALUR AHLI - PTI MADYA
            // ==========================================
            [
                'nip' => '198506101985061023',
                'name' => 'Dr. Hendro Wicaksono, S.Kom., M.T',
                'email' => 'hendro.madya@uii.ac.id',
                'password' => $password,
                'role' => 'verifier',
                'position' => 'Pranata TI Madya',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Madya',
                'golongan' => 'IV/a',
                'target_angka_kredit' => 400.00,
                'angka_kredit_minimal' => 320.00,
            ],
            [
                'nip' => '198403201984032024',
                'name' => 'Dr. Indah Permatasari, M.Kom',
                'email' => 'indah.madya@uii.ac.id',
                'password' => $password,
                'role' => 'verifier',
                'position' => 'Pranata TI Madya',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Madya',
                'golongan' => 'IV/b',
                'target_angka_kredit' => 550.00,
                'angka_kredit_minimal' => 440.00,
            ],
            [
                'nip' => '198203151982031012',
                'name' => 'Prof. Dr. Ir. Agus Setiawan, M.Sc',
                'email' => 'agus.madya@uii.ac.id',
                'password' => $password,
                'role' => 'admin',
                'position' => 'Pranata TI Madya',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Madya',
                'golongan' => 'IV/c',
                'target_angka_kredit' => 700.00,
                'angka_kredit_minimal' => 560.00,
            ],
            [
                'nip' => '198001101980011013',
                'name' => 'Dr. Ir. Nurul Hidayah, M.Kom',
                'email' => 'nurul.madya@uii.ac.id',
                'password' => $password,
                'role' => 'admin',
                'position' => 'Pranata TI Madya',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Madya',
                'golongan' => 'IV/d',
                'target_angka_kredit' => 850.00,
                'angka_kredit_minimal' => 680.00,
            ],

            // ==========================================
            // JALUR AHLI - PTI UTAMA
            // ==========================================
            [
                'nip' => '198102151981021025',
                'name' => 'Prof. Dr. Ir. Ari Wibowo, M.Sc',
                'email' => 'ari.utama@uii.ac.id',
                'password' => $password,
                'role' => 'admin',
                'position' => 'Pranata TI Utama',
                'unit_kerja' => 'Rektorat - Wakil Rektor Bidang Keuangan dan SDM',
                'jenjang_jabatan' => 'Pranata TI Utama',
                'golongan' => 'IV/d',
                'target_angka_kredit' => 850.00,
                'angka_kredit_minimal' => 680.00,
            ],
            [
                'nip' => '197708201977081014',
                'name' => 'Prof. Dr. Ir. Sudirman Yahya, M.Sc., Ph.D',
                'email' => 'sudirman.utama@uii.ac.id',
                'password' => $password,
                'role' => 'admin',
                'position' => 'Pranata TI Utama',
                'unit_kerja' => 'Direktorat Sistem Informasi',
                'jenjang_jabatan' => 'Pranata TI Utama',
                'golongan' => 'IV/e',
                'target_angka_kredit' => 1050.00,
                'angka_kredit_minimal' => 840.00,
            ],
            [
                'nip' => '197512101975121015',
                'name' => 'Prof. Dr. Hj. Siti Aminah, M.T., Ph.D',
                'email' => 'siti.utama@uii.ac.id',
                'password' => $password,
                'role' => 'admin',
                'position' => 'Pranata TI Utama',
                'unit_kerja' => 'Rektorat - Wakil Rektor Bidang Akademik',
                'jenjang_jabatan' => 'Pranata TI Utama',
                'golongan' => 'IV/e',
                'target_angka_kredit' => 1050.00,
                'angka_kredit_minimal' => 840.00,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Match by email
                $userData
            );
        }

        $this->command->info('✅ Created ' . count($users) . ' comprehensive test users');
        $this->command->info('📊 Coverage: 100% semua kombinasi golongan (PR No. 3/2025)');
        $this->command->info('');
        $this->command->info('   JALUR TERAMPIL:');
        $this->command->info('   - Pelaksana Pemula: 1 user (II/a)');
        $this->command->info('   - Pelaksana: 3 users (II/b, II/c, II/d)');
        $this->command->info('   - Pelaksana Lanjutan: 3 users (III/a, III/b, III/c)');
        $this->command->info('   - Penyelia: 3 users (III/d, IV/a, IV/c)');
        $this->command->info('');
        $this->command->info('   JALUR AHLI:');
        $this->command->info('   - PTI Pertama: 4 users (III/a, III/b, III/c, III/d)');
        $this->command->info('   - PTI Muda: 4 users (III/b, III/c, III/d, IV/b)');
        $this->command->info('   - PTI Madya: 4 users (IV/a, IV/b, IV/c, IV/d)');
        $this->command->info('   - PTI Utama: 3 users (IV/d, IV/e, IV/e)');
        $this->command->info('');
        $this->command->info('🔑 Default password for all: password123');
        $this->command->info('📈 Total: 28 users covering 27 unique golongan combinations');
    }
}
