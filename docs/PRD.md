# PRD — SIAKAD (Sistem Informasi Akademik)

> **Versi:** 1.0 · **Tanggal:** 21 September 2026 · **Status:** Draft untuk review
> **Stack target:** Laravel 13 (PHP 8.3+) · Inertia.js v3 · Vue 3 · Tailwind CSS v4 · MySQL 8.0+ · Fortify · Spatie Permission · Pest

---

## 1. Ringkasan Produk

### 1.1 Identitas Produk

| Atribut | Nilai |
|---|---|
| Nama Produk | **SIAKAD** (Sistem Informasi Akademik) |
| Kategori | Aplikasi web enterprise (Academic Information System / ERP akademik) |
| Skala | Multi-fakultas, multi-program-studi, multi-periode-akademik |
| Bahasa UI | Bahasa Indonesia |
| Model Deployment | Self-hosted (VPS / on-premise universitas) |

### 1.2 Deskripsi Produk

SIAKAD adalah aplikasi manajemen akademik modern untuk universitas dan perguruan tinggi yang mendigitalisasi seluruh proses administrasi kampus dalam satu platform terpadu. Aplikasi ini menggantikan proses manual berbasis kertas dan spreadsheet dengan alur kerja digital yang terintegrasi, mencakup perencanaan studi (KRS), pencatatan hasil studi (KHS & transkrip), presensi, e-learning (LMS), pembimbingan skripsi dan kerja praktek, hingga pelaporan eksekutif bagi pimpinan.

Nilai utama yang ditawarkan SIAKAD:

1. **Satu sumber kebenaran (single source of truth)** — data mahasiswa, dosen, kurikulum, nilai, dan kehadiran tersimpan terpusat dan saling terkait, menghilangkan duplikasi dan inkonsistensi data antar unit.
2. **Otomasi perhitungan akademik** — IPK, IPS, batas SKS, grade, dan status kelulusan dihitung otomatis oleh sistem sehingga meminimalkan kesalahan manusia.
3. **Akses per peran yang terkontrol** — setiap aktor (mahasiswa, dosen, admin, pimpinan) melihat dan mengelola data sesuai wewenangnya melalui RBAC yang ketat.
4. **Pengambilan keputusan berbasis data** — dashboard statistik dan laporan memberikan visibilitas real-time kepada pimpinan tanpa harus meminta laporan manual ke biro administrasi.
5. **Asisten akademik berbasis AI** — mahasiswa dapat berkonsultasi tentang rencana studi, kurikulum, dan performa akademiknya secara mandiri 24/7.

### 1.3 Tujuan & Sasaran (Goals)

**Tujuan bisnis:**
- Memangkas waktu proses administrasi akademik (pengisian KRS, input nilai, approval) hingga minimal 60%.
- Mengurangi kesalahan data akademik (nilai, SKS, kehadiran) mendekati nol melalui validasi otomatis.
- Meningkatkan kepuasan mahasiswa dan dosen melalui layanan mandiri (self-service) yang responsif di desktop maupun mobile.

**Sasaran terukur (success metrics):**
- 100% mahasiswa aktif melakukan KRS secara online dalam satu semester berjalan.
- Waktu input & publikasi nilai satu kelas selesai dalam < 15 menit per dosen.
- Laporan eksekutif dapat diakses pimpinan kapan pun tanpa intervensi staf (real-time).
- Waktu penyelesaian proses approval KRS < 2 hari kerja.

### 1.4 Target Pengguna (Persona)

| Persona | Deskripsi | Kebutuhan Utama |
|---|---|---|
| **Mahasiswa** | Mahasiswa aktif program studi, pengguna terbanyak dan paling sering | Isi KRS, lihat nilai/KHS/transkrip, akses materi & tugas, konsultasi AI, tracking skripsi/KP |
| **Dosen** | Dosen pengampu & pembimbing akademik (PA), pengguna aktif per semester | Input nilai, kelola presensi kelas, setujui KRS mahasiswa bimbingan, upload materi, bimbing skripsi |
| **Admin Akademik** | Staf biro/baak yang mengelola master data & operasional | Kelola data master, kelola akun, monitoring KRS, atur periode akademik, assign pembimbing |
| **Pimpinan** | Dekan, wakil dekan, ketua prodi, rektorat (read-only) | Melihat dashboard eksekutif dan laporan akademik untuk pengambilan keputusan |

### 1.5 Ruang Lingkup

**Termasuk dalam scope (In-Scope):**
- Seluruh fitur akademik inti: KRS, KHS, transkrip, presensi, jadwal, LMS, skripsi, KP, AI advisor.
- Manajemen master data akademik dan periode akademik.
- RBAC dengan dukungan *faculty-scoped admin access* (admin hanya mengelola fakultasnya).
- Ekspor dokumen (PDF transkrip, PDF/Excel nilai, laporan).

**Di luar scope (Out-of-Scope) versi 1.0:**
- Pembayaran / keuangan mahasiswa (SPP/UKT) — ditangani sistem terpisah.
- Perpustakaan digital.
- Aplikasi mobile native (versi 1.0 mengandalkan responsive web; PWA dapat dievaluasi kemudian).
- Integrasi dengan sistem pemerintah (PDDikti) — hanya disiapkan skema ekspor data.

---

## 2. Fitur per Role

### 2.1 Mahasiswa

| No | Fitur | Deskripsi | Aturan Bisnis Kunci |
|---|---|---|---|
| M1 | **KRS Online** | Pengisian Kartu Rencana Studi dengan validasi SKS otomatis. Mahasiswa memilih mata kuliah yang ditawarkan pada semester aktif, lalu mengirim untuk persetujuan PA. | Batas maksimum SKS ditentukan oleh IPK semester sebelumnya; tidak boleh bentrok jadwal; kuota kelas dihormati; status KRS: draft → submitted → approved/rejected. |
| M2 | **KHS** | Kartu Hasil Studi per semester menampilkan nilai semua MK, SKS, bobot nilai, IPS, dan IPK kumulatif. | IPS dihitung = Σ(bobot nilai × SKS) / Σ SKS semester tersebut; IPK kumulatif = total seluruh semester. |
| M3 | **Transkrip Nilai** | Transkrip lengkap seluruh nilai per semester dengan IPK final, plus tombol export PDF. | Menampilkan MK yang lulus & tidak lulus; ekspor PDF berformat resmi. |
| M4 | **Presensi** | Riwayat kehadiran per mata kuliah (hadir/izin/sakit/alpha) beserta persentase kehadiran. | Persentase kehadiran menentukan kelayakan ikut UAS (threshold konfigurable). |
| M5 | **Jadwal Kuliah** | Jadwal kuliah mingguan dalam tampilan tabel/agenda. | Hanya menampilkan kelas yang sedang diambil pada semester aktif. |
| M6 | **E-Learning (LMS)** | Akses materi kuliah, mengerjakan dan mengumpulkan tugas, melihat nilai tugas & feedback. | Tugas yang lewat tenggat tidak dapat disubmit (kecuali ada pengecualian). |
| M7 | **AI Academic Advisor** | Chat konsultasi akademik berbasis AI yang memahami konteks mahasiswa (IPK, SKS, kurikulum) dan memberi rekomendasi. | AI bersifat *advisory* (saran), bukan keputusan resmi; jawaban disertai disclaimer. |
| M8 | **Skripsi Tracking** | Progress skripsi: judul, abstrak, pembimbing, status, dan log bimbingan. | Mahasiswa melihat status & riwayat bimbingan; pembimbing menyetujui tiap aktivitas bimbingan. |
| M9 | **Kerja Praktek (KP)** | Manajemen KP: data perusahaan, pembimbing, periode, status, dan logbook harian. | Logbook harian divalidasi/di-approve oleh pembimbing lapangan atau dosen pembimbing. |

### 2.2 Dosen

| No | Fitur | Deskripsi | Aturan Bisnis Kunci |
|---|---|---|---|
| D1 | **Input Nilai** | Dosen menginput nilai mahasiswa per kelas (tugas, UTS, UAS), sistem menghitung nilai akhir & grade otomatis. | Nilai akhir = Tugas 20% + UTS 30% + UAS 50%; grade mengikuti tabel konversi; nilai final tidak bisa diedit setelah batas waktu periode nilai. |
| D2 | **Presensi Kelas** | Kelola pertemuan per kelas dan presensi mahasiswa per pertemuan. | Dosen menandai hadir/izin/sakit/alpha; rekap kehadiran otomatis. |
| D3 | **Bimbingan PA** | Dosen PA melihat daftar mahasiswa bimbingannya, mereview dan menyetujui/menolak KRS. | PA hanya dapat menyetujui mahasiswa di bawah bimbingannya; alasan penolakan wajib diisi. |
| D4 | **Bimbingan Skripsi** | Review progres skripsi, update status, approve log bimbingan. | Dosen hanya melihat mahasiswa bimbingannya (sebagai pembimbing 1 atau 2). |
| D5 | **LMS Management** | Upload materi, buat & kelola tugas, nilai submission. | Dosen hanya mengelola LMS pada kelas yang diampu. |
| D6 | **Kehadiran Dosen** | Absensi kehadiran mengajar dosen (check-in/check-out per pertemuan). | Rekap kehadiran dosen tersedia bagi admin & pimpinan. |

### 2.3 Admin Akademik

| No | Fitur | Deskripsi |
|---|---|---|
| A1 | **Dashboard Statistik** | Statistik akademik: total mahasiswa, dosen, prodi, tren pendaftaran, aktivitas terbaru. |
| A2 | **Master Data** | CRUD Fakultas, Program Studi, Mata Kuliah, Kelas, Ruangan, Jadwal. |
| A3 | **User Management** | Kelola akun Dosen dan Mahasiswa (buat, edit, nonaktifkan, reset password, assign role). |
| A4 | **KRS Approval** | Monitoring seluruh KRS mahasiswa; melihat status dan detail per mahasiswa. |
| A5 | **Skripsi & KP** | Assign dosen pembimbing skripsi/KP, update status, monitoring. |
| A6 | **Kehadiran Dosen** | Monitoring rekap kehadiran mengajar dosen. |
| A7 | **Periode Akademik** | Kelola tahun ajaran & semester (rentang tanggal perkuliahan, semester aktif). |
| A8 | **Announcements** | Kelola pengumuman yang tampil di dashboard per target role. |

### 2.4 Pimpinan (Read-Only)

| No | Fitur | Deskripsi |
|---|---|---|
| P1 | **Dashboard Eksekutif** | Ringkasan statistik akademik tingkat fakultas/universitas: jumlah mahasiswa, IPK rata-rata, distribusi nilai, tren. |
| P2 | **Laporan Akademik** | Laporan KRS, KHS, transkrip, presensi, dan kinerja dosen (beban mengajar, kehadiran). |

> **Catatan RBAC:** Pimpinan hanya memiliki akses baca (read-only). Tidak ada aksi mutasi data pada seluruh menu pimpinan.

---

## 3. Persyaratan Non-Fungsional (NFR)

### 3.1 Keamanan (Security)

- **RBAC** menggunakan Spatie Laravel Permission dengan permission granular (`krs.create`, `grade.update`, dst.).
- **Faculty-scoped admin access** — admin fakultas hanya dapat mengelola data di dalam fakultasnya (diterapkan di repository layer sebagai filter wajib).
- **CSRF protection** menyala untuk semua request state-changing; form Inertia memakai token CSRF bawaan.
- **Security headers middleware** — `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Strict-Transport-Security` (production), `Content-Security-Policy` (baseline).
- **Rate limiting** pada endpoint sensitif: login, reset password, submit KRS, input nilai, endpoint AI advisor.
- **Otorisasi per-data (row-level)** — dosen hanya bisa akses kelas/nilai/mahasiswa miliknya; mahasiswa hanya data dirinya. Diterapkan lewat *policies* + filter repository.
- **Audit trail** — seluruh aksi penting (approval KRS, perubahan nilai, perubahan master data) dicatat di `activity_logs` (actor, action, model, before/after).

### 3.2 Performa & Skalabilitas

- **Tidak ada `SELECT *`** — setiap query hanya mengambil kolom yang dibutuhkan (`->select([...])`) dan eager-load relasi dengan kolom spesifik (`with('relation:col1,col2')`).
- **Database indexes** pada kolom yang sering di-filter/join: NIM, NIDN, kode mata kuliah, `semester_id`, `student_id`, `course_offering_id`, foreign key.
- **Caching strategy** — cache hasil dashboard & master data yang jarang berubah (Redis); invalidasi otomatis saat data berubah.
- **Pagination** pada semua list (KRS, nilai, presensi, log).
- **Queue** untuk job berat/asinkron: export PDF/Excel, embedding dokumen akademik untuk semantic search, notifikasi.

### 3.3 Usability & Responsiveness

- **Responsive design** — seluruh halaman dapat digunakan nyaman di mobile (menu sidebar collapse, tabel responsif, form satu kolom).
- **Bahasa Indonesia** untuk seluruh label UI, pesan validasi, dan dokumen cetak.

### 3.4 Maintainability & Kode

- **Arsitektur berlapis**: Controller → Service → Repository → Model, dengan **DTO** sebagai kontrak data antar layer (menggunakan `spatie/laravel-data`).
- **PHP enums** untuk seluruh kolom status (bukan magic string / enum DB), agar type-safe dan mudah diubah.
- **Testing** dengan Pest PHP: feature test untuk alur bisnis, unit test untuk service/domain logic.
- **Laravel Pint** untuk standar kode, **Larastan** (opsional) untuk static analysis.

### 3.5 Kompatibilitas

- PHP 8.3+ (8.3–8.5), Laravel 13, MySQL 8.0+ atau PostgreSQL 16+.
- Browser modern (Chrome/Edge/Firefox/Safari versi 2 tahun terakhir).

---

## 4. User Stories

### 4.1 Mahasiswa

1. Sebagai **mahasiswa**, saya ingin mengisi KRS secara online agar dapat mengambil mata kuliah semester depan tanpa harus datang ke kampus.
2. Sebagai **mahasiswa**, saya ingin melihat KHS dan IPK/IPS saya setiap semester agar saya tahu performa akademik saya.
3. Sebagai **mahasiswa**, saya ingin mengunduh transkrip nilai dalam PDF agar bisa saya lampirkan untuk keperluan beasiswa/kerja.
4. Sebagai **mahasiswa**, saya ingin melihat riwayat kehadiran per mata kuliah agar saya tahu apakah saya masih memenuhi syarat ikut UAS.
5. Sebagai **mahasiswa**, saya ingin melihat jadwal kuliah mingguan agar saya tidak melewatkan kelas.
6. Sebagai **mahasiswa**, saya ingin mengakses materi dan mengumpulkan tugas secara online agar saya bisa belajar dari mana saja.
7. Sebagai **mahasiswa**, saya ingin berkonsultasi dengan AI Academic Advisor agar saya mendapat rekomendasi rencana studi yang tepat.
8. Sebagai **mahasiswa**, saya ingin melihat progress skripsi dan log bimbingan saya agar saya tahu sudah sampai mana.
9. Sebagai **mahasiswa**, saya ingin mengisi logbook kerja praktek harian agar progress KP saya tercatat rapi.

### 4.2 Dosen

10. Sebagai **dosen**, saya ingin menginput nilai mahasiswa agar KHS dapat digenerate otomatis tanpa perhitungan manual.
11. Sebagai **dosen**, saya ingin mengelola presensi kelas per pertemuan agar rekap kehadiran akurat.
12. Sebagai **dosen PA**, saya ingin menyetujui/menolak KRS mahasiswa bimbingan saya agar mereka mengambil mata kuliah yang sesuai.
13. Sebagai **dosen pembimbing**, saya ingin mereview dan meng-update status bimbingan skripsi agar progres mahasiswa terpantau.
14. Sebagai **dosen**, saya ingin mengupload materi dan membuat tugas di LMS agar mahasiswa mendapat bahan ajar.

### 4.3 Admin

15. Sebagai **admin**, saya ingin mengelola data master (fakultas, prodi, mata kuliah) agar kurikulum dapat diperbarui.
16. Sebagai **admin**, saya ingin mengelola akun dosen dan mahasiswa agar akses setiap pengguna terkendali.
17. Sebagai **admin**, saya ingin memonitor status KRS seluruh mahasiswa agar proses akademik berjalan tepat waktu.
18. Sebagai **admin**, saya ingin menetapkan periode akademik aktif (tahun ajaran & semester) agar seluruh proses mengacu pada periode yang benar.
19. Sebagai **admin**, saya ingin menetapkan dosen pembimbing skripsi/KP agar setiap mahasiswa mendapat bimbingan.
20. Sebagai **admin**, saya ingin mengelola pengumuman agar informasi penting sampai ke target pengguna.

### 4.4 Pimpinan

21. Sebagai **pimpinan**, saya ingin melihat dashboard ringkasan statistik akademik agar saya dapat memantau kondisi kampus secara cepat.
22. Sebagai **pimpinan**, saya ingin melihat laporan kinerja dosen (beban mengajar & kehadiran) agar saya dapat mengevaluasi sumber daya.

---

## 5. Kriteria Penerimaan (Acceptance Criteria) — Contoh Kunci

### 5.1 KRS Online

- Mahasiswa hanya bisa memilih mata kuliah pada semester aktif yang ditawarkan untuk program studinya.
- Sistem menolak pengajuan bila total SKS melebihi batas SKS sesuai IPK, atau terdapat bentrok jadwal.
- KRS berstatus `draft` dapat diedit; setelah `submitted` terkunci dari edit mahasiswa.
- PA hanya melihat & menyetujui KRS mahasiswa bimbingannya; penolakan wajib disertai alasan.

### 5.2 Input Nilai

- Nilai akhir terhitung otomatis sesuai formula dan grade terkonversi otomatis.
- Setelah nilai tersimpan, IPK/IPS dan total SKS mahasiswa ter-update otomatis dan konsisten.
- Dosen hanya bisa menginput nilai pada kelas yang diampu pada periode nilai yang aktif.

### 5.3 AI Academic Advisor

- AI menerima konteks mahasiswa (IPK, SKS, semester, kurikulum prodi) dan menjawab dalam Bahasa Indonesia.
- Riwayat percakapan tersimpan dan hanya dapat diakses oleh mahasiswa bersangkutan.
- Jawaban AI menampilkan disclaimer bahwa ini adalah saran, bukan keputusan akademik resmi.

---

## 6. Dependensi & Asumsi

**Dependensi eksternal:**
- Laravel AI SDK memerlukan kredensial penyedia AI (mis. OpenAI/Anthropic/atau provider lain yang didukung) yang dikonfigurasi via environment.
- Queue worker (Redis/DB) untuk export dokumen & embedding.

**Asumsi:**
- Satu mahasiswa terdaftar pada satu program studi aktif pada satu waktu.
- Satu kelas (`course_offering`) diampu oleh satu dosen utama (dapat diperluas menjadi banyak dosen di versi berikutnya).
- Skema penilaian (20/30/50) dan tabel grade bersifat global, namun didesain agar mudah dikonfigurasi per prodi di kemudian hari.
