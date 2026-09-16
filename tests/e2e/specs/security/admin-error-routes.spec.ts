import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { sql } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S11: admin GET routes that answered 500 for staff answer 404 or a validation error.
test.beforeAll(() => requireLocal('uses the local parity accounts'));

const GONE = ['/en/admin/notification', '/en/admin/users/show', '/en/admin/roles/show', '/en/admin/projects/show', '/en/admin/opportunity/show'];

for (const who of ['parity-superadmin', 'parity-admin'] as const) {
  test(`dead admin GET routes answer 404 to ${who}, never 500`, async ({ page }) => {
    await blockProduction(page.context());
    await loginAs(page, who);
    for (const url of GONE) {
      expect((await page.request.get(url, { maxRedirects: 0 })).status(), url).toBe(404);
    }
  });
}

test('tags/list requires a locale and still lists tags with one', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  expect((await page.request.get('/en/admin/tags/list', { headers: AJAX_HEADERS })).status()).toBe(422);
  const listed = await page.request.get('/en/admin/tags/list?locale=en&q=&count=0', { headers: AJAX_HEADERS });
  expect(listed.status()).toBe(200);
  expect((JSON.parse(await listed.text()) as unknown[]).length).toBeGreaterThan(1);
});

test('validate_ needs the edited user id, ignores that user, and the user summary still links to users/show', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const probe = await page.request.get('/en/admin/users/identity/validate_?name=username&keyword=parity-probe', { headers: AJAX_HEADERS });
  expect(probe.status()).toBe(422);

  const id = sql("SELECT id FROM users WHERE username = 'parity-admin'");
  const own = await page.request.get(`/en/admin/users/identity/validate_?name=username&keyword=parity-admin&model=${id}`, { headers: AJAX_HEADERS });
  expect(own.status()).toBe(200);
  expect((await own.json()).is_valid).toBe(true);

  const summary = await page.request.get(`/en/admin/users/summary?model=${id}`, { headers: AJAX_HEADERS });
  expect(summary.status()).toBe(200);
  expect((await summary.json()).summary).toContain('/en/admin/users/show?model=');
});
