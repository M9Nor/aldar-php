import { test, expect, Page } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { BASE_URL, requireLocal } from '../../support/env';
import { maskVolatileValues } from '../../parity/normalize';

// Row values of public, non-personal admin tables (Phase 1 addition, recorded on Laravel 7).
// Rows are fetched page by page and sorted by id, so query order does not matter.
const TABLES: Record<string, string> = {
  countries: '/en/admin/countries',
  cities: '/en/admin/cities',
  areas: '/en/admin/areas',
  'contents-faqs': '/en/admin/contents/faqs',
};

let page: Page;
test.beforeAll(async ({ browser }) => {
  requireLocal('uses the local parity accounts');
  page = await (await browser.newContext()).newPage();
  await loginAs(page, 'parity-superadmin');
});
test.afterAll(async () => page?.context().close());

for (const [name, url] of Object.entries(TABLES)) {
  test(`admin table values ${name}`, async () => {
    const tableResponse = page.waitForResponse(async response => {
      if (!['xhr', 'fetch'].includes(response.request().resourceType())) return false;
      const body = await response.json().catch(() => null);
      return body !== null && typeof body === 'object' && 'draw' in body;
    }, { timeout: 20_000 });
    expect((await page.goto(url))?.status()).toBe(200);
    const first = await tableResponse;
    const total = ((await first.json()) as { recordsFiltered: number }).recordsFiltered;
    const pageLength = Number(new URL(first.url()).searchParams.get('length')) || 50;

    const rows: Array<Record<string, unknown>> = [];
    for (let start = 0; rows.length < total; start += pageLength) {
      const pageUrl = new URL(first.url());
      pageUrl.searchParams.set('start', String(start));
      pageUrl.searchParams.set('length', String(pageLength));
      const json = await (await page.request.get(pageUrl.toString(), { headers: AJAX_HEADERS })).json();
      if ('error' in json) throw new Error(`DataTables error while walking ${name} at start=${start}`);
      if (json.data.length === 0) break;
      rows.push(...json.data);
    }
    expect(new Set(rows.map(row => String(row.id))).size, `${name}: distinct ids`).toBe(total);
    rows.sort((a, b) => Number(a.id) - Number(b.id));

    // Mask per string value: the same CSRF/origin masks as the golden master, applied before JSON escaping hides the quotes.
    const mask = (value: unknown): unknown =>
      typeof value === 'string' ? maskVolatileValues(value, BASE_URL)
        : Array.isArray(value) ? value.map(mask)
        : value !== null && typeof value === 'object' ? Object.fromEntries(Object.entries(value).map(([k, v]) => [k, mask(v)]))
        : value;
    const body = JSON.stringify(rows.map(mask), null, 2);
    expect(body).toMatchSnapshot(`${name}.json`);
  });
}
