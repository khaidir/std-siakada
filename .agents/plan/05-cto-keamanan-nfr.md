# 05 — CTO / Tech Architect: Keamanan & Non-Fungsional

## 1. Keamanan

| Area | Implementasi |
|---|---|
| RBAC | Spatie Permission; roles & permission seperti `plan/04` |
| Row-level auth | Policy per entitas (`GradePolicy`, `AttendancePolicy`, `MaterialPolicy`, `StudyPlanPolicy`, `StudyPlanDetailPolicy`, `ThesisPolicy`, `InternshipPolicy`, `LecturerAttendancePolicy`, `AcademicPeriodPolicy`, `CoursePolicy`, `UserPolicy`) |
| Scoping | Repository menerima scope (prodi/kelas/student) dari context user; `kaprodi` & `dosen` & `mahasiswa` tidak bisa membaca data di luar lingkupnya |
| CSRF | Aktif bawaan Laravel untuk semua request state-changing (form Inertia) |
| Security headers | Middleware global: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `HSTS` (production) |
| Rate limiting | Login & endpoint sensitif (simpan nilai, submit tugas, KRS, endpoint AI advisor) |
| Password | Hashing bawaan Fortify; reset password via email |

## 2. Performa

- **Tanpa `SELECT *`** — semua query `->select([...])`; eager-load kolom spesifik.
- **Index** sesuai `plan/03`.
- **Pagination** pada list (nilai, absen, pengguna, mata kuliah).
- **Caching** opsional untuk master data (prodi, mata kuliah) dengan invalidasi saat berubah.

## 3. Maintainability

- Arsitektur service–repository–DTO konsisten; PHP enums; `Laravel Pint` untuk format.
- Nama route konsisten (`dosen.nilai.index`, `mahasiswa.krs.index`, dst.) dengan Ziggy di frontend.

## 4. Usability

- UI Bahasa Indonesia; responsive (sidebar collapse, tabel responsif, form satu kolom di mobile).
- Pesan validasi & flash (sukses/gagal) di seluruh aksi.

## 5. Testing (target)

- Unit: service & kalkulasi (grade, validasi KRS).
- Feature: alur tiap role + penolakan akses lintas role.
- E2E (Playwright): login → peran → aksi kritis per role (lihat `plan/11`).
