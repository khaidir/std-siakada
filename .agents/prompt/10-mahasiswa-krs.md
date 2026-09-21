# 10 — Prompt: Mahasiswa — KRS (Pilih Mata Kuliah)

```text
Buatkan fitur KRS (study_plan + study_plan_details) untuk mahasiswa.

1. Halaman pages/mahasiswa/Krs.vue:
   - Info card: semester aktif, batas SKS (dari IPK), total SKS yang dipilih.
   - Tabel course_offerings prodi mahasiswa (kode, nama MK, SKS, dosen, jadwal, kuota, status pilih/batal).
   - Pilih/batal memilih (study_plan_details); validasi frontend bentrok jadwal & batas SKS.
   - Tombol "Submit KRS" dengan ConfirmDialog; Badge status KRS (draft/submitted/approved/rejected).

2. Backend:
   - app/Repositories/Contracts/StudyPlanRepository.php + StudyPlanDetailRepository.php + CourseOfferingRepository.php:
     - CourseOfferingRepository::availableForStudent(int $studentId, int $semesterId): Collection — SELECT id, course_id, lecturer_id, classroom_id, day, start_time, end_time, quota + eager-load course (id, code, name, sks), lecturer.user (id, name). Filter prodi mahasiswa + semester aktif.
     - StudyPlanRepository::activeForStudent(int $studentId, int $semesterId): ?StudyPlan (SELECT id, student_id, semester_id, status, approved_by, approved_at).
     - StudyPlanDetailRepository::listByPlan(int $planId): Collection (SELECT id, study_plan_id, course_offering_id + eager-load offering.course).
     - add/remove detail.
   - app/Services/KrsService.php:
     - addCourse(studentId, offeringId): cek status draft, duplikat & bentrok jadwal & batas SKS; tambah detail.
     - removeCourse(detailId): hapus detail.
     - submit(studentId): ubah status study_plan → submitted (terkunci).
   - app/Http/Controllers/Mahasiswa/KrsController.php (index, store, destroy, submit) + StoreKrsRequest.php.
   - app/Policies/StudyPlanPolicy.php (miliknya) + StudyPlanDetailPolicy.php.

3. Aturan validasi SKS (di KrsService):
   - IPK >= 3.00 → maks 24 SKS; 2.50-2.99 → 21; 2.00-2.49 → 18; < 2.00 → 15.
   - Cek kuota offering; cek bentrok jadwal (hari+jam).
   - Setelah submitted tidak bisa diedit mahasiswa.

4. Pest test:
   - KrsTest: tambah/remove/submit sukses; bentrok jadwal ditolak; batas SKS ditolak; duplikat ditolak; mahasiswa lain tidak bisa hapus.
```
