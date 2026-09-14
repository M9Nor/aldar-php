import { test, expect } from '@playwright/test';

for (const worker of ['/service-worker.js', '/firebase-messaging-sw.js']) {
  test(`${worker} carries no vendor Firebase config and unregisters itself`, async ({ request }) => {
    const response = await request.get(worker);
    expect(response.status()).toBe(200);
    const body = await response.text();
    expect(body).not.toContain('binaa-prod');
    expect(body).not.toContain('firebase');
    expect(body).toContain('registration.unregister()');
  });
}
