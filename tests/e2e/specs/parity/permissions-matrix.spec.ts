import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS } from '../../support/csrf';
import { BASE_URL, E2E_PASSWORD, requireLocal } from '../../support/env';
import { MATRIX_URLS } from '../../parity/admin-urls';

// A fixed Referer makes redirect()->back() (the app's "permission denied" response) predictable.
const REFERER = `${BASE_URL}/en/parity-referer`;
const PRINCIPALS = ['anonymous', 'parity-admin', 'parity-superadmin'] as const;

async function signIn(request: APIRequestContext, username: string): Promise<void> {
  const html = await (await request.get('/en/authenticate/login')).text();
  const token = html.match(/name="_token" value="([^"]+)"/)![1];
  const response = await request.post('/en/authenticate/login', {
    headers: AJAX_HEADERS,
    form: { _token: token, identity: username, password: E2E_PASSWORD },
  });
  expect(response.status(), `login as ${username}`).toBe(200);
}

/** 200, 404, 500 ... or "302 login" / "302 back" / "302 <path>" for redirects. */
async function outcome(request: APIRequestContext, url: string): Promise<string> {
  const response = await request.get(url, { maxRedirects: 0, headers: { Referer: REFERER }, timeout: 60_000 });
  const status = response.status();
  if (status < 300 || status >= 400) return String(status);
  const location = response.headers()['location'] ?? '';
  if (/\/authenticate\/login$/.test(location)) return `${status} login`;
  if (location === REFERER) return `${status} back`;
  return `${status} ${location.replace(BASE_URL, '')}`;
}

test('admin GET routes give each role the same outcome as the Laravel 7 baseline', async ({ playwright }) => {
  requireLocal('uses the local parity accounts');
  test.setTimeout(10 * 60_000);

  const contexts: Record<string, APIRequestContext> = {};
  for (const principal of PRINCIPALS) {
    contexts[principal] = await playwright.request.newContext({ baseURL: BASE_URL });
    if (principal !== 'anonymous') await signIn(contexts[principal], principal);
  }

  const matrix: Record<string, Record<string, string>> = {};
  for (const url of MATRIX_URLS) {
    matrix[url] = {};
    for (const principal of PRINCIPALS) matrix[url][principal] = await outcome(contexts[principal], url);
  }
  for (const context of Object.values(contexts)) await context.dispose();

  expect(JSON.stringify(matrix, null, 2)).toMatchSnapshot('matrix.json');
});
