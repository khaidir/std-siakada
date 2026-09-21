# 08 — Prompt: Dosen — Absen (Presensi)

```text
Buatkan fitur presensi/absen untuk dosen.

1. Halaman pages/dosen/Presensi.vue:
   - Dropdown pilih kelas yang diampu + input nomor pertemuan + tanggal.
   - Tabel mahasiswa terdaftar (dari study_plan_details approved) kolom: NIM, Nama, Status (radio: hadir/izin/sakit/alpha).
   - Tombol Simpan (batch).
   - Tampilkan rekap pertemuan sebelumnya (jika ada).

2. Backend:
   - app/Enums/AttendanceStatus.php (hadir, izin, sakit, alpha).
   - app/DTO/AttendanceData.php + BatchAttendanceData.
   - app/Repositories/Contracts/AttendanceRepository.php:
     - listByOffering(int $offeringId): Collection (SELECT kolom spesifik + eager-load student.user id,nim,name).
     - upsertBatch(array $rows): void.
   - app/Services/AttendanceService.php (store batch dalam transaksi).
   - app/Http/Controllers/Dosen/AttendanceController.php (index, store) + StoreAttendanceRequest.php.
   - app/Policies/AttendancePolicy.php.

3. Aturan:
   - Dosen hanya akses kelas yang diampu.
   - Satu mahasiswa hanya satu record per (offering, meeting_number, date) — upsert.
   - Status divalidasi via enum; tidak ada SELECT *.

4. Pest test:
   - AttendanceTest: simpan batch sukses; dosen lintas kelas 403; rekap benar.
```
