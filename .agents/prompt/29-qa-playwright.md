# 17 — Prompt: QA — Playwright E2E

```text
Buatkan suite E2E Playwright untuk SIAKAD.

1. Setup:
   - npm install -D @playwright/test ; npx playwright install chromium
   - playwright.config.ts: baseURL http://localhost:8000, testDir tests/e2e.
   - Helper tests/e2e/helpers.ts: loginAs(page, email, password) → mengisi form login & menunggu navigasi dashboard.

2. Akun seed (dari seeder): admin@siakad.test, kaprodi@siakad.test, dosen@siakad.test, mahasiswa@siakad.test, pimpinan@siakad.test (password sama).

3. Test file:
   - auth.spec.ts: login sukses redirect dashboard role; login gagal menampilkan error.
   - superadmin.spec.ts: buat user dosen baru → muncul di tabel; buat fakultas/prodi/mata kuliah/ruangan → muncul; buat periode akademik → muncul; kelola role permission tersimpan.
   - kaprodi.spec.ts: sidebar tidak menampilkan menu super-admin; buat mata kuliah tampil; monitoring nilai & KRS tampil; akses /admin/users → 403/ditangguhkan.
   - dosen.spec.ts: pilih kelas → input nilai → simpan → skor & grade muncul; tandai absen tersimpan; upload materi tampil; approve KRS mahasiswa bimbingan; approve logbook KP; isi kehadiran dosen; akses /mahasiswa/krs ditolak.
   - mahasiswa.spec.ts: pilih mata kuliah (KRS) & submit tersimpan; lihat materi tampil; kumpul tugas (upload) berstatus ter-submit; lihat nilai & KHS tampil; unduh transkrip PDF; lihat presensi; kirim chat AI advisor mendapat jawaban + disclaimer; lihat & isi logbook skripsi/KP; akses /dosen/nilai ditolak.
   - pimpinan.spec.ts: login redirect dashboard; dashboard menampilkan StatCard & grafik; laporan akademik tampil; semua menu read-only (tidak ada tombol mutasi); akses /admin/users ditolak.

4. Aturan:
   - Gunakan selector stabil (data-testid bila perlu).
   - Setiap test independen (seed state via beforeEach login ulang atau DB reset).
   - Assert error konsol tidak ada 5xx pada alur yang diuji.

5. Jalankan: npx playwright test — lampirkan laporan hasil.
```
