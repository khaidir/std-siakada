# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: superadmin.spec.ts >> Super Admin >> buat user dosen baru muncul di tabel
- Location: tests/e2e/superadmin.spec.ts:15:5

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('text=dosenbaru@test.com')
Expected: visible
Timeout: 10000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" locator('text=dosenbaru@test.com') with timeout 10000ms
  - waiting for locator('text=dosenbaru@test.com')

```

```yaml
- complementary:
  - text: S SIAKAD
  - navigation:
    - link "🏠 Dashboard":
      - /url: /admin/dashboard
    - button "👥 Pengguna":
      - text: 👥 Pengguna
      - img
    - button "🗂️ Master Data":
      - text: 🗂️ Master Data
      - img
    - link "Fakultas":
      - /url: /admin/faculties
    - link "Program Studi":
      - /url: /admin/study-programs
    - link "Mata Kuliah":
      - /url: /admin/courses
    - link "Ruangan":
      - /url: /admin/classrooms
    - link "Kelas & Jadwal":
      - /url: /admin/course-offerings
    - link "Periode Akademik":
      - /url: /admin/periods
    - link "📋 KRS (Monitoring)":
      - /url: /admin/krs-approval
    - link "🎓 Skripsi & KP":
      - /url: /admin/skripsi-kp
    - link "🕐 Kehadiran Dosen":
      - /url: /admin/kehadiran-dosen
    - link "📢 Pengumuman":
      - /url: /admin/announcements
  - text: v0.1 · SIAKAD
- banner:
  - button:
    - img
  - navigation: SIAKAD / Pengguna / Daftar Pengguna
  - button "Mode gelap":
    - img
  - button "Notifikasi":
    - img
  - button "SA Super Admin super-admin"
- main:
  - heading "Pengguna" [level=1]
  - paragraph: Kelola pengguna, role, dan akses sistem.
  - heading "Daftar Pengguna" [level=2]
  - paragraph: Semua pengguna yang terdaftar di sistem.
  - button "➕ Tambah Pengguna"
  - table:
    - rowgroup:
      - row "Nama Email Role Dibuat Aksi":
        - columnheader "Nama"
        - columnheader "Email"
        - columnheader "Role"
        - columnheader "Dibuat"
        - columnheader "Aksi"
    - rowgroup:
      - row "Shakila Rahmawati tnuraini@example.com Super Admin 21 Sep 2026 Edit Hapus":
        - cell "Shakila Rahmawati"
        - cell "tnuraini@example.com"
        - cell "Super Admin"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Waluyo Maman Sitompul atmaja.tamba@example.org Super Admin 21 Sep 2026 Edit Hapus":
        - cell "Waluyo Maman Sitompul"
        - cell "atmaja.tamba@example.org"
        - cell "Super Admin"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Ganjaran Kamal Prasasta M.Pd budiman.raden@example.org Super Admin 21 Sep 2026 Edit Hapus":
        - cell "Ganjaran Kamal Prasasta M.Pd"
        - cell "budiman.raden@example.org"
        - cell "Super Admin"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Rahmat Maulana iswahyudi.aslijan@example.com 21 Sep 2026 Edit Hapus":
        - cell "Rahmat Maulana"
        - cell "iswahyudi.aslijan@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Emil Tarihoran S.T. irajata@example.net 21 Sep 2026 Edit Hapus":
        - cell "Emil Tarihoran S.T."
        - cell "irajata@example.net"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Luluh Respati Sitorus agnes15@example.com 21 Sep 2026 Edit Hapus":
        - cell "Luluh Respati Sitorus"
        - cell "agnes15@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Budi Habibi wahyuni.restu@example.com 21 Sep 2026 Edit Hapus":
        - cell "Budi Habibi"
        - cell "wahyuni.restu@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Lintang Halimah pia.hutasoit@example.com 21 Sep 2026 Edit Hapus":
        - cell "Lintang Halimah"
        - cell "pia.hutasoit@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Eman Habibi oni62@example.net 21 Sep 2026 Edit Hapus":
        - cell "Eman Habibi"
        - cell "oni62@example.net"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Ophelia Janet Palastri M.Pd eprastuti@example.com 21 Sep 2026 Edit Hapus":
        - cell "Ophelia Janet Palastri M.Pd"
        - cell "eprastuti@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Danang Prasetyo rosman.ardianto@example.net 21 Sep 2026 Edit Hapus":
        - cell "Danang Prasetyo"
        - cell "rosman.ardianto@example.net"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Ayu Kuswandari ira57@example.net 21 Sep 2026 Edit Hapus":
        - cell "Ayu Kuswandari"
        - cell "ira57@example.net"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Samiah Yuniar S.Pt pnajmudin@example.com 21 Sep 2026 Edit Hapus":
        - cell "Samiah Yuniar S.Pt"
        - cell "pnajmudin@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Lintang Hassanah cahyadi.suwarno@example.net 21 Sep 2026 Edit Hapus":
        - cell "Lintang Hassanah"
        - cell "cahyadi.suwarno@example.net"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Umaya Adriansyah galar94@example.net 21 Sep 2026 Edit Hapus":
        - cell "Umaya Adriansyah"
        - cell "galar94@example.net"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Pimpinan pimpinan@siakad.test Pimpinan 21 Sep 2026 Edit Hapus":
        - cell "Pimpinan"
        - cell "pimpinan@siakad.test"
        - cell "Pimpinan"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Mahasiswa mahasiswa@siakad.test Mahasiswa 21 Sep 2026 Edit Hapus":
        - cell "Mahasiswa"
        - cell "mahasiswa@siakad.test"
        - cell "Mahasiswa"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Dosen Pengampu dosen@siakad.test Dosen 21 Sep 2026 Edit Hapus":
        - cell "Dosen Pengampu"
        - cell "dosen@siakad.test"
        - cell "Dosen"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Kepala Program Studi kaprodi@siakad.test Kaprodi 21 Sep 2026 Edit Hapus":
        - cell "Kepala Program Studi"
        - cell "kaprodi@siakad.test"
        - cell "Kaprodi"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Super Admin admin@siakad.test Super Admin 21 Sep 2026 Edit Hapus":
        - cell "Super Admin"
        - cell "admin@siakad.test"
        - cell "Super Admin"
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Harsaya Nugroho nmustofa@example.com 21 Sep 2026 Edit Hapus":
        - cell "Harsaya Nugroho"
        - cell "nmustofa@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Ida Tari Widiastuti nurdiyanti.ifa@example.com 21 Sep 2026 Edit Hapus":
        - cell "Ida Tari Widiastuti"
        - cell "nurdiyanti.ifa@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Tantri Astuti osafitri@example.com 21 Sep 2026 Edit Hapus":
        - cell "Tantri Astuti"
        - cell "osafitri@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
      - row "Fathonah Oktaviani tania.budiman@example.com 21 Sep 2026 Edit Hapus":
        - cell "Fathonah Oktaviani"
        - cell "tania.budiman@example.com"
        - cell
        - cell "21 Sep 2026"
        - cell "Edit Hapus":
          - button "Edit"
          - button "Hapus"
- heading [level=3]
- button "✕"
- text: Nama *
- textbox "Nama lengkap": Dosen Baru E2E
- text: Email *
- textbox "email@example.com": dosenbaru@test.com
- text: Password *
- textbox "Minimal 8 karakter": "1234567890"
- text: Role *
- combobox:
  - option "Pilih…" [disabled]
  - option "Super Admin"
  - option "Kaprodi"
  - option "Dosen" [selected]
  - option "Mahasiswa"
  - option "Pimpinan"
- text: NIDN *
- textbox "Nomor Induk Dosen Nasional"
- text: Program Studi *
- combobox:
  - option "Pilih…" [disabled]
  - option "Ilmu Komputer" [selected]
  - option "Informatika"
  - option "Sistem Informasi"
  - option "Teknik Elektro"
  - option "Teknik Komputer"
  - option "Teknologi Informasi"
- text: Pangkat Akademik *
- combobox:
  - option "Pilih…" [disabled]
  - option "Asisten Ahli" [selected]
  - option "Lektor"
  - option "Lektor Kepala"
  - option "Guru Besar"
- button "Batal"
- button "Tambah Pengguna"
```

# Test source

```ts
  1   | import { test, expect } from '@playwright/test';
  2   | import { loginAs, waitForPage } from './helpers';
  3   | 
  4   | test.describe('Super Admin', () => {
  5   |     test.beforeEach(async ({ page }) => {
  6   |         await loginAs(page, 'admin@siakad.test', 'password');
  7   |     });
  8   | 
  9   |     test('dashboard tampil', async ({ page }) => {
  10  |         await expect(page).toHaveURL(/\/dashboard/);
  11  |         // Dashboard should have some content
  12  |         await expect(page.locator('h1, h2').first()).toBeVisible();
  13  |     });
  14  | 
  15  |     test('buat user dosen baru muncul di tabel', async ({ page }) => {
  16  |         await page.goto('/admin/users');
  17  |         await waitForPage(page);
  18  | 
  19  |         // Click Tambah Pengguna to open modal
  20  |         await page.click('button:has-text("Tambah Pengguna")');
  21  | 
  22  |         // Wait for modal to appear
  23  |         await page.waitForSelector('text=Tambah Pengguna', { timeout: 5000 });
  24  |         await page.waitForSelector('input', { timeout: 5000 });
  25  | 
  26  |         // Fill form
  27  |         const inputs = page.locator('input');
  28  |         await inputs.nth(0).fill('Dosen Baru E2E');
  29  | 
  30  |         // Find email input
  31  |         const emailInput = page.locator('input[type="email"]');
  32  |         await emailInput.fill('dosenbaru@test.com');
  33  | 
  34  |         // Find password inputs
  35  |         const passwordInputs = page.locator('input[type="password"]');
  36  |         await passwordInputs.first().fill('password');
  37  |         if (await passwordInputs.count() > 1) {
  38  |             await passwordInputs.nth(1).fill('password');
  39  |         }
  40  | 
  41  |         // Select role dosen
  42  |         const select = page.locator('select').first();
  43  |         await select.selectOption('dosen');
  44  | 
  45  |         // Fill NIDN (required for dosen)
  46  |         const nidnInput = page.locator('input').filter({ has: page.locator('[placeholder="Nomor Induk Dosen Nasional"]') });
  47  |         if (await nidnInput.count() > 0) {
  48  |             await nidnInput.fill('1234567890');
  49  |         } else {
  50  |             // Fallback: fill the 3rd input (after name, email)
  51  |             const allInputs = page.locator('input');
  52  |             const count = await allInputs.count();
  53  |             if (count > 2) await allInputs.nth(2).fill('1234567890');
  54  |         }
  55  | 
  56  |         // Select study program (required for dosen)
  57  |         const studyProgramSelect = page.locator('select').nth(1);
  58  |         if (await studyProgramSelect.count() > 0) {
  59  |             const options = await studyProgramSelect.locator('option').all();
  60  |             if (options.length > 1) {
  61  |                 await studyProgramSelect.selectOption({ index: 1 });
  62  |             }
  63  |         }
  64  | 
  65  |         // Select academic rank (required for dosen)
  66  |         const rankSelect = page.locator('select').nth(2);
  67  |         if (await rankSelect.count() > 0) {
  68  |             const options = await rankSelect.locator('option').all();
  69  |             if (options.length > 1) {
  70  |                 await rankSelect.selectOption({ index: 1 });
  71  |             }
  72  |         }
  73  | 
  74  |         // Submit
  75  |         await page.click('button[type="submit"]');
  76  |         await waitForPage(page);
  77  | 
  78  |         // Verify user appears in table
> 79  |         await expect(page.locator('text=dosenbaru@test.com')).toBeVisible({ timeout: 10000 });
      |                                                               ^ Error: expect(locator).toBeVisible() failed
  80  |     });
  81  | 
  82  |     test('buat fakultas baru muncul', async ({ page }) => {
  83  |         await page.goto('/admin/faculties');
  84  |         await waitForPage(page);
  85  | 
  86  |         // Click Tambah Fakultas to open modal
  87  |         await page.click('button:has-text("Tambah Fakultas")');
  88  | 
  89  |         // Wait for modal
  90  |         await page.waitForSelector('text=Tambah Fakultas', { timeout: 5000 });
  91  |         await page.waitForSelector('input', { timeout: 5000 });
  92  | 
  93  |         // Fill form
  94  |         const inputs = page.locator('input');
  95  |         await inputs.nth(0).fill('FIK2');
  96  |         await inputs.nth(1).fill('Fakultas E2E');
  97  |         await page.click('button:has-text("Tambah")', { force: true });
  98  |         await waitForPage(page);
  99  | 
  100 |         await expect(page.locator('text=Fakultas E2E')).toBeVisible({ timeout: 10000 });
  101 |     });
  102 | 
  103 |     test('buat program studi baru muncul', async ({ page }) => {
  104 |         await page.goto('/admin/study-programs');
  105 |         await waitForPage(page);
  106 | 
  107 |         // Click Tambah Prodi to open modal
  108 |         await page.click('button:has-text("Tambah Prodi")');
  109 | 
  110 |         // Wait for modal
  111 |         await page.waitForSelector('text=Tambah Program Studi', { timeout: 5000 });
  112 |         await page.waitForSelector('input', { timeout: 5000 });
  113 | 
  114 |         // Fill form
  115 |         const inputs = page.locator('input');
  116 |         await inputs.nth(0).fill('TE');
  117 |         await inputs.nth(1).fill('Teknik E2E');
  118 | 
  119 |         // Select faculty
  120 |         const selects = page.locator('select');
  121 |         await selects.first().selectOption({ index: 1 });
  122 | 
  123 |         // Select degree
  124 |         if (await selects.count() > 1) {
  125 |             await selects.nth(1).selectOption({ index: 1 });
  126 |         }
  127 | 
  128 |         await page.click('button:has-text("Tambah")', { force: true });
  129 |         await waitForPage(page);
  130 | 
  131 |         await expect(page.locator('text=Teknik E2E')).toBeVisible({ timeout: 10000 });
  132 |     });
  133 | 
  134 |     test('buat mata kuliah baru muncul', async ({ page }) => {
  135 |         await page.goto('/admin/courses');
  136 |         await waitForPage(page);
  137 | 
  138 |         // Click Tambah Mata Kuliah to open modal
  139 |         await page.click('button:has-text("Tambah Mata Kuliah")');
  140 | 
  141 |         // Wait for modal
  142 |         await page.waitForSelector('text=Tambah Mata Kuliah', { timeout: 5000 });
  143 |         await page.waitForSelector('input', { timeout: 5000 });
  144 | 
  145 |         // Fill form
  146 |         const inputs = page.locator('input');
  147 |         await inputs.nth(0).fill('E2E101');
  148 |         await inputs.nth(1).fill('Mata Kuliah E2E');
  149 | 
  150 |         // SKS
  151 |         const numberInputs = page.locator('input[type="number"]');
  152 |         await numberInputs.first().fill('3');
  153 | 
  154 |         // Select study program
  155 |         const selects = page.locator('select');
  156 |         await selects.first().selectOption({ index: 1 });
  157 | 
  158 |         // Select semester
  159 |         if (await selects.count() > 1) {
  160 |             await selects.nth(1).selectOption({ index: 1 });
  161 |         }
  162 | 
  163 |         // Select type
  164 |         if (await selects.count() > 2) {
  165 |             await selects.nth(2).selectOption('Wajib');
  166 |         }
  167 | 
  168 |         await page.click('button:has-text("Tambah")', { force: true });
  169 |         await waitForPage(page);
  170 | 
  171 |         await expect(page.locator('text=Mata Kuliah E2E')).toBeVisible({ timeout: 10000 });
  172 |     });
  173 | 
  174 |     test('buat ruangan baru muncul', async ({ page }) => {
  175 |         await page.goto('/admin/classrooms');
  176 |         await waitForPage(page);
  177 | 
  178 |         // Click Tambah Ruangan to open modal
  179 |         await page.click('button:has-text("Tambah Ruangan")');
```