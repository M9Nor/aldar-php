import { test, expect, APIRequestContext } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S1: request-controlled page sizes are capped at the largest size the UI uses.
const resultCount = async (request: APIRequestContext, url: string) =>
  (JSON.parse(await (await request.get(url)).text()) as { results: unknown[] }).results.length;

test('the public Select2 endpoints never return more than 20 items per page', async ({ request }) => {
  // The dump has 57 named areas and 116 contents, so an uncapped request returns more than 20.
  expect(await resultCount(request, '/en/get-areas?items_per_page=999999')).toBe(20);
  expect(await resultCount(request, '/en/get-contents?type=all&items_per_page=999999')).toBe(20);
  expect(await resultCount(request, '/en/get-areas?items_per_page=20')).toBe(20);
  expect(await resultCount(request, '/en/get-areas')).toBe(15);
  expect(await resultCount(request, '/en/get-areas?items_per_page=abc')).toBe(15);
});

test('the lead list keeps its "All" page size beyond the 500-row DataTables cap', async ({ page }) => {
  requireLocal('uses the local parity accounts');
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const response = await page.request.get('/en/admin/projects/data_requests?draw=1&start=0&length=1000', { headers: AJAX_HEADERS });
  const json = JSON.parse(await response.text()) as { error?: string; recordsFiltered: number; data: unknown[] };
  expect(json.error).toBeUndefined();
  expect(json.recordsFiltered).toBeGreaterThan(1000);
  expect(json.data.length).toBe(1000);
});
