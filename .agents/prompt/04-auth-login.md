# 04 — Prompt: Auth & Login

```text
Buatkan autentikasi SIAKAD dengan Laravel Fortify (headless) + Inertia + Vue 3.

1. Fortify:
   - Aktifkan fitur login & logout (registration opsional; reset password opsional).
   - Fortify::loginView() → Inertia::render('auth/login').

2. Halaman pages/auth/Login.vue:
   - Form email + password memakai useForm dari @inertiajs/vue3.
   - Kirim ke route('login'); tampilkan error validasi & flash gagal.
   - Tombol submit disabled saat processing.

3. Redirect per role setelah login:
   - super-admin → /admin/dashboard
   - kaprodi → /kaprodi/dashboard
   - dosen → /dosen/dashboard
   - mahasiswa → /mahasiswa/dashboard
   - pimpinan → /pimpinan/dashboard
   (Implementasikan di app/Providers/AppServiceProvider atau Fortify boot: sesuaikan redirect berdasar role user.)

4. Logout: tombol di dropdown avatar → POST route('logout').

5. Middleware:
   - 'auth' bawaan.
   - role middleware: role:super-admin, role:kaprodi, role:dosen, role:mahasiswa, role:pimpinan.
   - Daftarkan di app/Http/Kernel.php (alias).

6. Shared props di HandleInertiaRequests: auth (user, role, permissions, profil), flash.

7. Pest test AuthTest:
   - login sukses redirect ke dashboard role masing-masing.
   - login gagal (password salah) tetap di /login dengan error.
   - logout mengarah ke /login.

PASTIKAN: tidak ada SELECT * (query role/permission pakai kolom spesifik), UI Bahasa Indonesia.
```
