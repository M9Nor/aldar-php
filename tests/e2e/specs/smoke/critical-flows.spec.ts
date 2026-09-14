import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { sqlScalar } from '../../support/docker';
import { IS_LOCAL } from '../../support/env';

const PAGES = [
  '/en', '/ar',
  '/en/contact-us', '/ar/contact-us',
  '/en/articles', '/ar/articles',
  '/en/services', '/en/faqs',
  '/en/apartments/for-sale/istanbul',
];

for (const url of PAGES) {
  test(`page ${url} renders and every image on it loads`, async ({ page, request }) => {
    const response = await page.goto(url);
    expect(response?.status()).toBe(200);

    const imageUrls = await page.$$eval('[src*="/img/"], [data-src*="/img/"]', elements =>
      Array.from(new Set(elements.map(e => e.getAttribute('data-src') || e.getAttribute('src') || ''))).filter(Boolean));
    expect(imageUrls.length).toBeGreaterThan(0);

    for (const imageUrl of imageUrls) {
      expect((await request.get(imageUrl)).status(), imageUrl).toBe(200);
    }
  });
}

test('contact form stores a lead', async ({ request }) => {
  test.skip(!IS_LOCAL, 'writes a lead');
  const before = sqlScalar('SELECT COUNT(*) FROM contact_us');
  const token = await anonymousCsrfToken(request);

  const response = await request.post('/en/contact-us/store', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
    multipart: {
      fullname: 'Smoke Test', phone: '5551234567', email: 'smoke@aldar.test',
      description: 'Smoke test lead', country_code: '+90',
    },
  });

  expect(await response.json()).toMatchObject({ success: true });
  expect(sqlScalar('SELECT COUNT(*) FROM contact_us')).toBe(before + 1);
});

test('admin can log in and reach the dashboard', async ({ page }) => {
  test.skip(!IS_LOCAL, 'uses local test accounts');
  await loginAs(page, 'parity-superadmin');
  await expect(page.locator('.kt-widget1').first()).toBeVisible();
});
