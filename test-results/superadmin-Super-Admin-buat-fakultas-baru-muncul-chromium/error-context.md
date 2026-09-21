# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: superadmin.spec.ts >> Super Admin >> buat fakultas baru muncul
- Location: tests/e2e/superadmin.spec.ts:82:5

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('text=Fakultas E2E')
Expected: visible
Timeout: 10000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" locator('text=Fakultas E2E') with timeout 10000ms
  - waiting for locator('text=Fakultas E2E')

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
  - navigation: SIAKAD / Master Data / Fakultas
  - button "Mode gelap":
    - img
  - button "Notifikasi":
    - img
  - button "SA Super Admin super-admin"
- main:
  - heading "Fakultas" [level=1]
  - paragraph: Kelola data fakultas.
  - heading "Daftar Fakultas" [level=2]
  - paragraph: Semua fakultas yang terdaftar.
  - button "➕ Tambah Fakultas"
  - table:
    - rowgroup:
      - row "Kode Nama Aksi":
        - columnheader "Kode"
        - columnheader "Nama"
        - columnheader "Aksi"
    - rowgroup:
      - row "FIK Fakultas Ilmu Komputer ✏️ Edit 🗑️ Hapus":
        - cell "FIK"
        - cell "Fakultas Ilmu Komputer"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "FT Fakultas Teknik ✏️ Edit 🗑️ Hapus":
        - cell "FT"
        - cell "Fakultas Teknik"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
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
  79  |         await expect(page.locator('text=dosenbaru@test.com')).toBeVisible({ timeout: 10000 });
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
> 100 |         await expect(page.locator('text=Fakultas E2E')).toBeVisible({ timeout: 10000 });
      |                                                         ^ Error: expect(locator).toBeVisible() failed
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
  180 | 
  181 |         // Wait for modal
  182 |         await page.waitForSelector('text=Tambah Ruangan', { timeout: 5000 });
  183 |         await page.waitForSelector('input', { timeout: 5000 });
  184 | 
  185 |         // Fill form
  186 |         const inputs = page.locator('input');
  187 |         await inputs.nth(0).fill('R-E2E');
  188 |         await inputs.nth(1).fill('Ruangan E2E');
  189 | 
  190 |         // Capacity
  191 |         const numberInput = page.locator('input[type="number"]').first();
  192 |         await numberInput.fill('40');
  193 | 
  194 |         await page.click('button:has-text("Tambah")', { force: true });
  195 |         await waitForPage(page);
  196 | 
  197 |         await expect(page.locator('text=Ruangan E2E')).toBeVisible({ timeout: 10000 });
  198 |     });
  199 | 
  200 |     test('buat periode akademik muncul', async ({ page }) => {
```