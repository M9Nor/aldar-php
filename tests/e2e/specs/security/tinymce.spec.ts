import { test, expect, Page } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, csrfToken } from '../../support/csrf';
import { appShell } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { jpegFixture } from '../../support/fixtures';

const TINYMCE_DIR = 'public/graph/uploads/original/tinymce';
const MARKER = '/tmp/e2e-tinymce-marker';

async function upload(page: Page, filename: string, content: Buffer) {
  const token = await csrfToken(page);
  const response = await page.request.post('/en/admin/tinymce/uploader', {
    headers: AJAX_HEADERS,
    form: { _token: token, 'tinymce[filename]': filename, 'tinymce[base64]': content.toString('base64') },
  });
  return response.json();
}

test.beforeAll(() => {
  requireLocal('TinyMCE tests write files');
  appShell(`touch ${MARKER}`);
});
test.afterAll(() => appShell(`find ${TINYMCE_DIR} -type f -newer ${MARKER} -delete; rm -f ${MARKER}`));

test('staff cannot upload PHP, even disguised as an image', async ({ page }) => {
  await loginAs(page, 'parity-admin');
  const payload = Buffer.from('<?php echo "pwned-tinymce";');

  expect(await upload(page, 'shell.php', payload)).toMatchObject({ success: false });
  expect(await upload(page, 'image.jpg', payload)).toMatchObject({ success: false });
  expect(appShell(`grep -rl "pwned-tinymce" ${TINYMCE_DIR} 2>/dev/null | wc -l`)).toBe('0');
});

test('staff image uploads get a server-generated name and are served', async ({ page, request }) => {
  await loginAs(page, 'parity-admin');
  const body = await upload(page, '../../evil name.php.jpg', jpegFixture());

  expect(body.success).toBe(true);
  expect(body.location).toMatch(/^\/graph\/uploads\/original\/tinymce\/[A-Za-z0-9]{40}\.jpg$/);
  const image = await request.get(body.location);
  expect(image.status()).toBe(200);
  expect(image.headers()['content-type']).toContain('image/jpeg');
});

test('scripts placed under public/graph are never executed', async ({ request }) => {
  appShell(`printf '<?php echo "executed";' > ${TINYMCE_DIR}/probe-exec.php`);
  const response = await request.get('/graph/uploads/original/tinymce/probe-exec.php');
  expect(response.status()).toBe(403);
  expect(await response.text()).not.toContain('executed');
});
