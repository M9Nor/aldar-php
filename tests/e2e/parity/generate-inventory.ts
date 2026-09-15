// Writes parity/url-inventory.json from the local Docker DB. Run it straight after scripts/e2e/db-reset.sh.
// The output holds URL paths built from published slugs only: no customer data.
import { writeFileSync } from 'node:fs';
import { sql } from '../support/docker';
import { INVENTORY_FILE, InventoryEntry } from './inventory';

const LOCALES = ['en', 'ar'];

function rows(query: string): string[][] {
  const out = sql(query);
  return out === '' ? [] : out.split('\n').map(line => line.split('\t'));
}

// Live projects with their top-level type, numbered per type in id order.
const LIVE_PROJECTS = `
  SELECT p.id, p.slug, c.slug AS type_slug, c.type AS type_kind,
         ROW_NUMBER() OVER (PARTITION BY c.id ORDER BY p.id) AS rn
  FROM be_projects p
  JOIN cms_categorizables cz ON cz.categorizable_id = p.id AND cz.categorizable_type LIKE '%Project' AND cz.options = 'top_type'
  JOIN cms_categories c ON c.id = cz.category_id AND c.deleted_at IS NULL AND c.disabled_at IS NULL
  WHERE p.deleted_at IS NULL AND p.disabled_at IS NULL AND p.slug IS NOT NULL`;

const contentSlugs = (type: string, extra = ''): string[] => rows(
  `SELECT slug FROM cms_contents WHERE type = '${type}' AND deleted_at IS NULL AND disabled_at IS NULL AND slug IS NOT NULL ${extra} ORDER BY id`,
).map(([slug]) => slug);

export function localePaths(): InventoryEntry[] {
  const entries: InventoryEntry[] = [];
  const add = (kind: string, path: string) => entries.push({ kind, path });

  for (const path of ['', '/contact-us', '/articles', '/articles?page=2', '/services', '/faqs']) add('static', path);
  for (const path of ['/search?q=istanbul', '/search?q=villa&type=projects', '/search?q=turkish&type=articles']) add('search', path);

  // Project pages: per top-level type, the first three by id plus every 15th.
  for (const [, slug, typeSlug, typeKind] of rows(`SELECT id, slug, type_slug, type_kind FROM (${LIVE_PROJECTS}) s WHERE rn <= 3 OR rn % 15 = 0 ORDER BY type_kind, type_slug, id`)) {
    if (typeKind === 'property_classifications') add('property', `/${typeSlug}/${slug}`);
    else add('opportunity', `/opportunities/${typeSlug}/${slug}`);
  }

  // Listing filters: every city, every property type, the three busiest Istanbul areas, sort and paging.
  for (const [city] of rows('SELECT native_name FROM cms_cities WHERE deleted_at IS NULL ORDER BY id')) add('listing', `/properties/for-sale/${city}`);
  for (const [typeSlug] of rows(`SELECT DISTINCT type_slug FROM (${LIVE_PROJECTS}) s WHERE type_kind = 'property_classifications' ORDER BY type_slug`)) add('listing', `/${typeSlug}/for-sale/turkey`);
  for (const [area] of rows(`SELECT a.native_name FROM cms_areas a
      JOIN cms_cities c ON c.id = a.city_id AND c.native_name = 'istanbul'
      JOIN be_projects p ON p.area_id = a.id AND p.deleted_at IS NULL AND p.disabled_at IS NULL
      WHERE a.deleted_at IS NULL GROUP BY a.id, a.native_name ORDER BY COUNT(p.id) DESC, a.id LIMIT 3`)) add('listing', `/properties/for-sale/istanbul/${area}`);
  for (const path of ['/properties/all/turkey', '/properties/all/turkey?sort=date_desc', '/properties/all/turkey?page=2', '/apartments/for-rent/istanbul', '/opportunities/all_properties/all/turkey']) add('listing', path);

  // CMS content served by /{slug}.
  for (const slug of contentSlugs('filters')) add('filter-page', `/${slug}`);
  for (const slug of contentSlugs('services')) add('service', `/${slug}`);
  for (const slug of contentSlugs('pages', "AND slug <> 'contact-us'")) add('page', `/${slug}`);
  for (const slug of contentSlugs('stories')) add('story', `/${slug}`);

  // Articles: every 5th by id, plus every article category.
  for (const [slug] of rows(`SELECT slug FROM (SELECT slug, ROW_NUMBER() OVER (ORDER BY id) AS rn FROM cms_contents
      WHERE type = 'articles' AND deleted_at IS NULL AND disabled_at IS NULL AND slug IS NOT NULL) s WHERE rn % 5 = 1 ORDER BY rn`)) add('article', `/articles/${slug}`);
  for (const [slug] of rows("SELECT slug FROM cms_categories WHERE type = 'articles' AND deleted_at IS NULL AND disabled_at IS NULL ORDER BY id")) add('article-category', `/articles/${slug}`);

  // Landing pages, including soft-deleted ones (they must keep returning 404), and missing pages.
  for (const [slug] of rows('SELECT slug FROM landing_pages ORDER BY id')) add('landing-page', `/landing-page/${slug}`);
  for (const path of ['/parity-missing-page', '/articles/parity-missing-article', '/apartments/parity-missing-project']) add('not-found', path);

  return entries;
}

export function buildInventory(): InventoryEntry[] {
  const paths = localePaths();
  return LOCALES.flatMap(locale => paths.map(({ kind, path }) => ({ kind, path: `/${locale}${path}` })));
}

if (require.main === module) {
  const inventory = buildInventory();
  writeFileSync(INVENTORY_FILE, `${JSON.stringify(inventory, null, 2)}\n`);
  const counts: Record<string, number> = {};
  for (const { kind } of inventory) counts[kind] = (counts[kind] ?? 0) + 1;
  console.log(`${inventory.length} URLs written to parity/url-inventory.json`);
  console.log(JSON.stringify(counts));
}
