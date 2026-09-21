# .agents — Rencana & Prompt SIAKAD (lengkap per PRD)

Struktur ini berisi **Plan** (rencana teknis) dan **Prompt** (perintah siap-pakai untuk AI coding assistant) untuk membangun SIAKAD lengkap sesuai PRD. Setiap file di-split dengan prefix `01`, `02`, … agar bisa dieksekusi berurutan.

> **Dev Cycle:** Setiap kali sebuah prompt di `.agents/prompt/*` dirujuk, coding assistant wajib menjalankan siklus 4 fase — **Analisis → Write Code → Unit Test → QA** — dengan gate per fase. Definisi lengkap ada di [`AGENTS.md`](../AGENTS.md) (auto-loaded). Helper: `bash scripts/dev-cycle.sh`.

## Stack

- **Backend:** Laravel 13 (PHP 8.3+) · Fortify (auth) · Spatie Permission (RBAC) · Pest (test)
- **Frontend:** Inertia.js v3 · Vue 3 (Composition API) · Tailwind CSS v4 · Vite 7
- **Database:** MySQL 8.0+
- **E2E:** Playwright

## Role & Fitur (ringkasan)

| Role | Fitur |
|---|---|
| **super-admin** | Kelola semua data: pengguna, roles, permission, master data (fakultas, prodi, mata kuliah, ruangan, kelas/jadwal, periode akademik), monitoring KRS, skripsi & KP, kehadiran dosen |
| **kaprodi** | Kelola mata kuliah (scoped prodi), monitoring nilai & KRS & skripsi/KP, kehadiran dosen |
| **dosen** | Input nilai, presensi kelas, LMS (materi & tugas), bimbingan PA (approval KRS), bimbingan skripsi, kehadiran mengajar |
| **mahasiswa** | KRS, jadwal, presensi, materi, tugas, nilai, KHS, transkrip (PDF), AI advisor, skripsi, kerja praktek |
| **pimpinan** | Dashboard eksekutif & laporan akademik (read-only) |

## Struktur Folder

```
.agents/
├── README.md
├── plan/          # Rencana teknis (CTO/Tech Architect + Senior SWE + QA)
│   ├── 01-...md
│   └── ...
└── prompt/        # Prompt eksekusi (copy-paste ke Cursor/Copilot/Claude)
    ├── 01-...md
    └── ...
```

## Aturan Arsitektur Global (WAJIB)

Disematkan ulang di tiap prompt. Ringkasnya:

1. Lapisan **Controller → Service → Repository → Model**. Controller tipis (tanpa business logic).
2. Input berpindah sebagai **DTO** (`spatie/laravel-data`), divalidasi di **FormRequest**.
3. Repository men-enkapsulasi SEMUA query. Interface di `app/Repositories/Contracts`, implementasi di `app/Repositories/Eloquent`.
4. **DILARANG `SELECT *`** — selalu `->select([...])`; eager-load kolom spesifik `with('x:id,col')`.
5. Kolom status pakai **PHP backed enum**, disimpan sebagai string.
6. Otorisasi ganda: middleware permission + **Policy** (row-level, `$this->authorize()`).
7. Operasi multi-tabel dibungkus `DB::transaction()`.
8. Setiap fitur punya **Pest test** (feature + unit service).
9. UI Bahasa Indonesia; komponen Vue ringan di `resources/js/components/ui/`.

## Urutan Eksekusi

| Langkah | Plan | Prompt |
|---|---|---|
| 1 | `plan/01` – `plan/05` (arsitektur, DB, RBAC) | `prompt/01` setup project |
| 2 | `plan/06` – `plan/09` + `plan/12` – `plan/13` (backend/frontend/routes/fitur/domain) | `prompt/02` migrasi, `prompt/03` seeder |
| 3 | — | `prompt/04` auth, `prompt/05` layout, `prompt/06` RBAC |
| 4 | — | `prompt/07` – `prompt/31` fitur per role |
| 5 | `plan/10` – `plan/11` (testing) | `prompt/29` QA Playwright |

Setiap selesai satu prompt, jalankan **dev cycle** (lihat [`AGENTS.md`](../AGENTS.md)): `php artisan migrate` → `php artisan db:seed` → `./vendor/bin/pest` → `npm run build` → `npx playwright test` (QA), atau ringkasnya `bash scripts/dev-cycle.sh`.
