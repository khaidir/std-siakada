# 27 — Prompt: Admin — Periode Akademik

```text
Buatkan fitur kelola periode akademik (tahun ajaran & semester) untuk admin.

1. Halaman pages/admin/AcademicPeriods.vue:
   - Tab Tahun Ajaran: tabel academic_years (kode, nama, start_date, end_date, is_active) + aksi tambah/edit/hapus + toggle aktif.
   - Tab Semester: tabel semesters (tahun ajaran, tipe ganjil/genap, start_date, end_date, is_active) + aksi.
   - Badge aktif; toggle aktif memakai ConfirmDialog.

2. Backend:
   - app/Enums/SemesterType.php (ganjil, genap).
   - app/DTO/AcademicYearData.php + SemesterData.php.
   - app/Repositories/Contracts/AcademicPeriodRepository.php:
     - listYears(): Collection (SELECT id, code, name, start_date, end_date, is_active).
     - listSemesters(int $yearId): Collection (SELECT id, academic_year_id, type, start_date, end_date, is_active).
     - create/update/delete/setActive.
   - app/Services/AcademicPeriodService.php:
     - setActiveYear(yearId): DB::transaction → nonaktifkan tahun lain, aktifkan ini.
     - setActiveSemester(semesterId): DB::transaction → nonaktifkan semester lain, aktifkan ini.
   - app/Http/Controllers/Admin/AcademicPeriodController.php (index, store, update, destroy, setActive) + Request.
   - app/Policies/AcademicPeriodPolicy.php (kelola: super-admin).

3. Aturan:
   - Hanya satu tahun ajaran aktif & satu semester aktif pada satu waktu.
   - Seluruh fitur akademik mengacu pada semester aktif.
   - Validasi rentang tanggal (start < end).
   - Tidak ada SELECT *.

4. Pest test:
   - AcademicPeriodTest: set aktif menonaktifkan yang lain; CRUD sukses; tanggal invalid ditolak; role lain 403.
```
