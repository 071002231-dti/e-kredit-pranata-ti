# e-Kredit Pranata TI - Development Setup Guide

## 🛠️ Prerequisites Installation

### 1. Required Software
```bash
# Node.js (untuk frontend)
# Download dari: https://nodejs.org/ (LTS version)

# Composer (untuk Laravel)
# Download dari: https://getcomposer.org/download/

# XAMPP atau Laragon (untuk MySQL & PHP)
# XAMPP: https://www.apachefriends.org/
# Laragon: https://laragon.org/ (recommended untuk Windows)
```

## 📁 Project Structure
```
e-kredit-pranata-ti/
├── frontend/              # React TypeScript
├── backend/              # Laravel API
├── database/             # SQL files & seeders
└── docs/                # Documentation
```

## 🎯 Phase 1: Backend Setup (Laravel + MySQL)

### 1. Create Laravel Project
```bash
# Buat direktori utama
mkdir e-kredit-pranata-ti
cd e-kredit-pranata-ti

# Create Laravel backend
composer create-project laravel/laravel backend
cd backend

# Install additional packages
composer require laravel/sanctum
composer require spatie/laravel-permission
composer require maatwebsite/excel
```

### 2. Environment Configuration
```env
# Edit file .env di folder backend
APP_NAME="e-Kredit Pranata TI"
APP_ENV=local
APP_KEY=base64:... # akan generate otomatis
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_kredit_pranata_ti
DB_USERNAME=root
DB_PASSWORD=

# Email Configuration (opsional untuk notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### 3. Database Schema Migration
```bash
# Generate migrations
php artisan make:migration create_users_table --create=users
php artisan make:migration create_credit_schema_table --create=credit_schema
php artisan make:migration create_activities_table --create=activities
php artisan make:migration create_approvals_table --create=approvals
```

### 4. Migration Files Content

#### users migration:
```php
// database/migrations/xxxx_create_users_table.php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nip', 18)->unique();
        $table->string('name');
        $table->string('email')->unique()->nullable();
        $table->string('position')->nullable();
        $table->string('unit_kerja')->nullable();
        $table->enum('role', ['user', 'verifier', 'admin'])->default('user');
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}
```

#### credit_schema migration:
```php
// database/migrations/xxxx_create_credit_schema_table.php
public function up()
{
    Schema::create('credit_schema', function (Blueprint $table) {
        $table->id();
        $table->string('category', 50);
        $table->string('subcategory', 50);
        $table->string('description');
        $table->decimal('credit_value', 5, 2);
        $table->decimal('max_credit', 5, 2)->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
```

#### activities migration:
```php
// database/migrations/xxxx_create_activities_table.php
public function up()
{
    Schema::create('activities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('category', 50);
        $table->string('subcategory', 50);
        $table->string('title', 200);
        $table->text('description')->nullable();
        $table->date('activity_date');
        $table->string('volume', 50)->nullable();
        $table->decimal('credit_value', 5, 2);
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->string('proof_file')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}
```

#### approvals migration:
```php
// database/migrations/xxxx_create_approvals_table.php
public function up()
{
    Schema::create('approvals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('activity_id')->constrained()->onDelete('cascade');
        $table->foreignId('verifier_id')->constrained('users')->onDelete('cascade');
        $table->enum('status', ['approved', 'rejected']);
        $table->text('comments')->nullable();
        $table->timestamp('approved_at');
        $table->timestamps();
    });
}
```

### 5. Create Models
```bash
# Generate models
php artisan make:model User
php artisan make:model CreditSchema
php artisan make:model Activity
php artisan make:model Approval

# Generate controllers
php artisan make:controller API/AuthController
php artisan make:controller API/ActivityController --resource
php artisan make:controller API/CreditSchemaController --resource
php artisan make:controller API/DashboardController
```

### 6. Seeders for Initial Data
```bash
# Create seeders
php artisan make:seeder CreditSchemaSeeder
php artisan make:seeder UserSeeder
```

#### CreditSchemaSeeder content:
```php
// database/seeders/CreditSchemaSeeder.php
public function run()
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
}
```

### 7. Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed --class=CreditSchemaSeeder

# Install Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# Generate application key
php artisan key:generate
```

## 🎯 Phase 2: Frontend Setup (React + TypeScript)

### 1. Create React Project
```bash
# Kembali ke root directory
cd .. # dari folder backend ke root

# Create React frontend
npx create-react-app frontend --template typescript
cd frontend

# Install dependencies
npm install lucide-react axios
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### 2. Configure Tailwind CSS
```javascript
// tailwind.config.js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

### 3. Update CSS
```css
/* src/index.css */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### 4. API Configuration
```typescript
// src/config/api.ts
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

export const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add auth token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
```

### 5. Replace App.tsx
Replace the content of `src/App.tsx` with the .tsx file you downloaded earlier.

## 🚀 Phase 3: Basic API Routes (Laravel)

### 1. API Routes
```php
// routes/api.php
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\CreditSchemaController;
use App\Http\Controllers\API\DashboardController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::apiResource('activities', ActivityController::class);
    Route::get('/credit-schema', [CreditSchemaController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // File upload
    Route::post('/activities/{activity}/upload', [ActivityController::class, 'uploadProof']);
});
```

## 📝 Development Commands

### Backend (Laravel)
```bash
# Start Laravel development server
cd backend
php artisan serve # akan berjalan di http://localhost:8000

# Watch for file changes (optional)
php artisan serve --host=0.0.0.0 --port=8000
```

### Frontend (React)
```bash
# Start React development server
cd frontend
npm start # akan berjalan di http://localhost:3000
```

## 🔧 Next Steps (Week 1-2)

1. ✅ Follow setup guide di atas
2. ✅ Test koneksi database (buka http://localhost:8000)
3. ✅ Test React app (buka http://localhost:3000)
4. 🔄 Create basic AuthController untuk login/register
5. 🔄 Create basic ActivityController untuk CRUD
6. 🔄 Test API endpoints dengan Postman
7. 🔄 Connect frontend dengan backend API

## 📞 Support & Troubleshooting

### Common Issues:
- **MySQL Connection**: Pastikan XAMPP/Laragon MySQL service running
- **CORS Error**: Install `laravel-cors` package
- **File Permission**: Set proper permissions untuk storage folder
- **Node Modules**: Jika error, coba `npm install` ulang

Ready untuk mulai development! 🚀