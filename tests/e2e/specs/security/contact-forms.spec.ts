import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { artisan, sqlScalar } from '../../support/docker';
import { requireLocal } from '../../support/env';

const leads = () => sqlScalar('SELECT COUNT(*) FROM contact_us');

async function subscribe(request: APIRequestContext, extra: Record<string, string> = {}) {
  const token = await anonymousCsrfToken(request);
  const response = await request.post('/en/contact-us/subscribe', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
    multipart: { emails: `lead-${Date.now()}-${Math.random().toString(36).slice(2)}@aldar.test`, ...extra },
  });
  expect(response.status()).toBe(200);
  return response.json();
}

test.beforeEach(() => {
  requireLocal('contact form tests write leads');
  artisan('cache:clear'); // resets the limiter between tests
});

test('a filled honeypot looks successful but stores nothing', async ({ request }) => {
  const before = leads();
  expect(await subscribe(request, { aldar_hp: 'http://spam.example' })).toMatchObject({ success: true });
  expect(leads()).toBe(before);
});

test('the sixth successful submission within ten minutes is refused', async ({ request }) => {
  const before = leads();
  for (let i = 0; i < 5; i++) {
    expect(await subscribe(request)).toMatchObject({ success: true });
  }
  const refused = await subscribe(request);
  expect(refused.success).toBe(false);
  expect(refused.message).toBeTruthy();
  expect(leads()).toBe(before + 5);
});

test('every contact form renders the honeypot field', async ({ page }) => {
  for (const url of ['/en/contact-us', '/en']) {
    await page.goto(url);
    const forms = page.locator('form[action*="/contact-us/"]');
    const count = await forms.count();
    expect(count).toBeGreaterThan(0);
    for (let i = 0; i < count; i++) {
      await expect(forms.nth(i).locator('input[name="aldar_hp"]')).toHaveCount(1);
      await expect(forms.nth(i).locator('input[name="aldar_hp"]')).not.toBeInViewport();
    }
  }
});

test('a real visitor can still subscribe through the footer form', async ({ page }) => {
  const before = leads();
  await page.goto('/en/contact-us');
  await page.fill('form.bloq-email input[name="emails"]', `footer-${Date.now()}@aldar.test`);
  await page.click('form.bloq-email button[type="submit"]');
  await expect.poll(leads, { timeout: 15_000 }).toBe(before + 1);
});
