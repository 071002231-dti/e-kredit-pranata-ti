# e-Kredit Pranata TI

> **Sistem Manajemen Angka Kredit untuk Jabatan Fungsional Pranata Teknologi Informasi**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19.1.1-61DAFB?logo=react)](https://reactjs.org)
[![WhatsApp](https://img.shields.io/badge/WhatsApp-API-25D366?logo=whatsapp)](https://developers.facebook.com/docs/whatsapp)
[![Status](https://img.shields.io/badge/Status-Active-success)]()

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Dokumentasi](#dokumentasi)
- [Quick Start](#quick-start)
- [Struktur Proyek](#struktur-proyek)
- [Status Implementasi](#status-implementasi)
- [Kontribusi](#kontribusi)

---

## Tentang Proyek

**e-Kredit Pranata TI** adalah sistem manajemen angka kredit yang dirancang khusus untuk Jabatan Fungsional Pranata Teknologi Informasi sesuai dengan **Peraturan Menteri PANRB No. 3 Tahun 2025**.

Sistem ini menyediakan:
- ✅ Platform web untuk manajemen aktivitas
- ✅ Integrasi WhatsApp untuk akses mobile
- ✅ Validasi compliance otomatis
- ✅ Workflow approval yang terstruktur
- ✅ Dashboard analytics & reporting

### Compliance

Sistem ini memastikan kepatuhan terhadap aturan:
- **Unsur Utama**: Minimal 80% dari total angka kredit
- **Unsur Penunjang**: Maksimal 20% dari total angka kredit
- **Jenjang Jabatan**: 5 tingkatan (Pelaksana s.d. Ahli Utama)

---

## Fitur Utama

### 🌐 Platform Web (React + Laravel)

#### Untuk Pranata TI:
- 📝 Submit aktivitas dengan detail lengkap
- 📊 Dashboard angka kredit real-time
- 📋 Riwayat aktivitas dan status
- ✅ Validasi compliance otomatis
- 📈 Progress tracking

#### Untuk Verifier/Admin:
- 🔍 Review queue aktivitas pending
- ✅ Approve/reject dengan komentar
- 📊 Dashboard overview semua user
- 📈 Analytics & reporting
- 👥 User management

### 📱 Integrasi WhatsApp (Phase 1-3 Complete!)

#### User Features:
- **`/register`** - Daftar via WhatsApp
- **`/stats`** - Lihat statistik lengkap
- **`/activities`** - Browse riwayat (pagination)
- **`/detail <ID>`** - Detail aktivitas
- **`/submit`** - Ajukan aktivitas via Flow
- **`/help`** - Bantuan

#### System Features:
- 🔔 Notifikasi otomatis (approve/reject)
- 📝 Form native di WhatsApp (Flows)
- 💬 Conversational interface
- 🔐 Secure authentication
- 📊 Real-time statistics

---

## Teknologi

### Backend
- **Framework**: Laravel 12
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum
- **Queue**: Laravel Queue (async processing)
- **API**: RESTful + WhatsApp Cloud API

### Web Client (Frontend)
- **Framework**: React 19 + Vite
- **Language**: TypeScript
- **UI Library**: Tailwind CSS v3 + Custom Components
- **State Management**: TanStack Query (React Query)
- **Form Handling**: React Hook Form + Zod
- **Router**: React Router v7
- **HTTP Client**: Axios
- **Icons**: Lucide React

### DevOps
- **Containerization**: Docker + Docker Compose
- **Web Server**: Nginx (via Docker)
- **Development**: LocalTunnel / ngrok
- **Production**: TBD

### Integrasi
- **WhatsApp Cloud API** (Meta Business Platform)
- **WhatsApp Flows** (Interactive forms)
- **Event-driven** (Laravel Events & Listeners)

---

## Dokumentasi

### 📚 Panduan Lengkap

| Dokumen | Deskripsi | Target Audiens |
|---------|-----------|----------------|
| [02-WHATSAPP-INTEGRATION](docs/02-WHATSAPP-INTEGRATION.md) | ⭐ **Panduan lengkap WhatsApp** | Developer, Admin |
| [03-API-DOCUMENTATION](docs/03-API-DOCUMENTATION.md) | REST API endpoints | Developer |
| [04-DATABASE-SCHEMA](docs/04-DATABASE-SCHEMA.md) | Struktur database | Developer, DBA |
| [05-COMPLIANCE-GUIDE](docs/05-COMPLIANCE-GUIDE.md) | PR No. 3 Tahun 2025 | Product Owner, Admin |
| [06-FRONTEND-GUIDE](docs/06-FRONTEND-GUIDE.md) | React app development | Frontend Dev |

### 🎨 Mockups & Demos

| File | Deskripsi |
|------|-----------|
| [mockups/VISUAL_MOCKUP.html](mockups/VISUAL_MOCKUP.html) | Interactive visual mockup |
| [mockups/USER_SCENARIOS.md](mockups/USER_SCENARIOS.md) | User journey scenarios |

---

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Node.js 18+ & npm
- Git

### Installation

```bash
# 1. Clone repository
git clone https://github.com/your-org/e-kredit-pranata-ti.git
cd e-kredit-pranata-ti

# 2. Start backend (Laravel + MySQL)
cd backend
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed

# 3. Start web client (React)
cd ../web-client
npm install
npm run dev

# 4. Access aplikasi
# Backend API: http://localhost/api
# Web Client: http://localhost:5173
```

### Setup WhatsApp (Optional)

Untuk mengaktifkan fitur WhatsApp:

1. Baca panduan: [`docs/02-WHATSAPP-INTEGRATION.md`](docs/02-WHATSAPP-INTEGRATION.md)
2. Setup Meta Business Account
3. Configure webhook
4. Update `.env` dengan credentials
5. Test end-to-end

**Status WhatsApp**: ✅ Kode complete, pending Meta Business setup

---

## Struktur Proyek

```
e-kredit-pranata-ti/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/  # API Controllers
│   │   ├── Models/            # Eloquent Models
│   │   ├── Services/          # Business Logic
│   │   │   └── WhatsApp/      # WhatsApp Integration
│   │   ├── Events/            # Laravel Events
│   │   └── Listeners/         # Event Listeners
│   ├── database/
│   │   ├── migrations/        # Database migrations
│   │   └── seeders/           # Data seeders
│   ├── routes/
│   │   └── api.php            # API routes
│   └── .env.example           # Environment template
│
├── web-client/                 # React Web App (Vite)
│   ├── src/
│   │   ├── components/        # React components
│   │   ├── pages/             # Page components
│   │   ├── services/          # API services
│   │   └── types/             # TypeScript types
│   ├── package.json
│   └── README.md              # Web client docs
│
├── archive/                    # Archived files
│   └── frontend-old/          # Original frontend (backup)
│
├── docs/                       # Documentation
│   ├── 02-WHATSAPP-INTEGRATION.md ⭐
│   ├── 03-API-DOCUMENTATION.md
│   ├── 04-DATABASE-SCHEMA.md
│   ├── 05-COMPLIANCE-GUIDE.md
│   └── 06-FRONTEND-GUIDE.md
│
├── mockups/                    # Visual mockups
│   ├── VISUAL_MOCKUP.html
│   └── USER_SCENARIOS.md
│
├── docker-compose.yml          # Docker configuration
└── README.md                   # This file
```

---

## Status Implementasi

### ✅ Complete

- [x] Backend API (Laravel)
- [x] Frontend Dashboard (React)
- [x] Authentication & Authorization
- [x] Activity Management
- [x] Approval Workflow
- [x] Compliance Validation
- [x] **WhatsApp Integration Phase 1-3**
  - [x] Infrastructure & Webhook
  - [x] Enhanced Messaging
  - [x] WhatsApp Flows
  - [x] Notifications

### 🚧 In Progress

- [ ] Meta Business Account setup
- [ ] End-to-end WhatsApp testing
- [ ] Production deployment

### 📋 Roadmap

- [ ] File upload di WhatsApp Flows
- [ ] Verifier approval via WhatsApp
- [ ] Multi-language (ID/EN)
- [ ] Mobile app (React Native)
- [ ] Advanced analytics
- [ ] Export reports (PDF/Excel)

---

## Compliance Rules

Sesuai **PR No. 3 Tahun 2025**:

### Jenjang Jabatan
1. Pelaksana
2. Penyelia
3. Ahli Pertama
4. Ahli Muda
5. Ahli Madya
6. Ahli Utama

### Komposisi Angka Kredit
- **Unsur Utama**: ≥ 80%
- **Unsur Penunjang**: ≤ 20%

Sistem akan **otomatis validate** dan memberikan warning jika tidak compliance.

**Analisis lengkap**: [`docs/05-COMPLIANCE-GUIDE.md`](docs/05-COMPLIANCE-GUIDE.md)

---

## Kontribusi

Kami menerima kontribusi! Silakan:

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

### Development Guidelines
- Follow PSR-12 (PHP)
- Use TypeScript (Frontend)
- Write tests for new features
- Update documentation
- Follow commit message conventions

---

## Support & Contact

### Dokumentasi
- 📚 [Documentation Hub](docs/)
- 💬 [WhatsApp Integration Guide](docs/02-WHATSAPP-INTEGRATION.md)
- 🎨 [Visual Mockup](mockups/VISUAL_MOCKUP.html)

### Issues
- 🐛 Report bugs via GitHub Issues
- 💡 Request features via GitHub Issues

---

## Changelog

### Version 1.0.0 (2025-11-13)
- ✅ Initial release
- ✅ Complete WhatsApp integration (Phase 1-3)
- ✅ Web platform (React + Laravel)
- ✅ Compliance engine
- ✅ Notification system
- ✅ Comprehensive documentation

---

## Acknowledgments

- **Meta** - WhatsApp Cloud API & Flows
- **Laravel** - Web application framework
- **React** - Frontend library
- **Kementerian PANRB** - PR No. 3 Tahun 2025

---

**Made with ❤️ for Pranata Teknologi Informasi Indonesia**

**Last Updated**: 2025-11-13 | **Version**: 1.0.0 | **Status**: ✅ Production Ready
