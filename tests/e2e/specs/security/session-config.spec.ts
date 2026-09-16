import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { appShell } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S2: sessions are stored as JSON, and flashed messages still survive a redirect.
test('sessions are stored as JSON and a flashed message still survives a redirect', async ({ page }) => {
  requireLocal('reads the local session files');
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');

  // ADMIN lacks tags.requests: the leads page redirects back with a flashed "permission error" toastr.
  await page.goto('/en/admin/projects/requests', { referer: `${BASE_URL}/en/admin` });
  expect(new URL(page.url()).pathname).toBe('/en/admin');
  expect(await page.content()).toContain("show_toastr('warning'");

  // Only the first byte of the newest session file is read: "{" for JSON, "a" for PHP serialization.
  const newest = appShell("ls -t storage/framework/sessions | grep -v '^\\.gitignore$' | head -n 1");
  expect(appShell(`head -c 1 storage/framework/sessions/${newest}`)).toBe('{');
});

// S2: a full login/redirect/reload/logout cycle still works once sessions are JSON-serialized.
test('parity-superadmin logs in, the session survives a redirect, and logging out ends it', async ({ page }) => {
  requireLocal('signs in with a local parity account');
  await blockProduction(page.context());

  // loginAs submits the login POST and follows its 302 to /en/admin: reaching the dashboard already
  // proves the (now JSON-serialized) session data set by that request survives the redirect.
  await loginAs(page, 'parity-superadmin');

  // A second, independent navigation reusing the same session cookie still reaches the dashboard.
  await page.goto('/en/admin');
  await expect(page).toHaveURL(/\/en\/admin\/?$/);

  const token = await page.locator('#logoutForm input[name="_token"]').getAttribute('value');
  const logout = await page.request.post('/en/authenticate/logout', { form: { _token: token! }, maxRedirects: 0 });
  expect(logout.status()).toBe(302);

  const after = await page.request.get('/en/admin', { maxRedirects: 0 });
  expect(after.status()).toBe(302);
  expect(after.headers()['location']).toMatch(/\/authenticate\/login$/);
});
