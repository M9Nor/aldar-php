import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken, csrfToken } from '../../support/csrf';
import { artisan, sql } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

test.beforeAll(() => requireLocal('GET /seed runs the seeders on unpatched code'));

// A cache entry only a real flush removes. CACHE_DRIVER=file is shared by the CLI and the web server.
const SENTINEL = 'e2e-s7-sentinel';
const setSentinel = () => artisan('tinker', '--execute', `\\Illuminate\\Support\\Facades\\Cache::forever('${SENTINEL}', 1);`);
const sentinelKept = () =>
  artisan('tinker', '--execute', `echo \\Illuminate\\Support\\Facades\\Cache::has('${SENTINEL}') ? 'kept' : 'flushed';`).includes('kept');

for (const url of ['/seed', '/migrate', '/update_currency', '/en/clear-cache']) {
  test(`${url} is not publicly reachable`, async ({ request }) => {
    expect((await request.get(url)).status()).toBe(404);
  });
}

test('anonymous visitors cannot use the admin maintenance routes', async ({ request }) => {
  const token = await anonymousCsrfToken(request);
  const update = await request.post('/en/admin/update-currency', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(update.status()).toBe(401);

  expect((await request.get('/en/admin/clear-cache', { maxRedirects: 0 })).status()).toBe(404);
  const clearAjax = await request.post('/en/admin/clear-cache', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(clearAjax.status()).toBe(401);
  const clear = await request.post('/en/admin/clear-cache', { form: { _token: token }, maxRedirects: 0 });
  expect(clear.status()).toBe(302);
  expect(clear.headers()['location']).toContain('/authenticate/login');
});

test('the dashboard still offers the currency update to staff', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const form = page.locator('form[action$="/en/admin/update-currency"]');
  await expect(form).toHaveCount(1);
  await expect(form.locator('input[name="_token"]')).toHaveCount(1);
  await expect(form.locator('button.btn-success')).toBeVisible();
});

test('the dashboard currency button really submits its form (P2-R6)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const methods: string[] = [];
  // The request never reaches the app: the command it triggers calls an external currency API.
  await page.context().route('**/en/admin/update-currency', async route => {
    methods.push(route.request().method());
    await route.abort();
  });
  await page.locator('form[action$="/en/admin/update-currency"] button').click();
  await expect.poll(() => methods.length, { timeout: 5_000 }).toBe(1);
  expect(methods[0]).toBe('POST');
});

test('cache holders clear the cache only with a CSRF POST; a GET, a forged POST or a disabled account cannot', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const token = await csrfToken(page);

  setSentinel();
  expect((await page.request.get('/en/admin/clear-cache', { maxRedirects: 0 })).status()).toBe(404);
  const forged = await page.request.post('/en/admin/clear-cache', { headers: { 'Sec-Fetch-Site': 'cross-site' }, maxRedirects: 0 });
  expect(forged.status()).toBe(302);
  expect(forged.headers()['location']).toContain('/authenticate/login');
  expect(sentinelKept()).toBe(true);

  const ok = await page.request.post('/en/admin/clear-cache', { form: { _token: token }, maxRedirects: 0 });
  expect(ok.status()).toBe(302);
  expect(sentinelKept()).toBe(false);

  sql("UPDATE users SET disabled_at = NOW() WHERE username = 'parity-superadmin'");
  try {
    setSentinel();
    const denied = await page.request.post('/en/admin/clear-cache', { form: { _token: token }, maxRedirects: 0 });
    expect(denied.status()).toBe(403);
    expect(sentinelKept()).toBe(true);
  } finally {
    sql("UPDATE users SET disabled_at = NULL WHERE username = 'parity-superadmin'");
  }
});

test('ADMIN, who does not see the menu item, cannot clear the cache (P2-R6)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const token = await csrfToken(page);
  setSentinel();
  const referer = `${BASE_URL}/en/parity-referer`;
  const denied = await page.request.post('/en/admin/clear-cache', { form: { _token: token }, headers: { Referer: referer }, maxRedirects: 0 });
  expect(denied.status()).toBe(302);
  expect(denied.headers()['location']).toBe(referer);
  expect(sentinelKept()).toBe(true);
});

test('the aside clear-cache item submits a hidden CSRF form', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const form = page.locator('form#clearCacheForm');
  await expect(form).toHaveAttribute('method', 'POST');
  await expect(form).toHaveAttribute('action', /\/en\/admin\/clear-cache$/);
  await expect(form.locator('input[name="_token"]')).toHaveCount(1);

  setSentinel();
  const posted = page.waitForRequest(r => r.url().endsWith('/en/admin/clear-cache') && r.method() === 'POST');
  await page.locator('#kt_aside_menu a[onclick*="clearCacheForm"]').click();
  await posted;
  await page.waitForLoadState('load');
  await expect.poll(sentinelKept).toBe(false);
});
