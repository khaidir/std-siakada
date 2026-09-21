# 09 — Senior SWE: Breakdown Fitur per Role (Lengkap per PRD)

## 1. Auth & Login (semua role)

- Halaman login (email + password), logout.
- Setelah login, redirect berdasarkan role ke dashboard masing-masing.
- Menu sidebar & akses ditentukan role + permission.

## 2. Dosen

### 2.1 Input Nilai
- Pilih kelas (`course_offering`) yang diampu.
- Tabel mahasiswa (dari `study_plan_details` berstatus approved) + input nilai (tugas/UTS/UAS opsional, atau skor langsung).
- Sistem menghitung `score`, `letter_grade`, `grade_point` otomatis.
- Simpan batch dalam transaksi; setelah simpan hitung ulang `gpa` & `total_sks` mahasiswa.

**Konversi:** A (85–100, 4.0) · A- (80–84, 3.75) · B+ (75–79, 3.25) · B (70–74, 3.0) · B- (65–69, 2.75) · C+ (60–64, 2.25) · C (55–59, 2.0) · D (40–54, 1.0) · E (0–39, 0).

### 2.2 Absen (Presensi)
- Pilih kelas + nomor pertemuan + tanggal.
- Tandai status per mahasiswa: hadir/izin/sakit/alpha.
- Simpan batch; tampilkan rekap.

### 2.3 Kelola Materi
- CRUD materi per kelas (judul, deskripsi, file opsional).

### 2.4 Kelola Tugas (LMS Management)
- CRUD `assignments` per kelas (judul, deskripsi, tenggat, skor maks).
- Lihat & nilai `assignment_submissions` (skor + feedback).

### 2.5 Bimbingan PA (Persetujuan KRS)
- Lihat daftar `study_plans` mahasiswa bimbingannya berstatus `submitted`.
- Approve / reject (wajib alasan); set `approved_by` & `approved_at`.

### 2.6 Bimbingan Skripsi
- Lihat mahasiswa bimbingannya (sebagai pembimbing 1/2).
- Update status skripsi; approve log bimbingan (`thesis_logs`).

### 2.7 Kehadiran Dosen
- Check-in/check-out mengajar per kelas (`lecturer_attendances`).
- Rekap kehadiran pribadi.

## 3. Mahasiswa

### 3.1 KRS (Pilih Mata Kuliah)
- Lihat `course_offerings` prodi-nya pada semester aktif.
- Buat `study_plan` (draft) + pilih/batal `study_plan_details`.
- Submit KRS (status `submitted`); tidak bisa edit setelah submitted.
- Validasi: tidak bentrok jadwal; batas SKS berdasar IPK.

### 3.2 Lihat Materi
- Daftar materi dari kelas yang disetujui (study_plan_detail approved).

### 3.3 Kumpul Tugas
- Daftar tugas dari kelas yang diikuti; upload file (sebelum `due_date`).
- Lihat nilai & feedback.

### 3.4 Lihat Nilai
- Nilai per kelas: skor, huruf, grade point; ringkasan IPK & total SKS.

### 3.5 KHS & Transkrip
- KHS per semester (IPS) + IPK kumulatif.
- Transkrip lengkap + export PDF.

### 3.6 Presensi & Jadwal
- Riwayat kehadiran per MK + persentase.
- Jadwal mingguan.

### 3.7 AI Academic Advisor
- Chat konsultasi akademik (memakai Laravel AI SDK) dengan konteks IPK/SKS/kurikulum.

### 3.8 Skripsi Tracking
- Lihat judul, status, pembimbing, log bimbingan.

### 3.9 Kerja Praktek (KP)
- Data perusahaan, pembimbing, periode, status; logbook harian.

## 4. Ka Prodi

### 4.1 Kelola Mata Kuliah
- CRUD `courses` milik prodi-nya (kode, nama, SKS, semester, tipe wajib/pilihan).

### 4.2 Kelas & Jadwal (view) / Monitoring Nilai (view) / Skripsi-KP (view) / Kehadiran Dosen (view)
- Read-only pada data prodi-nya.

## 5. Super Admin

### 5.1 Pengguna
- CRUD user; assign role. Buat profil `students`/`lecturers` sesuai role.

### 5.2 Roles & Permissions
- Kelola roles & permissions (Spatie UI); assign/revoke permission.

### 5.3 Master Data
- CRUD `faculties`, `study_programs`, `classrooms`, `courses`, `course_offerings` (kelas/jadwal).

### 5.4 Periode Akademik
- CRUD `academic_years` & `semesters`; set semester aktif.

### 5.5 KRS Approval (monitoring)
- Lihat seluruh KRS; status per mahasiswa.

### 5.6 Skripsi & KP Management
- Assign pembimbing skripsi/KP; update status.

### 5.7 Kehadiran Dosen (monitoring)
- Rekap `lecturer_attendances`.

### 5.8 Announcements
- CRUD pengumuman (target_role opsional).

## 6. Pimpinan (read-only)

- Dashboard eksekutif: ringkasan statistik akademik (mahasiswa, IPK rata-rata, distribusi nilai).
- Laporan akademik: KRS/KHS, transkrip & presensi, kinerja dosen.
