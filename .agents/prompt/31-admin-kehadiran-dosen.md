# 31 — Prompt: Admin — Monitoring Kehadiran Dosen

```text
Buatkan fitur monitoring rekap kehadiran mengajar dosen untuk admin (dan kaprodi/pimpinan read-only).

1. Halaman pages/admin/KehadiranDosen.vue:
   - Filter: program studi + semester + (opsional) dosen.
   - Tabel rekap per dosen: NIDN, Nama, Prodi, jumlah pertemuan, hadir, terlambat, izin, alpha, persentase kehadiran.
   - Klik dosen → detail riwayat kehadiran (tanggal, kelas/MK, check-in, check-out, status).
   - Statistik ringkas: total kehadiran & persentase rata-rata.

2. Backend:
   - app/Enums/LecturerAttendanceStatus.php (hadir, terlambat, izin, alpha) — reuse dari prompt/24.
   - app/Repositories/Contracts/LecturerAttendanceRepository.php:
     - summary(array $filters): Collection — agregat per dosen (count total + per status) dengan SELECT + groupBy; eager-load lecturer.user (id, nidn, name), lecturer.studyProgram (id, name).
     - detail(int $lecturerId, int $semesterId): Collection — SELECT id, course_offering_id, date, check_in, check_out, status + eager-load offering.course (id, code, name).
   - app/Services/LecturerAttendanceService.php:
     - summary(array $filters): Collection — rekap + hitung persentase.
   - app/Http/Controllers/Admin/LecturerAttendanceController.php (index, show) + Request filter.
   - app/Http/Controllers/Kaprodi/LecturerAttendanceController.php (index — scoped prodi).
   - app/Policies/LecturerAttendancePolicy.php (view: admin semua; kaprodi prodi-nya; pimpinan read-only).

3. Aturan:
   - Admin melihat semua fakultas/prodi; kaprodi hanya prodi-nya; pimpinan read-only (reuse route pimpinan bila perlu).
   - Semua query agregat memakai ->select([...]) + groupBy, tanpa SELECT *.
   - Pagination pada detail.

4. Pest test:
   - LecturerAttendanceReportTest: admin lihat rekap semua dosen; kaprodi hanya prodi-nya; role lain 403; persentase kehadiran benar.
```
