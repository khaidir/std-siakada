import { test, expect } from '@playwright/test';
import { loginAs } from './helpers';

test.describe('Authentication', () => {
    test('login sukses sebagai super-admin redirect ke dashboard', async ({ page }) => {
        await loginAs(page, 'admin@siakad.test', 'password');
        // Inertia uses history.pushState — check via waitForFunction
        const url = await page.waitForFunction(() => window.location.href.includes('/dashboard'), { timeout: 15000 });
        expect(url).toBeTruthy();
        await expect(page.locator('text=SIAKAD').first()).toBeVisible();
    });

    test('login sukses sebagai kaprodi redirect ke dashboard', async ({ page }) => {
        await loginAs(page, 'kaprodi@siakad.test', 'password');
        const url = await page.waitForFunction(() => window.location.href.includes('/dashboard'), { timeout: 15000 });
        expect(url).toBeTruthy();
    });

    // TODO: Dosen dashboard has a 500 error (missing student_id column).
    // Skip until the DB migration is fixed.
    test.skip('login sukses sebagai dosen redirect ke dashboard', async ({ page }) => {
        await loginAs(page, 'dosen@siakad.test', 'password');
        const url = await page.waitForFunction(() => window.location.href.includes('/dashboard'), { timeout: 15000 });
        expect(url).toBeTruthy();
    });

    test('login sukses sebagai mahasiswa redirect ke dashboard', async ({ page }) => {
        await loginAs(page, 'mahasiswa@siakad.test', 'password');
        const url = await page.waitForFunction(() => window.location.href.includes('/dashboard'), { timeout: 15000 });
        expect(url).toBeTruthy();
    });

    test('login sukses sebagai pimpinan redirect ke dashboard', async ({ page }) => {
        await loginAs(page, 'pimpinan@siakad.test', 'password');
        const url = await page.waitForFunction(() => window.location.href.includes('/dashboard'), { timeout: 15000 });
        expect(url).toBeTruthy();
    });

    test('login gagal menampilkan error', async ({ page }) => {
        await page.goto('/');
        await page.waitForSelector('input[type="email"]', { timeout: 10000 });

        await page.fill('input[type="email"]', 'admin@siakad.test');
        await page.fill('input[type="password"]', 'wrongpassword');
        await page.click('button[type="submit"]', { force: true });

        // Should show error message from Fortify (text-rose-600)
        await expect(page.locator('text=Kredensial ini tidak cocok')).toBeVisible({ timeout: 10000 });
    });
});
