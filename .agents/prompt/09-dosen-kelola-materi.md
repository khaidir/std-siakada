# 09 — Prompt: Dosen — Kelola Materi

```text
Buatkan fitur kelola materi kuliah untuk dosen.

1. Halaman pages/dosen/Materi.vue:
   - Dropdown pilih kelas yang diampu.
   - Daftar materi (judul, deskripsi, file opsional) dengan tombol tambah/edit/hapus.
   - Modal form (judul, deskripsi, upload file opsional).
   - Hapus memakai ConfirmDialog.

2. Backend:
   - app/DTO/MaterialData.php (termasuk uploaded_by dari auth user).
   - app/Repositories/Contracts/MaterialRepository.php:
     - listByOffering(int $offeringId): Collection (SELECT id, title, description, file_path, uploaded_by, created_at).
     - create/update/delete.
   - app/Services/MaterialService.php.
   - app/Http/Controllers/Dosen/MaterialController.php (index, store, update, destroy) + MaterialRequest.php.
   - app/Policies/MaterialPolicy.php.
   - Upload file (opsional) memakai Storage::disk('public') + validasi mime/size.

3. Aturan:
   - Dosen hanya kelola materi kelas yang diampu.
   - Tidak ada SELECT *; query kolom spesifik.

4. Pest test:
   - MaterialTest: CRUD sukses; dosen lintas kelas 403; validasi file.
```
