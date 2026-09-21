# 13 — Senior SWE: Domain LMS, AI Advisor, Periode Akademik & Laporan

## 1. LMS (Materi + Tugas)

- `course_materials` — materi per kelas (upload file opsional).
- `assignments` + `assignment_submissions` — tugas & pengumpulan.
- Dosen kelola materi & tugas (kelas yang diampu); nilai submission (skor + feedback).
- Mahasiswa lihat materi & kumpul tugas (kelas yang diikuti).

## 2. AI Academic Advisor (Laravel AI SDK)

- Halaman chat mahasiswa; riwayat percakapan tersimpan di DB.
- Konteks AI: data mahasiswa (IPK, SKS, semester), riwayat nilai, kurikulum prodi, peraturan akademik.
- Kemampuan SDK: text generation (jawaban), embeddings (semantic search dokumen), tool calling (query IPK/SKS).
- Disclaimer: jawaban adalah saran, bukan keputusan resmi.
- Rate limiting pada endpoint chat.

## 3. Periode Akademik

- CRUD `academic_years` (kode, nama, rentang tanggal, is_active).
- CRUD `semesters` (academic_year_id, tipe ganjil/genap, rentang, is_active).
- Hanya satu semester aktif pada satu waktu (validasi saat set aktif).
- Seluruh fitur akademik (KRS, nilai, absen) mengacu semester aktif.

## 4. Laporan & Dashboard Pimpinan

- Dashboard eksekutif: total mahasiswa, IPK rata-rata, distribusi nilai, tren.
- Laporan akademik: KRS/KHS, transkrip & presensi, kinerja dosen (beban mengajar + kehadiran).
- Read-only; agregasi pakai query `->select([...])` + groupBy; cache ringkasan (Redis, opsional).

## 5. Konvensi

- Service/repository per domain; tanpa `SELECT *`; Pest test per domain.
- Laporan memakai query agregat spesifik (bukan load semua baris ke PHP).
