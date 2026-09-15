import { test, expect } from '@playwright/test';
import { BASE_URL } from '../../support/env';
import { loadInventory, snapshotName } from '../../parity/inventory';
import { normalizeHtml } from '../../parity/normalize';

// Read-only. Each URL is fetched once with a fresh cookie jar, exactly as the server sends it
// (no JavaScript, no redirects followed), normalised, and compared with its committed snapshot.
for (const entry of loadInventory()) {
  test(`${entry.kind} ${entry.path}`, async ({ request, page }) => {
    const response = await request.get(entry.path, { maxRedirects: 0 });
    const head = [
      `status: ${response.status()}`,
      `content-type: ${(response.headers()['content-type'] ?? '').toLowerCase()}`,
    ].join('\n');
    const body = await normalizeHtml(page, await response.text(), entry.kind, BASE_URL);
    expect(`${head}\n\n${body}`).toMatchSnapshot(snapshotName(entry.path));
  });
}
