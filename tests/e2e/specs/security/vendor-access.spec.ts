import { test, expect } from '@playwright/test';
import { attemptLogin } from '../../support/auth';
import { appShell, artisan, sql } from '../../support/docker';
import { requireLocal } from '../../support/env';

for (const worker of ['/service-worker.js', '/firebase-messaging-sw.js']) {
  test(`${worker} carries no vendor Firebase config and unregisters itself`, async ({ request }) => {
    const response = await request.get(worker);
    expect(response.status()).toBe(200);
    const body = await response.text();
    expect(body).not.toContain('binaa-prod');
    expect(body).not.toContain('firebase');
    expect(body).toContain('registration.unregister()');
  });
}

test.describe('former vendor account', () => {
  test.beforeEach(() => {
    requireLocal('changes account state');
    const hash = appShell(`php -r 'echo password_hash("vendor-knows-this", PASSWORD_BCRYPT);'`);
    sql(`UPDATE users SET password = '${hash}', status = 'ACTIVE', disabled_at = NULL, deleted_at = NULL WHERE email = 'root@namaa-solutions.com'`);
    artisan('cache:clear');
  });

  test('a disabled account cannot log in even with the right password', async ({ page }) => {
    sql("UPDATE users SET disabled_at = NOW() WHERE email = 'root@namaa-solutions.com'");
    expect(await attemptLogin(page, 'developer', 'vendor-knows-this')).toBe(401);
  });

  test('after offboarding, the old password no longer works', async ({ page }) => {
    artisan('aldar:offboard-vendor');
    expect(await attemptLogin(page, 'developer', 'vendor-knows-this')).toBe(401);
  });
});
