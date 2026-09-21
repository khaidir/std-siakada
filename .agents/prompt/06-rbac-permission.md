# 06 — Prompt: RBAC (Roles, Permissions, Middleware, Policy)

```text
Implementasikan RBAC SIAKAD dengan Spatie Laravel Permission + Policy row-level.

1. RoleAndPermissionSeeder (per .agents/prompt/03):
   - Roles: super-admin, kaprodi, dosen, mahasiswa, pimpinan.
   - Permissions & mapping sesuai plan/04.

2. Gate::before di AuthServiceProvider:
   - Jika user punya role super-admin → return true (akses penuh).

3. Middleware:
   - Alias 'role' → \Spatie\Permission\Middleware\RoleMiddleware
   - Alias 'permission' → \Spatie\Permission\Middleware\PermissionMiddleware
   - Route memakai middleware(['auth','role:<role>']) dan/atau permission.

4. Policy (row-level) untuk setiap entitas:
   - UserPolicy: hanya super-admin yang kelola user.
   - CoursePolicy: super-admin & kaprodi (scoped prodi); dosen/mahasiswa hanya view.
   - CourseOfferingPolicy: super-admin kelola; kaprodi view prodi-nya; dosen view kelas yang diampu; mahasiswa view prodi-nya.
   - StudyPlanPolicy & StudyPlanDetailPolicy: mahasiswa hanya kelola KRS miliknya; dosen PA approve KRS mahasiswa bimbingannya; admin view semua.
   - GradePolicy: dosen hanya nilai di kelas yang diampu; mahasiswa hanya lihat nilainya; kaprodi view prodi-nya; pimpinan read-only.
   - AttendancePolicy: dosen hanya kelas yang diampu; mahasiswa lihat miliknya.
   - MaterialPolicy: dosen kelola kelas yang diampu; mahasiswa lihat kelas yang diikuti.
   - AssignmentPolicy & SubmissionPolicy: dosen kelola kelasnya; mahasiswa submit miliknya.
   - ThesisPolicy & ThesisLogPolicy: mahasiswa lihat/mengisi miliknya; dosen pembimbing view/update/approve bimbingannya; admin assign pembimbing; kaprodi view prodi-nya.
   - InternshipPolicy & InternshipLogPolicy: mahasiswa lihat/mengisi miliknya; dosen pembimbing approve logbook; admin assign pembimbing; kaprodi view prodi-nya.
   - LecturerAttendancePolicy: dosen isi miliknya; admin & pimpinan view.
   - AcademicPeriodPolicy: hanya super-admin kelola; role lain read.

5. Scoping data di repository layer:
   - Contoh: CourseRepository::listForStudyProgram(int $prodiId) memakai ->select([...]) + ->where('study_program_id', $prodiId).
   - GradeRepository::listByOffering(int $offeringId) memfilter by kelas.
   - StudyPlanRepository::listForStudent(int $studentId) memfilter by mahasiswa; listForAdvisor(int $lecturerId) memfilter by mahasiswa bimbingan.
   - ThesisRepository::listForSupervisor(int $lecturerId) memfilter by supervisor_1/2.

6. Setiap Controller memanggil $this->authorize(...) di tiap aksi.

7. Pest test PermissionTest:
   - Setiap role hanya bisa akses route miliknya (403 untuk yang lain).
   - super-admin bisa akses semua.

PASTIKAN: tidak ada SELECT *; query permission/role pakai kolom spesifik.
```
