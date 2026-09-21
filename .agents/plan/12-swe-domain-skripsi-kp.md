# 12 — Senior SWE: Domain Skripsi & KP

## 1. Skripsi

### Alur
1. Admin assign pembimbing 1 & 2 (`theses.supervisor_1_id`, `supervisor_2_id`) + status awal.
2. Mahasiswa melihat judul, status, pembimbing, dan log bimbingan.
3. Mahasiswa/dosen menambah `thesis_logs` (aktivitas + catatan); dosen pembimbing menyetujui (`supervisor_approval`).
4. Dosen pembimbing meng-update status skripsi (proposal → seminar_proposal → sidang → lulus/revisi).

### Enums
`ThesisStatus`: proposal, seminar_proposal, sidang, lulus, revisi.

## 2. Kerja Praktek (KP)

### Alur
1. Admin assign dosen pembimbing KP.
2. Mahasiswa mengisi data perusahaan, pembimbing lapangan, periode (start/end), status.
3. Mahasiswa menambah `internship_logs` (logbook harian); dosen pembimbing approve/reject (`approval`).

### Enums
`InternshipStatus`: draft, berjalan, selesai, ditolak.
`InternshipLogApproval`: pending, approved, rejected.

## 3. Scoping & Policy

| Entitas | Policy |
|---|---|
| Thesis | mahasiswa → miliknya; dosen → bimbingannya (supervisor 1/2); kaprodi/admin → prodi/semua view; admin → assign |
| ThesisLog | mahasiswa → miliknya; dosen → bimbingannya |
| Internship | mahasiswa → miliknya; dosen → bimbingannya; admin → assign |
| InternshipLog | mahasiswa → miliknya; dosen → bimbingannya (approve) |

## 4. Service & Repository

- `ThesisService`, `ThesisRepository` (listForStudent, listForSupervisor, listForStudyProgram, assign, updateStatus, addLog).
- `InternshipService`, `InternshipRepository` (listForStudent, listForSupervisor, assign, addLog, approveLog).
- Semua query `->select([...])` kolom spesifik; eager-load `student.user(id,name)`, `supervisor.user(id,name)`.
