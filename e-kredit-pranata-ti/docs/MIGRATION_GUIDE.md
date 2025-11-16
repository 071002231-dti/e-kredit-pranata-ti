# 📦 Migration Guide - Konsolidasi ke Monorepo

**Date**: November 16, 2025
**Version**: 2.0.0
**Migration Type**: Struktur Project Konsolidasi

---

## 🎯 Tujuan Migrasi

Menyatukan dua aplikasi terpisah (`e-kredit-web` dan `e-kredit-pranata-ti`) menjadi satu monorepo dengan struktur yang lebih terorganisir dan maintainable.

---

## 📋 Perubahan Struktur

### Struktur LAMA

```
/Users/4h3/myproject/
├── e-kredit-web/                    # Standalone React app
│   └── src/
└── e-kredit-pranata-ti/
    ├── backend/                     # Laravel API
    └── frontend/                    # Old React app (submodule)
```

### Struktur BARU (Monorepo)

```
/Users/4h3/myproject/
└── e-kredit-pranata-ti/             # Root monorepo
    ├── backend/                     # Laravel API + WhatsApp Integration
    ├── web-client/                  # React Web App (dari e-kredit-web)
    ├── archive/
    │   └── frontend-old/            # Backup old frontend
    ├── docs/                        # Documentation
    └── mockups/                     # Visual mockups
```

---

## 🔧 Perubahan Teknis

### Backend (Tidak Berubah)
- Location: `backend/`
- Port: `http://localhost/api` (port 80)
- Stack: Laravel 12 + MySQL + WhatsApp Integration
- ✅ Tidak ada perubahan pada kode backend

### Web Client (Updated)

| Aspek | Lama | Baru |
|-------|------|------|
| **Location** | `/e-kredit-web/` | `/e-kredit-pranata-ti/web-client/` |
| **Framework** | CRA | Vite |
| **Dev Port** | 3000 | 5173 |
| **Stack** | React 19 + CRA | React 19 + Vite + Tailwind |
| **Command** | `npm start` | `npm run dev` |

### WhatsApp Integration
- Tetap di `backend/app/Services/WhatsApp/`
- Tidak ada perubahan lokasi atau konfigurasi

---

## 🚀 Setup Development (Post-Migration)

### 1. Backend Setup

```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/backend
./vendor/bin/sail up -d
```

Backend API: http://localhost/api

### 2. Web Client Setup

```bash
cd /Users/4h3/myproject/e-kredit-pranata-ti/web-client
npm install  # Jika belum
npm run dev
```

Web Client: http://localhost:5173

---

## 📝 File Konfigurasi

### web-client/.env

```env
VITE_API_URL=http://localhost/api
VITE_APP_NAME=e-Kredit Pranata TI
```

### backend/.env

Tidak ada perubahan dari sebelumnya. WhatsApp config tetap sama.

---

## ✅ Checklist Post-Migration

### Developer Checklist

- [ ] Pull latest code from repository
- [ ] Update bookmark/alias untuk folder baru
- [ ] Verify `web-client/.env` sudah ada
- [ ] Run `npm install` di web-client
- [ ] Test dev server: `npm run dev` di web-client
- [ ] Verify API connection ke backend
- [ ] Test login dengan test users

### Git Workflow

```bash
# Status check
git status

# Frontend lama sudah deleted
git add frontend

# Tambah web-client baru
git add web-client/

# Tambah archive
git add archive/

# Update documentation
git add README.md docs/

# Commit
git commit -m "Konsolidasi ke monorepo: web-client + backend + WhatsApp"
```

---

## 🗑️ Yang Dihapus/Diarsipkan

### Dihapus Permanen
- `/e-kredit-web/` folder (sudah di-move ke web-client)
- Old frontend submodule references

### Diarsipkan (Backup)
- `archive/frontend-old/` - Original frontend sebelum migration
- Archive dokumentasi di `archive/` folder

**Note**: Archive bisa dihapus setelah 1-2 sprint jika tidak ada issue.

---

## 🐛 Troubleshooting

### Port 5173 sudah digunakan?

```bash
# Kill process di port 5173
lsof -ti:5173 | xargs kill -9

# Atau gunakan port alternatif
npm run dev -- --port 3000
```

### API Connection Error?

Pastikan backend running:

```bash
cd backend
./vendor/bin/sail ps
```

### Import Errors setelah migration?

Hapus node_modules dan reinstall:

```bash
cd web-client
rm -rf node_modules package-lock.json
npm install
```

---

## 📚 Dokumentasi Update

Semua dokumentasi sudah diupdate untuk reflect struktur baru:

- ✅ Root `README.md`
- ✅ `web-client/README.md`
- ✅ `docs/06-FRONTEND-GUIDE.md` → Sekarang `Web Client Guide`
- ✅ Quick Start guides
- ✅ API documentation (tidak berubah)

---

## 🎉 Benefits dari Migrasi

1. **Single Repository** - Semua kode dalam satu repo
2. **Better Organization** - Struktur folder lebih jelas
3. **Modern Stack** - Vite untuk faster development
4. **Clear Separation** - Backend, Web Client, WhatsApp Integration jelas terpisah
5. **Easier Deployment** - Docker compose bisa orchestrate semua services

---

## 📞 Support

Jika ada issue setelah migration:

1. Check troubleshooting section di atas
2. Verify semua services running: backend + web-client
3. Check git status untuk ensure semua files committed
4. Restore dari archive jika diperlukan

---

**Migration Date**: November 16, 2025
**Status**: ✅ Complete
**Next Steps**: Test end-to-end functionality
