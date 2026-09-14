import { randomBytes } from 'node:crypto';
import { test, expect } from '@playwright/test';

// Read-only: safe against any environment. The template mail scripts under public/modules
// put request input into mail() headers; the web server must refuse every PHP file there.

test('the template mail scripts under /modules are refused', async ({ request }) => {
  expect((await request.get('/modules/frontend/form/process-contact.php')).status()).toBe(403);
});

test('any PHP file name under /modules is refused, even one that does not exist', async ({ request }) => {
  expect((await request.get(`/modules/verify-${randomBytes(8).toString('hex')}.php`)).status()).toBe(403);
});

test('static assets under /modules are still served', async ({ request }) => {
  const response = await request.get('/modules/frontend/css/font-awesome.min.css');
  expect(response.status()).toBe(200);
  expect(response.headers()['content-type']).toMatch(/^text\/css/);
});
