# 14 — Prompt: Ka Prodi — Kelola Mata Kuliah

```text
Buatkan fitur kelola mata kuliah untuk kaprodi (scoped ke prodi-nya).

1. Halaman pages/kaprodi/Courses.vue:
   - Tabel mata kuliah prodi-nya: Kode, Nama, SKS, Semester, Tipe (wajib/pilihan), aksi edit/hapus.
   - Modal form tambah/edit (kode, nama, sks, semester, type).
   - Hapus memakai ConfirmDialog.

2. Backend:
   - app/DTO/CourseData.php.
   - app/Repositories/Contracts/CourseRepository.php:
     - listForStudyProgram(int $prodiId): Collection — SELECT id, study_program_id, code, name, sks, semester, type.
     - create/update/delete.
   - app/Services/CourseService.php.
   - app/Http/Controllers/Kaprodi/CourseController.php (index, store, update, destroy) + CourseRequest.php.
   - app/Policies/CoursePolicy.php (kelola: super-admin atau kaprodi prodi-nya).

3. Aturan:
   - Kaprodi hanya mengelola mata kuliah prodi-nya (study_program_id dari profil lecturer-nya / mapping kaprodi).
   - Tidak bisa melihat/mengubah mata kuliah prodi lain (403).
   - Kode mata kuliah unik.

4. Pest test:
   - CourseTest: kaprodi CRUD MK prodi-nya sukses; MK prodi lain 403.
```
