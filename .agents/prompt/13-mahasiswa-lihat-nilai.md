# 13 — Prompt: Mahasiswa — Lihat Nilai

```text
Buatkan fitur lihat nilai hasil belajar untuk mahasiswa.

1. Halaman pages/mahasiswa/Nilai.vue:
   - Ringkasan: rata-rata grade point (IPK sederhana) + total SKS.
   - Tabel per kelas: Kode MK, Nama MK, SKS, Skor, Huruf, Grade Point.
   - Badge warna per grade.

2. Backend:
   - Reuse GradeRepository::listForStudent(int $studentId): Collection — SELECT grade kolom + eager-load studyPlanDetail.offering.course (id, code, name, sks).
   - app/Services/GradeService::summaryForStudent(int $studentId): menghitung rata-rata grade point & total SKS.
   - app/Http/Controllers/Mahasiswa/GradeController.php (index).
   - app/Policies/GradePolicy.php (view: miliknya).

3. Aturan:
   - Mahasiswa hanya lihat nilainya sendiri.
   - Tidak ada SELECT *.

4. Pest test:
   - GradeTest (mahasiswa): lihat nilai miliknya; nilai mahasiswa lain tidak tampil.
```
