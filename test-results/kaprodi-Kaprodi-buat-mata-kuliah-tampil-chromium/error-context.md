# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: kaprodi.spec.ts >> Kaprodi >> buat mata kuliah tampil
- Location: tests/e2e/kaprodi.spec.ts:22:5

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('text=Mata Kuliah Kaprodi E2E')
Expected: visible
Timeout: 10000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" locator('text=Mata Kuliah Kaprodi E2E') with timeout 10000ms
  - waiting for locator('text=Mata Kuliah Kaprodi E2E')

```

```yaml
- complementary:
  - text: S SIAKAD
  - navigation:
    - link "🏠 Dashboard":
      - /url: /kaprodi/dashboard
    - link "📚 Mata Kuliah":
      - /url: /kaprodi/courses
    - link "🗓️ Kelas & Jadwal":
      - /url: /kaprodi/offerings
    - link "📊 Monitoring Nilai":
      - /url: /kaprodi/grades
    - link "🎓 Skripsi & KP":
      - /url: /kaprodi/skripsi-kp
    - link "🕐 Kehadiran Dosen":
      - /url: /kaprodi/kehadiran
    - link "📢 Pengumuman":
      - /url: /kaprodi/announcements
  - text: v0.1 · SIAKAD
- banner:
  - button:
    - img
  - navigation: SIAKAD / Mata Kuliah
  - button "Mode gelap":
    - img
  - button "Notifikasi":
    - img
  - button "KP Kepala Program Studi kaprodi"
- main:
  - heading "Mata Kuliah" [level=1]
  - paragraph: Kelola mata kuliah program studi Anda.
  - heading "Daftar Mata Kuliah" [level=2]
  - paragraph: Mata kuliah yang tersedia di program studi Anda.
  - button "➕ Tambah Mata Kuliah"
  - table:
    - rowgroup:
      - row "Kode Nama SKS Semester Tipe Aksi":
        - columnheader "Kode"
        - columnheader "Nama"
        - columnheader "SKS"
        - columnheader "Semester"
        - columnheader "Tipe"
        - columnheader "Aksi"
    - rowgroup:
      - row "IF101 Pemrograman Dasar 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IF101"
        - cell "Pemrograman Dasar"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IF102 Algoritma & Struktur Data 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IF102"
        - cell "Algoritma & Struktur Data"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IF201 Basis Data 3 2 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IF201"
        - cell "Basis Data"
        - cell "3"
        - cell "2"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IF301 Kecerdasan Buatan 3 3 Pilihan ✏️ Edit 🗑️ Hapus":
        - cell "IF301"
        - cell "Kecerdasan Buatan"
        - cell "3"
        - cell "3"
        - cell "Pilihan"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | import { loginAs, waitForPage } from './helpers';
  3  | 
  4  | test.describe('Kaprodi', () => {
  5  |     test.beforeEach(async ({ page }) => {
  6  |         await loginAs(page, 'kaprodi@siakad.test', 'password');
  7  |     });
  8  | 
  9  |     test('sidebar tidak menampilkan menu super-admin', async ({ page }) => {
  10 |         // Navigate to kaprodi dashboard
  11 |         await page.goto('/kaprodi/dashboard');
  12 |         await waitForPage(page);
  13 | 
  14 |         // Super-admin specific menus should NOT be visible
  15 |         // Use specific selectors to avoid ambiguity
  16 |         await expect(page.locator('a:has-text("Pengguna"), button:has-text("Pengguna")')).not.toBeVisible();
  17 |         await expect(page.locator('text=Roles & Permissions')).not.toBeVisible();
  18 |         await expect(page.locator('a:has-text("Fakultas")')).not.toBeVisible();
  19 |         await expect(page.locator('a:has-text("Program Studi")')).not.toBeVisible();
  20 |     });
  21 | 
  22 |     test('buat mata kuliah tampil', async ({ page }) => {
  23 |         await page.goto('/kaprodi/courses');
  24 |         await waitForPage(page);
  25 | 
  26 |         // Click tambah button to open modal
  27 |         await page.click('button:has-text("Tambah Mata Kuliah")');
  28 | 
  29 |         // Wait for modal to appear
  30 |         await page.waitForSelector('text=Tambah Mata Kuliah', { timeout: 5000 });
  31 |         await page.waitForSelector('input', { timeout: 5000 });
  32 | 
  33 |         // Fill form
  34 |         const inputs = page.locator('input');
  35 |         await inputs.nth(0).fill('IF999');
  36 |         await inputs.nth(1).fill('Mata Kuliah Kaprodi E2E');
  37 | 
  38 |         // Fill SKS
  39 |         const numberInput = page.locator('input[type="number"]').first();
  40 |         await numberInput.fill('3');
  41 | 
  42 |         // Select semester
  43 |         const selects = page.locator('select');
  44 |         await selects.first().selectOption({ index: 1 });
  45 | 
  46 |         // Select type
  47 |         if (await selects.count() > 1) {
  48 |             await selects.nth(1).selectOption('wajib');
  49 |         }
  50 | 
  51 |         await page.click('button:has-text("Tambah")', { force: true });
  52 |         await waitForPage(page);
  53 | 
> 54 |         await expect(page.locator('text=Mata Kuliah Kaprodi E2E')).toBeVisible({ timeout: 10000 });
     |                                                                    ^ Error: expect(locator).toBeVisible() failed
  55 |     });
  56 | 
  57 |     test('monitoring nilai tampil', async ({ page }) => {
  58 |         await page.goto('/kaprodi/grades');
  59 |         await waitForPage(page);
  60 | 
  61 |         // Should show grade monitoring page
  62 |         await expect(page.locator('h1, h2').first()).toBeVisible();
  63 |     });
  64 | 
  65 |     test('akses /admin/users ditolak 403', async ({ page }) => {
  66 |         await page.goto('/admin/users');
  67 |         await waitForPage(page);
  68 | 
  69 |         // Should get 403 or redirect
  70 |         const currentUrl = page.url();
  71 |         // Either we're on a 403 page or redirected
  72 |         const isForbidden = await page.locator('text=403').isVisible().catch(() => false);
  73 |         const isRedirected = !currentUrl.includes('/admin/users');
  74 |         expect(isForbidden || isRedirected).toBeTruthy();
  75 |     });
  76 | });
  77 | 
```