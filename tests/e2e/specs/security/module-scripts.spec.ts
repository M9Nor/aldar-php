import { existsSync } from 'node:fs';
import path from 'node:path';
import { randomBytes } from 'node:crypto';
import { test, expect } from '@playwright/test';

// Read-only: safe against any environment. Template mail scripts under public/modules put request input into
// mail() headers (H9). The dead contact-form copies are deleted (S16); the web server refuses every other PHP file there.
const REPO_ROOT = path.resolve(__dirname, '../../../..');

test('the deleted contact-form scripts are not served, and the landing-page mail scripts are refused', async ({ request }) => {
  for (const name of ['process-contact.php', 'quote-contact.php']) {
    // With form/ gone the request falls through to Laravel's locale redirect, which ends in a 404.
    expect((await request.get(`/modules/frontend/form/${name}`)).status(), name).toBe(404);
  }
  for (const name of ['contact-form-process.php', 'quote-form-process.php']) {
    expect((await request.get(`/modules/frontend/landingpage/libs/${name}`)).status(), name).toBe(403);
  }
});

test('the dead contact-form scripts are gone from the module sources and from public/modules (S16)', async () => {
  for (const relative of [
    'Modules/Frontend/Resources/assets/form/process-contact.php',
    'Modules/Frontend/Resources/assets/form/quote-contact.php',
    'public/modules/frontend/form/process-contact.php',
    'public/modules/frontend/form/quote-contact.php',
  ]) {
    expect(existsSync(path.join(REPO_ROOT, relative)), relative).toBe(false);
  }
});

test('any PHP file name under /modules is refused, even one that does not exist', async ({ request }) => {
  expect((await request.get(`/modules/verify-${randomBytes(8).toString('hex')}.php`)).status()).toBe(403);
});

test('static assets under /modules are still served', async ({ request }) => {
  const response = await request.get('/modules/frontend/css/font-awesome.min.css');
  expect(response.status()).toBe(200);
  expect(response.headers()['content-type']).toMatch(/^text\/css/);
});
