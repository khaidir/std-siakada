# PROMPT TEKNIS — SIAKAD (siap copy-paste ke Cursor / Claude Code / Copilot)

> **Cara pakai:** Jalankan prompt per fase secara berurutan. Jalankan migrasi, seeder, dan test di setiap fase sebelum lanjut.
> **Konteks global yang WAJIB disertakan di awal setiap sesi (atau simpan sebagai `AGENTS.md`/`.cursorrules`):**

````markdown
## KONTEKS PROYEK SIAKAD

- Stack: Laravel 13 (PHP 8.3+) · Inertia.js v3 · Vue 3 (Composition API, <script setup>) · Tailwind CSS v4 · Vite 7 · MySQL 8.0+.
- Auth: Laravel Fortify (headless). RBAC: Spatie Laravel Permission.
- Testing: Pest PHP. DTO: spatie/laravel-data. AI: laravel/ai (first-party SDK).
- Bahasa UI: Indonesia. Export PDF: barryvdh/laravel-dompdf. Export Excel: maatwebsite/excel.

### ATURAN ARSITEKTUR (WAJIB DIPATUHI)
1. Lapisan: Controller → Service → Repository → Model. Controller tipis (tanpa business logic).
2. Semua input berpindah sebagai DTO (spatie/laravel-data), divalidasi di FormRequest.
3. Repository men-enkapsulasi SEMUA query. Interface di app/Repositories/Contracts, implementasi di app/Repositories/Eloquent.
4. **DILARANG `SELECT *`** — selalu `->select([...])` daftar kolom yang dibutuhkan. Dilarang `Model::all()` tanpa select.
5. Eager-load dengan kolom spesifik: `with('student:id,nim,user_id')`. Tidak boleh lazy-load / N+1.
6. Kolom status pakai PHP backed enum (bukan string bebas / enum DB). Simpan sebagai string.
7. Otorisasi ganda: middleware permission (role) + Policy (row-level, via `$this->authorize()`).
8. Operasi multi-tabel wajib dibungkus `DB::transaction()`.
9. Semua fitur harus punya Pest test (feature + unit untuk service).
10. UI: komponen Vue ringan di resources/js/components/ui/ (Button, Card, Table, Modal, Input, Select, Badge, Tabs, Toast, Skeleton, Dropdown).
````

---

## 3.1 Prompt Setup Awal

```text
Buatkan proyek SIAKAD baru dengan Laravel 13 dan stack Inertia v3 + Vue 3 + Tailwind v4.

1. Buat proyek Laravel 13:
   composer create-project laravel/laravel siakad "^13.0"

2. Install package backend:
   composer require laravel/fortify spatie/laravel-permission spatie/laravel-data inertiajs/inertia-laravel laravel/ai laravel/sanctum barryvdh/laravel-dompdf maatwebsite/excel

3. Install package frontend:
   npm install @inertiajs/vue3 vue @vitejs/plugin-vue
   npm install -D tailwindcss @tailwindcss/vite
   npm install ziggy-js recharts-vue

4. Konfigurasi Inertia v3 dengan Vue 3:
   - app/Http/Middleware/HandleInertiaRequests.php membagikan shared props (auth, role, permissions, flash).
   - resources/js/app.js: createInertiaApp dengan resolve Page dari resources/js/pages/ dan plugin ZiggyVue.

5. Konfigurasi Tailwind CSS v4:
   - vite.config.js memakai plugin laravel + vue + tailwindcss.
   - resources/css/app.css: @import "tailwindcss";

6. Buat komponen UI dasar (resources/js/components/ui/):
   Button, Card, Input, Select, Textarea, Table, Modal (Dialog), Badge, Tabs, Toast, Skeleton, Dropdown.
   - Gunakan Vue 3 Composition API (<script setup>).
   - Gunakan slot + props typed (defineProps/defineEmits) tanpa TypeScript.

7. Setup Fortify:
   - Aktifkan features (login, registration, reset password, email verification).
   - Buat FortifyServiceProvider, customisasi Fortify::loginView/loginView untuk mengembalikan Inertia::render('auth/login').
   - Buat halaman Vue: pages/auth/Login.vue, Register.vue, ForgotPassword.vue, ResetPassword.vue, VerifyEmail.vue.

8. Setup Spatie Permission:
   - Publikasikan migrasi spatie.
   - Definisikan roles: admin, admin_fakultas, dosen, mahasiswa, pimpinan (di seeder).
   - Buat middleware alias role & permission.

9. Setup struktur folder arsitektur:
   app/Enums, app/DTO, app/Services, app/Repositories/Contracts, app/Repositories/Eloquent, app/Policies.

10. Setup Pest PHP (composer require pestphp/pest --dev, lalu ./vendor/bin/pest --init).

Sertakan instruksi menjalankan: composer install && npm install && php artisan migrate && npm run dev.
```

---

## 3.2 Prompt Migrasi Database & Model

```text
Buatkan migrasi database lengkap untuk SIAKAD. Tabel dan kolom mengikuti daftar di bawah.
Aturan umum:
- Primary key pakai $table->id().
- Foreign key pakai $table->foreignId('x_id')->constrained()->cascadeOnDelete() (atau nullOnDelete bila relasi opsional).
- Tambahkan index() pada kolom yang sering di-filter/join: nim, nidn, code, semester_id, student_id, course_offering_id, date.
- Kolom status/type pakai $table->string('status') (bukan enum DB) — nilainya divalidasi via PHP enum.
- Semua tabel pakai $table->timestamps().
- Tabel master (faculties, study_programs, courses, classrooms) pakai $table->softDeletes().

DAFTAR TABEL:
1. users: id, name, email(unique), password, email_verified_at(nullable), remember_token, timestamps.
2. faculties: id, code(unique), name, dean_id(fk users nullable), timestamps, softDeletes.
3. study_programs: id, faculty_id(fk), code(unique), name, degree_level(string), head_id(fk users nullable), timestamps, softDeletes.
4. academic_years: id, code(unique), name, start_date(date), end_date(date), is_active(bool default false), timestamps.
5. semesters: id, academic_year_id(fk), type(string), start_date, end_date, is_active(bool default false), timestamps.
6. courses: id, study_program_id(fk), code(unique), name, sks(tinyInteger), semester(integer), type(string), timestamps, softDeletes.
7. classrooms: id, code(unique), name, capacity(integer), building(string nullable), timestamps, softDeletes.
8. students: id, user_id(fk unique), nim(unique), study_program_id(fk), entry_year(string), status(string), gpa(decimal 5,2 default 0), total_sks(integer default 0), timestamps.
9. lecturers: id, user_id(fk unique), nidn(unique), study_program_id(fk nullable), academic_rank(string), timestamps.
10. course_offerings: id, course_id(fk), semester_id(fk), lecturer_id(fk), classroom_id(fk), day(string), start_time(time), end_time(time), quota(integer), timestamps.
11. study_plans: id, student_id(fk), semester_id(fk), status(string default 'draft'), approved_by(fk users nullable), approved_at(timestamp nullable), timestamps.
12. study_plan_details: id, study_plan_id(fk), course_offering_id(fk), status(string default 'pending'), timestamps.
13. grades: id, study_plan_detail_id(fk unique), student_id(fk), course_offering_id(fk), assignment_score(decimal nullable), midterm_score(decimal nullable), final_score(decimal nullable), score(decimal), letter_grade(string 2), grade_point(decimal 5,2), timestamps.
14. attendances: id, course_offering_id(fk), student_id(fk), meeting_number(integer), date(date), status(string), timestamps.
15. lecturer_attendances: id, lecturer_id(fk), course_offering_id(fk), date(date), check_in(time nullable), check_out(time nullable), status(string), timestamps.
16. theses: id, student_id(fk unique), title, abstract(text), supervisor_1_id(fk lecturers), supervisor_2_id(fk lecturers nullable), status(string), submission_date(date), timestamps.
17. thesis_logs: id, thesis_id(fk), date(date), activity, notes(text), supervisor_approval(bool default false), timestamps.
18. internships: id, student_id(fk), company_name, address(text), supervisor_id(fk lecturers), field_supervisor(string), start_date(date), end_date(date), status(string), timestamps.
19. internship_logs: id, internship_id(fk), date(date), activity, notes(text), approval(string default 'pending'), timestamps.
20. course_materials: id, course_offering_id(fk), title, description(text nullable), file_path(string nullable), uploaded_by(fk users), timestamps.
21. assignments: id, course_offering_id(fk), title, description(text), due_date(datetime), max_score(decimal), timestamps.
22. assignment_submissions: id, assignment_id(fk), student_id(fk), file_path, submitted_at(timestamp nullable), score(decimal nullable), feedback(text nullable), timestamps.
23. announcements: id, title, content(text), target_role(string nullable), published_at(timestamp nullable), timestamps.
24. activity_logs: id, user_id(fk users nullable), action(string), model_type(string), model_id(bigInteger nullable), old_values(json nullable), new_values(json nullable), timestamps.

Setelah migrasi, buat:
1. PHP backed enum untuk setiap kolom status/type: StudentStatus, DegreeLevel, SemesterType, CourseType, DayOfWeek, StudyPlanStatus, StudyPlanDetailStatus, AttendanceStatus, LecturerAttendanceStatus, ThesisStatus, InternshipStatus, InternshipLogApproval, AcademicRank, GradeLetter.
2. Model Eloquent untuk SEMUA tabel dengan:
   - Relasi lengkap (hasMany, belongsTo, hasOne, belongsToMany bila perlu) dengan tipe return yang tepat.
   - $fillable atau $guarded.
   - $casts untuk enum, datetime, json, decimal.
   - Scope kecil yang sering dipakai (scopeActive untuk semester/academic_year).
3. Seeder (DatabaseSeeder + seeder per domain): RoleSeeder (roles+permissions), FacultySeeder, StudyProgramSeeder, CourseSeeder, UserSeeder (admin, dosen, mahasiswa dummy), AcademicYearSemesterSeeder, ClassRoomSeeder, CourseOfferingSeeder.
Gunakan Factory untuk users, students, lecturers, courses, course_offerings, grades, attendances.
```

---

## 3.3 Prompt Layout & Komponen UI (Vue 3)

```text
Buatkan layout utama aplikasi SIAKAD dengan Vue 3 + Inertia + Tailwind v4.

1. resources/js/Layouts/AuthenticatedLayout.vue:
   - Sidebar kiri: logo SIAKAD, menu per role (dari props auth.role), collapsible (mode icon-only), indikator menu aktif (Inertia usePage().url), sub-menu Master Data untuk admin.
   - Header: breadcrumbs (pakai route() dari Ziggy), search bar global (command palette sederhana dengan dropdown), dropdown notifikasi, dropdown avatar (profil, logout), toggle dark mode.
   - Main content pakai <slot />.

2. Menu per role:
   - mahasiswa: Dashboard, KRS, KHS, Transkrip, Presensi, Jadwal, LMS, Skripsi, KP, AI Advisor.
   - dosen: Dashboard, Input Nilai, Presensi Kelas, Bimbingan PA, Bimbingan Skripsi, LMS Management.
   - admin: Dashboard, Master Data (submenu: Fakultas, Prodi, Mata Kuliah, Kelas, Ruangan, Periode Akademik), Users, KRS Approval, Skripsi & KP, Kehadiran Dosen, Announcements.
   - pimpinan: Dashboard, Laporan.

3. Buat komponen shared (resources/js/components/shared/):
   - StatCard.vue (ikon, label, nilai, sublabel)
   - DataTable.vue (props: columns, rows, pagination; slot aksi; empty state; loading skeleton)
   - PageHeader.vue (judul + aksi + breadcrumbs)
   - ConfirmDialog.vue (untuk konfirmasi hapus/approve/reject)

4. Dashboard:
   - 4 StatCard di atas.
   - Area chart / bar chart dengan recharts-vue.
   - Tabel aktivitas terbaru pakai DataTable.
   - Skeleton saat loading (Inertia defer).

Gunakan Tailwind v4 dengan CSS variables untuk tema terang/gelap. Semua komponen reusable, tanpa TypeScript, tanpa shadcn/ui.
```

---

## 3.4 Prompt Fitur KRS Online

```text
Buatkan fitur KRS Online untuk mahasiswa (Laravel 13 + Inertia + Vue 3) mengikuti arsitektur Service-Repository-DTO.

1. Halaman /krs (pages/mahasiswa/Krs.vue):
   - Info card: semester aktif, batas SKS (dari IPK), total SKS yang sudah dipilih.
   - Table mata kuliah ditawarkan: Kode, Nama, SKS, Dosen, Jadwal, Kuota, Status, aksi pilih/batal.
   - Validasi di frontend: cegah pilih bila melebihi batas SKS atau bentrok jadwal (tampilkan pesan).
   - Tombol "Submit KRS" dengan ConfirmDialog.
   - Badge status KRS: draft, submitted, approved, rejected.

2. Backend:
   - app/Enums/StudyPlanStatus.php, StudyPlanDetailStatus.php.
   - app/DTO/StudyPlanData.php, StudyPlanDetailData.php (spatie/laravel-data).
   - app/Repositories/Contracts/StudyPlanRepository.php + CourseOfferingRepository.php + StudentRepository.php.
   - Implementasi Eloquent: SEMUA query pakai ->select([...]) kolom spesifik (jangan SELECT *), eager-load kolom spesifik.
   - app/Services/KrsService.php dengan method: index(studentId), addCourse, removeCourse, submit, approve, reject.
   - app/Http/Controllers/KrsController.php (tipis) + app/Http/Requests/StoreStudyPlanRequest.php + ApproveStudyPlanRequest.php.

3. Aturan validasi SKS otomatis (di KrsService):
   - IPK >= 3.00 -> maks 24 SKS
   - IPK 2.50 - 2.99 -> maks 21 SKS
   - IPK 2.00 - 2.49 -> maks 18 SKS
   - IPK < 2.00 -> maks 15 SKS
   - Cek kuota course_offering (jangan melebihi quota).
   - Cek bentrok jadwal (hari + jam yang sama).

4. Role & policy:
   - Route dilindungi middleware role:mahasiswa.
   - StudyPlanPolicy: mahasiswa hanya akses KRS miliknya; PA hanya approve mahasiswa bimbingannya.
   - Saat approve/reject, catat ke activity_logs dan set approved_by/approved_at.

5. Tulis Pest test: KrsTest (submit, validasi SKS, approval, akses antar-role) + KrsServiceTest (unit validasi SKS & bentrok jadwal).
```

---

## 3.5 Prompt Fitur Input Nilai

```text
Buatkan fitur Input Nilai untuk dosen (arsitektur Service-Repository-DTO).

1. Halaman /nilai (pages/dosen/Nilai.vue):
   - Dropdown pilih kelas (course_offering) yang diampu dosen saat ini.
   - Tabel editable (komponen DataTable custom) kolom: NIM, Nama, Nilai Tugas, Nilai UTS, Nilai UAS, Nilai Akhir (auto), Grade (auto).
   - Formula: Nilai Akhir = Tugas*20% + UTS*30% + UAS*50% (dihitung di frontend sebagai preview, dan DIHITUNG ULANG di backend sebagai source of truth).
   - Konversi grade: A (85-100), A- (80-84), B+ (75-79), B (70-74), B- (65-69), C+ (60-64), C (55-59), D (40-54), E (0-39).
   - Tombol Simpan (batch), tombol Export Excel/PDF.

2. Backend:
   - app/Enums/GradeLetter.php (dengan grade point: A=4.0, A-=3.75, ... E=0).
   - app/Support/GradeCalculator.php: method final(assignment, midterm, final) dan letter(score) dan point(letter).
   - app/DTO/GradeData.php, BatchGradeData.php.
   - app/Repositories/Contracts/GradeRepository.php + StudentRepository.php (query pakai ->select kolom spesifik).
   - app/Services/GradeService.php: storeBatch(BatchGradeData), updateGpa(studentId).
   - app/Http/Controllers/GradeController.php + GradeRequest.php.

3. Setelah nilai tersimpan (dalam DB::transaction):
   - Simpan grade per mahasiswa.
   - Recalculate IPK & total_sks mahasiswa (StudentRepository::recalculateGpa).
   - Update study_plan_detail status menjadi approved.

4. Otorisasi: GradePolicy — dosen hanya bisa input nilai kelas yang diampu pada periode nilai aktif.

5. Tulis Pest test: GradeTest (input batch, akses lintas dosen ditolak) + GradeServiceTest + GradeCalculatorTest (semua batas grade).
```

---

## 3.6 Prompt Fitur AI Academic Advisor

```text
Buatkan fitur AI Academic Advisor menggunakan Laravel AI SDK (first-party, laravel/ai).

1. Halaman /ai-advisor (pages/mahasiswa/AiAdvisor.vue):
   - Chat interface: message bubbles (user vs assistant), auto-scroll.
   - Input text + tombol kirim, loading indicator saat AI memproses.
   - Riwayat percakapan dimuat dari database.

2. Backend:
   - Migrasi & model Conversation + ConversationMessage (id, user_id, role, content, timestamps).
   - app/Services/AcademicAdvisorService.php:
     - Menggunakan Laravel AI SDK untuk: text generation (jawaban), embedding (semantic search dokumen akademik), tool calling (query IPK/SKS mahasiswa).
   - app/Http/Controllers/AiAdvisorController.php (index, store) + AiAdvisorRequest.php.
   - app/Repositories/Contracts/ConversationRepository.php (select kolom spesifik).

3. Konteks AI yang diberikan (dibangun dari data mahasiswa via repository, tanpa SELECT *):
   - Data mahasiswa (IPK, total SKS, semester).
   - Riwayat nilai.
   - Kurikulum program studi (mata kuliah wajib/pilihan + SKS).
   - Peraturan akademik (dari pengaturan/master).

4. Sistem prompt (Bahasa Indonesia):
   "Kamu adalah asisten akademik SIAKAD. Gunakan data akademik yang diberikan untuk menjawab. Jika data tidak tersedia, katakan tidak tahu. Beri rekomendasi rencana studi yang jelas. Selalu ingatkan bahwa ini saran, bukan keputusan resmi."

5. Contoh prompt user:
   "Berdasarkan IPK saya saat ini (3.25) dan SKS yang sudah ditempuh (105), mata kuliah apa yang sebaiknya saya ambil semester depan untuk memaksimalkan peluang lulus tepat waktu?"

6. Rate limiting pada endpoint chat. Riwayat hanya bisa diakses pemiliknya (Policy).

7. Tulis Pest test: AcademicAdvisorServiceTest (mock SDK, assert prompt & tool calling) + AiAdvisorTest (akses role).
```

---

## 3.7 Prompt Fitur Pendukung (Master Data, Presensi, Skripsi/KP, LMS)

```text
Buatkan fitur-fitur berikut mengikuti pola yang sama (Controller tipis + FormRequest + DTO + Service + Repository + Policy, tanpa SELECT *, dengan Pest test):

A. Master Data Admin (CRUD): Fakultas, Prodi, Mata Kuliah, Kelas & Jadwal (course_offerings), Ruangan, Periode Akademik, User Management, Announcements.
   - Halaman: tabel + modal form + confirm delete (soft delete untuk master).
   - Faculty-scoped: admin_fakultas hanya melihat/mengelola data fakultasnya (filter di repository).

B. Presensi Kelas (dosen): kelola pertemuan, tandai hadir/izin/sakit/alpha per mahasiswa, rekap otomatis.

C. Presensi Mahasiswa & Jadwal (mahasiswa): riwayat kehadiran + persentase; jadwal mingguan.

D. KHS & Transkrip: hitung IPS/IPK, export PDF (laravel-dompdf).

E. Skripsi & KP: theses, thesis_logs, internships, internship_logs; assign pembimbing (admin); logbook & approval.

F. LMS: course_materials, assignments, assignment_submissions (upload file, submit, nilai & feedback).

G. Kehadiran Dosen: lecturer_attendances (check-in/check-out) + monitoring admin & pimpinan.

H. Dashboard & Laporan pimpinan (read-only): statistik + laporan akademik (presensi, kinerja dosen).

Untuk setiap fitur, pastikan:
- Semua query repository memakai ->select([...]) kolom spesifik.
- Otorisasi row-level via Policy.
- Pest feature test + unit test untuk service.
```

---

## 3.8 Prompt Testing & Polish

```text
Buatkan test suite Pest PHP lengkap dan lakukan optimasi akhir.

1. Feature Test:
   - AuthTest: login, register, logout, forgot password, rate limiting.
   - KrsTest: submit KRS, validasi SKS, approval, akses lintas role ditolak.
   - GradeTest: input batch nilai, kalkulasi IPK, dosen lintas kelas ditolak.
   - PermissionTest: setiap role hanya mengakses route miliknya.
   - AttendanceTest, ThesisTest, InternshipTest, LmsTest, AnnouncementTest.

2. Unit Test:
   - GradeCalculatorTest (semua batas konversi grade).
   - KrsServiceTest (validasi SKS & bentrok jadwal).
   - AcademicAdvisorServiceTest (mock SDK).
   - Repository test bila perlu (assert query memakai select kolom spesifik).

3. Aturan test:
   - Gunakan RefreshDatabase dan factories.
   - Gunakan Roles/Permissions seeder di setUp.
   - Jangan menyentuh jaringan eksternal (mock HTTP/SDK).

4. Optimasi & polish:
   - Audit seluruh repository: pastikan tidak ada SELECT * dan tidak ada N+1 (aktifkan laravel debugbar/Pulse).
   - Tambahkan caching (Redis) untuk dashboard & master data + invalidasi.
   - Pastikan security headers & rate limiting aktif.
   - Responsive check (mobile sidebar collapse, tabel responsif).

Jalankan: ./vendor/bin/pest dan laporkan hasil.
```

---

## Urutan Eksekusi yang Disarankan

| Urutan | Prompt | Fokus |
|---|---|---|
| 1 | 3.1 | Setup awal (project, packages, Inertia+Vue+Tailwind, Fortify, Permission) |
| 2 | 3.2 | Migrasi, enums, model, seeder |
| 3 | 3.3 | Layout, sidebar, komponen UI, dashboard |
| 4 | 3.4 | KRS Online |
| 5 | 3.5 | Input Nilai |
| 6 | 3.7 (A) | Master data admin |
| 7 | 3.7 (B–H) | Presensi, KHS/transkrip, skripsi/KP, LMS, kehadiran dosen, laporan |
| 8 | 3.6 | AI Academic Advisor |
| 9 | 3.8 | Testing & polish |

> Setiap selesai satu prompt: jalankan `php artisan migrate`, `php artisan db:seed`, `./vendor/bin/pest`, dan `npm run dev` untuk verifikasi sebelum lanjut.
