# 22 — Prompt: Dosen — Bimbingan PA (Approval KRS)

```text
Buatkan fitur Bimbingan PA (review & approval KRS mahasiswa bimbingan) untuk dosen.

1. Halaman pages/dosen/BimbinganPa.vue:
   - Daftar mahasiswa bimbingan (PA) dengan KRS berstatus submitted.
   - Kolom: NIM, Nama, Semester, Total SKS, Status.
   - Klik mahasiswa → detail KRS (daftar MK + total SKS) + aksi Setujui/Tolak.
   - Tolak wajib mengisi alasan (modal).

2. Backend:
   - app/DTO/KrsApprovalData.php (study_plan_id, decision, reason?).
   - app/Repositories/Contracts/StudyPlanRepository.php:
     - listForAdvisor(int $lecturerId): Collection — KRS submitted milik mahasiswa bimbingan; SELECT id, student_id, semester_id, status, total SKS (agregat), eager-load student.user (id, nim, name).
     - detail(int $planId): StudyPlan + details (eager-load offering.course id, code, name, sks).
   - app/Services/KrsApprovalService.php:
     - approve(lecturerId, planId): DB::transaction → cek dosen adalah PA mahasiswa tsb, set status approved + approved_by + approved_at, dan detail → approved.
     - reject(lecturerId, planId, reason): set status rejected + simpan alasan.
   - app/Http/Controllers/Dosen/KrsApprovalController.php (index, show, approve, reject) + KrsApprovalRequest.php.
   - app/Policies/StudyPlanPolicy.php (approve: dosen PA mahasiswa bimbingan).

3. Aturan:
   - PA hanya bisa menyetujui KRS mahasiswa di bawah bimbingannya (mapping advisor ↔ mahasiswa).
   - Penolakan wajib disertai alasan.
   - Tidak ada SELECT *.

4. Pest test:
   - KrsApprovalTest: dosen approve KRS bimbingan sukses (status & detail ter-update); dosen bukan PA → 403; reject tanpa alasan ditolak.
```
