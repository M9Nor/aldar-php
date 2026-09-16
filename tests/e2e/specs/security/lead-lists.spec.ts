import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { sql } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S5, S12, P1-R39. Synthetic leads only (aldar.test addresses): planted here, removed in afterAll.
const XSS_EMAIL = 's5-probe@aldar.test';
const LINK_EMAIL = 's5-link@aldar.test';
const UTF8_EMAIL = 's12-probe@aldar.test';
const MARKER = `S5-${Date.now()}`;
const MESSAGE = `${MARKER} <img src=x onerror="window.__s5=1"> & "q" 'a'`;
const JS_LINK = 'javascript://%0Awindow.__s5link=1';
const HTTP_LINK = 'https://example.invalid/s5-page';
// U+1F600 as a CESU-8 surrogate pair: MariaDB's utf8mb4 stores these bytes, json_encode rejects them.
const INVALID_UTF8_HEX = 'EDA0BDEDB880';
// No dump lead is this old, so the list's own date filter isolates the planted lead.
const UTF8_DAY = { 'filter[0][name]': 'daterange', 'filter[0][value]': '01/01/2001 - 01/01/2001' };

const quote = (value: string) => `'${value.replace(/\\/g, '\\\\').replace(/'/g, "''")}'`;
const removeFixtures = () =>
  sql(`DELETE FROM contact_us WHERE email IN (${[XSS_EMAIL, LINK_EMAIL, UTF8_EMAIL].map(quote).join(', ')})`);

test.beforeAll(() => {
  requireLocal('plants lead fixtures in the local database');
  removeFixtures();
  // A future date puts both S5 leads on page 1: the list sorts by date, newest first.
  sql(`INSERT INTO contact_us (sender, email, phone, description, link, created_at, updated_at) VALUES
    ('S5 Probe', ${quote(XSS_EMAIL)}, '0', ${quote(MESSAGE)}, ${quote(JS_LINK)}, '2030-01-01 12:00:00', '2030-01-01 12:00:00'),
    ('S5 Link', ${quote(LINK_EMAIL)}, '0', ${quote(`${MARKER} plain`)}, ${quote(HTTP_LINK)}, '2030-01-01 12:00:01', '2030-01-01 12:00:01')`);
  sql(`INSERT INTO contact_us (sender, email, phone, description, created_at, updated_at) VALUES
    ('S12 Probe', ${quote(UTF8_EMAIL)}, '0', CONCAT('S12 probe ', UNHEX('${INVALID_UTF8_HEX}'), ' end'), '2001-01-01 12:00:00', '2001-01-01 12:00:00')`);
  // Precondition: the invalid bytes really reached the column, so the S12 test cannot pass vacuously.
  expect(sql(`SELECT HEX(description) FROM contact_us WHERE email = ${quote(UTF8_EMAIL)}`)).toContain(INVALID_UTF8_HEX);
});
test.afterAll(() => removeFixtures());

test('the lead list JSON escapes a visitor message (S5)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const table = page.waitForResponse(r => r.url().includes('/en/admin/projects/data_requests'));
  await page.goto('/en/admin/projects/requests');
  const json = (await (await table).json()) as { data: Array<Record<string, string>> };
  const row = json.data.find(r => r.email === XSS_EMAIL);
  expect(row, 'the planted lead is on page 1').toBeDefined();
  expect(row!.breef).toBe(`${MARKER} &lt;img src=x onerror=&quot;window.__s5=1&quot;&gt; &amp; &quot;q&quot; &#039;a&#039;`);
});

test('the lead list shows the message as text and links only http(s) addresses (S5)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  await page.goto('/en/admin/projects/requests');
  const body = page.locator('#datatable tbody');
  await expect(body).toContainText(MARKER);
  await expect(body).toContainText(MESSAGE);
  await expect(page.locator('#datatable img[src="x"]')).toHaveCount(0);
  await page.waitForLoadState('networkidle');
  expect(await page.evaluate(() => (window as unknown as { __s5?: number }).__s5)).toBeUndefined();
  // The sender cells legitimately use href="javascript:;" — only a javascript:// link is the attack.
  await expect(page.locator('#datatable a[href^="javascript://"]')).toHaveCount(0);
  await expect(body).toContainText(JS_LINK);
  await expect(page.locator(`#datatable a[href="${HTTP_LINK}"]`)).toHaveCount(1);
});

test('both lead lists and both lead summaries answer valid JSON for a lead with invalid UTF-8 (S12, P1-R39)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const id = sql(`SELECT id FROM contact_us WHERE email = ${quote(UTF8_EMAIL)}`);
  for (const section of ['projects', 'opportunity']) {
    const list = await page.request.get(`/en/admin/${section}/data_requests`, {
      headers: AJAX_HEADERS,
      params: { draw: '1', start: '0', length: '50', ...UTF8_DAY },
    });
    expect(list.status(), `${section} list status`).toBe(200);
    const json = JSON.parse(await list.text()) as { error?: string; recordsFiltered: number; data: Array<Record<string, string>> };
    expect(json.error, `${section} list error`).toBeUndefined();
    expect(json.recordsFiltered, `${section} filtered rows`).toBe(1);
    expect(json.data[0].breef).toContain('S12 probe');
    expect(json.data[0].breef).toContain('�');

    const summary = await page.request.get(`/en/admin/${section}/request_summary`, { headers: AJAX_HEADERS, params: { model: id } });
    expect(summary.status(), `${section} summary status`).toBe(200);
    expect((JSON.parse(await summary.text()) as { success: boolean }).success).toBe(true);
  }
});
