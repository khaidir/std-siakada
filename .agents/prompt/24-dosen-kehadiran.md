# 24 — Prompt: Dosen — Kehadiran Mengajar (Check-in/Check-out)

```text
Buatkan fitur kehadiran mengajar dosen (check-in/check-out per pertemuan).

1. Halaman pages/dosen/Kehadiran.vue:
   - Dropdown pilih kelas yang diampu + tanggal.
   - Tombol Check-in (catat jam masuk) & Check-out (catat jam keluar).
   - Tabel riwayat kehadiran mengajar: Tanggal, Kelas, Check-in, Check-out, Status.

2. Backend:
   - app/Enums/LecturerAttendanceStatus.php (hadir, terlambat, izin, alpha).
   - app/DTO/LecturerAttendanceData.php.
   - app/Repositories/Contracts/LecturerAttendanceRepository.php:
     - listForLecturer(int $lecturerId): Collection — SELECT id, course_offering_id, date, check_in, check_out, status + eager-load offering.course (id, code, name).
     - checkIn/checkOut/upsert.
   - app/Services/LecturerAttendanceService.php:
     - checkIn(lecturerId, offeringId, date): buat/update record (status hadir/terlambat bila > jam mulai).
     - checkOut(recordId): set check_out.
   - app/Http/Controllers/Dosen/LecturerAttendanceController.php (index, checkIn, checkOut) + Request.
   - app/Policies/LecturerAttendancePolicy.php (isi: miliknya).

3. Aturan:
   - Dosen hanya mengisi kehadiran untuk kelas yang diampu.
   - Status otomatis (hadir/terlambat) berdasarkan jam check-in vs jadwal.
   - Tidak ada SELECT *.

4. Pest test:
   - LecturerAttendanceTest: check-in/check-out sukses; status terlambat benar; dosen lintas kelas 403.
```
