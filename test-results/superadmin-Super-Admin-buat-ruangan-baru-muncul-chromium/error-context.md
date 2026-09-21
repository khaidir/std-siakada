# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: superadmin.spec.ts >> Super Admin >> buat ruangan baru muncul
- Location: tests/e2e/superadmin.spec.ts:174:5

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: page.click: Test timeout of 30000ms exceeded.
Call log:
  - waiting for locator('button:has-text("Tambah Ruangan")')

```

# Page snapshot

```yaml
- generic [ref=f1e2]:
  - generic [ref=f1e4]:
    - generic [ref=f1e5]: Internal Server Error
    - button "Copy as Markdown" [ref=f1e11] [cursor=pointer]
  - generic [ref=f1e18]:
    - generic [ref=f1e19]:
      - heading "Illuminate\\Contracts\\Container\\BindingResolutionException" [level=1] [ref=f1e20]
      - generic [ref=f1e21]: vendor/laravel/framework/src/Illuminate/Container/Container.php:1149
      - paragraph [ref=f1e23]: Target class [App\Repositories\Eloquent\ClassroomRepository] does not exist.
    - generic [ref=f1e24]:
      - generic [ref=f1e25]:
        - generic [ref=f1e26]:
          - generic [ref=f1e27]: LARAVEL
          - generic [ref=f1e28]: 13.32.0
        - generic [ref=f1e29]:
          - generic [ref=f1e30]: PHP
          - generic [ref=f1e31]: 8.4.25
      - generic [ref=f1e32]: UNHANDLED
      - generic [ref=f1e36]: CODE 0
    - generic [ref=f1e38]:
      - generic [ref=f1e39]: "500"
      - generic [ref=f1e43]: GET
      - generic [ref=f1e47]: http://localhost:8000/admin/classrooms
      - button [ref=f1e48] [cursor=pointer]
  - generic [ref=f1e53]:
    - generic [ref=f1e54]:
      - generic [ref=f1e55]:
        - heading "Exception trace" [level=3] [ref=f1e60]
        - link "1 previous exception" [ref=f1e61] [cursor=pointer]:
          - /url: "#previous-exceptions"
      - generic [ref=f1e62]:
        - generic [ref=f1e64] [cursor=pointer]:
          - generic [ref=f1e69]: 80 vendor frames
          - button [ref=f1e70]
        - generic [ref=f1e75]:
          - generic [ref=f1e76] [cursor=pointer]:
            - generic [ref=f1e79]:
              - code [ref=f1e83]:
                - generic [ref=f1e84]: Illuminate\Foundation\Application->handleRequest(object(Illuminate\Http\Request))
              - generic [ref=f1e85]: public/index.php:20
            - button [ref=f1e88]
          - code [ref=f1e97]:
            - generic [ref=f1e98]: "15"
            - generic [ref=f1e99]: 16// Bootstrap Laravel and handle the request...
            - generic [ref=f1e100]: 17/** @var Application $app */
            - generic [ref=f1e101]: 18$app = require_once __DIR__.'/../bootstrap/app.php';
            - generic [ref=f1e102]: "19"
            - generic [ref=f1e103]: 20$app->handleRequest(Request::capture());
            - generic [ref=f1e104]: "21"
        - generic [ref=f1e106] [cursor=pointer]:
          - generic [ref=f1e111]: 1 vendor frame
          - button [ref=f1e112]
    - generic [ref=f1e117]:
      - heading "Previous exception" [level=3] [ref=f1e123]
      - generic [ref=f1e127] [cursor=pointer]:
        - generic [ref=f1e128]:
          - heading "ReflectionException" [level=4] [ref=f1e129]
          - paragraph [ref=f1e130]: Class "App\Repositories\Eloquent\ClassroomRepository" does not exist
        - button [ref=f1e131]
    - generic [ref=f1e136]:
      - generic [ref=f1e137]:
        - heading "Queries" [level=3] [ref=f1e142]
        - generic [ref=f1e143]: 1-5 of 5
      - generic [ref=f1e145]:
        - generic [ref=f1e146]:
          - generic [ref=f1e147]:
            - generic [ref=f1e148]: mysql
            - code [ref=f1e155]:
              - generic [ref=f1e156]: "select * from `sessions` where `id` = 'jjvsBby6JhZSa2ltqpUZQEDeMEPtsOLKBGaHsbiY' limit 1"
          - generic [ref=f1e157]: 0.95ms
        - generic [ref=f1e158]:
          - generic [ref=f1e159]:
            - generic [ref=f1e160]: mysql
            - code [ref=f1e167]:
              - generic [ref=f1e168]: "select * from `users` where `id` = 5 limit 1"
          - generic [ref=f1e169]: 0.19ms
        - generic [ref=f1e170]:
          - generic [ref=f1e171]:
            - generic [ref=f1e172]: mysql
            - code [ref=f1e179]:
              - generic [ref=f1e180]: "select `roles`.*, `model_has_roles`.`model_id` as `pivot_model_id`, `model_has_roles`.`role_id` as `pivot_role_id`, `model_has_roles`.`model_type` as `pivot_model_type` from `roles` inner join `model_has_roles` on `roles`.`id` = `model_has_roles`.`role_id` where `model_has_roles`.`model_id` in (5) and `model_has_roles`.`model_type` = 'App\\Models\\User'"
          - generic [ref=f1e181]: 0.2ms
        - generic [ref=f1e182]:
          - generic [ref=f1e183]:
            - generic [ref=f1e184]: mysql
            - code [ref=f1e191]:
              - generic [ref=f1e192]: "select * from `cache` where `key` in ('siakad-cache-spatie.permission.cache')"
          - generic [ref=f1e193]: 0.13ms
        - generic [ref=f1e194]:
          - generic [ref=f1e195]:
            - generic [ref=f1e196]: mysql
            - code [ref=f1e203]:
              - generic [ref=f1e204]: "select `permissions`.*, `model_has_permissions`.`model_id` as `pivot_model_id`, `model_has_permissions`.`permission_id` as `pivot_permission_id`, `model_has_permissions`.`model_type` as `pivot_model_type` from `permissions` inner join `model_has_permissions` on `permissions`.`id` = `model_has_permissions`.`permission_id` where `model_has_permissions`.`model_id` in (5) and `model_has_permissions`.`model_type` = 'App\\Models\\User'"
          - generic [ref=f1e205]: 0.34ms
  - generic [ref=f1e207]:
    - generic [ref=f1e208]:
      - heading "Headers" [level=2] [ref=f1e209]
      - generic [ref=f1e210]:
        - generic [ref=f1e211]:
          - generic [ref=f1e212]: host
          - generic [ref=f1e214]: localhost:8000
        - generic [ref=f1e215]:
          - generic [ref=f1e216]: connection
          - generic [ref=f1e218]: keep-alive
        - generic [ref=f1e219]:
          - generic [ref=f1e220]: sec-ch-ua
          - generic [ref=f1e222]: "\"HeadlessChrome\";v=\"153\", \"Not_A Brand\";v=\"8\", \"Chromium\";v=\"153\""
        - generic [ref=f1e223]:
          - generic [ref=f1e224]: sec-ch-ua-mobile
          - generic [ref=f1e226]: "?0"
        - generic [ref=f1e227]:
          - generic [ref=f1e228]: sec-ch-ua-platform
          - generic [ref=f1e230]: "\"macOS\""
        - generic [ref=f1e231]:
          - generic [ref=f1e232]: upgrade-insecure-requests
          - generic [ref=f1e234]: "1"
        - generic [ref=f1e235]:
          - generic [ref=f1e236]: user-agent
          - generic [ref=f1e238]: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/153.0.8010.12 Safari/537.36
        - generic [ref=f1e239]:
          - generic [ref=f1e240]: accept-language
          - generic [ref=f1e242]: en-US
        - generic [ref=f1e243]:
          - generic [ref=f1e244]: accept
          - generic [ref=f1e246]: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
        - generic [ref=f1e247]:
          - generic [ref=f1e248]: sec-fetch-site
          - generic [ref=f1e250]: none
        - generic [ref=f1e251]:
          - generic [ref=f1e252]: sec-fetch-mode
          - generic [ref=f1e254]: navigate
        - generic [ref=f1e255]:
          - generic [ref=f1e256]: sec-fetch-user
          - generic [ref=f1e258]: "?1"
        - generic [ref=f1e259]:
          - generic [ref=f1e260]: sec-fetch-dest
          - generic [ref=f1e262]: document
        - generic [ref=f1e263]:
          - generic [ref=f1e264]: accept-encoding
          - generic [ref=f1e266]: gzip, deflate, br, zstd
        - generic [ref=f1e267]:
          - generic [ref=f1e268]: cookie
          - generic [ref=f1e270]: XSRF-TOKEN=eyJpdiI6IjhiK3ZoSnVPMEEyTHVGYkRxNDgyeFE9PSIsInZhbHVlIjoiSnJ2RDROY1FKMldWS1Frai9HS1RtL1lWRmxVNDh3M3JDYTlVcWdNY0hTZzBFRUxMQVUxYmNLekpPWjdwNWp2OXdZZTZDVVhHR0t2Mjl2NHUzQjBnVGNkT29BZkk2S0Q4NjdTTEVpNnJ1RlRwUjQzZEM5OGxwOHNUNVZlN3FHTXkiLCJtYWMiOiJkYmFmZDA4MmE0N2RiYjVmNWY1MTUyODA1OTM4YzYwYjg2YTk0ZTM1MjMwMTkwY2JiYWFiMTcxZDViYWQ3ZjUxIiwidGFnIjoiIn0%3D; siakad-session=eyJpdiI6Ik5VWm1qN2o2VGM3dHVTR2p1RW5Qd0E9PSIsInZhbHVlIjoiMjk2Sk85NCtCclVYUXhBOEN0UDlsQThxUzF6OGd5bkpmeldqSld2eG1ySUN5Tk8wTTJqSVUyanFhQzRERE91YzM5SFlXWVJVOGppQ2ovU0dDbFJ5em5aOVU5REZVZ0JCQTVWQWNwTmFVSTcvZmxvcFl2Tyt1QkJ1UjVHelh6YjkiLCJtYWMiOiI4OGFhMDRkNTk1YWE1MDY4OGFiZGY1MDU2ZmIyY2JiMmUxNmY2NWYxZjU5ODlhNzJkMjAxNzdmYTlhM2NhNzU1IiwidGFnIjoiIn0%3D
    - generic [ref=f1e271]:
      - heading "Body" [level=2] [ref=f1e272]
      - generic [ref=f1e273]: // No request body
    - generic [ref=f1e274]:
      - heading "Routing" [level=2] [ref=f1e275]
      - generic [ref=f1e276]:
        - generic [ref=f1e277]:
          - generic [ref=f1e278]: controller
          - generic [ref=f1e280]: App\Http\Controllers\Admin\ClassroomController@index
        - generic [ref=f1e281]:
          - generic [ref=f1e282]: route name
          - generic [ref=f1e284]: admin.classrooms.index
        - generic [ref=f1e285]:
          - generic [ref=f1e286]: middleware
          - generic [ref=f1e288]: web, auth, role:super-admin, permission:master.view
    - generic [ref=f1e289]:
      - heading "Routing parameters" [level=2] [ref=f1e290]
      - generic [ref=f1e291]: // No routing parameters
```

# Test source

```ts
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
  171 |         await expect(page.locator('text=Mata Kuliah E2E')).toBeVisible({ timeout: 10000 });
  172 |     });
  173 | 
  174 |     test('buat ruangan baru muncul', async ({ page }) => {
  175 |         await page.goto('/admin/classrooms');
  176 |         await waitForPage(page);
  177 | 
  178 |         // Click Tambah Ruangan to open modal
> 179 |         await page.click('button:has-text("Tambah Ruangan")');
      |                    ^ Error: page.click: Test timeout of 30000ms exceeded.
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