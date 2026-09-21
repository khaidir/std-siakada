# 04 — CTO / Tech Architect: RBAC & Menu Sidebar per Role

## 1. Roles

| Role | Deskripsi |
|---|---|
| `super-admin` | Akses penuh: kelola semua data + pengguna + roles/permissions |
| `kaprodi` | Kepala program studi — kelola mata kuliah prodi-nya |
| `dosen` | Dosen pengampu & pembimbing — nilai, absen, materi, bimbingan PA/skripsi |
| `mahasiswa` | Mahasiswa — KRS, materi, tugas, nilai, skripsi/KP, AI advisor |
| `pimpinan` | Read-only — dashboard eksekutif & laporan akademik |

## 2. Permission & Matriks

| Permission | super-admin | kaprodi | dosen | mahasiswa | pimpinan |
|---|---|---|---|---|---|
| `dashboard.view` | ✅ | ✅ | ✅ | ✅ | ✅ |
| `master.view/create/update/delete` (fakultas, prodi, ruangan) | ✅ | ❌ | ❌ | ❌ | ❌ |
| `users.view/create/update/delete` | ✅ | ❌ | ❌ | ❌ | ❌ |
| `roles.manage` | ✅ | ❌ | ❌ | ❌ | ❌ |
| `courses.view` | ✅ | ✅ (prodi) | ✅ | ✅ | ✅ |
| `courses.create/update/delete` | ✅ | ✅ (prodi) | ❌ | ❌ | ❌ |
| `offerings.view` | ✅ | ✅ (prodi) | ✅ (kelasnya) | ✅ | ✅ |
| `offering.manage` (kelas/jadwal) | ✅ | ❌ | ❌ | ❌ | ❌ |
| `period.manage` (tahun ajaran/semester) | ✅ | ❌ | ❌ | ❌ | ❌ |
| `krs.manage` (study_plan) | ✅ | ❌ | ❌ | ✅ (miliknya) | ❌ |
| `krs.approve` (bimbingan PA) | ✅ | ❌ | ✅ (bimbingannya) | ❌ | ❌ |
| `grades.manage` | ✅ | ❌ | ✅ (kelasnya) | ❌ | ❌ |
| `grades.view` | ✅ | ✅ (prodi) | ✅ | ✅ (miliknya) | ✅ |
| `attendance.manage` | ✅ | ❌ | ✅ (kelasnya) | ❌ | ❌ |
| `attendance.view` | ✅ | ✅ | ✅ | ✅ (miliknya) | ✅ |
| `material.manage` | ✅ | ❌ | ✅ (kelasnya) | ❌ | ❌ |
| `material.view` | ✅ | ✅ | ✅ | ✅ (terdaftar) | ✅ |
| `assignment.manage` | ✅ | ❌ | ✅ (kelasnya) | ❌ | ❌ |
| `submission.manage` | ✅ | ❌ | ❌ | ✅ (miliknya) | ❌ |
| `thesis.view/manage` | ✅ | ✅ view | ✅ (bimbingannya) | ✅ (miliknya) | ✅ view |
| `internship.view/manage` | ✅ | ✅ view | ✅ (bimbingannya) | ✅ (miliknya) | ✅ view |
| `lecturer-attendance.view/manage` | ✅ | ✅ view | ✅ (miliknya) | ❌ | ✅ view |
| `advisor.manage` (assign pembimbing) | ✅ | ❌ | ❌ | ❌ | ❌ |
| `ai-advisor.use` | ✅ | ❌ | ❌ | ✅ | ❌ |
| `report.view` | ✅ | ✅ | ❌ | ❌ | ✅ |
| `announcement.manage/view` | ✅ manage | ✅ view | ✅ view | ✅ view | ✅ view |

> `super-admin` menggunakan `Gate::before` → selalu `true`. Scoping (fakultas/prodi/kelas/milik/bimbingan) diterapkan di **repository layer** + **Policy**.

## 3. Menu Sidebar per Role

### super-admin
```
Dashboard
Pengguna
  └─ Daftar Pengguna
Roles & Permissions
Master Data
  ├─ Fakultas
  ├─ Program Studi
  ├─ Mata Kuliah
  ├─ Kelas & Jadwal
  └─ Ruangan
Periode Akademik
KRS Approval
Skripsi & KP
Kehadiran Dosen
Pengumuman
```

### kaprodi
```
Dashboard
Mata Kuliah            (CRUD, hanya prodi-nya)
Kelas & Jadwal         (view, hanya prodi-nya)
Monitoring Nilai       (view, hanya prodi-nya)
Skripsi & KP           (view, hanya prodi-nya)
Kehadiran Dosen        (view)
Pengumuman
```

### dosen
```
Dashboard
Kelas Saya             (daftar kelas yang diampu)
Input Nilai
Presensi               (absen mahasiswa)
Materi Kuliah
Kelola Tugas           (LMS: buat tugas, nilai submission)
Bimbingan PA           (persetujuan KRS)
Bimbingan Skripsi
Kehadiran Dosen        (check-in/check-out mengajar)
```

### mahasiswa
```
Dashboard
KRS (Pilih Mata Kuliah)
Jadwal Kuliah
KHS                    (kartu hasil studi)
Transkrip              (+ export PDF)
Presensi               (riwayat kehadiran)
Materi Kuliah
Tugas                  (kumpul tugas)
Nilai                  (hasil belajar)
AI Advisor             (konsultasi akademik)
Skripsi                (progress & log bimbingan)
Kerja Praktek          (KP & logbook)
```

### pimpinan
```
Dashboard               (eksekutif, read-only)
Laporan Akademik
  ├─ KRS & KHS
  ├─ Transkrip & Presensi
  └─ Kinerja Dosen
```

## 4. Aturan Menu

- Menu dirender dari `props.auth.role` + `props.auth.permissions` (shared prop Inertia).
- Item tanpa permission tidak dirender; akses URL tetap dilindungi middleware + Policy.
- Indikator aktif pakai `usePage().url`; sub-menu (admin/pimpinan) dapat collapse.
- Role `pimpinan` read-only: tidak ada tombol aksi mutasi pada seluruh halamannya.
