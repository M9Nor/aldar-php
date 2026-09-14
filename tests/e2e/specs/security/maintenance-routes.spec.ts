import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { sql } from '../../support/docker';
import { requireLocal } from '../../support/env';

test.beforeAll(() => requireLocal('GET /seed runs the seeders on unpatched code'));

for (const url of ['/seed', '/migrate', '/update_currency', '/en/clear-cache']) {
  test(`${url} is not publicly reachable`, async ({ request }) => {
    expect((await request.get(url)).status()).toBe(404);
  });
}

test('anonymous visitors cannot use the admin maintenance routes', async ({ request }) => {
  const token = await anonymousCsrfToken(request);
  const update = await request.post('/en/admin/update-currency', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(update.status()).toBe(401);

  const clear = await request.get('/en/admin/clear-cache', { maxRedirects: 0 });
  expect(clear.status()).toBe(302);
  expect(clear.headers()['location']).toContain('/authenticate/login');
});

test('the dashboard still offers the currency update to staff', async ({ page }) => {
  await loginAs(page, 'parity-admin');
  const form = page.locator('form[action$="/en/admin/update-currency"]');
  await expect(form).toHaveCount(1);
  await expect(form.locator('input[name="_token"]')).toHaveCount(1);
  await expect(form.locator('button.btn-success')).toBeVisible();
});

test('staff can clear the cache; disabled staff cannot', async ({ page }) => {
  await loginAs(page, 'parity-admin');
  const ok = await page.request.get('/en/admin/clear-cache', { maxRedirects: 0 });
  expect(ok.status()).toBe(302);

  sql("UPDATE users SET disabled_at = NOW() WHERE username = 'parity-admin'");
  try {
    const denied = await page.request.get('/en/admin/clear-cache', { maxRedirects: 0 });
    expect(denied.status()).toBe(403);
  } finally {
    sql("UPDATE users SET disabled_at = NULL WHERE username = 'parity-admin'");
  }
});
