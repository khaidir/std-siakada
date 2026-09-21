# 11 — Prompt: Mahasiswa — Lihat Materi

```text
Buatkan fitur lihat materi untuk mahasiswa.

1. Halaman pages/mahasiswa/Materi.vue:
   - Daftar kelas yang diikuti (dari study_plan_details berstatus approved).
   - Pilih kelas → daftar materi (judul, deskripsi, tautan unduh file bila ada).
   - Kosong (EmptyState) bila belum ada materi.

2. Backend:
   - Reuse MaterialRepository (listByOffering).
   - app/Http/Controllers/Mahasiswa/MaterialController.php (index):
     - Ambil offering id yang diikuti mahasiswa (dari study_plan_details approved), lalu list materi.
   - app/Policies/MaterialPolicy.php (view: mahasiswa harus terdaftar di kelas).

3. Aturan:
   - Mahasiswa hanya lihat materi kelas yang diikuti (bukan semua).
   - Tidak ada SELECT *.

4. Pest test:
   - MaterialTest (mahasiswa): lihat materi kelas diikuti; kelas lain 403.
```
