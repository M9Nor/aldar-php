import { expect, Page } from '@playwright/test';
import { E2E_PASSWORD } from './env';

export async function attemptLogin(page: Page, identity: string, password: string): Promise<number> {
  await page.context().clearCookies();
  await page.goto('/en/authenticate/login');
  await page.fill('#identity', identity);
  await page.fill('#password', password);
  const [response] = await Promise.all([
    page.waitForResponse(r => r.request().method() === 'POST' && r.url().includes('/authenticate/login')),
    page.click('button[type=submit]'),
  ]);
  return response.status();
}

export async function loginAs(page: Page, username: 'parity-superadmin' | 'parity-admin'): Promise<void> {
  expect(await attemptLogin(page, username, E2E_PASSWORD)).toBe(200);
  await expect(page).toHaveURL(/\/en\/admin\/?$/, { timeout: 30_000 });
}
