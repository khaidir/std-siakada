# 26 — Prompt: Admin — Skripsi & KP (Assign Pembimbing & Monitoring)

```text
Buatkan fitur manajemen skripsi & KP untuk admin (assign pembimbing + monitoring).

1. Halaman pages/admin/Skripsi.vue:
   - Tabel skripsi seluruh mahasiswa: NIM, Nama, Judul, Pembimbing 1/2, Status.
   - Form assign pembimbing 1 & 2 (pilih dosen).
   - Update status skripsi.

2. Halaman pages/admin/Kp.vue:
   - Tabel KP seluruh mahasiswa: NIM, Nama, Perusahaan, Dosen Pembimbing, Status.
   - Form assign dosen pembimbing + update status.

3. Backend:
   - app/DTO/ThesisAssignData.php + InternshipAssignData.php.
   - app/Repositories/Contracts/ThesisRepository.php:
     - listAll(array $filters): LengthAwarePaginator (SELECT kolom + eager-load student.user, supervisor_1.user, supervisor_2.user).
     - assignSupervisors(int $thesisId, int $s1, ?int $s2): void.
     - updateStatus(int $thesisId, ThesisStatus $status): void.
   - app/Repositories/Contracts/InternshipRepository.php:
     - listAll(array $filters): LengthAwarePaginator.
     - assignSupervisor(int $internshipId, int $lecturerId): void.
     - updateStatus(int $internshipId, InternshipStatus $status): void.
   - app/Services/ThesisService.php + InternshipService.php.
   - app/Http/Controllers/Admin/ThesisController.php (index, assign, updateStatus) + InternshipController.php + Request.
   - app/Policies/ThesisPolicy.php + InternshipPolicy.php.

4. Aturan:
   - Admin assign pembimbing & monitoring seluruh prodi; kaprodi view prodi-nya.
   - Tidak ada SELECT *; pagination.

5. Pest test:
   - ThesisAdminTest: assign pembimbing sukses; update status sukses; role lain 403.
   - InternshipAdminTest: assign pembimbing sukses; role lain 403.
```
