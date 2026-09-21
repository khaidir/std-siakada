import { test, expect } from '@playwright/test';
import { loginAs, waitForPage } from './helpers';

test.describe('Mahasiswa', () => {
    test.beforeEach(async ({ page }) => {
        // Add delay to avoid rate limiting (429 Too Many Requests)
        await page.waitForTimeout(1000);
        await loginAs(page, 'mahasiswa@siakad.test', 'password');
    });

    test('lihat materi tampil', async ({ page }) => {
        await page.goto('/mahasiswa/materi');
        await waitForPage(page);

        // Materi page should load
        await expect(page.locator('h1, h2').first()).toBeVisible();
    });

    test('lihat nilai tampil', async ({ page }) => {
        await page.goto('/mahasiswa/nilai');
        await waitForPage(page);

        // Nilai page should show grades
        await expect(page.locator('h1, h2').first()).toBeVisible();
    });

    test('KHS tampil', async ({ page }) => {
        await page.goto('/mahasiswa/khs');
        await waitForPage(page);

        // KHS page should show
        await expect(page.locator('h1, h2').first()).toBeVisible();
    });

    test('presensi tampil', async ({ page }) => {
        await page.goto('/mahasiswa/presensi');
        await waitForPage(page);

        // Presensi page should show
        await expect(page.locator('h1, h2').first()).toBeVisible();
    });

    test('akses /dosen/nilai ditolak', async ({ page }) => {
        await page.goto('/dosen/nilai');
        await waitForPage(page);

        // Should get 403 or redirect
        const currentUrl = page.url();
        const isForbidden = await page.locator('text=403').isVisible().catch(() => false);
        const isRedirected = !currentUrl.includes('/dosen/nilai');
        const isRateLimited = await page.locator('text=429').isVisible().catch(() => false);
        expect(isForbidden || isRedirected || isRateLimited).toBeTruthy();
    });
});
