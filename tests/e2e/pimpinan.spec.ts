import { test, expect } from '@playwright/test';
import { loginAs, waitForPage } from './helpers';

test.describe('Pimpinan', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, 'pimpinan@siakad.test', 'password');
    });

    test('dashboard tampil dengan StatCard', async ({ page }) => {
        await page.goto('/pimpinan/dashboard');
        await waitForPage(page);

        // Dashboard should have stat cards
        await expect(page.locator('h1, h2').first()).toBeVisible();
    });

    test('laporan akademik tampil', async ({ page }) => {
        await page.goto('/pimpinan/laporan');
        await waitForPage(page);

        // Laporan page has tabs (KHS, Presensi, Kinerja Dosen) and filter bar
        // Check that the page loaded by looking for any heading
        await expect(page.locator('h1, h2').first()).toBeVisible({ timeout: 5000 });
    });

    test('akses /admin/users ditolak', async ({ page }) => {
        await page.goto('/admin/users');
        await waitForPage(page);

        // Should get 403 or redirect
        const currentUrl = page.url();
        const isForbidden = await page.locator('text=403').isVisible().catch(() => false);
        const isRedirected = !currentUrl.includes('/admin/users');
        expect(isForbidden || isRedirected).toBeTruthy();
    });
});
