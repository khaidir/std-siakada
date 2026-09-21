# 01 — Prompt: Setup Project

```text
Buatkan proyek SIAKAD ringan dengan Laravel 13 dan stack Inertia v3 + Vue 3 + Tailwind v4.

KONTEKS (WAJIB DIPATUHI SEPANJANG PROYEK):
- Laravel 13 (PHP 8.3+) · Inertia.js v3 · Vue 3 (Composition API, <script setup>) · Tailwind CSS v4 · Vite 7 · MySQL 8.0+.
- Auth: Laravel Fortify (headless). RBAC: Spatie Laravel Permission. DTO: spatie/laravel-data. Test: Pest.
- Bahasa UI: Indonesia. Tanpa TypeScript, tanpa shadcn/ui (komponen Vue ringan).
- Arsitektur: Controller → Service → Repository → Model.
  - Controller tipis, business logic di Service, SEMUA query di Repository (interface di app/Repositories/Contracts, impl di app/Repositories/Eloquent).
  - DILARANG SELECT * — selalu ->select([...]); eager-load kolom spesifik with('x:id,col').
  - Kolom status pakai PHP backed enum (disimpan string).
  - Otorisasi ganda: middleware permission + Policy (row-level).
  - Operasi multi-tabel dibungkus DB::transaction().
  - Setiap fitur punya Pest test.

LANGKAH:
1. Buat proyek: composer create-project laravel/laravel siakad "^13.0"
2. Backend: composer require laravel/fortify spatie/laravel-permission spatie/laravel-data inertiajs/inertia-laravel
3. Frontend: npm install @inertiajs/vue3 vue @vitejs/plugin-vue ziggy-js ; npm install -D tailwindcss @tailwindcss/vite
4. Konfigurasi Inertia v3 + Vue 3:
   - app/Http/Middleware/HandleInertiaRequests.php: shared props (auth, role, permissions, flash).
   - resources/js/app.js: createInertiaApp + resolve halaman dari resources/js/pages/ + plugin ZiggyVue.
5. Tailwind v4: vite.config.js plugin laravel+vue+tailwindcss; resources/css/app.css: @import "tailwindcss";
6. Buat komponen UI dasar di resources/js/components/ui/: Button, Card, Input, Select, Textarea, Table, Modal, Badge, Tabs, Toast, Skeleton, Dropdown, EmptyState.
   - Vue 3 <script setup>, props via defineProps/defineEmits, tanpa TypeScript.
7. Setup Fortify:
   - Aktifkan features (login, registration optional, reset password).
   - FortifyServiceProvider; Fortify::loginView() mengembalikan Inertia::render('auth/login').
   - Halaman pages/auth/Login.vue.
8. Setup Spatie Permission: publikasikan migrasi; siapkan RoleSeeder (roles: super-admin, kaprodi, dosen, mahasiswa + permission).
9. Buat struktur folder: app/Enums, app/DTO, app/Services, app/Repositories/Contracts, app/Repositories/Eloquent, app/Policies.
10. Setup Pest: composer require pestphp/pest --dev && ./vendor/bin/pest --init

Sertakan cara menjalankan: composer install && npm install && php artisan migrate && php artisan db:seed && npm run dev.
```
