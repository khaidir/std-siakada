# 07 — Prompt: Dosen — Input Nilai

```text
Buatkan fitur Input Nilai untuk dosen (arsitektur Service-Repository-DTO).

1. Halaman pages/dosen/Nilai.vue:
   - Dropdown pilih kelas (course_offering) yang diampu dosen login.
   - Tabel mahasiswa (dari study_plan_details berstatus approved pada kelas tsb) kolom:
     NIM, Nama, Nilai Tugas (opsional), Nilai UTS (opsional), Nilai UAS (opsional), atau Skor Akhir, Huruf, Grade Point.
   - Huruf & grade point dihitung otomatis (preview frontend), dihitung ulang di backend (source of truth).
   - Formula skor akhir (bila input tugas/UTS/UAS): Tugas 20% + UTS 30% + UAS 50%.
   - Tombol Simpan (batch) dengan ConfirmDialog.

2. Backend:
   - app/Support/GradeCalculator.php:
     - final(assignment, midterm, final) bila komponen ada, else pakai skor langsung.
     - letterAndPoint(float $score): [GradeLetter, float]
     - Konversi: A 85-100 (4.0), A- 80-84 (3.75), B+ 75-79 (3.25), B 70-74 (3.0), B- 65-69 (2.75), C+ 60-64 (2.25), C 55-59 (2.0), D 40-54 (1.0), E 0-39 (0).
   - app/DTO/GradeData.php (study_plan_detail_id, student_id, course_offering_id, assignment_score?, midterm_score?, final_score?, score) + BatchGradeData.
   - app/Repositories/Contracts/GradeRepository.php + StudyPlanDetailRepository.php + StudentRepository.php.
     - GradeRepository::listByOffering(int $offeringId): Collection — SELECT id, study_plan_detail_id, student_id, score, letter_grade, grade_point + eager-load student.user (id, nim, name).
     - upsertBatch(array $rows): void.
   - app/Services/GradeService.php:
     - store(int $offeringId, BatchGradeData $data): dalam DB::transaction, validasi detail milik offering tsb, hitung final & letter & point, upsert, lalu recalculate gpa & total_sks mahasiswa.
   - app/Http/Controllers/Dosen/GradeController.php (index, store) + StoreGradeRequest.php (validasi skor 0-100, authorize dosen pengampu).
   - app/Policies/GradePolicy.php.

3. Aturan:
   - Dosen hanya bisa akses kelas yang diampu (lecturer_id = user lecturer login).
   - Skor wajib 0-100; tidak ada SELECT *.

4. Pest test:
   - GradeTest: dosen simpan nilai sukses; dosen lintas kelas 403; skor invalid ditolak.
   - GradeCalculatorTest: semua batas konversi + formula komponen.
```
