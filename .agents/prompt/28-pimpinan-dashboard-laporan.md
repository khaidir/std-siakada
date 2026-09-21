# 28 — Prompt: Pimpinan — Dashboard Eksekutif & Laporan

```text
Buatkan fitur dashboard eksekutif & laporan akademik untuk pimpinan (read-only).

1. Halaman pages/pimpinan/Dashboard.vue:
   - StatCard: total mahasiswa, IPK rata-rata, total dosen, total prodi.
   - Grafik distribusi nilai (bar) + tren jumlah mahasiswa (line).
   - Tabel aktivitas akademik terbaru.

2. Halaman pages/pimpinan/Laporan.vue:
   - Filter: prodi + semester.
   - Tabs laporan: KRS/KHS, Transkrip & Presensi, Kinerja Dosen.
   - Kinerja dosen: beban mengajar (jumlah SKS/kelas) + persentase kehadiran mengajar.
   - Tombol export (PDF/Excel) memakai barryvdh/laravel-dompdf / maatwebsite/excel (opsional).

3. Backend:
   - app/Repositories/Contracts/ReportRepository.php:
     - studentStats(): array (total, avg_gpa) — query agregat SELECT + groupBy.
     - gradeDistribution(): Collection — groupBy letter_grade, count.
     - lecturerWorkload(int $semesterId): Collection — jumlah kelas & SKS per dosen.
     - lecturerAttendance(int $semesterId): Collection — persentase kehadiran per dosen.
   - app/Services/ReportService.php (agregasi + cache ringkas, opsional Redis).
   - app/Http/Controllers/Pimpinan/DashboardController.php (index) + ReportController.php (index, export).
   - app/Policies/ReportPolicy.php (view: pimpinan read-only).

4. Aturan:
   - Pimpinan read-only: tidak ada endpoint mutasi data.
   - Semua query agregat memakai ->select([...]) + groupBy, tanpa SELECT *.
   - Cache hasil dashboard (invalidasi saat data berubah).

5. Pest test:
   - PimpinanReportTest: dashboard menampilkan statistik; laporan kinerja dosen benar; role lain 403; endpoint mutasi tidak ada/ditolak.
```
