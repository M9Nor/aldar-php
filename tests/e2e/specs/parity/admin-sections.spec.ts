import { test, expect, Page } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { requireLocal } from '../../support/env';
import { ADMIN_SECTIONS } from '../../parity/admin-urls';

// yajra/laravel-datatables adds these keys only while APP_DEBUG is true.
const DEBUG_ONLY_KEYS = new Set(['input', 'queries']);

interface DataTableJson {
  recordsTotal: number;
  recordsFiltered: number;
  data: Array<Record<string, unknown> & { actions?: { icons?: Array<{ action: string }>; dropdown?: Array<{ action: string }> } }>;
  [key: string]: unknown;
}

// One signed-in page for all sections: logging in per test would add a minute.
let page: Page;

test.beforeAll(async ({ browser }) => {
  requireLocal('uses the local parity accounts');
  page = await (await browser.newContext()).newPage();
  await loginAs(page, 'parity-superadmin');
});
test.afterAll(async () => page?.context().close());

for (const section of ADMIN_SECTIONS) {
  test(`admin section ${section.name}`, async () => {
    const tableResponse = page.waitForResponse(async response => {
      if (!['xhr', 'fetch'].includes(response.request().resourceType())) return false;
      const body = await response.json().catch(() => null);
      return body !== null && typeof body === 'object' && 'draw' in body;
    }, { timeout: 20_000 });
    tableResponse.catch(() => undefined);

    const response = await page.goto(section.url);
    const shape: Record<string, unknown> = { status: response?.status() };

    if (response?.status() === 200) {
      const table = await tableResponse;
      const json = (await table.json()) as DataTableJson;
      const rows = json.data;

      if (table.request().method() !== 'GET') {
        throw new Error(`Expected the DataTables request for ${section.name} to be GET, got ${table.request().method()}`);
      }
      // Row keys and action kinds come from every row: page 1 of an unordered query is not a
      // stable sample. A single length=-1 (skip-paging) request is not safe against this app:
      // it exhausts the web SAPI's 128M memory_limit for larger tables (verified on `projects`,
      // 198 rows). Walk the same endpoint page by page instead, at its own page size.
      // structureOnly tables (leads, users, forms) have a model-fixed schema and no state-dependent actions; walking thousands of customer rows adds nothing.
      let everyRow: DataTableJson['data'];
      if (section.structureOnly) {
        everyRow = rows;
      } else {
        const pageLength = Number(new URL(table.url()).searchParams.get('length')) || rows.length || 1;
        everyRow = [];
        for (let start = 0; everyRow.length < json.recordsFiltered; start += pageLength) {
          const pageUrl = new URL(table.url());
          pageUrl.searchParams.set('start', String(start));
          pageUrl.searchParams.set('length', String(pageLength));
          const pageJson = (await (await page.request.get(pageUrl.toString(), { headers: AJAX_HEADERS })).json()) as DataTableJson;
          if ('error' in pageJson) {
            throw new Error(`DataTables error while walking ${section.name} at start=${start}`);
          }
          if (pageJson.data.length === 0) break;
          everyRow.push(...pageJson.data);
        }
        // An OFFSET walk over an unordered query can repeat or skip rows if the order shifts between pages; fail loudly rather than record a wrong union.
        if (everyRow.every(row => 'id' in row)) {
          const distinct = new Set(everyRow.map(row => String(row.id))).size;
          if (distinct !== json.recordsFiltered) {
            throw new Error(`Unstable DataTables walk for ${section.name}: ${distinct} distinct ids over ${everyRow.length} rows, expected ${json.recordsFiltered}`);
          }
        }
      }

      shape.columns = (await page.locator('#datatable thead th').allInnerTexts()).map(text => text.replace(/\s+/g, ' ').trim());
      shape.table = {
        endpoint: new URL(table.url()).pathname,
        method: table.request().method(),
        status: table.status(),
        keys: Object.keys(json).filter(key => !DEBUG_ONLY_KEYS.has(key)).sort(),
        ...(section.structureOnly ? {} : { recordsTotal: json.recordsTotal, rows: rows.length }),
        rowKeys: [...new Set(everyRow.flatMap(row => Object.keys(row)))].sort(),
        actions: [...new Set(everyRow.flatMap(row => [...(row.actions?.icons ?? []), ...(row.actions?.dropdown ?? [])].map(a => a.action)))].sort(),
      };
    }

    expect(JSON.stringify(shape, null, 2)).toMatchSnapshot(`${section.name}.json`);
  });
}

test.describe('authentication', () => {
  test('a wrong password is refused with the login_failed JSON', async ({ request }) => {
    requireLocal('uses the local parity accounts');
    const html = await (await request.get('/en/authenticate/login')).text();
    const token = html.match(/name="_token" value="([^"]+)"/)![1];
    const response = await request.post('/en/authenticate/login', {
      headers: AJAX_HEADERS,
      form: { _token: token, identity: 'parity-admin', password: 'not-the-password' },
    });
    expect(response.status()).toBe(401);
    expect(JSON.stringify(await response.json(), null, 2)).toMatchSnapshot('login-failed.json');
  });

  test('logout ends the session', async ({ page: staff }) => {
    requireLocal('uses the local parity accounts');
    await loginAs(staff, 'parity-admin');
    const token = await staff.locator('#logoutForm input[name="_token"]').getAttribute('value');
    const logout = await staff.request.post('/en/authenticate/logout', { form: { _token: token! }, maxRedirects: 0 });
    expect(logout.status()).toBe(302);
    const after = await staff.request.get('/en/admin', { maxRedirects: 0 });
    expect(after.status()).toBe(302);
    expect(after.headers()['location']).toMatch(/\/authenticate\/login$/);
  });
});
