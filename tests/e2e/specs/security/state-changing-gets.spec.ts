import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { csrfToken } from '../../support/csrf';
import { sql, sqlScalar } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S10: no admin GET route changes state.
test.beforeAll(() => requireLocal('uses the local parity accounts'));

// The filter pages the removed seeding method created or overwrote.
const SEEDED_FILTERS = `SELECT COUNT(*) FROM cms_contents WHERE type = 'filters' AND slug IN (
  'apartments-for-sale-in-turkey', 'installments-apartments-for-sale-in-turkey', 'sea-view-apartments-for-sale-in-turkey',
  'cheap-apartments-for-sale-in-turkey', 'shops-for-sale-in-turkey', 'villas-for-sale-in-turkey', 'cheap-villas-for-sale-in-turkey',
  'sea-view-villas-for-sale-in-turkey', 'installments-apartments-for-sale-in-istanbul', 'cheap-villas-for-sale-in-istanbul',
  'sea-view-villas-for-sale-in-istanbul', 'apartments-for-sale-in-istanbul')`;

test('update_prices and the category seeding URL are gone and seed nothing', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const before = sqlScalar(SEEDED_FILTERS);
  // update_prices first: on unpatched code it answers 500 and this test stops here, before the seeding GET could write.
  expect((await page.request.get('/en/admin/projects/update_prices', { maxRedirects: 0 })).status()).toBe(404);
  expect((await page.request.get('/en/admin/categories/asdwadwadwdaw', { maxRedirects: 0 })).status()).toBe(404);
  expect(sqlScalar(SEEDED_FILTERS)).toBe(before);
});

test('login_as needs a CSRF POST and never switches a non-ROOT session', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const target = sql("SELECT id FROM users WHERE username = 'parity-superadmin'");

  expect((await page.request.get(`/en/admin/users/login_as/${target}`, { maxRedirects: 0 })).status()).toBe(404);
  const forged = await page.request.post(`/en/admin/users/login_as/${target}`, { headers: { 'Sec-Fetch-Site': 'cross-site' }, maxRedirects: 0 });
  expect(forged.status()).toBe(302);
  expect(forged.headers()['location']).toContain('/authenticate/login');

  const token = await csrfToken(page);
  const posted = await page.request.post(`/en/admin/users/login_as/${target}`, { form: { _token: token }, maxRedirects: 0 });
  expect(posted.status()).toBe(200);

  // Still ADMIN: the leads page (tags.requests, SUPERADMIN only) still redirects back.
  const referer = `${BASE_URL}/en/parity-referer`;
  const leads = await page.request.get('/en/admin/projects/requests', { headers: { Referer: referer }, maxRedirects: 0 });
  expect(leads.status()).toBe(302);
  expect(leads.headers()['location']).toBe(referer);
});
