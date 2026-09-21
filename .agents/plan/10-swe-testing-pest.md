# 10 — Senior SWE: Strategi Testing (Pest)

## 1. Unit Test

| Test | Target |
|---|---|
| `GradeCalculatorTest` | Semua batas konversi letter & grade point (0, 39, 40, 54, 55, 59, 60, 64, 65, 69, 70, 74, 75, 79, 80, 84, 85, 100) + formula komponen 20/30/50 |
| `KrsServiceTest` | Validasi bentrok jadwal, batas SKS per IPK, duplikat |
| `KhsServiceTest` | Perhitungan IPS & IPK kumulatif |
| `AttendanceServiceTest` | Validasi status & penyimpanan batch |
| `LecturerAttendanceServiceTest` | Status hadir/terlambat & check-in/out |
| `SubmissionServiceTest` | Validasi tenggat waktu submission |

## 2. Feature Test

| Test | Skenario |
|---|---|
| `AuthTest` | Login sukses, login gagal, logout, redirect per role |
| `GradeTest` | Dosen simpan nilai; dosen lintas kelas ditolak (403); IPK ter-recalculate |
| `AttendanceTest` | Dosen tandai absen; rekap benar |
| `LecturerAttendanceTest` | Dosen check-in/out; rekap kehadiran dosen |
| `MaterialTest` | Dosen CRUD materi; mahasiswa lihat hanya materi kelasnya |
| `KrsTest` | Mahasiswa pilih MK, submit; bentrok jadwal & batas SKS ditolak |
| `KrsApprovalTest` | Dosen PA approve/reject KRS bimbingan; bukan PA 403 |
| `SubmissionTest` | Mahasiswa submit sebelum & sesudah due date |
| `KhsTest` | IPS/IPK benar; mahasiswa lain 403 |
| `TranskripTest` | Export PDF sukses |
| `ThesisTest` | Mahasiswa lihat & log bimbingan; dosen approve; admin assign pembimbing |
| `InternshipTest` | Logbook KP; dosen approve; admin assign pembimbing |
| `AiAdvisorTest` | Chat menyimpan pesan; riwayat milik mahasiswa; mock SDK |
| `AcademicPeriodTest` | Set aktif menonaktifkan yang lain; CRUD |
| `CourseTest` | Kaprodi CRUD MK prodi-nya; prodi lain ditolak |
| `UserRoleTest` | Super admin buat user + assign role; permission berlaku |
| `PimpinanReportTest` | Dashboard & laporan read-only; role lain 403 |
| `PermissionTest` | Setiap role hanya akses route miliknya |

## 3. Aturan

- `RefreshDatabase` + factories (lihat .agents/prompt/03 daftar factory).
- Seeder roles/permissions dipanggil di `setUp()`.
- Tidak menyentuh jaringan eksternal (mock SDK AI).
