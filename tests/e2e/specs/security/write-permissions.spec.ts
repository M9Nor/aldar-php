import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { sql } from '../../support/docker';
import { BASE_URL, E2E_PASSWORD, requireLocal } from '../../support/env';

// S13: admin POST routes give each role the expected outcome and change nothing.
// Ids name rows in the committed dump (PARITY.md, "When the dump changes").
const REFERER = `${BASE_URL}/en/parity-referer`;
const PRINCIPALS = ['anonymous', 'parity-admin', 'parity-superadmin'] as const;
type Principal = (typeof PRINCIPALS)[number];
type Outcomes = Record<Principal, string>;

const adminDenied = (superadmin: string): Outcomes => ({ anonymous: '302 login', 'parity-admin': '302 back', 'parity-superadmin': superadmin });
const bothRoles = (both: string): Outcomes => ({ anonymous: '302 login', 'parity-admin': both, 'parity-superadmin': both });

const PROBES: Array<{ url: string; form?: Record<string, string>; expected: Outcomes }> = [
  // Abilities ADMIN lacks; empty bodies fail validation for SUPERADMIN.
  { url: '/en/admin/categories/agents/store', expected: adminDenied('422') },
  { url: '/en/admin/categories/filters/store', expected: adminDenied('422') },
  { url: '/en/admin/categories/opportunity_classifications/store', expected: adminDenied('422') },
  { url: '/en/admin/categories/agents/608/update', expected: adminDenied('422') },
  { url: '/en/admin/categories/filters/598/update', expected: adminDenied('422') },
  { url: '/en/admin/contents/achievements/store', expected: adminDenied('422') },
  { url: '/en/admin/landing_pages/store', expected: adminDenied('422') },
  { url: '/en/admin/notification/postCreate', expected: adminDenied('422') },
  { url: '/en/admin/roles/update', form: { model: '3' }, expected: adminDenied('422') },
  // No-op writes: 139 is already special, 133 is already not special.
  { url: '/en/admin/projects/special/139', expected: adminDenied('200') },
  { url: '/en/admin/projects/not_special/133', expected: adminDenied('200') },
  // Read-only POST: the notification DataTables list (notifications.view since S21).
  { url: '/en/admin/notification', expected: adminDenied('200') },
  // Controls: reachable and never 500.
  { url: '/en/admin/tags/destroy/999999999', expected: bothRoles('404') },
  { url: '/en/admin/tags/save', expected: bothRoles('422') },
];

// Every table the probes above could write to.
const CHECKSUM = 'CHECKSUM TABLE cms_categories, cms_category_translations, cms_categorizables, cms_contents, cms_content_translations, '
  + 'landing_pages, landing_page_translations, perms_roles, perms_role_translations, be_projects, be_projects_translations, '
  + 'notif_notifications, notif_notification_translations, notif_notification_receivers, cms_tags, cms_tag_translations';

async function signIn(request: APIRequestContext, username: string): Promise<void> {
  const html = await (await request.get('/en/authenticate/login')).text();
  const token = html.match(/name="_token" value="([^"]+)"/)![1];
  const response = await request.post('/en/authenticate/login', {
    headers: AJAX_HEADERS,
    form: { _token: token, identity: username, password: E2E_PASSWORD },
  });
  expect(response.status(), `login as ${username}`).toBe(200);
}

/** Same mapping as permissions-matrix.spec.ts: "200", "404", "422" ... or "302 login" / "302 back" / "302 <path>". */
async function postOutcome(request: APIRequestContext, url: string, form: Record<string, string> = {}): Promise<string> {
  const response = await request.post(url, {
    form: { _token: await anonymousCsrfToken(request), ...form },
    headers: { ...AJAX_HEADERS, Referer: REFERER },
    maxRedirects: 0,
  });
  const status = response.status();
  if (status < 300 || status >= 400) return String(status);
  const location = response.headers()['location'] ?? '';
  if (/\/authenticate\/login$/.test(location)) return `${status} login`;
  if (location === REFERER) return `${status} back`;
  return `${status} ${location.replace(BASE_URL, '')}`;
}

test('admin POST routes give each role its expected outcome and change nothing', async ({ playwright }) => {
  requireLocal('uses the local parity accounts');
  test.setTimeout(5 * 60_000);

  const contexts = {} as Record<Principal, APIRequestContext>;
  for (const principal of PRINCIPALS) {
    contexts[principal] = await playwright.request.newContext({ baseURL: BASE_URL });
    if (principal !== 'anonymous') await signIn(contexts[principal], principal);
  }

  const before = sql(CHECKSUM);
  const actual: Record<string, Outcomes> = {};
  const expected: Record<string, Outcomes> = {};
  for (const probe of PROBES) {
    expected[probe.url] = probe.expected;
    actual[probe.url] = {} as Outcomes;
    for (const principal of PRINCIPALS) {
      actual[probe.url][principal] = await postOutcome(contexts[principal], probe.url, probe.form);
    }
  }
  const after = sql(CHECKSUM);
  for (const context of Object.values(contexts)) await context.dispose();

  expect(actual).toEqual(expected);
  expect(after).toBe(before);
});
