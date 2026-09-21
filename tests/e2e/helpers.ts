import type { Page } from '@playwright/test';

/**
 * Login as a given user via the login form.
 * Waits for navigation to /dashboard after successful login.
 * Uses force click because Inertia.js may have ongoing animations
 * that prevent Playwright's stability check.
 */
export async function loginAs(page: Page, email: string, password: string) {
    await page.goto('/');

    // Wait for the login form to render
    await page.waitForSelector('input[type="email"]', { timeout: 10000 });

    // Fill credentials
    await page.fill('input[type="email"]', email);
    await page.fill('input[type="password"]', password);

    // Click submit — use force to bypass stability checks (Inertia animations)
    await page.click('button[type="submit"]', { force: true });

    // Wait for navigation to dashboard (Inertia uses history.pushState)
    await page.waitForFunction(() => window.location.href.includes('/dashboard'), { timeout: 15000 });
}

/**
 * Wait for Inertia page to finish loading (no more loading indicator).
 */
export async function waitForPage(page: Page) {
    await page.waitForLoadState('networkidle');
    // Small buffer for Inertia rendering
    await page.waitForTimeout(500);
}

/**
 * Get a flash message from the page if present.
 */
export async function getFlashMessage(page: Page): Promise<string | null> {
    const flash = await page.locator('[class*="bg-emerald-50"], [class*="bg-rose-50"]').first();
    if (await flash.isVisible()) {
        return flash.textContent();
    }
    return null;
}
