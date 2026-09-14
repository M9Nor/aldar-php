import { APIRequestContext, Page } from '@playwright/test';

export const AJAX_HEADERS = { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' };

/** Token for the page's own session (works for logged-in pages too). */
export async function csrfToken(page: Page): Promise<string> {
  await page.goto('/en/contact-us');
  const token = await page.locator('input[name="_token"]').first().getAttribute('value');
  if (!token) throw new Error('No CSRF token on /en/contact-us');
  return token;
}

/** Token for a cookie-isolated API context with no login. */
export async function anonymousCsrfToken(request: APIRequestContext): Promise<string> {
  const html = await (await request.get('/en/contact-us')).text();
  const match = html.match(/name="_token" value="([^"]+)"/);
  if (!match) throw new Error('No CSRF token on /en/contact-us');
  return match[1];
}
