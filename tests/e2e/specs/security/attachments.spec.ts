import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken, csrfToken } from '../../support/csrf';
import { appShell, sql, sqlScalar } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { jpegFixture } from '../../support/fixtures';

const PHP_PAYLOAD = Buffer.from('<?php echo "pwned-attachment";');
const UPLOADS = 'storage/app/public/uploads';

test.beforeAll(() => requireLocal('attachment tests write files'));
test.afterAll(() => {
  appShell(`grep -rl "pwned-attachment" ${UPLOADS} 2>/dev/null | xargs -r rm -f`);
  appShell(`rm -rf ${UPLOADS}/attachments/e2e`);
});

test('anonymous upload is refused and writes nothing', async ({ request }) => {
  const token = await anonymousCsrfToken(request);
  const response = await request.post('/en/admin/attachments/store', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
    multipart: {
      attachment: { name: 'probe.php', mimeType: 'application/x-php', buffer: PHP_PAYLOAD },
      validation_rules: 'nullable',
      sub_folder: '../projects',
    },
  });

  expect(response.status()).toBe(401);
  expect(appShell(`grep -rl "pwned-attachment" ${UPLOADS} 2>/dev/null | wc -l`)).toBe('0');
});

test('anonymous delete is refused and keeps the attachment', async ({ request }) => {
  appShell(`mkdir -p ${UPLOADS}/attachments/e2e && printf x > ${UPLOADS}/attachments/e2e/keep.jpg`);
  sql("INSERT INTO cms_attachments (type, filename, uid, size, mime, created_at, updated_at) VALUES ('TEMP', 'keep.jpg', 'attachments/e2e/keep.jpg', 1, 'image/jpeg', NOW(), NOW())");
  const id = sql("SELECT id FROM cms_attachments WHERE uid = 'attachments/e2e/keep.jpg' ORDER BY id DESC LIMIT 1");

  const token = await anonymousCsrfToken(request);
  const response = await request.post('/en/admin/attachments/delete', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
    form: { file_id: id },
  });

  expect(response.status()).toBe(401);
  expect(sqlScalar(`SELECT COUNT(*) FROM cms_attachments WHERE id = ${Number(id)}`)).toBe(1);
  expect(appShell(`test -f ${UPLOADS}/attachments/e2e/keep.jpg && echo present`)).toBe('present');
});

test('staff uploads ignore client rules, folder and filename', async ({ page }) => {
  await loginAs(page, 'parity-admin');
  const token = await csrfToken(page);
  const headers = { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token };

  const php = await page.request.post('/en/admin/attachments/store', {
    headers,
    multipart: {
      attachment: { name: 'shell.php', mimeType: 'application/x-php', buffer: PHP_PAYLOAD },
      validation_rules: 'nullable',
      sub_folder: '../projects',
    },
  });
  expect(php.status()).toBe(422);
  expect(appShell(`grep -rl "pwned-attachment" ${UPLOADS} 2>/dev/null | wc -l`)).toBe('0');

  const jpeg = await page.request.post('/en/admin/attachments/store', {
    headers,
    multipart: {
      attachment: { name: 'photo.jpg', mimeType: 'image/jpeg', buffer: jpegFixture() },
      validation_rules: 'required|image|max:1024|mimes:jpeg,jpg,png',
      sub_folder: '../projects',
    },
  });
  expect(jpeg.status()).toBe(200);
  const { attachment } = await jpeg.json();
  const uid = sql(`SELECT uid FROM cms_attachments WHERE id = ${Number(attachment)}`);
  expect(uid).toMatch(/^attachments\/general\/[A-Za-z0-9]{40}\.jpe?g$/);
  expect(sql(`SELECT filename FROM cms_attachments WHERE id = ${Number(attachment)}`)).toBe('photo.jpg');

  appShell(`rm -f ${UPLOADS}/${uid}`);
  sql(`DELETE FROM cms_attachments WHERE id = ${Number(attachment)}`);
});
