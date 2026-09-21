# 25 — Prompt: Admin — Monitoring KRS

```text
Buatkan fitur monitoring KRS seluruh mahasiswa untuk admin.

1. Halaman pages/admin/KrsMonitoring.vue:
   - Filter: program studi + semester + status KRS.
   - Tabel: NIM, Nama, Prodi, Semester, Total SKS, Status (badge), aksi lihat detail.
   - Detail KRS: daftar MK + total SKS + riwayat approval (approved_by, approved_at).
   - Statistik ringkas: jumlah draft/submitted/approved/rejected.

2. Backend:
   - app/Repositories/Contracts/StudyPlanRepository.php:
     - listAll(array $filters): LengthAwarePaginator — SELECT id, student_id, semester_id, status, approved_by, approved_at + eager-load student.user (id, nim, name), semester (id, type), filterable.
     - detail(int $planId): StudyPlan + details (eager-load offering.course).
   - app/Services/KrsMonitoringService.php: summary() → hitungan per status.
   - app/Http/Controllers/Admin/KrsMonitoringController.php (index, show) + Request filter.
   - app/Policies/StudyPlanPolicy.php (view all: admin).

3. Aturan:
   - Admin read/monitor (bisa melihat semua prodi); aksi approval ada di dosen PA.
   - Tidak ada SELECT *; pagination.

4. Pest test:
   - KrsMonitoringTest: admin lihat semua KRS + filter; role lain 403.
```
