# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: superadmin.spec.ts >> Super Admin >> buat periode akademik muncul
- Location: tests/e2e/superadmin.spec.ts:200:5

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('text=2026/2027')
Expected: visible
Timeout: 10000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" locator('text=2026/2027') with timeout 10000ms
  - waiting for locator('text=2026/2027')

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
  - navigation: SIAKAD / Master Data / Periode Akademik
  - button "Mode gelap":
    - img
  - button "Notifikasi":
    - img
  - button "SA Super Admin super-admin"
- main:
  - heading "Periode Akademik" [level=1]
  - paragraph: Kelola tahun ajaran dan semester.
  - heading "2025 — 2025/2026" [level=2]
  - text: Aktif
  - button "✏️ Edit TA"
  - button "🗑️ Hapus TA"
  - button "➕ Tambah Semester"
  - text: 2025-08-01 — 2026-07-31
  - table:
    - rowgroup:
      - row "Semester Mulai Selesai Aktif Aksi":
        - columnheader "Semester"
        - columnheader "Mulai"
        - columnheader "Selesai"
        - columnheader "Aktif"
        - columnheader "Aksi"
    - rowgroup:
      - row "Ganjil 2025-08-01 2025-12-31 Aktif ✏️ Edit 🗑️ Hapus":
        - cell "Ganjil"
        - cell "2025-08-01"
        - cell "2025-12-31"
        - cell "Aktif"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "Genap 2026-01-01 2026-07-31 Tidak ✏️ Edit 🗑️ Hapus":
        - cell "Genap"
        - cell "2026-01-01"
        - cell "2026-07-31"
        - cell "Tidak"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
  - button "➕ Tambah Tahun Ajaran"
```

# Test source

```ts
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
  201 |         await page.goto('/admin/periods');
  202 |         await waitForPage(page);
  203 | 
  204 |         // Click Tambah Tahun Ajaran to open modal
  205 |         await page.click('button:has-text("Tambah Tahun Ajaran")');
  206 | 
  207 |         // Wait for modal
  208 |         await page.waitForSelector('text=Tambah Tahun Ajaran', { timeout: 5000 });
  209 |         await page.waitForSelector('input', { timeout: 5000 });
  210 | 
  211 |         // Fill form
  212 |         const inputs = page.locator('input');
  213 |         await inputs.nth(0).fill('2026/2027');
  214 |         await inputs.nth(1).fill('Tahun Akademik 2026/2027');
  215 | 
  216 |         // Date inputs
  217 |         const dateInputs = page.locator('input[type="date"]');
  218 |         if (await dateInputs.count() > 0) {
  219 |             await dateInputs.nth(0).fill('2026-09-01');
  220 |         }
  221 |         if (await dateInputs.count() > 1) {
  222 |             await dateInputs.nth(1).fill('2027-08-31');
  223 |         }
  224 | 
  225 |         await page.click('button:has-text("Tambah")', { force: true });
  226 |         await waitForPage(page);
  227 | 
> 228 |         await expect(page.locator('text=2026/2027')).toBeVisible({ timeout: 10000 });
      |                                                      ^ Error: expect(locator).toBeVisible() failed
  229 |     });
  230 | 
  231 |     test('manage role permissions tampil', async ({ page }) => {
  232 |         await page.goto('/admin/roles');
  233 |         await waitForPage(page);
  234 | 
  235 |         // Should show roles page
  236 |         await expect(page.getByRole('heading', { name: 'Roles & Permissions' })).toBeVisible({ timeout: 10000 });
  237 |     });
  238 | });
  239 | 
```