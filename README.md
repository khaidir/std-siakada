# SIAKAD — Sistem Informasi Akademik

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white)](https://php.net)
[![Vue.js](https://img.shields.io/badge/Vue-3-4FC08D?logo=vuedotjs&logoColor=white)](https://vuejs.org)
[![Inertia](https://img.shields.io/badge/Inertia-v3-9553E9?logo=inertia&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Pest](https://img.shields.io/badge/Pest-4-CC0000?logo=pest&logoColor=white)](https://pestphp.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

**SIAKAD** adalah aplikasi manajemen akademik modern untuk universitas dan perguruan tinggi yang mendigitalisasi seluruh proses administrasi kampus dalam satu platform terpadu. Menggantikan proses manual berbasis kertas dan spreadsheet dengan alur kerja digital yang terintegrasi.

---

## ✨ Fitur

### 🎓 Mahasiswa
| Fitur | Deskripsi |
|-------|-----------|
| **KRS Online** | Pengisian Kartu Rencana Studi dengan validasi SKS otomatis, cek bentrok jadwal, dan kuota kelas |
| **KHS** | Kartu Hasil Studi per semester — nilai, SKS, IPS, dan IPK kumulatif |
| **Transkrip Nilai** | Transkrip lengkap seluruh semester dengan ekspor PDF |
| **Presensi** | Riwayat kehadiran per mata kuliah dengan persentase |
| **Jadwal Kuliah** | Jadwal mingguan dalam tampilan tabel/agenda |
| **E-Learning (LMS)** | Akses materi, kumpulkan tugas, lihat nilai & feedback |
| **AI Academic Advisor** | Chat konsultasi akademik berbasis AI yang memahami konteks akademik Anda |
| **Skripsi Tracking** | Progress skripsi: judul, pembimbing, status, log bimbingan |
| **Kerja Praktek (KP)** | Manajemen KP: data perusahaan, logbook harian, status |

### 👨‍🏫 Dosen
| Fitur | Deskripsi |
|-------|-----------|
| **Input Nilai** | Input nilai tugas/UTS/UAS, sistem hitung otomatis (20/30/50) + konversi grade |
| **Presensi Kelas** | Kelola pertemuan dan presensi mahasiswa per pertemuan |
| **Bimbingan PA** | Setujui/tolak KRS mahasiswa bimbingan |
| **Bimbingan Skripsi** | Review progres, update status, approve log bimbingan |
| **LMS Management** | Upload materi, buat tugas, nilai submission |
| **Kehadiran Dosen** | Check-in/check-out per pertemuan |

### 🔧 Admin Akademik
| Fitur | Deskripsi |
|-------|-----------|
| **Dashboard Statistik** | Statistik akademik real-time |
| **Master Data** | CRUD Fakultas, Program Studi, Mata Kuliah, Kelas, Ruangan, Jadwal |
| **User Management** | Kelola akun, assign role, reset password |
| **KRS Approval** | Monitoring seluruh KRS mahasiswa |
| **Skripsi & KP** | Assign pembimbing, monitoring status |
| **Periode Akademik** | Kelola tahun ajaran & semester |
| **Pengumuman** | Kelola pengumuman per target role |

### 📊 Pimpinan (Read-Only)
| Fitur | Deskripsi |
|-------|-----------|
| **Dashboard Eksekutif** | Ringkasan statistik tingkat fakultas/universitas |
| **Laporan Akademik** | Laporan KRS, KHS, transkrip, presensi, kinerja dosen |

---

## 🛠 Tech Stack

### Backend
| Teknologi | Kegunaan |
|-----------|----------|
| **Laravel 13** | Framework PHP — routing, ORM, queue, caching, event |
| **PHP 8.3+** | Bahasa pemrograman dengan typed properties, enums, readonly classes |
| **Laravel Fortify** | Backend auth (login, register, 2FA, password reset) — headless |
| **Spatie Laravel Permission** | RBAC — role & permission granular |
| **Spatie Laravel Data** | DTO sebagai kontrak data antar layer |
| **Laravel AI SDK** | AI Academic Advisor (first-party Laravel SDK) |
| **Laravel Sanctum** | API token authentication |
| **Barryvdh DomPDF** | Ekspor PDF (transkrip, laporan) |
| **Maatwebsite Excel** | Ekspor Excel (nilai, laporan) |

### Frontend
| Teknologi | Kegunaan |
|-----------|----------|
| **Vue 3** | Reactive UI (Composition API, `<script setup>`) |
| **Inertia.js v3** | Monolith SPA — routing Laravel, render Vue |
| **Tailwind CSS v4** | Utility-first CSS framework |
| **Vite 7** | Build tool — HMR super cepat |
| **Ziggy** | Type-safe route helper dari Laravel ke JavaScript |
| **Recharts Vue** | Grafik & chart untuk dashboard |

### Infrastruktur
| Teknologi | Kegunaan |
|-----------|----------|
| **MySQL 8.0+** | Database relasional |
| **Docker** | Containerization — development & deployment |
| **Redis** | Caching & queue (opsional) |
| **Pest PHP** | Testing framework (feature + unit test) |
| **Playwright** | E2E testing |

---

## 🏗 Arsitektur

```
┌─────────────────────────────────────────────────┐
│                    CLIENT                        │
│  Vue 3 + Inertia.js v3 + Tailwind CSS v4        │
└──────────────────────┬──────────────────────────┘
                       │ Inertia (XHR)
┌──────────────────────┴──────────────────────────┐
│                    SERVER                        │
│  Laravel 13                                      │
│  ┌───────────────────────────────────────────┐   │
│  │ Controller + FormRequest → DTO            │   │
│  ├───────────────────────────────────────────┤   │
│  │ Service Layer (Business Logic)            │   │
│  ├───────────────────────────────────────────┤   │
│  │ Repository Layer (Data Access — no SELECT*)│   │
│  ├───────────────────────────────────────────┤   │
│  │ Cross-cutting: Fortify, RBAC, Policies,   │   │
│  │ Queue, Cache, Middleware                   │   │
│  └───────────────────────────────────────────┘   │
└──────────────────────┬──────────────────────────┘
                       │ Eloquent
┌──────────────────────┴──────────────────────────┐
│                  DATABASE                        │
│  MySQL 8.0+ / PostgreSQL 16+                     │
└─────────────────────────────────────────────────┘
```

### Aturan Arsitektur Kunci
- **Controller tipis** — tanpa business logic, hanya delegasi ke Service
- **DTO** (`spatie/laravel-data`) sebagai kontrak input antar layer
- **Repository** enkapsulasi semua query — **dilarang `SELECT *`**
- **Eager-load** kolom spesifik — **dilarang lazy-load / N+1**
- **PHP Backed Enum** untuk semua kolom status
- **Otorisasi ganda**: middleware permission + Policy (row-level)
- **`DB::transaction()`** untuk operasi multi-tabel
- **Pest test** untuk setiap fitur (feature + unit)

---

## 🚀 Panduan Deployment

### Prasyarat
- PHP 8.3+
- Composer 2.x
- Node.js 20+
- MySQL 8.0+ atau PostgreSQL 16+
- Redis (opsional, untuk cache & queue)

### Instalasi (Manual)

```bash
# 1. Clone repositori
git clone https://github.com/your-org/siakad.git
cd siakad

# 2. Install dependensi PHP
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
#    DB_DATABASE=siakad
#    DB_USERNAME=...
#    DB_PASSWORD=...

# 5. Install dependensi frontend & build
npm install
npm run build

# 6. Migrasi & seeder
php artisan migrate --seed

# 7. Jalankan development server
php artisan serve
```

### Instalasi (Docker)

```bash
# Build & jalankan container
docker compose up -d

# Jalankan migrasi & seeder
docker compose run --rm app php artisan migrate --seed

# Akses di http://localhost:8000
```

### Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@siakad.test` | `password` |
| Kaprodi | `kaprodi@siakad.test` | `password` |
| Dosen | `dosen@siakad.test` | `password` |
| Mahasiswa | `mahasiswa@siakad.test` | `password` |
| Pimpinan | `pimpinan@siakad.test` | `password` |

### Production Deployment

```bash
# Optimasi Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimasi Composer (tanpa dev)
composer install --optimize-autoloader --no-dev

# Build frontend production
npm run build

# Setup scheduler (crontab)
* * * * * cd /path/to/siakad && php artisan schedule:run >> /dev/null 2>&1

# Setup queue worker (supervisor)
php artisan queue:work --daemon
```

---

## 🧪 Testing

```bash
# Seluruh test suite
./vendor/bin/pest

# Filter per fitur
./vendor/bin/pest --filter=GradeTest
./vendor/bin/pest --filter=AttendanceTest

# Dengan coverage (Xdebug/PCOV required)
./vendor/bin/pest --coverage

# E2E (Playwright)
npx playwright test
```

---

## 📦 Struktur Direktori

```
app/
├── DTO/                    # Data Transfer Objects (spatie/laravel-data)
├── Enums/                  # PHP Backed Enums (status, grade, dll)
├── Http/
│   ├── Controllers/        # Controller tipis per role
│   │   ├── Admin/
│   │   ├── Dosen/
│   │   ├── Mahasiswa/
│   │   └── Pimpinan/
│   ├── Middleware/          # Custom middleware
│   └── Requests/            # FormRequest + validasi
├── Models/                  # Eloquent Models
├── Policies/                # Row-level authorization
├── Providers/               # Service Providers
├── Repositories/
│   ├── Contracts/           # Interface repository
│   └── Eloquent/            # Implementasi Eloquent
├── Services/                # Business logic
└── Support/                 # Helper, Calculator, dll

resources/
├── js/
│   ├── Components/          # Vue komponen global
│   │   └── ui/              # UI primitives (Button, Card, Table, dll)
│   └── Pages/               # Halaman per role
│       ├── Admin/
│       ├── Dosen/
│       ├── Mahasiswa/
│       └── Pimpinan/

tests/
├── Feature/                 # Feature test (HTTP + database)
├── Unit/                    # Unit test (service, support)
└── E2E/                     # Playwright E2E test
```

---

## 🤝 Kontribusi

Kami menyambut kontribusi! Silakan buka *issue* atau kirim *pull request*.

1. Fork repositori
2. Buat branch fitur: `git checkout -b feat/amazing-feature`
3. Commit perubahan: `git commit -m 'feat: add amazing feature'`
4. Push ke branch: `git push origin feat/amazing-feature`
5. Buka Pull Request

Pastikan semua test lulus (`./vendor/bin/pest`) dan kode sesuai standar (`./vendor/bin/pint`).

---

## 📄 Lisensi

Hak cipta © 2026. Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

## 📞 Dukungan Profesional

Butuh bantuan implementasi, kustomisasi, atau deployment SIAKAD di institusi Anda?

| Layanan | Deskripsi |
|---------|-----------|
| 🚀 **Instalasi & Deployment** | Setup server, Docker, CI/CD, domain, SSL |
| 🔧 **Kustomisasi Fitur** | Modul tambahan sesuai kebutuhan institusi |
| 🎓 **Pelatihan Tim** | Workshop penggunaan & pengembangan untuk tim IT kampus |
| ☁️ **Managed Hosting** | Hosting & maintenance bulanan dengan SLA |
| 🛡️ **Audit Keamanan** | Security review, penetration testing, hardening |

📧 **Email:** support@siakad.dev
🌐 **Website:** https://siakad.dev
💬 **WhatsApp / Telegram:** +62-xxx-xxxx-xxxx

---

> Dibangun dengan ❤️ untuk dunia pendidikan Indonesia.
