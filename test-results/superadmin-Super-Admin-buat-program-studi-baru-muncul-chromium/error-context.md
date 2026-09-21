# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: superadmin.spec.ts >> Super Admin >> buat program studi baru muncul
- Location: tests/e2e/superadmin.spec.ts:103:5

# Error details

```
TimeoutError: page.waitForSelector: Timeout 5000ms exceeded.
Call log:
  - waiting for locator('text=Tambah Program Studi') to be visible

```

# Page snapshot

```yaml
- generic [ref=f1e1]:
  - generic [ref=f1e3]:
    - complementary [ref=f1e4]:
      - generic [ref=f1e5]:
        - generic [ref=f1e6]: S
        - generic [ref=f1e7]: SIAKAD
      - navigation [ref=f1e8]:
        - link "🏠 Dashboard" [ref=f1e9] [cursor=pointer]:
          - /url: /admin/dashboard
          - generic [ref=f1e10]: 🏠
          - generic [ref=f1e11]: Dashboard
        - button "👥 Pengguna" [ref=f1e13]:
          - generic [ref=f1e14]: 👥
          - generic [ref=f1e15]: Pengguna
        - generic [ref=f1e18]:
          - button "🗂️ Master Data" [ref=f1e19]:
            - generic [ref=f1e20]: 🗂️
            - generic [ref=f1e21]: Master Data
          - generic [ref=f1e24]:
            - link "Fakultas" [ref=f1e25] [cursor=pointer]:
              - /url: /admin/faculties
            - link "Program Studi" [ref=f1e26] [cursor=pointer]:
              - /url: /admin/study-programs
            - link "Mata Kuliah" [ref=f1e27] [cursor=pointer]:
              - /url: /admin/courses
            - link "Ruangan" [ref=f1e28] [cursor=pointer]:
              - /url: /admin/classrooms
            - link "Kelas & Jadwal" [ref=f1e29] [cursor=pointer]:
              - /url: /admin/course-offerings
            - link "Periode Akademik" [ref=f1e30] [cursor=pointer]:
              - /url: /admin/periods
        - link "📋 KRS (Monitoring)" [ref=f1e31] [cursor=pointer]:
          - /url: /admin/krs-approval
          - generic [ref=f1e32]: 📋
          - generic [ref=f1e33]: KRS (Monitoring)
        - link "🎓 Skripsi & KP" [ref=f1e34] [cursor=pointer]:
          - /url: /admin/skripsi-kp
          - generic [ref=f1e35]: 🎓
          - generic [ref=f1e36]: Skripsi & KP
        - link "🕐 Kehadiran Dosen" [ref=f1e37] [cursor=pointer]:
          - /url: /admin/kehadiran-dosen
          - generic [ref=f1e38]: 🕐
          - generic [ref=f1e39]: Kehadiran Dosen
        - link "📢 Pengumuman" [ref=f1e40] [cursor=pointer]:
          - /url: /admin/announcements
          - generic [ref=f1e41]: 📢
          - generic [ref=f1e42]: Pengumuman
      - generic [ref=f1e43]: v0.1 · SIAKAD
    - generic [ref=f1e44]:
      - banner [ref=f1e45]:
        - generic [ref=f1e46]:
          - button [ref=f1e47]
          - navigation [ref=f1e50]:
            - generic [ref=f1e51]: SIAKAD
            - generic [ref=f1e52]: /
            - generic [ref=f1e53]: Master Data
            - generic [ref=f1e54]: /
            - generic [ref=f1e55]: Program Studi
        - generic [ref=f1e56]:
          - button "Mode gelap" [ref=f1e57]
          - button "Notifikasi" [ref=f1e62]
          - button "SA Super Admin super-admin" [ref=f1e67]:
            - generic [ref=f1e68]: SA
            - generic [ref=f1e69]:
              - generic [ref=f1e70]: Super Admin
              - generic [ref=f1e71]: super-admin
      - main [ref=f1e72]:
        - generic [ref=f1e75]:
          - heading "Program Studi" [level=1] [ref=f1e76]
          - paragraph [ref=f1e77]: Kelola data program studi.
        - generic [ref=f1e78]:
          - generic [ref=f1e80]:
            - generic [ref=f1e81]:
              - heading "Daftar Program Studi" [level=2] [ref=f1e82]
              - paragraph [ref=f1e83]: Semua program studi yang terdaftar.
            - button "➕ Tambah Prodi" [active] [ref=f1e84]
          - table [ref=f1e87]:
            - rowgroup [ref=f1e88]:
              - row [ref=f1e89]:
                - columnheader "Kode" [ref=f1e90]
                - columnheader "Nama" [ref=f1e91]
                - columnheader "Fakultas" [ref=f1e92]
                - columnheader "Jenjang" [ref=f1e93]
                - columnheader "Aksi" [ref=f1e94]
            - rowgroup [ref=f1e95]:
              - row [ref=f1e96]:
                - cell "IF" [ref=f1e97]
                - cell "Informatika" [ref=f1e98]
                - cell "Fakultas Teknik" [ref=f1e99]
                - cell "S1" [ref=f1e100]
                - cell [ref=f1e102]:
                  - generic [ref=f1e103]:
                    - button "✏️ Edit" [ref=f1e104]
                    - button "🗑️ Hapus" [ref=f1e105]
              - row [ref=f1e106]:
                - cell "IK" [ref=f1e107]
                - cell "Ilmu Komputer" [ref=f1e108]
                - cell "Fakultas Ilmu Komputer" [ref=f1e109]
                - cell "S1" [ref=f1e110]
                - cell [ref=f1e112]:
                  - generic [ref=f1e113]:
                    - button "✏️ Edit" [ref=f1e114]
                    - button "🗑️ Hapus" [ref=f1e115]
              - row [ref=f1e116]:
                - cell "SI" [ref=f1e117]
                - cell "Sistem Informasi" [ref=f1e118]
                - cell "Fakultas Ilmu Komputer" [ref=f1e119]
                - cell "S1" [ref=f1e120]
                - cell [ref=f1e122]:
                  - generic [ref=f1e123]:
                    - button "✏️ Edit" [ref=f1e124]
                    - button "🗑️ Hapus" [ref=f1e125]
              - row [ref=f1e126]:
                - cell "TE" [ref=f1e127]
                - cell "Teknik Elektro" [ref=f1e128]
                - cell "Fakultas Teknik" [ref=f1e129]
                - cell "S1" [ref=f1e130]
                - cell [ref=f1e132]:
                  - generic [ref=f1e133]:
                    - button "✏️ Edit" [ref=f1e134]
                    - button "🗑️ Hapus" [ref=f1e135]
              - row [ref=f1e136]:
                - cell "TI" [ref=f1e137]
                - cell "Teknologi Informasi" [ref=f1e138]
                - cell "Fakultas Ilmu Komputer" [ref=f1e139]
                - cell "S1" [ref=f1e140]
                - cell [ref=f1e142]:
                  - generic [ref=f1e143]:
                    - button "✏️ Edit" [ref=f1e144]
                    - button "🗑️ Hapus" [ref=f1e145]
              - row [ref=f1e146]:
                - cell "TK" [ref=f1e147]
                - cell "Teknik Komputer" [ref=f1e148]
                - cell "Fakultas Teknik" [ref=f1e149]
                - cell "S1" [ref=f1e150]
                - cell [ref=f1e152]:
                  - generic [ref=f1e153]:
                    - button "✏️ Edit" [ref=f1e154]
                    - button "🗑️ Hapus" [ref=f1e155]
  - generic [ref=f1e158]:
    - generic [ref=f1e159]:
      - heading [level=3]
      - button "✕" [ref=f1e160]
    - generic [ref=f1e162]:
      - generic [ref=f1e163]:
        - generic [ref=f1e164]: Fakultas *
        - combobox [ref=f1e165]:
          - option "Pilih…" [disabled] [selected]
          - option "Fakultas Ilmu Komputer"
          - option "Fakultas Teknik"
      - generic [ref=f1e166]:
        - generic [ref=f1e167]: Kode Prodi *
        - 'textbox "Contoh: IF" [ref=f1e168]'
      - generic [ref=f1e169]:
        - generic [ref=f1e170]: Nama Prodi *
        - 'textbox "Contoh: Informatika" [ref=f1e171]'
      - generic [ref=f1e172]:
        - generic [ref=f1e173]: Jenjang *
        - combobox [ref=f1e174]:
          - option "Pilih…" [disabled]
          - option "D3"
          - option "D4"
          - option "S1" [selected]
          - option "S2"
          - option "S3"
          - option "Profesi"
    - generic [ref=f1e176]:
      - button "Batal" [ref=f1e177]
      - button "Tambah" [ref=f1e178]
```

# Test source

```ts
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
> 111 |         await page.waitForSelector('text=Tambah Program Studi', { timeout: 5000 });
      |                    ^ TimeoutError: page.waitForSelector: Timeout 5000ms exceeded.
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
```