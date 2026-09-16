import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { artisan, sql, sqlScalar } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

const WEB_TOKEN = '/en/admin/notification/postWebToken';
const PROBE_TOKENS = "SELECT COUNT(*) FROM notif_tokens WHERE token LIKE 'e2e-s6-%'";

test.beforeAll(() => requireLocal('uses the local parity accounts and writes notif_tokens rows'));

test('the Firebase web config is for staff only (S9)', async ({ page, request }) => {
  const anonymous = await request.get('/en/admin/notification/config', { maxRedirects: 0 });
  expect(anonymous.status()).toBe(302);
  expect(anonymous.headers()['location']).toMatch(/\/authenticate\/login$/);
  expect((await request.get('/en/admin/notification/config', { headers: AJAX_HEADERS, maxRedirects: 0 })).status()).toBe(401);

  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const staff = await page.request.get('/en/admin/notification/config', { headers: AJAX_HEADERS });
  expect(staff.status()).toBe(200);
  expect(Object.keys(await staff.json()).sort()).toEqual([
    'apiKey', 'appId', 'authDomain', 'databaseURL', 'measurementId', 'messagingSenderId', 'projectId',
  ]);
});

test.describe('postWebToken (S6)', () => {
  test.beforeEach(() => {
    artisan('cache:clear'); // resets the per-IP limiter
  });
  test.afterAll(() => sql("DELETE FROM notif_tokens WHERE token LIKE 'e2e-s6-%'"));

  test('an oversized token is refused and stores nothing', async ({ request }) => {
    const token = await anonymousCsrfToken(request);
    const before = sqlScalar(PROBE_TOKENS);
    const response = await request.post(WEB_TOKEN, {
      headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
      form: { data_token: `e2e-s6-${'x'.repeat(300)}` },
    });
    expect(response.status()).toBe(422);
    expect(sqlScalar(PROBE_TOKENS)).toBe(before);
  });

  test('the eleventh attempt within a minute is refused with a generic JSON 429', async ({ request }) => {
    const token = await anonymousCsrfToken(request);
    const attempt = () => request.post(WEB_TOKEN, { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token }, form: {} });
    for (let i = 1; i <= 10; i++) {
      expect((await attempt()).status(), `attempt ${i}`).toBe(422);
    }
    const refused = await attempt();
    expect(refused.status()).toBe(429);
    const body = await refused.json();
    expect(body.success).toBe(false);
    expect(body.errors).toEqual([]);
  });
});
