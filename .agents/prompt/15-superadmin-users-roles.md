# 15 — Prompt: Super Admin — Pengguna, Roles & Permissions

```text
Buatkan fitur manajemen pengguna, roles, dan permissions untuk super-admin.

1. Halaman pages/admin/Users.vue:
   - Tabel pengguna: Nama, Email, Role, Status, aksi.
   - Modal form buat/edit user (nama, email, password saat buat, role).
   - Saat role=dosen → input tambahan nidn, prodi, pangkat akademik (buat profil lecturers).
   - Saat role=mahasiswa → input tambahan nim, prodi, angkatan (buat profil students).
   - Saat role=pimpinan → tanpa profil tambahan.

2. Halaman pages/admin/Roles.vue:
   - Daftar roles + permission (checkbox per permission).
   - Simpan mapping role↔permission (Spatie).

3. Backend:
   - app/DTO/UserData.php.
   - app/Repositories/Contracts/UserRepository.php + StudentRepository.php + LecturerRepository.php:
     - UserRepository::listWithRole(): Collection — SELECT id, name, email, created_at + eager-load roles (id, name).
     - create/update/delete.
   - app/Services/UserService.php:
     - create(UserData $data): DB::transaction → buat user, assign role, buat profil student/lecturer bila perlu.
   - app/Http/Controllers/Admin/UserController.php (index, store, update, destroy) + UserRequest.php.
   - app/Http/Controllers/Admin/RoleController.php (index, updatePermissions) + RoleRequest.php.
   - app/Policies/UserPolicy.php (hanya super-admin).

4. Aturan:
   - Hanya super-admin yang mengelola user, role, permission.
   - Email unik; password di-hash.
   - Tidak ada SELECT *.

5. Pest test:
   - UserRoleTest: super-admin buat user dosen & mahasiswa + profil terbuat + role aktif; role lain 403; update permission role berlaku.
```
