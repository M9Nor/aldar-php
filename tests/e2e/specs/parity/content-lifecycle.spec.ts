import { test, expect, Page } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { appShell, artisan, sql } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';
import { pngFixture } from '../../support/fixtures';
import { blockProduction } from '../../support/network';
import { normalizeHtml } from '../../parity/normalize';

const SLUG = 'parity-lifecycle-article';
const TITLE_EN = 'Parity lifecycle article';
const TITLE_AR = 'مقالة اختبار دورة الحياة';
const FORM_POST = /\/admin\/contents\/articles\/(store|\d+\/update)$/;

// P0-R9: ContentController@store saves uploads via Laravel's UploadedFile::store($type), which
// writes storage/app/public/uploads/articles/<40-char-random>.<ext> — a single path segment, no
// directory traversal possible. Guard the deletion so a corrupt/unexpected `image` column value
// (empty string, 'articles', '..', a path with spaces) can never turn into `rm -rf` on the shared
// 3.1 GB uploads directory.
const SAFE_ARTICLE_IMAGE = /^articles\/[A-Za-z0-9._-]+\.(png|jpe?g)$/;

function removeArticle(): void {
  for (const id of sql(`SELECT id FROM cms_contents WHERE slug = '${SLUG}'`).split('\n').filter(Boolean).map(Number)) {
    for (const image of sql(`SELECT image FROM cms_content_translations WHERE content_id = ${id} AND image IS NOT NULL`).split('\n').filter(Boolean)) {
      if (SAFE_ARTICLE_IMAGE.test(image)) {
        appShell(`rm -rf storage/app/public/uploads/${image} storage/app/public/uploads/.cache/${image}`);
      }
    }
    sql(`DELETE FROM cms_categorizables WHERE categorizable_id = ${id} AND categorizable_type LIKE '%Content'`);
    sql(`DELETE FROM cms_content_translations WHERE content_id = ${id}`);
    sql(`DELETE FROM cms_contents WHERE id = ${id}`);
  }
  artisan('cache:clear'); // the home page caches the latest articles
}

/** Clicks the toolbar Submit link and returns the JSON the form's AJAX call received. */
async function submitContentForm(page: Page): Promise<{ status: number; body: Record<string, unknown> }> {
  let captured: { status: number; body: Record<string, unknown> } | undefined;
  await page.route(FORM_POST, async route => {
    const response = await route.fetch();
    captured = { status: response.status(), body: await response.json() };
    await route.fulfill({ response });
  });
  await page.click('a.submit_form.btn-success');
  await expect.poll(() => captured, { timeout: 30_000 }).toBeTruthy();
  await page.unroute(FORM_POST);
  return captured!;
}

test.beforeAll(() => {
  requireLocal('creates and deletes content');
  removeArticle();
});
test.afterAll(() => removeArticle());

test('an article can be created with an image, edited and deleted through the admin', async ({ page, request }) => {
  test.setTimeout(3 * 60_000);
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');

  // Create through the real form: select2 category, slug, both locales, image upload.
  await page.goto('/en/admin/contents/articles/create');
  await page.click('#categories_ids + .select2 .select2-selection');
  await page.fill('.select2-container--open .select2-search__field', 'Turkish Cit');
  await page.click('.select2-results__option:has-text("Turkish Citizenship")');
  await page.fill('#slug', SLUG);
  await page.fill('#title_en', TITLE_EN);
  await page.fill('#brief_en', 'Created by the parity suite.');
  await page.setInputFiles('input[name="image_en"]', { name: 'parity.png', mimeType: 'image/png', buffer: pngFixture() });
  await page.click('a.nav-link[href="#tab_ar"]');
  await page.fill('#title_ar', TITLE_AR);

  const created = await submitContentForm(page);
  expect(created).toMatchObject({ status: 200, body: { success: true, redirect_url: `${BASE_URL}/en/admin/contents/articles` } });
  const id = Number((created.body.model as { model_id: number }).model_id);

  // Visible on the front end, with its image served through /img.
  const single = await request.get(`/en/articles/${SLUG}`);
  expect(single.status()).toBe(200);
  expect(await single.text()).toContain(TITLE_EN);
  const listing = await (await request.get('/en/articles')).text();
  const imageUrl = listing.match(new RegExp(`data-src="([^"]*/img/1000x750/articles/[A-Za-z0-9]+\\.png)"[^>]*alt="${TITLE_EN}"`))?.[1];
  expect(imageUrl, 'article card image on /en/articles').toBeTruthy();
  const image = await request.get(imageUrl!);
  expect(image.status()).toBe(200);
  expect(image.headers()['content-type']).toBe('image/jpeg'); // the image route re-encodes uploads as JPEG

  // Edit.
  await page.goto(`/en/admin/contents/articles/${id}/edit`);
  await page.fill('#title_en', `${TITLE_EN} (edited)`);
  const updated = await submitContentForm(page);
  expect(updated).toMatchObject({ status: 200, body: { success: true } });
  expect(await (await request.get(`/en/articles/${SLUG}`)).text()).toContain(`${TITLE_EN} (edited)`);

  // Delete with the request the index page's delete action sends.
  const token = await page.locator('#addNewForm input[name="_token"]').first().getAttribute('value');
  const deleted = await page.request.post(`/en/admin/contents/destroy/${id}`, { headers: AJAX_HEADERS, multipart: { _token: token! } });
  expect(await deleted.json()).toMatchObject({ success: true });
  expect((await request.get(`/en/articles/${SLUG}`)).status()).toBe(404);
});

test('the soft-deleted landing page still renders when restored', async ({ request, page }) => {
  requireLocal('restores a landing page row');
  const deletedAt = sql("SELECT deleted_at FROM landing_pages WHERE slug = 'new'");
  expect(deletedAt).not.toBe('NULL');
  sql("UPDATE landing_pages SET deleted_at = NULL WHERE slug = 'new'");
  try {
    for (const locale of ['en', 'ar']) {
      const response = await request.get(`/${locale}/landing-page/new`);
      expect(response.status()).toBe(200);
      const body = await normalizeHtml(page, await response.text(), 'landing-page', BASE_URL);
      expect(body).toMatchSnapshot([locale, 'landing-page-new.html']);
    }
  } finally {
    sql(`UPDATE landing_pages SET deleted_at = '${deletedAt}' WHERE slug = 'new'`);
  }
});
