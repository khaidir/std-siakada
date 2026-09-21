# 05 — Prompt: Layout & Menu Sidebar per Role

```text
Buatkan layout utama + sidebar SIAKAD dengan Vue 3 + Inertia + Tailwind v4.

1. resources/js/Layouts/AuthenticatedLayout.vue:
   - Sidebar kiri: logo SIAKAD, menu per role (dari props auth.role + auth.permissions).
   - Collapsible (mode icon-only); indikator menu aktif (usePage().url); sub-menu untuk super-admin (Pengguna, Master Data).
   - Header: breadcrumbs (Ziggy route), dropdown notifikasi (opsional), dropdown avatar (profil, logout), toggle dark mode.
   - Main content <slot />.

2. Menu sidebar per role (lihat .agents/plan/04):
   - super-admin: Dashboard, Pengguna (sub: Daftar Pengguna, Roles & Permissions), Master Data (sub: Fakultas, Program Studi, Mata Kuliah, Ruangan, Kelas & Jadwal, Periode Akademik), KRS (Monitoring), Skripsi & KP, Kehadiran Dosen, Pengumuman.
   - kaprodi: Dashboard, Mata Kuliah, Kelas & Jadwal, Monitoring Nilai, KRS (Monitoring), Skripsi & KP, Kehadiran Dosen, Pengumuman.
   - dosen: Dashboard, Kelas Saya, Input Nilai, Presensi Kelas, Materi Kuliah & Tugas, Bimbingan PA (KRS), Bimbingan Skripsi, Kehadiran Dosen.
   - mahasiswa: Dashboard, KRS (Pilih Mata Kuliah), Jadwal Kuliah, Presensi, Materi Kuliah, Tugas, Nilai, KHS, Transkrip, AI Advisor, Skripsi, Kerja Praktek.
   - pimpinan: Dashboard, Laporan Akademik (KRS/KHS, Transkrip & Presensi, Kinerja Dosen).

3. Item menu tanpa permission TIDAK dirender; akses URL tetap dilindungi middleware + Policy.

4. Komponen shared (resources/js/components/shared/):
   - StatCard.vue (ikon, label, nilai, sublabel)
   - DataTable.vue (props: columns, rows; slot aksi; empty state; loading skeleton)
   - PageHeader.vue (judul + breadcrumbs + slot aksi)
   - ConfirmDialog.vue (konfirmasi hapus/simpan)

5. Dashboard per role (ringkas):
   - super-admin: 4 StatCard (total pengguna, dosen, mahasiswa, mata kuliah) + tabel aktivitas terbaru.
   - kaprodi: StatCard (jumlah MK, kelas, mahasiswa prodi) + daftar MK.
   - dosen: StatCard (kelas diampu, jumlah mahasiswa, tugas belum dinilai) + jadwal hari ini.
   - mahasiswa: StatCard (SKS diambil, jumlah kelas, rata-rata nilai) + jadwal hari ini.
   - pimpinan: StatCard (total mahasiswa, IPK rata-rata, total dosen, prodi) + grafik distribusi nilai + tren.

PASTIKAN: komponen Vue ringan tanpa TypeScript; Tailwind v4 CSS variables untuk tema terang/gelap; Bahasa Indonesia.
```
