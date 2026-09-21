# AGENTS.md — Instruksi & Dev Cycle SIAKAD

File ini otomatis dimuat oleh coding assistant di repo ini dan berlaku untuk **semua sesi**.

## Stack & Konvensi

- **Backend:** Laravel 13 (PHP 8.3+) · Laravel Fortify (headless) · Spatie Permission · `spatie/laravel-data` · Pest.
- **Frontend:** Inertia.js v3 · Vue 3 (Composition API, `<script setup>`) · Tailwind CSS v4 · Vite 7 · Ziggy.
- **DB:** MySQL 8.0+ · **E2E:** Playwright.
- **Bahasa UI:** Indonesia. **Tanpa** TypeScript. **Tanpa** shadcn/ui (pakai komponen Vue ringan di `resources/js/components/ui/`).

## Aturan Arsitektur Global (WAJIB)

1. Lapisan **Controller → Service → Repository → Model**. Controller tipis (tanpa business logic).
2. Input berpindah sebagai **DTO** (`spatie/laravel-data`), divalidasi di **FormRequest**.
3. Repository men-enkapsulasi **SEMUA** query. Interface di `app/Repositories/Contracts`, implementasi di `app/Repositories/Eloquent`.
4. **DILARANG `SELECT *`** — selalu `->select([...])`; eager-load kolom spesifik `with('x:id,col')`.
5. Kolom status pakai **PHP backed enum** (disimpan sebagai string).
6. Otorisasi ganda: middleware permission + **Policy** (row-level, `$this->authorize()`).
7. Operasi multi-tabel dibungkus `DB::transaction()`.
8. Setiap fitur punya **Pest test** (feature + unit untuk service/support).

---

## DEV CYCLE — Otomatis saat `/agents/prompt/*`

> **Trigger:** Ketika user merujuk file di `.agents/prompt/` (mis. `/agents/prompt/07-dosen-input-nilai.md` atau "jalankan prompt 07"), **JANGAN langsung menulis kode**. Jalankan 4 fase di bawah **secara berurutan**, dan setiap fase memiliki **gate** yang WAJIB lulus sebelum lanjut ke fase berikutnya.

```
/agents/prompt/NN-*.md
        │
        ▼
┌─────────────────┐   ┌─────────────────┐   ┌─────────────────┐   ┌─────────────────┐
│ 1. ANALISIS     │──▶│ 2. WRITE CODE   │──▶│ 3. UNIT TEST    │──▶│ 4. QA           │
│  pahami & petakan│  │  implementasi   │   │  Pest hijau     │   │  E2E + audit    │
└─────────────────┘   └─────────────────┘   └─────────────────┘   └─────────────────┘
   Gate 1: daftar file   Gate 2: bebas error   Gate 3: pest hijau   Gate 4: siap rilis
```

### Fase 1 · ANALISIS

Tujuan: memahami fitur & dampaknya **sebelum** menulis kode.

1. Baca prompt target di `.agents/prompt/NN-*.md`.
2. Baca plan terkait di `.agents/plan/*.md` (arsitektur, rute, breakdown, testing) bila relevan.
3. Periksa kode yang sudah ada: migrasi, model, enum, repository, service, policy, routes, komponen Vue yang terkait.
4. Susun daftar file yang akan **dibuat** dan **diubah** (konkret, dengan path lengkap).
5. Tulis ringkasan analisis + daftar file ke todo list (dan bila perlu ke `/memories/session/`).

**Gate 1** — daftar file target + dependensi jelas, tidak ada konflik dengan kode existing.

### Fase 2 · WRITE CODE

1. Implementasi mengikuti arsitektur: Controller tipis → FormRequest + DTO → Service → Repository → Model.
2. Kerjakan backend dahulu, lalu frontend (Vue). Patuhi semua Aturan Arsitektur Global.
3. Perbarui routes + middleware permission (`role:...` / `permission:...`) dan daftarkan Policy di `AuthServiceProvider`.
4. Setelah tiap langkah signifikan, cek error (`get_errors` / compiler).

**Gate 2** — tidak ada error compile/lint; `php artisan migrate` sukses; `npm run build` bersih.

### Fase 3 · UNIT TEST

1. Tulis Pest test: **feature test** (akses per role, happy path, validasi, otorisasi 403) + **unit test** untuk Service/Support/Calculator.
2. Jalankan test fitur: `./vendor/bin/pest --filter=NamaTest` (lalu full `./vendor/bin/pest` bila diminta).
3. Perbaiki sampai semua **hijau** (tidak ada failure/risky).

**Gate 3** — seluruh Pest test terkait fitur lulus.

### Fase 4 · QA

1. **Audit arsitektur:** pastikan tidak ada `SELECT *`, tidak ada N+1/lazy-load, Policy terpasang & dipanggil, transaksi multi-tabel terpasang.
2. Jalankan E2E Playwright yang relevan: `npx playwright test <spec>` (spec sesuai `prompt/29`).
3. Verifikasi keamanan akses antar role (route scope) dan validasi input.

**Gate 4** — E2E lulus + audit bersih. **Laporkan ringkasan akhir**: file dibuat/diubah, hasil test, perintah verifikasi, dan catatan QA.

---

## Definition of Done (per fitur)

- [ ] Kode backend + frontend selesai, sesuai arsitektur.
- [ ] Pest feature test + unit test **hijau**.
- [ ] E2E Playwright (bila tersedia) **hijau**.
- [ ] Tidak ada `SELECT *` / N+1 / lazy-load.
- [ ] Policy + middleware permission terpasang.
- [ ] `php artisan migrate` & `php artisan db:seed` bersih.

## Perintah Verifikasi (gate)

```bash
php artisan migrate
php artisan db:seed
./vendor/bin/pest                      # Fase 3
npm run build                          # Fase 2/4
npx playwright test <spec>             # Fase 4
```

> Bisa dijalankan sekaligus lewat helper: `bash scripts/dev-cycle.sh`.
