# 18 — Prompt: Mahasiswa — KHS & Transkrip

```text
Buatkan fitur KHS (Kartu Hasil Studi) dan Transkrip Nilai untuk mahasiswa.

1. Halaman pages/mahasiswa/Khs.vue:
   - Dropdown pilih semester.
   - Kartu ringkasan: IPS semester, IPK kumulatif, total SKS semester, total SKS kumulatif.
   - Tabel MK: Kode, Nama MK, SKS, Skor, Huruf, Grade Point, Status (lulus/tidak).
   - Tombol cetak/export PDF.

2. Halaman pages/mahasiswa/Transkrip.vue:
   - Transkrip lengkap seluruh semester (dikelompokkan per semester).
   - Header: NIM, Nama, Prodi, Fakultas, IPK final.
   - Tombol export PDF (berformat resmi).

3. Backend:
   - app/Repositories/Contracts/GradeRepository.php:
     - listForStudentBySemester(int $studentId, int $semesterId): Collection — SELECT grade kolom + eager-load studyPlanDetail.offering.course (id, code, name, sks).
     - listAllForStudent(int $studentId): Collection (untuk transkrip, grouped by semester).
   - app/Services/KhsService.php:
     - ipSemester(int $studentId, int $semesterId): float — Σ(bobot × SKS)/Σ SKS semester.
     - ipkKumulatif(int $studentId): float — Σ(bobot × SKS)/Σ SKS seluruh semester.
     - summary(int $studentId): array (ips, ipk, total_sks, cumulative_sks).
   - app/Http/Controllers/Mahasiswa/KhsController.php (index) + TranskripController.php (index, exportPdf).
   - PDF export memakai barryvdh/laravel-dompdf (view Blade).
   - app/Policies/GradePolicy.php (view: miliknya sendiri).

4. Aturan:
   - Mahasiswa hanya melihat KHS/transkrip miliknya.
   - Perhitungan IPS/IPK di backend (source of truth), bukan frontend.
   - Tidak ada SELECT *.

5. Pest test:
   - KhsTest: IPS & IPK dihitung benar; mahasiswa lain 403.
   - TranskripTest: export PDF sukses (assert header Content-Type application/pdf).
```
