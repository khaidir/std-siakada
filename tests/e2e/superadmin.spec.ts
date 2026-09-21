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

        // Fill form
        const inputs = page.locator('input');
        await inputs.nth(0).fill('Dosen Baru E2E');

        // Find email input
        const emailInput = page.locator('input[type="email"]');
        await emailInput.fill('dosenbaru@test.com');

        // Find password inputs
        const passwordInputs = page.locator('input[type="password"]');
        await passwordInputs.first().fill('password');
        if (await passwordInputs.count() > 1) {
            await passwordInputs.nth(1).fill('password');
        }

        // Select role dosen
        const select = page.locator('select').first();
        await select.selectOption('dosen');

        // Fill NIDN (required for dosen)
        const nidnInput = page.locator('input').filter({ has: page.locator('[placeholder="Nomor Induk Dosen Nasional"]') });
        if (await nidnInput.count() > 0) {
            await nidnInput.fill('1234567890');
        } else {
            // Fallback: fill the 3rd input (after name, email)
            const allInputs = page.locator('input');
            const count = await allInputs.count();
            if (count > 2) await allInputs.nth(2).fill('1234567890');
        }

        // Select study program (required for dosen)
        const studyProgramSelect = page.locator('select').nth(1);
        if (await studyProgramSelect.count() > 0) {
            const options = await studyProgramSelect.locator('option').all();
            if (options.length > 1) {
                await studyProgramSelect.selectOption({ index: 1 });
            }
        }

        // Select academic rank (required for dosen)
        const rankSelect = page.locator('select').nth(2);
        if (await rankSelect.count() > 0) {
            const options = await rankSelect.locator('option').all();
            if (options.length > 1) {
                await rankSelect.selectOption({ index: 1 });
            }
        }

        // Submit
        await page.click('button[type="submit"]');
        await waitForPage(page);

        // Verify user appears in table
        await expect(page.locator('text=dosenbaru@test.com')).toBeVisible({ timeout: 10000 });
    });

    test('buat fakultas baru muncul', async ({ page }) => {
        await page.goto('/admin/faculties');
        await waitForPage(page);

        // Click Tambah Fakultas to open modal
        await page.click('button:has-text("Tambah Fakultas")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Fakultas', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form
        const inputs = page.locator('input');
        await inputs.nth(0).fill('FIK2');
        await inputs.nth(1).fill('Fakultas E2E');
        await page.click('button:has-text("Tambah")', { force: true });
        await waitForPage(page);

        await expect(page.locator('text=Fakultas E2E')).toBeVisible({ timeout: 10000 });
    });

    test('buat program studi baru muncul', async ({ page }) => {
        await page.goto('/admin/study-programs');
        await waitForPage(page);

        // Click Tambah Prodi to open modal
        await page.click('button:has-text("Tambah Prodi")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Program Studi', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form
        const inputs = page.locator('input');
        await inputs.nth(0).fill('TE');
        await inputs.nth(1).fill('Teknik E2E');

        // Select faculty
        const selects = page.locator('select');
        await selects.first().selectOption({ index: 1 });

        // Select degree
        if (await selects.count() > 1) {
            await selects.nth(1).selectOption({ index: 1 });
        }

        await page.click('button:has-text("Tambah")', { force: true });
        await waitForPage(page);

        await expect(page.locator('text=Teknik E2E')).toBeVisible({ timeout: 10000 });
    });

    test('buat mata kuliah baru muncul', async ({ page }) => {
        await page.goto('/admin/courses');
        await waitForPage(page);

        // Click Tambah Mata Kuliah to open modal
        await page.click('button:has-text("Tambah Mata Kuliah")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Mata Kuliah', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form
        const inputs = page.locator('input');
        await inputs.nth(0).fill('E2E101');
        await inputs.nth(1).fill('Mata Kuliah E2E');

        // SKS
        const numberInputs = page.locator('input[type="number"]');
        await numberInputs.first().fill('3');

        // Select study program
        const selects = page.locator('select');
        await selects.first().selectOption({ index: 1 });

        // Select semester
        if (await selects.count() > 1) {
            await selects.nth(1).selectOption({ index: 1 });
        }

        // Select type
        if (await selects.count() > 2) {
            await selects.nth(2).selectOption('Wajib');
        }

        await page.click('button:has-text("Tambah")', { force: true });
        await waitForPage(page);

        await expect(page.locator('text=Mata Kuliah E2E')).toBeVisible({ timeout: 10000 });
    });

    test('buat ruangan baru muncul', async ({ page }) => {
        await page.goto('/admin/classrooms');
        await waitForPage(page);

        // Click Tambah Ruangan to open modal
        await page.click('button:has-text("Tambah Ruangan")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Ruangan', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form
        const inputs = page.locator('input');
        await inputs.nth(0).fill('R-E2E');
        await inputs.nth(1).fill('Ruangan E2E');

        // Capacity
        const numberInput = page.locator('input[type="number"]').first();
        await numberInput.fill('40');

        await page.click('button:has-text("Tambah")', { force: true });
        await waitForPage(page);

        await expect(page.locator('text=Ruangan E2E')).toBeVisible({ timeout: 10000 });
    });

    test('buat periode akademik muncul', async ({ page }) => {
        await page.goto('/admin/periods');
        await waitForPage(page);

        // Click Tambah Tahun Ajaran to open modal
        await page.click('button:has-text("Tambah Tahun Ajaran")');

        // Wait for modal
        await page.waitForSelector('text=Tambah Tahun Ajaran', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form
        const inputs = page.locator('input');
        await inputs.nth(0).fill('2026/2027');
        await inputs.nth(1).fill('Tahun Akademik 2026/2027');

        // Date inputs
        const dateInputs = page.locator('input[type="date"]');
        if (await dateInputs.count() > 0) {
            await dateInputs.nth(0).fill('2026-09-01');
        }
        if (await dateInputs.count() > 1) {
            await dateInputs.nth(1).fill('2027-08-31');
        }

        await page.click('button:has-text("Tambah")', { force: true });
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
