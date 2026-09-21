# 08 — Senior SWE: Daftar Route & Endpoint (Lengkap per PRD)

## 1. Auth (Fortify)

| Method | Route | Nama |
|---|---|---|
| GET/POST | `/login` | `login` |
| POST | `/logout` | `logout` |

## 2. super-admin

| Method | Route | Nama | Permission |
|---|---|---|---|
| GET | `/admin/dashboard` | `admin.dashboard` | `dashboard.view` |
| GET/POST/PUT/DELETE | `/admin/users` | `admin.users.*` | `users.*` |
| GET/POST/PUT/DELETE | `/admin/roles` | `admin.roles.*` | `roles.manage` |
| GET/POST/PUT/DELETE | `/admin/faculties` | `admin.faculties.*` | `master.*` |
| GET/POST/PUT/DELETE | `/admin/study-programs` | `admin.prodi.*` | `master.*` |
| GET/POST/PUT/DELETE | `/admin/classrooms` | `admin.classrooms.*` | `master.*` |
| GET/POST/PUT/DELETE | `/admin/courses` | `admin.courses.*` | `master.*` |
| GET/POST/PUT/DELETE | `/admin/course-offerings` | `admin.offerings.*` | `offering.manage` |
| GET/POST/PUT/DELETE | `/admin/periods` | `admin.periods.*` | `period.manage` |
| GET | `/admin/krs-approval` | `admin.krs.index` | `krs.approve` |
| GET | `/admin/skripsi-kp` | `admin.skripsi-kp.index` | `advisor.manage` |
| POST | `/admin/skripsi-kp/assign` | `admin.skripsi-kp.assign` | `advisor.manage` |
| GET | `/admin/kehadiran-dosen` | `admin.kehadiran.index` | `lecturer-attendance.view` |
| GET/POST/PUT/DELETE | `/admin/announcements` | `admin.announcements.*` | `announcement.manage` |

## 3. kaprodi

| Method | Route | Nama | Permission |
|---|---|---|---|
| GET | `/kaprodi/dashboard` | `kaprodi.dashboard` | `dashboard.view` |
| GET/POST/PUT/DELETE | `/kaprodi/courses` | `kaprodi.courses.*` | `courses.*` (scoped prodi) |
| GET | `/kaprodi/offerings` | `kaprodi.offerings.index` | `offerings.view` (scoped prodi) |
| GET | `/kaprodi/grades` | `kaprodi.grades.index` | `grades.view` (scoped prodi) |
| GET | `/kaprodi/skripsi-kp` | `kaprodi.skripsi-kp.index` | `thesis.view`/`internship.view` |
| GET | `/kaprodi/kehadiran` | `kaprodi.kehadiran.index` | `lecturer-attendance.view` |

## 4. dosen

| Method | Route | Nama | Permission |
|---|---|---|---|
| GET | `/dosen/dashboard` | `dosen.dashboard` | `dashboard.view` |
| GET | `/dosen/kelas` | `dosen.kelas.index` | `offerings.view` (kelasnya) |
| GET | `/dosen/nilai` | `dosen.nilai.index` | `grades.manage` |
| POST | `/dosen/nilai` | `dosen.nilai.store` | `grades.manage` |
| GET/POST | `/dosen/presensi` | `dosen.presensi.*` | `attendance.manage` |
| GET/POST/PUT/DELETE | `/dosen/materi` | `dosen.materi.*` | `material.manage` |
| GET/POST/PUT/DELETE | `/dosen/tugas` | `dosen.tugas.*` | `assignment.manage` |
| POST | `/dosen/tugas/{assignment}/nilai` | `dosen.tugas.nilai` | `assignment.manage` |
| GET | `/dosen/bimbingan-pa` | `dosen.bimbingan-pa.index` | `krs.approve` |
| POST | `/dosen/bimbingan-pa/{studyPlan}/approve` | `dosen.bimbingan-pa.approve` | `krs.approve` |
| POST | `/dosen/bimbingan-pa/{studyPlan}/reject` | `dosen.bimbingan-pa.reject` | `krs.approve` |
| GET | `/dosen/bimbingan-skripsi` | `dosen.skripsi.index` | `thesis.view` |
| POST | `/dosen/skripsi/{thesis}/log` | `dosen.skripsi.log` | `thesis.view` (bimbingannya) |
| GET/POST | `/dosen/kehadiran` | `dosen.kehadiran.*` | `lecturer-attendance.view` (miliknya) |

## 5. mahasiswa

| Method | Route | Nama | Permission |
|---|---|---|---|
| GET | `/mahasiswa/dashboard` | `mahasiswa.dashboard` | `dashboard.view` |
| GET | `/mahasiswa/krs` | `mahasiswa.krs.index` | `krs.manage` |
| POST | `/mahasiswa/krs` | `mahasiswa.krs.store` | `krs.manage` |
| POST | `/mahasiswa/krs/submit` | `mahasiswa.krs.submit` | `krs.manage` |
| DELETE | `/mahasiswa/krs/{detail}` | `mahasiswa.krs.destroy` | `krs.manage` |
| GET | `/mahasiswa/jadwal` | `mahasiswa.jadwal.index` | `offerings.view` |
| GET | `/mahasiswa/khs` | `mahasiswa.khs.index` | `grades.view` |
| GET | `/mahasiswa/transkrip` | `mahasiswa.transkrip.index` | `grades.view` |
| GET | `/mahasiswa/transkrip/pdf` | `mahasiswa.transkrip.pdf` | `grades.view` |
| GET | `/mahasiswa/presensi` | `mahasiswa.presensi.index` | `attendance.view` |
| GET | `/mahasiswa/materi` | `mahasiswa.materi.index` | `material.view` |
| GET | `/mahasiswa/tugas` | `mahasiswa.tugas.index` | `material.view` |
| POST | `/mahasiswa/tugas/{assignment}/submit` | `mahasiswa.tugas.submit` | `submission.manage` |
| GET | `/mahasiswa/nilai` | `mahasiswa.nilai.index` | `grades.view` |
| GET/POST | `/mahasiswa/ai-advisor` | `mahasiswa.ai.*` | `ai-advisor.use` |
| GET | `/mahasiswa/skripsi` | `mahasiswa.skripsi.index` | `thesis.view` |
| POST | `/mahasiswa/skripsi/log` | `mahasiswa.skripsi.log` | `thesis.view` (miliknya) |
| GET/POST | `/mahasiswa/kp` | `mahasiswa.kp.*` | `internship.view` |
| POST | `/mahasiswa/kp/log` | `mahasiswa.kp.log` | `internship.view` (miliknya) |

## 6. pimpinan (read-only)

| Method | Route | Nama | Permission |
|---|---|---|---|
| GET | `/pimpinan/dashboard` | `pimpinan.dashboard` | `dashboard.view` |
| GET | `/pimpinan/laporan/krs-khs` | `pimpinan.laporan.krs-khs` | `report.view` |
| GET | `/pimpinan/laporan/transkrip-presensi` | `pimpinan.laporan.transkrip-presensi` | `report.view` |
| GET | `/pimpinan/laporan/kinerja-dosen` | `pimpinan.laporan.kinerja-dosen` | `report.view` |

> Middleware: `auth` + `role:<role>` + `permission:<perm>`. Scoping data dijamin Policy di dalam controller; `pimpinan` tanpa endpoint mutasi (hanya GET).
