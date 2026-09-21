# 23 — Prompt: Dosen — Bimbingan Skripsi

```text
Buatkan fitur bimbingan skripsi untuk dosen (sebagai pembimbing 1/2).

1. Halaman pages/dosen/BimbinganSkripsi.vue:
   - Daftar mahasiswa bimbingan skripsi (pembimbing 1 atau 2): NIM, Nama, Judul, Status.
   - Detail: abstrak, status, log bimbingan (dengan approval belum/disetujui).
   - Tombol setujui log bimbingan (supervisor_approval = true).
   - Update status skripsi (dropdown proposal → seminar_proposal → sidang → lulus/revisi).

2. Backend:
   - app/DTO/ThesisStatusData.php + ThesisLogApprovalData.php.
   - app/Repositories/Contracts/ThesisRepository.php:
     - listForSupervisor(int $lecturerId): Collection — SELECT id, student_id, title, status + eager-load student.user (id, nim, name).
     - detail(int $thesisId): Thesis + logs (eager-load).
     - updateStatus(int $thesisId, ThesisStatus $status): void.
     - approveLog(int $logId): void.
   - app/Services/ThesisService.php:
     - updateStatus(lecturerId, thesisId, status): cek dosen pembimbing (1/2).
     - approveLog(lecturerId, logId): cek dosen pembimbing.
   - app/Http/Controllers/Dosen/ThesisController.php (index, show, updateStatus, approveLog) + Request.
   - app/Policies/ThesisPolicy.php + ThesisLogPolicy.php.

3. Aturan:
   - Dosen hanya melihat mahasiswa bimbingannya (supervisor_1_id / supervisor_2_id = lecturer_id).
   - Hanya pembimbing yang bisa update status & approve log.
   - Tidak ada SELECT *.

4. Pest test:
   - ThesisSupervisorTest: dosen approve log & update status sukses; dosen bukan pembimbing 403.
```
