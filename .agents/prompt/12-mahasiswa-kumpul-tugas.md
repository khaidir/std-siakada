# 12 — Prompt: Mahasiswa — Kumpul Tugas

```text
Buatkan fitur kumpul tugas untuk mahasiswa.

1. Halaman pages/mahasiswa/Tugas.vue:
   - Daftar tugas dari kelas yang diikuti (judul, deskripsi, tenggat, status sudah/belum submit, nilai & feedback bila ada).
   - Modal submit: upload file + tombol Kumpul.
   - Nonaktifkan submit bila lewat tenggat (due_date).

2. Backend:
   - app/DTO/SubmissionData.php.
   - app/Repositories/Contracts/AssignmentRepository.php + SubmissionRepository.php:
     - AssignmentRepository::listForStudent(int $studentId): Collection — SELECT id, course_offering_id, title, description, due_date, max_score + eager-load offering.course (id, name) + submission milik mahasiswa (id, score, feedback, submitted_at).
     - SubmissionRepository::create/update.
   - app/Services/SubmissionService.php:
     - submit(studentId, assignmentId, file): cek mahasiswa terdaftar di kelas (via study_plan_detail approved), cek due_date belum lewat, simpan file.
   - app/Http/Controllers/Mahasiswa/AssignmentController.php (index, submit) + SubmitRequest.php.
   - app/Policies/SubmissionPolicy.php.

3. Aturan:
   - Mahasiswa hanya submit tugas kelas yang diikuti.
   - Submit setelah due_date ditolak (403/validasi).
   - Tidak ada SELECT *.

4. Pest test:
   - SubmissionTest: submit sebelum tenggat sukses; setelah tenggat ditolak; lintas kelas 403.
```
