# 30 — Prompt: Dosen — Kelola Tugas & Nilai Submission

```text
Buatkan fitur kelola tugas (assignments) dan penilaian submission untuk dosen.

1. Halaman pages/dosen/Tugas.vue:
   - Dropdown pilih kelas yang diampu.
   - Daftar tugas: Judul, Deskripsi, Tenggat, Skor Maks, jumlah submission.
   - Modal form tambah/edit tugas (judul, deskripsi, due_date, max_score).
   - Hapus memakai ConfirmDialog.

2. Halaman pages/dosen/PenilaianTugas.vue (atau tab pada Tugas.vue):
   - Pilih tugas → daftar submission mahasiswa: NIM, Nama, File (unduh), Waktu submit, Skor, Feedback.
   - Input skor (0..max_score) + feedback; tombol Simpan (batch).

3. Backend:
   - app/DTO/AssignmentData.php (course_offering_id, title, description, due_date, max_score).
   - app/DTO/SubmissionGradeData.php (submission_id, score, feedback).
   - app/Repositories/Contracts/AssignmentRepository.php:
     - listByOffering(int $offeringId): Collection — SELECT id, title, description, due_date, max_score + count submissions.
     - create/update/delete.
   - app/Repositories/Contracts/SubmissionRepository.php:
     - listByAssignment(int $assignmentId): Collection — SELECT id, assignment_id, student_id, file_path, submitted_at, score, feedback + eager-load student.user (id, nim, name).
     - gradeBatch(array $rows): void.
   - app/Services/AssignmentService.php:
     - store/update/destroy (cek dosen pengampu kelas).
     - gradeSubmissions(int $assignmentId, array $rows): DB::transaction → validasi skor 0..max_score, upsert skor & feedback.
   - app/Http/Controllers/Dosen/AssignmentController.php (index, store, update, destroy) + SubmissionGradeController.php (index, store) + Request.
   - app/Policies/AssignmentPolicy.php + SubmissionPolicy.php (dosen hanya kelas yang diampu).

4. Aturan:
   - Dosen hanya kelola tugas & nilai submission pada kelas yang diampu.
   - Skor submission wajib 0..max_score.
   - Tidak ada SELECT *.

5. Pest test:
   - AssignmentTest: dosen CRUD tugas sukses; dosen lintas kelas 403.
   - SubmissionGradeTest: dosen nilai submission sukses; skor melebihi max_score ditolak; dosen lintas kelas 403.
```