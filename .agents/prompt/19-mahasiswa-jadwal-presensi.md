# 19 — Prompt: Mahasiswa — Jadwal Kuliah & Presensi

```text
Buatkan fitur jadwal kuliah mingguan dan riwayat presensi untuk mahasiswa.

1. Halaman pages/mahasiswa/Jadwal.vue:
   - Grid/agenda jadwal mingguan (Senin–Jumat) dari kelas yang diambil pada semester aktif.
   - Setiap blok: kode MK, nama MK, dosen, ruangan, jam.
   - Kosong (EmptyState) bila belum ada jadwal.

2. Halaman pages/mahasiswa/Presensi.vue:
   - Pilih kelas → tabel riwayat pertemuan: Pertemuan, Tanggal, Status (badge hadir/izin/sakit/alpha).
   - Ringkasan persentase kehadiran per kelas.

3. Backend:
   - app/Repositories/Contracts/CourseOfferingRepository.php:
     - scheduleForStudent(int $studentId, int $semesterId): Collection — dari study_plan_details approved, eager-load course (id, code, name, sks), lecturer.user (id, name), classroom (id, name).
   - app/Repositories/Contracts/AttendanceRepository.php:
     - listForStudent(int $studentId, int $offeringId): Collection — SELECT meeting_number, date, status.
   - app/Services/PresenceService.php: attendancePercentage(int $studentId, int $offeringId): float.
   - app/Http/Controllers/Mahasiswa/ScheduleController.php (index) + PresenceController.php (index).
   - app/Policies/AttendancePolicy.php.

4. Aturan:
   - Hanya menampilkan kelas yang diambil (study_plan_details approved) pada semester aktif.
   - Tidak ada SELECT *.

5. Pest test:
   - ScheduleTest: hanya jadwal kelas yang diambil.
   - PresenceTest: persentase kehadiran benar; mahasiswa lain 403.
```
