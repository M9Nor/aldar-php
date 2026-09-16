import { test, expect, APIResponse } from '@playwright/test';
import { blockProduction } from '../../support/network';

// S3: security headers on every response the application sends. Read-only.
const EXPECTED: Record<string, string> = {
  'strict-transport-security': 'max-age=31536000',
  'x-content-type-options': 'nosniff',
  'x-frame-options': 'SAMEORIGIN',
  'referrer-policy': 'strict-origin-when-cross-origin',
};

function expectSecurityHeaders(response: APIResponse, label: string): void {
  const headers = response.headersArray();
  for (const [name, value] of Object.entries(EXPECTED)) {
    expect(headers.filter(h => h.name.toLowerCase() === name).map(h => h.value), `${label}: ${name}`).toEqual([value]);
  }
  expect(headers.filter(h => h.name.toLowerCase() === 'content-security-policy-report-only').length, `${label}: CSP report-only`).toBe(1);
  expect(headers.some(h => h.name.toLowerCase() === 'content-security-policy'), `${label}: no enforcing CSP`).toBe(false);
}

test('pages, redirects, JSON, images and error pages carry the security headers once', async ({ request }) => {
  const urls: Array<[string, string]> = [
    ['English home', '/en'],
    ['Arabic home', '/ar'],
    ['admin login', '/en/authenticate/login'],
    ['Select2 JSON', '/en/get-areas?items_per_page=2'],
    ['image', '/img/85x85/defaults/base.png'],
    ['not found', '/en/zz-no-such-type/zz-no-such-page'],
    ['locale redirect', '/'],
  ];
  for (const [label, url] of urls) {
    expectSecurityHeaders(await request.get(url, { maxRedirects: 0 }), label);
  }
});

test('the Content-Security-Policy is report-only and names the CDNs the pages load', async ({ request }) => {
  const policy = (await request.get('/en')).headers()['content-security-policy-report-only'] ?? '';
  for (const origin of [
    'www.amcharts.com', 'cdn.jsdelivr.net', 'unpkg.com', 'cdnjs.cloudflare.com', 'maxcdn.bootstrapcdn.com',
    'stackpath.bootstrapcdn.com', 'cdn.ampproject.org', 'kq9v7r75.rocketcdn.com', 'www.youtube.com',
    'fonts.googleapis.com', 'fonts.gstatic.com',
    // Confirmed live via a real browser's Report-Only console violations on the homepage (grep-based
    // research missed both, since neither is a literal `https://` string in a Blade template):
    // the phone-country picker's flag images (`$country->flag`) and the iframe the Google Maps JS
    // widget renders into, a different host from the `maps.google.com` script that builds it.
    'flags.fmcdn.net', 'www.google.com',
  ]) {
    expect(policy, origin).toContain(origin);
  }
});

test('a real page renders and its scripts still run with the headers on', async ({ page }) => {
  await blockProduction(page.context());

  const response = await page.goto('/en');
  expect(response?.status()).toBe(200);
  expect(response?.headers()['content-security-policy']).toBeUndefined();
  expect(response?.headers()['content-security-policy-report-only']).toBeTruthy();

  await expect(page).toHaveTitle(/Aldar/i);
  // jQuery and the plugins built on it (owl-carousel, magnific-popup, the phone-country picker, ...)
  // are the bulk of the page's inline/external scripts; this proves script execution, not just markup.
  await expect.poll(() => page.evaluate(() => typeof (window as any).jQuery)).toBe('function');

  const consoleErrors: string[] = [];
  page.on('pageerror', err => consoleErrors.push(String(err)));
  await page.reload();
  await expect(page).toHaveTitle(/Aldar/i);
  expect(consoleErrors, 'uncaught script errors after reload').toEqual([]);
});
