# 01 — CTO / Tech Architect: Visi, Scope & Keputusan Teknologi

## 1. Visi

Membangun **SIAKAD ringan** — Sistem Informasi Akademik satu platform yang memungkinkan seluruh aktor kampus (super admin, kepala prodi, dosen, mahasiswa) menjalankan tugasnya secara digital tanpa proses kertas. Prioritas versi ini: **sederhana, cepat dibangun, mudah dipelihara**, namun tetap mengikuti praktik arsitektur yang benar (service–repository–DTO, tanpa `SELECT *`).

## 2. Scope (in-scope)

| Role | Fitur |
|---|---|
| Semua | Login / logout |
| **dosen** | Input nilai, absen (presensi mahasiswa), kelola materi kuliah |
| **mahasiswa** | Pilih mata kuliah (KRS), lihat materi, kumpul tugas, lihat nilai hasil belajar |
| **kaprodi** | Input/kelola mata kuliah (scoped ke program studinya) |
| **super-admin** | Insert pengguna, kelola roles & permissions, kelola semua data (prodi, mata kuliah, kelas/jadwal, pengumuman) |

**Out-of-scope versi 1:** keuangan, perpustakaan, AI advisor, aplikasi native mobile, integrasi PDDikti.

## 3. Keputusan Teknologi

| Area | Pilihan | Alasan |
|---|---|---|
| Backend | Laravel 13 (PHP 8.3+) | Ekosistem matang, migrasi & Eloquent, Fortify, Spatie |
| Frontend | Inertia.js v3 + Vue 3 + Tailwind v4 | SPA CSR ringan tanpa membangun REST terpisah; tanpa TypeScript & shadcn agar ringan |
| Auth | Laravel Fortify (headless) | Login/logout solid, siap dikustomisasi |
| RBAC | Spatie Laravel Permission | Roles & permissions granular |
| DB | MySQL 8.0+ | Standar, transaksional |
| Test | Pest (unit+feature) + Playwright (E2E) | Cepat & ekspresif |
| Ekspor | Tidak wajib di v1 (opsional dompdf) | Menjaga scope tetap ringan |

## 4. Keputusan Arsitektur Kunci

1. **Service–Repository–DTO** sebagai kontrak baku: Controller tipis → Service (business logic) → Repository (query, tanpa `SELECT *`) → Model.
2. **PHP backed enum** untuk seluruh status (bukan enum DB, bukan string bebas).
3. **Scoping data**: `kaprodi` hanya melihat prodi-nya; `dosen` hanya kelas yang diampu; `mahasiswa` hanya data dirinya. Diterapkan di repository layer.
4. **Roles**: `super-admin`, `kaprodi`, `dosen`, `mahasiswa`. `super-admin` mendapat semua permission lewat `Gate::before`.

## 5. Definisi Selesai (Definition of Done)

- Fitur memenuhi acceptance criteria-nya, lolos Pest (feature + unit), lolos Playwright untuk alur kritis.
- Tidak ada `SELECT *`, tidak ada N+1, query memakai eager-load kolom spesifik.
- Otorisasi row-level aktif (Policy) — akses lintas role ditolak.
- UI responsif dan berbahasa Indonesia.
