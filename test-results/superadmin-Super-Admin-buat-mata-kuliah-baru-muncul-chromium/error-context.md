# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: superadmin.spec.ts >> Super Admin >> buat mata kuliah baru muncul
- Location: tests/e2e/superadmin.spec.ts:134:5

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('text=Mata Kuliah E2E')
Expected: visible
Timeout: 10000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" locator('text=Mata Kuliah E2E') with timeout 10000ms
  - waiting for locator('text=Mata Kuliah E2E')

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
  - navigation: SIAKAD / Master Data / Mata Kuliah
  - button "Mode gelap":
    - img
  - button "Notifikasi":
    - img
  - button "SA Super Admin super-admin"
- main:
  - heading "Mata Kuliah" [level=1]
  - paragraph: Kelola semua mata kuliah di seluruh program studi.
  - heading "Daftar Mata Kuliah" [level=2]
  - paragraph: Semua mata kuliah yang terdaftar.
  - button "➕ Tambah Mata Kuliah"
  - table:
    - rowgroup:
      - row "Kode Nama Prodi SKS Semester Tipe Aksi":
        - columnheader "Kode"
        - columnheader "Nama"
        - columnheader "Prodi"
        - columnheader "SKS"
        - columnheader "Semester"
        - columnheader "Tipe"
        - columnheader "Aksi"
    - rowgroup:
      - row "IF101 Pemrograman Dasar Informatika 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IF101"
        - cell "Pemrograman Dasar"
        - cell "Informatika"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IF102 Algoritma & Struktur Data Informatika 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IF102"
        - cell "Algoritma & Struktur Data"
        - cell "Informatika"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IF201 Basis Data Informatika 3 2 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IF201"
        - cell "Basis Data"
        - cell "Informatika"
        - cell "3"
        - cell "2"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IF301 Kecerdasan Buatan Informatika 3 3 Pilihan ✏️ Edit 🗑️ Hapus":
        - cell "IF301"
        - cell "Kecerdasan Buatan"
        - cell "Informatika"
        - cell "3"
        - cell "3"
        - cell "Pilihan"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TK101 Elektronika Dasar Teknik Komputer 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "TK101"
        - cell "Elektronika Dasar"
        - cell "Teknik Komputer"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TK201 Mikroprosesor Teknik Komputer 3 2 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "TK201"
        - cell "Mikroprosesor"
        - cell "Teknik Komputer"
        - cell "3"
        - cell "2"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TK301 Sistem Embedded Teknik Komputer 3 3 Pilihan ✏️ Edit 🗑️ Hapus":
        - cell "TK301"
        - cell "Sistem Embedded"
        - cell "Teknik Komputer"
        - cell "3"
        - cell "3"
        - cell "Pilihan"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TE101 Rangkaian Listrik Teknik Elektro 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "TE101"
        - cell "Rangkaian Listrik"
        - cell "Teknik Elektro"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TE201 Elektronika Teknik Elektro 3 2 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "TE201"
        - cell "Elektronika"
        - cell "Teknik Elektro"
        - cell "3"
        - cell "2"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TE301 Sistem Kendali Teknik Elektro 3 3 Pilihan ✏️ Edit 🗑️ Hapus":
        - cell "TE301"
        - cell "Sistem Kendali"
        - cell "Teknik Elektro"
        - cell "3"
        - cell "3"
        - cell "Pilihan"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "SI101 Pengantar Sistem Informasi Sistem Informasi 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "SI101"
        - cell "Pengantar Sistem Informasi"
        - cell "Sistem Informasi"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "SI201 Analisis & Perancangan SI Sistem Informasi 3 2 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "SI201"
        - cell "Analisis & Perancangan SI"
        - cell "Sistem Informasi"
        - cell "3"
        - cell "2"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "SI301 Manajemen Proyek TI Sistem Informasi 3 3 Pilihan ✏️ Edit 🗑️ Hapus":
        - cell "SI301"
        - cell "Manajemen Proyek TI"
        - cell "Sistem Informasi"
        - cell "3"
        - cell "3"
        - cell "Pilihan"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IK101 Matematika Diskrit Ilmu Komputer 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IK101"
        - cell "Matematika Diskrit"
        - cell "Ilmu Komputer"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IK201 Sistem Operasi Ilmu Komputer 3 2 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "IK201"
        - cell "Sistem Operasi"
        - cell "Ilmu Komputer"
        - cell "3"
        - cell "2"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "IK301 Jaringan Komputer Ilmu Komputer 3 3 Pilihan ✏️ Edit 🗑️ Hapus":
        - cell "IK301"
        - cell "Jaringan Komputer"
        - cell "Ilmu Komputer"
        - cell "3"
        - cell "3"
        - cell "Pilihan"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TI101 Pengantar Teknologi Informasi Teknologi Informasi 3 1 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "TI101"
        - cell "Pengantar Teknologi Informasi"
        - cell "Teknologi Informasi"
        - cell "3"
        - cell "1"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TI201 Manajemen Data Teknologi Informasi 3 2 Wajib ✏️ Edit 🗑️ Hapus":
        - cell "TI201"
        - cell "Manajemen Data"
        - cell "Teknologi Informasi"
        - cell "3"
        - cell "2"
        - cell "Wajib"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
      - row "TI301 Keamanan Informasi Teknologi Informasi 3 3 Pilihan ✏️ Edit 🗑️ Hapus":
        - cell "TI301"
        - cell "Keamanan Informasi"
        - cell "Teknologi Informasi"
        - cell "3"
        - cell "3"
        - cell "Pilihan"
        - cell "✏️ Edit 🗑️ Hapus":
          - button "✏️ Edit"
          - button "🗑️ Hapus"
```

# Test source

```ts
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
> 171 |         await expect(page.locator('text=Mata Kuliah E2E')).toBeVisible({ timeout: 10000 });
      |                                                            ^ Error: expect(locator).toBeVisible() failed
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
  228 |         await expect(page.locator('text=2026/2027')).toBeVisible({ timeout: 10000 });
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