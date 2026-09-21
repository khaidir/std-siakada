import { test, expect } from '@playwright/test';
import { loginAs, waitForPage } from './helpers';

test.describe('Kaprodi', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, 'kaprodi@siakad.test', 'password');
    });

    test('sidebar tidak menampilkan menu super-admin', async ({ page }) => {
        // Navigate to kaprodi dashboard
        await page.goto('/kaprodi/dashboard');
        await waitForPage(page);

        // Super-admin specific menus should NOT be visible
        // Use specific selectors to avoid ambiguity
        await expect(page.locator('a:has-text("Pengguna"), button:has-text("Pengguna")')).not.toBeVisible();
        await expect(page.locator('text=Roles & Permissions')).not.toBeVisible();
        await expect(page.locator('a:has-text("Fakultas")')).not.toBeVisible();
        await expect(page.locator('a:has-text("Program Studi")')).not.toBeVisible();
    });

    test('buat mata kuliah tampil', async ({ page }) => {
        await page.goto('/kaprodi/courses');
        await waitForPage(page);

        // Click tambah button to open modal
        await page.click('button:has-text("Tambah Mata Kuliah")');

        // Wait for modal to appear
        await page.waitForSelector('text=Tambah Mata Kuliah', { timeout: 5000 });
        await page.waitForSelector('input', { timeout: 5000 });

        // Fill form
        const inputs = page.locator('input');
        await inputs.nth(0).fill('IF999');
        await inputs.nth(1).fill('Mata Kuliah Kaprodi E2E');

        // Fill SKS
        const numberInput = page.locator('input[type="number"]').first();
        await numberInput.fill('3');

        // Select semester
        const selects = page.locator('select');
        await selects.first().selectOption({ index: 1 });

        // Select type
        if (await selects.count() > 1) {
            await selects.nth(1).selectOption('wajib');
        }

        await page.click('button:has-text("Tambah")', { force: true });
        await waitForPage(page);

        await expect(page.locator('text=Mata Kuliah Kaprodi E2E')).toBeVisible({ timeout: 10000 });
    });

    test('monitoring nilai tampil', async ({ page }) => {
        await page.goto('/kaprodi/grades');
        await waitForPage(page);

        // Should show grade monitoring page
        await expect(page.locator('h1, h2').first()).toBeVisible();
    });

    test('akses /admin/users ditolak 403', async ({ page }) => {
        await page.goto('/admin/users');
        await waitForPage(page);

        // Should get 403 or redirect
        const currentUrl = page.url();
        // Either we're on a 403 page or redirected
        const isForbidden = await page.locator('text=403').isVisible().catch(() => false);
        const isRedirected = !currentUrl.includes('/admin/users');
        expect(isForbidden || isRedirected).toBeTruthy();
    });
});
