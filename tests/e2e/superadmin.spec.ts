import { test, expect } from '@playwright/test';
import { loginAs, waitForPage } from './helpers';

test.describe('Super Admin', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, 'admin@siakad.test', 'password');
    });

    test('dashboard tampil', async ({ page }) => {
        await expect(page).toHaveURL(/\/dashboard/);
        // Dashboard should have some content
        await expect(page.locator('h1, h2').first()).toBeVisible();
    });

    test('buat user dosen baru muncul di tabel', async ({ page }) => {
        await page.goto('/admin/users');
        await waitForPage(page);

        // Click Tambah Pengguna to open modal
        await page.click('button:has-text("Tambah Pengguna")');

        // Wait for modal to appear
        await page.waitForSelector('text=Tambah Pengguna', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill basic info
        await page.locator('input[placeholder="Nama lengkap"]').fill('Dosen Baru E2E');
        await page.locator('input[type="email"]').fill('dosenbaru@test.com');
        await page.locator('input[type="password"]').first().fill('password');

        // Select role dosen
        await page.locator('select').first().selectOption('dosen');
        await page.waitForTimeout(300);

        // Fill dosen-specific fields
        await page.locator('input[placeholder="Nomor Induk Dosen Nasional"]').fill('1234567890');
        await page.locator('select').nth(1).selectOption({ index: 1 }); // study program
        await page.locator('select').nth(2).selectOption({ index: 1 }); // academic rank

        // Submit
        await page.locator('button[type="submit"]').click();

        // Wait for new user to appear in table
        await page.waitForSelector('text=dosenbaru@test.com', { timeout: 15000 });
    });

    test('buat fakultas baru muncul', async ({ page }) => {
        await page.goto('/admin/faculties');
        await waitForPage(page);

        // Click Tambah Fakultas to open modal
        await page.click('button:has-text("Tambah Fakultas")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Fakultas', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form (input 0 is sidebar search, input 1=code, input 2=name)
        const inputs = page.locator('input');
        const uniqueCode = 'FIK' + Date.now().toString().slice(-4);
        await inputs.nth(1).fill(uniqueCode);
        await inputs.nth(2).fill('Fakultas E2E');

        // Click submit button (last "Tambah" button inside modal)
        await page.locator('button:has-text("Tambah")').last().click();
        await waitForPage(page);

        await expect(page.locator('text=Fakultas E2E').first()).toBeVisible({ timeout: 10000 });
    });

    test('buat program studi baru muncul', async ({ page }) => {
        await page.goto('/admin/study-programs');
        await waitForPage(page);

        // Click Tambah Prodi to open modal
        await page.click('button:has-text("Tambah Prodi")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Prodi', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form (input 0 is sidebar search, input 1=code, input 2=name)
        const inputs = page.locator('input');
        const uniqueCode = 'TE' + Date.now().toString().slice(-4);
        await inputs.nth(1).fill(uniqueCode);
        await inputs.nth(2).fill('Teknik E2E');

        // Select faculty
        const selects = page.locator('select');
        await selects.first().selectOption({ index: 1 });

        // Select degree
        if (await selects.count() > 1) {
            await selects.nth(1).selectOption({ index: 1 });
        }

        // Click submit button (last "Tambah" button inside modal)
        await page.locator('button:has-text("Tambah")').last().click();
        await waitForPage(page);

        await expect(page.locator('text=Teknik E2E').first()).toBeVisible({ timeout: 10000 });
    });

    test('buat mata kuliah baru muncul', async ({ page }) => {
        await page.goto('/admin/courses');
        await waitForPage(page);

        // Click Tambah Mata Kuliah to open modal
        await page.click('button:has-text("Tambah Mata Kuliah")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Mata Kuliah', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form (input 0 is sidebar search, input 1=code, input 2=name, input 3=SKS)
        const inputs = page.locator('input');
        const uniqueCode = 'E2E' + Date.now().toString().slice(-4);
        await inputs.nth(1).fill(uniqueCode);
        await inputs.nth(2).fill('Mata Kuliah E2E');
        await inputs.nth(3).fill('3');

        // Select study program
        const selects = page.locator('select');
        await selects.first().selectOption({ index: 1 });

        // Select semester
        if (await selects.count() > 1) {
            await selects.nth(1).selectOption({ index: 1 });
        }

        // Select type
        if (await selects.count() > 2) {
            await selects.nth(2).selectOption('wajib');
        }

        // Click submit button (last "Tambah" button inside modal)
        await page.locator('button:has-text("Tambah")').last().click();
        await waitForPage(page);

        await expect(page.locator('text=Mata Kuliah E2E').first()).toBeVisible({ timeout: 10000 });
    });

    test('buat ruangan baru muncul', async ({ page }) => {
        await page.goto('/admin/classrooms');
        await waitForPage(page);

        // Click Tambah Ruangan to open modal
        await page.click('button:has-text("Tambah Ruangan")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Ruangan', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form (input 0 is sidebar search, input 1=code, input 2=name, input 3=capacity, input 4=building)
        const inputs = page.locator('input');
        await inputs.nth(1).fill('R-E2E');
        await inputs.nth(2).fill('Ruangan E2E');
        await inputs.nth(3).fill('40');
        await inputs.nth(4).fill('Gedung E2E');

        // Click submit button (last "Tambah" button inside modal)
        await page.locator('button:has-text("Tambah")').last().click();
        await waitForPage(page);

        await expect(page.locator('text=Ruangan E2E').first()).toBeVisible({ timeout: 10000 });
    });

    test('buat periode akademik muncul', async ({ page }) => {
        await page.goto('/admin/periods');
        await waitForPage(page);

        // Click Tambah Tahun Ajaran to open modal
        await page.click('button:has-text("Tambah Tahun Ajaran")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Tahun Ajaran', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form (input 0 is sidebar search, input 1=year, input 2=name, input 3=start_date, input 4=end_date)
        const inputs = page.locator('input');
        await inputs.nth(1).fill('2026/2027');
        await inputs.nth(2).fill('Tahun Akademik 2026/2027');
        await inputs.nth(3).fill('2026-09-01');
        await inputs.nth(4).fill('2027-08-31');

        // Click submit button (last "Tambah" button inside modal)
        await page.locator('button:has-text("Tambah")').last().click();
        await waitForPage(page);

        await expect(page.locator('text=2026/2027')).toBeVisible({ timeout: 10000 });
    });

    test('manage role permissions tampil', async ({ page }) => {
        await page.goto('/admin/roles');
        await waitForPage(page);

        // Should show roles page
        await expect(page.getByRole('heading', { name: 'Roles & Permissions' })).toBeVisible({ timeout: 10000 });
    });
});
