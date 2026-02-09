<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CreditSchemaSeeder::class,  // Use full schema seeder with 165 schemas based on PR No. 3 Tahun 2025
            UserSeeder::class,
        ]);
    }
}
