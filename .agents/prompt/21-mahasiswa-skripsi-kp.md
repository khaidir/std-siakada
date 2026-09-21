# 21 — Prompt: Mahasiswa — Skripsi & Kerja Praktek (KP)

```text
Buatkan fitur tracking skripsi dan kerja praktek untuk mahasiswa.

1. Halaman pages/mahasiswa/Skripsi.vue:
   - Kartu info skripsi: judul, abstrak, pembimbing 1 & 2, status (badge), tanggal pengajuan.
   - Timeline status (proposal → seminar_proposal → sidang → lulus/revisi).
   - Tabel log bimbingan: Tanggal, Aktivitas, Catatan, Status approval pembimbing.
   - Form tambah log bimbingan.

2. Halaman pages/mahasiswa/Kp.vue:
   - Kartu info KP: perusahaan, alamat, dosen pembimbing, pembimbing lapangan, periode, status.
   - Tabel logbook harian: Tanggal, Aktivitas, Catatan, Status approval.
   - Form tambah logbook.

3. Backend:
   - app/DTO/ThesisLogData.php + InternshipLogData.php.
   - app/Repositories/Contracts/ThesisRepository.php:
     - findForStudent(int $studentId): ?Thesis (SELECT kolom + eager-load supervisor_1.user, supervisor_2.user).
     - listLogs(int $thesisId): Collection (SELECT id, date, activity, notes, supervisor_approval).
     - addLog(array $data): ThesisLog.
   - app/Repositories/Contracts/InternshipRepository.php:
     - findForStudent(int $studentId): ?Internship.
     - listLogs(int $internshipId): Collection.
     - addLog(array $data): InternshipLog.
   - app/Services/ThesisService.php + InternshipService.php (addLog: cek kepemilikan mahasiswa).
   - app/Http/Controllers/Mahasiswa/ThesisController.php (index, storeLog) + InternshipController.php (index, storeLog) + Request.
   - app/Policies/ThesisPolicy.php + InternshipPolicy.php (hanya miliknya).

4. Aturan:
   - Mahasiswa hanya melihat/mengisi data miliknya.
   - Log baru berstatus approval pending; dosen pembimbing yang meng-approve.
   - Tidak ada SELECT *.

5. Pest test:
   - ThesisTest (mahasiswa): lihat skripsi miliknya; tambah log sukses; skripsi mahasiswa lain 403.
   - InternshipTest (mahasiswa): tambah logbook sukses; KP mahasiswa lain 403.
```
