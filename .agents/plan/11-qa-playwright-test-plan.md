# 11 — QA: Test Plan (Playwright E2E)

## 1. Tujuan

Verifikasi alur kritis end-to-end per role di browser nyata (Chromium), termasuk penolakan akses lintas role.

## 2. Setup

- `@playwright/test`; base URL `http://localhost:8000` (atau `php artisan serve`).
- Helper `loginAs(page, email, password)` untuk login cepat tiap role.
- Seed data deterministik (users & role) sebelum suite berjalan.

## 3. Skenario per Role

### super-admin
1. Login → dashboard tampil.
2. Buat pengguna dosen baru → muncul di daftar.
3. Buat pengguna mahasiswa baru → muncul di daftar.
4. Buat role baru + assign permission → tersimpan.
5. Buat fakultas, prodi, mata kuliah, ruangan → muncul di master data.
6. Buat periode akademik (tahun ajaran & semester) → muncul & bisa diaktifkan.
7. Assign pembimbing skripsi/KP → tersimpan.

### kaprodi
1. Login → sidebar hanya berisi menu kaprodi (tidak ada menu super-admin).
2. Buat mata kuliah → tampil.
3. Monitoring nilai & KRS prodi-nya tampil.
4. Akses `/admin/users` → ditolak (403/redirect).

### dosen
1. Login → sidebar dosen.
2. Pilih kelas → input nilai → skor tersimpan, grade terhitung.
3. Tandai absen → status tersimpan.
4. Upload materi → tampil.
5. Approve KRS mahasiswa bimbingan → status berubah approved.
6. Approve logbook KP mahasiswa → status berubah approved.
7. Check-in/check-out kehadiran mengajar → riwayat tersimpan.
8. Akses `/mahasiswa/krs` → ditolak.

### mahasiswa
1. Login → sidebar mahasiswa.
2. Pilih mata kuliah (KRS) + submit → tersimpan & status submitted.
3. Lihat materi kelas yang diikuti → tampil.
4. Kumpul tugas (upload file) → status ter-submit.
5. Lihat nilai & KHS → skor, IPS/IPK tampil.
6. Unduh transkrip PDF → file terunduh.
7. Lihat presensi → persentase kehadiran tampil.
8. Kirim chat AI advisor → jawaban + disclaimer tampil.
9. Lihat & isi logbook skripsi/KP → tersimpan.
10. Akses `/dosen/nilai` → ditolak.

### pimpinan
1. Login → sidebar hanya menu read-only (Dashboard, Laporan Akademik).
2. Dashboard menampilkan StatCard & grafik.
3. Laporan akademik (kinerja dosen) tampil.
4. Tidak ada tombol mutasi data (read-only).
5. Akses `/admin/users` → ditolak.

## 4. Kriteria Lolos

- Semua aksi di atas menghasilkan state benar & UI sesuai.
- Semua akses lintas role menghasilkan 403 atau redirect yang benar.
- Tidak ada error konsol/network (5xx) pada alur yang diuji.
