import { randomBytes } from 'node:crypto';
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken, csrfToken } from '../../support/csrf';
import { appShell, sql } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S20: a password sent to the user forms never reaches storage/logs.
test('user store, update and profile update never write the password to the logs', async ({ page, request }) => {
  requireLocal('reads storage/logs in the app container');
  const linesMatching = (text: string) => Number(appShell(`grep -rh -- '${text}' storage/logs 2>/dev/null | wc -l`));

  // Non-vacuous: prove that a web request's log lines land in storage/logs (the honeypot logs at info).
  const honeypotBefore = linesMatching('dropped by the honeypot');
  const anonymousToken = await anonymousCsrfToken(request);
  await request.post('/en/contact-us/subscribe', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': anonymousToken },
    multipart: { emails: 's20-canary@aldar.test', aldar_hp: 'filled' },
  });
  expect(linesMatching('dropped by the honeypot')).toBe(honeypotBefore + 1);

  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const token = await csrfToken(page);
  const adminId = sql("SELECT id FROM users WHERE username = 'parity-admin'");
  const superadminId = sql("SELECT id FROM users WHERE username = 'parity-superadmin'");
  // 15 characters, inside the 8-20 rule; the confirmation differs, so validation fails and nothing is saved.
  const secret = `S20${randomBytes(6).toString('hex')}`;
  const body = { _token: token, password: secret, password_confirmation: `${secret}x` };

  for (const url of ['/en/admin/users/store', `/en/admin/users/${adminId}/update`, `/en/admin/users/${superadminId}/update_profile`]) {
    const response = await page.request.post(url, { headers: AJAX_HEADERS, form: body });
    expect(response.status(), url).toBe(422);
  }

  expect(linesMatching(secret)).toBe(0);
});
