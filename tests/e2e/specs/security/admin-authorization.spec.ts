import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { appShell, sql, sqlScalar } from '../../support/docker';
import { BASE_URL, E2E_PASSWORD, requireLocal } from '../../support/env';

// S21: lead data endpoints and the named admin POST routes enforce the ability of their admin page.
// Every row this spec touches is a synthetic fixture (s21-probe, aldar.test), deleted in afterAll.
const REFERER = `${BASE_URL}/en/parity-referer`;
const CLIENT = 'parity-client-s21';
const LEAD_EMAIL = 's21-lead@aldar.test';
const FORM_EMAIL = 's21-form@aldar.test';
const MARK = 's21-probe';
// Dump rows the fixtures hang off (PARITY.md, "When the dump changes").
const PROJECT_ID = 133;
const OPPORTUNITY_ID = 320;
const LANDING_PAGE_ID = 11;
const ARTICLE_ID = 251;

type FixtureId = 'lead' | 'form' | 'projectPayment' | 'opportunityPayment' | 'projectPrice' | 'opportunityPrice' | 'timeline' | 'attachment';
const ids = {} as Record<FixtureId, string>;

async function signIn(request: APIRequestContext, username: string): Promise<void> {
  const html = await (await request.get('/en/authenticate/login')).text();
  const token = html.match(/name="_token" value="([^"]+)"/)![1];
  const response = await request.post('/en/authenticate/login', {
    headers: AJAX_HEADERS,
    form: { _token: token, identity: username, password: E2E_PASSWORD },
  });
  expect(response.status(), `login as ${username}`).toBe(200);
}

/** "200", "404", "422" ... or "302 login" / "302 back" / "302 <path>", as in the permissions matrix. */
async function outcome(request: APIRequestContext, method: 'GET' | 'POST', url: string, form: Record<string, string> = {}): Promise<string> {
  const options = { headers: { ...AJAX_HEADERS, Referer: REFERER }, maxRedirects: 0 };
  const response = method === 'GET'
    ? await request.get(url, options)
    : await request.post(url, { ...options, form: { _token: await anonymousCsrfToken(request), ...form } });
  const status = response.status();
  if (status < 300 || status >= 400) return String(status);
  const location = response.headers()['location'] ?? '';
  if (/\/authenticate\/login$/.test(location)) return `${status} login`;
  if (location === REFERER) return `${status} back`;
  return `${status} ${location.replace(BASE_URL, '')}`;
}

function removeFixtures(): void {
  sql(`DELETE FROM contact_us WHERE email = '${LEAD_EMAIL}'`);
  sql(`DELETE FROM property_form WHERE advertisers_email = '${FORM_EMAIL}'`);
  sql(`DELETE FROM paying_method WHERE type = '${MARK}'`);
  sql(`DELETE FROM price WHERE balance = '${MARK}'`);
  sql(`DELETE FROM timeline WHERE title = '${MARK}'`);
  sql(`DELETE FROM cms_external_attachments WHERE name = '${MARK}'`);
  sql(`DELETE a FROM perms_assigned_roles a JOIN users u ON u.id = a.entity_id WHERE u.username = '${CLIENT}' AND a.entity_type = CONCAT('App', CHAR(92), 'User')`);
  sql(`DELETE FROM users WHERE username = '${CLIENT}'`);
}

const fixturesLeft = () => sqlScalar(`SELECT
  (SELECT COUNT(*) FROM paying_method WHERE type = '${MARK}') +
  (SELECT COUNT(*) FROM price WHERE balance = '${MARK}') +
  (SELECT COUNT(*) FROM timeline WHERE title = '${MARK}') +
  (SELECT COUNT(*) FROM cms_external_attachments WHERE name = '${MARK}' AND deleted_at IS NULL)`);

const leadReads = (section: 'projects' | 'opportunity') => [
  `/en/admin/${section}/data_requests?draw=1&start=0&length=1`,
  `/en/admin/${section}/data_properties?draw=1&start=0&length=1`,
  `/en/admin/${section}/request_summary?model=${ids.lead}`,
  `/en/admin/${section}/properties_summary?model=${ids.form}`,
  `/en/admin/${section}/show_details/${ids.form}`,
];

const childDeletes = () => [
  { url: '/en/admin/contents/delete-attachemnt', form: { attachment_id: ids.attachment } },
  { url: '/en/admin/landing_pages/delete-timeline', form: { timeline_id: ids.timeline } },
  { url: '/en/admin/projects/delete-payment', form: { payment_id: ids.projectPayment } },
  { url: '/en/admin/projects/delete-price', form: { price_id: ids.projectPrice } },
  { url: '/en/admin/opportunity/delete-payment', form: { payment_id: ids.opportunityPayment } },
  { url: '/en/admin/opportunity/delete-price', form: { price_id: ids.opportunityPrice } },
];
// Empty bodies: tags/save fails validation, and the two notification endpoints only read (getList marks the caller's own rows seen).
const otherPosts = ['/en/admin/tags/save', '/en/admin/notification', '/en/admin/notification/getList'];

test.beforeAll(() => {
  requireLocal('creates fixture rows and a fixture account in the local database');
  removeFixtures();
  const hash = appShell(`php -r 'echo password_hash("${E2E_PASSWORD}", PASSWORD_BCRYPT);'`);
  sql(`INSERT INTO users (username, email, status, verification_code, password, email_verified_at, created_at, updated_at)
       VALUES ('${CLIENT}', '${CLIENT}@aldar.test', 'ACTIVE', 'VERIFIED', '${hash}', NOW(), NOW(), NOW())`);
  sql(`INSERT INTO perms_assigned_roles (role_id, entity_id, entity_type)
       SELECT r.id, u.id, CONCAT('App', CHAR(92), 'User') FROM users u JOIN perms_roles r ON r.name = 'CLIENT' WHERE u.username = '${CLIENT}'`);
  sql(`INSERT INTO contact_us (sender, email, phone, description, created_at, updated_at)
       VALUES ('S21 Probe', '${LEAD_EMAIL}', '0', 'S21 probe lead', NOW(), NOW())`);
  sql(`INSERT INTO property_form (advertisers_name, advertisers_email, advertisers_phone, property_explanation, created_at, updated_at)
       VALUES ('S21 Probe', '${FORM_EMAIL}', '0', 'S21 probe form', NOW(), NOW())`);
  sql(`INSERT INTO paying_method (project_id, type, first_pay, created_at, updated_at)
       VALUES (${PROJECT_ID}, '${MARK}', '0', NOW(), NOW()), (${OPPORTUNITY_ID}, '${MARK}', '0', NOW(), NOW())`);
  sql(`INSERT INTO price (project_id, balance_id, balance, is_sold, created_at, updated_at)
       SELECT p.id, c.id, '${MARK}', 'no', NOW(), NOW()
       FROM be_projects p JOIN (SELECT MIN(id) AS id FROM cms_contents WHERE type = 'currencies') c
       WHERE p.id IN (${PROJECT_ID}, ${OPPORTUNITY_ID})`);
  sql(`INSERT INTO timeline (landing_id, sort_order, language, title, created_at) VALUES (${LANDING_PAGE_ID}, 0, 'en', '${MARK}', NOW())`);
  sql(`INSERT INTO cms_external_attachments (type, name, link, attachable_type, attachable_id, created_at, updated_at)
       VALUES ('file', '${MARK}', 'https://example.invalid/${MARK}', CONCAT('Modules', CHAR(92), 'Cms', CHAR(92), 'Entities', CHAR(92), 'Content'), ${ARTICLE_ID}, NOW(), NOW())`);
  ids.lead = sql(`SELECT id FROM contact_us WHERE email = '${LEAD_EMAIL}'`);
  ids.form = sql(`SELECT id FROM property_form WHERE advertisers_email = '${FORM_EMAIL}'`);
  ids.projectPayment = sql(`SELECT id FROM paying_method WHERE type = '${MARK}' AND project_id = ${PROJECT_ID}`);
  ids.opportunityPayment = sql(`SELECT id FROM paying_method WHERE type = '${MARK}' AND project_id = ${OPPORTUNITY_ID}`);
  ids.projectPrice = sql(`SELECT id FROM price WHERE balance = '${MARK}' AND project_id = ${PROJECT_ID}`);
  ids.opportunityPrice = sql(`SELECT id FROM price WHERE balance = '${MARK}' AND project_id = ${OPPORTUNITY_ID}`);
  ids.timeline = sql(`SELECT id FROM timeline WHERE title = '${MARK}'`);
  ids.attachment = sql(`SELECT id FROM cms_external_attachments WHERE name = '${MARK}'`);
});
test.afterAll(() => removeFixtures());

test('only holders of the leads page ability read leads through the data endpoints', async ({ playwright }) => {
  const admin = await playwright.request.newContext({ baseURL: BASE_URL });
  const superadmin = await playwright.request.newContext({ baseURL: BASE_URL });
  try {
    await signIn(admin, 'parity-admin');
    await signIn(superadmin, 'parity-superadmin');
    const urls = [...leadReads('projects'), ...leadReads('opportunity')];
    // SUPERADMIN first: on unchanged code these must already pass, so a failure here is not an S21 effect.
    for (const url of urls) {
      expect(await outcome(superadmin, 'GET', url), `parity-superadmin ${url}`).toBe('200');
    }
    for (const url of urls) {
      expect(await outcome(admin, 'GET', url), `parity-admin ${url}`).toBe('302 back');
    }
  } finally {
    await admin.dispose();
    await superadmin.dispose();
  }
});

test('accounts without the page ability cannot use the admin POST routes S21 names', async ({ playwright }) => {
  const client = await playwright.request.newContext({ baseURL: BASE_URL });
  const admin = await playwright.request.newContext({ baseURL: BASE_URL });
  try {
    await signIn(client, CLIENT);
    await signIn(admin, 'parity-admin');
    expect(fixturesLeft()).toBe(6);
    for (const { url, form } of childDeletes()) {
      expect(await outcome(client, 'POST', url, form), `${CLIENT} ${url}`).toBe('302 back');
    }
    for (const url of otherPosts) {
      expect(await outcome(client, 'POST', url), `${CLIENT} ${url}`).toBe('302 back');
    }
    // ADMIN lacks landingpages.* and notifications.view; it holds the abilities of the other routes.
    expect(await outcome(admin, 'POST', '/en/admin/landing_pages/delete-timeline', { timeline_id: ids.timeline })).toBe('302 back');
    expect(await outcome(admin, 'POST', '/en/admin/notification')).toBe('302 back');
    expect(await outcome(admin, 'POST', '/en/admin/notification/getList')).toBe('302 back');
    expect(fixturesLeft()).toBe(6);
  } finally {
    await client.dispose();
    await admin.dispose();
  }
});

test('SUPERADMIN still deletes payments, prices, timelines and attachments, and still reaches the tag and notification endpoints', async ({ playwright }) => {
  const superadmin = await playwright.request.newContext({ baseURL: BASE_URL });
  try {
    await signIn(superadmin, 'parity-superadmin');
    for (const { url, form } of childDeletes()) {
      expect(await outcome(superadmin, 'POST', url, form), url).toBe('200');
    }
    expect(fixturesLeft()).toBe(0);
    expect(await outcome(superadmin, 'POST', '/en/admin/tags/save'), 'tags/save with an empty body').toBe('422');
    expect(await outcome(superadmin, 'POST', '/en/admin/notification'), 'notification list').toBe('200');
    expect(await outcome(superadmin, 'POST', '/en/admin/notification/getList'), 'notification feed').toBe('200');
  } finally {
    await superadmin.dispose();
  }
});
