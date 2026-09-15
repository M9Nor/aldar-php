# Phase 0 — Parity Safety Net Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Record how the unchanged Laravel 7 site behaves, as a black-box Playwright suite with committed baselines, so Phase 1 (the Laravel 13 jump) can prove parity or accept each difference deliberately.

**Architecture:**
- A new `parity` project in the existing Playwright harness (`tests/e2e`), next to the Phase H `smoke` and `security` specs, run with `npm run parity`.
- Every run starts from `scripts/e2e/db-reset.sh` (global setup), so data is identical each time.
- Five layers, one spec each:
  - golden-master HTML over a generated 194-URL inventory;
  - behaviour (contact endpoints, currency, cookies, JSON endpoints, redirects);
  - admin (section and DataTables shapes, login, logout, content lifecycle);
  - permissions matrix (anonymous, ADMIN, SUPERADMIN over 170 admin GET routes);
  - visual (32 screenshots).
- Baselines are Playwright snapshots under `tests/e2e/snapshots/`. `UPDATE_PARITY=1` rewrites only the ones that differ, so Phase 1 reviews every change in `git diff`.

**Tech Stack:** Playwright 1.63 + TypeScript (Node 24), tsx (runs the inventory generator), MariaDB 11.8 and PHP 7.4 in Docker Compose, Laravel 7.3 (unchanged).

**Spec:** `docs/superpowers/specs/2026-09-14-aldar-security-hotfix-and-laravel-13-upgrade-design.md` (section "Phase 0 — Parity safety net"; Phase 1 consumes this suite).

## Global Constraints

- **Black-box.** "Black-box tests treat the site as a visitor would. They are independent of the Laravel and PHP versions, so one suite validates Laravel 7, Laravel 13, staging and production." Specs use HTTP and the browser only.
  - The one back door is `tests/e2e/support/docker.ts` (SQL and shell against the local containers), for fixtures and clean-up.
  - No spec imports PHP code, calls framework classes or depends on a Laravel version.
- **Tooling.** "Playwright (TypeScript) in `tests/e2e/`, run with `npm run parity`."
- **Deterministic data.** The reset script "re-imports the dump into the Docker DB, then creates `parity-superadmin` and `parity-admin`. Real accounts are never used by tests."
  - The script is `scripts/e2e/db-reset.sh`; see Spec deviations.
  - `support/global-setup.ts` runs it before every local run; `SKIP_DB_RESET=1` skips it for quick reruns only.
- **No PII in git.** "DB dumps, lead data, logs and `.env` files stay out. Snapshots of admin pages that show customer data assert structure only."
- **Privacy.** "Public-page snapshots are committed. Admin pages that show lead or user data assert structure only." The users, requests and property-form tables record column names and row keys, never sizes or values.
- **Local-only writes.** "Tests that write data refuse to run unless `BASE_URL` is the local Docker stack." Use `requireLocal()` from `support/env.ts`.
- **Production is never contacted.**
  - No `ssh codecamb`, no HTTP to `aldar-emlak.com`.
  - Global setup refuses a `BASE_URL` on that domain.
  - Browser contexts abort requests to it, because article bodies embed images from the live site.
- **Parity (1:1).** "Any intentional output change must be listed and accepted explicitly in its PR." Phase 0 records Laravel 7 as it is, bugs included (see Findings). No application code changes: nothing outside `tests/e2e/` changes (this plan itself lives in `docs/`).
- **Exit criteria.** "The suite is green twice in a row on Laravel 7, and is merged to `main` before any dependency changes."
- **Snapshot updates.** Only via `UPDATE_PARITY=1` (`npm run parity:update`), which writes changed and missing snapshots and leaves matching ones untouched. A normal run never writes a snapshot; a missing one fails.
- **One worker.** The specs share one database, one file cache and one rate limiter (see "Runtime" below).
- **Branching.** Work on `test/parity-baseline` (cut from the hotfix head `73cb9a1`). It lands on `main` through a PR.
- **Commit trailer.** Every commit message ends with `Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>`.
- **PHPUnit.** PHPUnit 8.5 runs one path per call: `docker compose exec -T app php vendor/phpunit/phpunit/phpunit <path>`. Phase 0 adds no PHPUnit tests.

## Evidence gathered while planning

All of it was measured on the local stack at `73cb9a1`, from a fresh `db-reset.sh`.

- **Inventory.** The generator below yields **194 URLs** (97 paths × en/ar): 182 answer 200 and 12 answer 404.
  - Of the 12 404s, 6 are deliberate missing-page probes. The other 3 paths × 2 locales are real Laravel 7 behaviour:
    - `/apartments-for-sale-in-turkey` and `/shop` are live filter pages whose stored link names a missing category;
    - `/landing-page/new` is the only landing page, and it is soft-deleted.
  - Running the generator twice, and again after a reset, gives a byte-identical file.
- **What varies between requests** (curl pairs, then 5 rounds with `cache:clear` in between, 970 documents):
  - **Per request:** the CSRF token (every `name="_token"` input and the inline `let c_token = "…"`).
  - **Per environment:** the origin (`http://localhost:8080` and `//localhost:8080`), Mix `?id=` hashes (only `page_not_translated` uses `mix()`), and `date('Y')` in the landing-page footer.
  - **Tie-ordered SQL:** `sort_order` is NULL for all 53 articles and all 7 FAQs, and filter contents share values. MariaDB returns ties in a plan-dependent order, so these regions change even on Laravel 7:
    - the footer menus (`.nav-footer ul`): changed in 1 of 5 cache-cleared rounds, on every page;
    - the FAQ accordion (`/faqs`): alternated between two orders within one cache lifetime;
    - the article-category grids (`/articles/{category}`): 10 of 15–31 articles, ordered by the tie, so even the set shown changes.
  - **Flaky URL replaced:** `/properties/all/turkey?sort=price_asc` ties on price (8 projects at 0, plus projects with no price). It changed 1 run in 3 and was replaced by `?sort=date_desc`; `created_at` is unique across the 160 live projects.
  - **Stable:** view counters (article visits increment `views`, which no inventory page renders), `inRandomOrder()` (its result is never rendered), and random banners (none are live).
  - **Result:** after the normaliser, all 970 probe documents collapse to one snapshot per URL. The golden master then passed 5 of 5 cache-cleared runs and 2 of 2 fresh-reset runs.
- **Admin.**
  - All 44 index pages load for SUPERADMIN except `/en/admin/notification`, which answers 500 (`No hint path defined for [admin]`).
  - Every DataTables response carries `draw, recordsTotal, recordsFiltered, data`; while `APP_DEBUG=true` it also carries `input` and `queries`.
  - Leads (`contact_us`) are listed at `/en/admin/projects/requests` and `/en/admin/opportunity/requests`.
- **Permissions (170 admin GET URLs × 3 principals).**
  - 130 URLs: anonymous goes to login, both roles get 200.
  - **22 URLs where ADMIN is denied and SUPERADMIN is allowed:** the achievements contents; the agents, filters and opportunity-classification categories; landing pages; requests; property forms; `roles/3/edit`; `notification/create`.
  - 8 answer 500 for both roles, 6 answer 404 for both, 3 answer "302 back" for both.
  - `/en/admin/notification/config` answers **200 even to anonymous visitors**.
- **Runtime** (1 worker, 2 fresh-reset runs): the whole parity project took 6.8 min for 309 tests. The final project has 312: the 4-test inventory spec was added and two login tests were merged into one, and neither changes the timing noticeably.

  | Spec | Time |
  |---|---|
  | golden master | 53 s |
  | behaviour | 17 s |
  | admin sections | 66 s |
  | content lifecycle | 8 s |
  | permissions matrix | 198 s |
  | visual | 62 s |

  One worker stays: the matrix dominates and every request hits the same single PHP container, the cache warm-up order feeds the tie-ordered regions, and the Phase H specs disable `parity-admin` and flush the cache mid-run. Parallel workers would save about 2 minutes and add flake.
- **Snapshots:** 289 files, about 19 MB on disk.
  - 194 golden-master files: 11.6 MB raw, 2.4 MB gzipped.
  - 32 PNGs: 7.8 MB.
  - The rest: under 0.4 MB.

## Spec deviations and decisions

- **Reset script path.** The spec names `scripts/parity/db-reset.sh`; Phase H already built `scripts/e2e/db-reset.sh`, which does exactly that. The plan reuses it rather than duplicating it.
- **`img/{size}/{path}` behaviour** is already covered by `tests/e2e/specs/security/images.spec.ts` (allowed size, unknown size, parameters, non-canonical paths). The lifecycle spec adds a freshly uploaded image served through `/img`. Nothing is duplicated.
- **Login.** Admin login is covered by `specs/smoke/critical-flows.spec.ts`, and a disabled-account login by `specs/security/vendor-access.spec.ts`. Phase 0 adds failed login (JSON shape) and logout.
- **Landing page.** The inventory records the soft-deleted landing page as 404. `content-lifecycle.spec.ts` restores that row for one test to snapshot the template's rendering, then puts `deleted_at` back.
- **Visual baselines are per platform** (`{platform}` in the path; taken on `darwin`). A Linux runner needs its own `npm run parity:update` for `visual.spec.ts` before comparing.
- **Permissions matrix exclusions**: four GET routes change state (`MATRIX_EXCLUDED` in `parity/admin-urls.ts`).

## Findings recorded as Laravel 7 behaviour (not fixed in Phase 0)

- `GET /en/admin/notification/config` is public and returns the Firebase config. It is not in the spec; propose it for Phase 2 next to S6.
- `GET admin/categories/asdwadwadwdaw` creates categories and filter pages, and `GET admin/projects/update_prices` rewrites prices. Both are state changes on GET, excluded from the matrix; propose for Phase 2.
- Admin GET routes answering 500 for staff:
  - `notification`, `users/show`, `users/identity/validate_`, `tags/list`, `roles/show`;
  - `projects/show`, `opportunity/show`;
  - `projects/data` without DataTables parameters.
- `POST {locale}/contact-us/store-visit` is routed but its method is commented out, so it answers 500.

---

## File Structure

| Path | Responsibility |
|---|---|
| `tests/e2e/playwright.config.ts` (modify) | Split into `parity` and `chromium` projects; snapshot paths; `UPDATE_PARITY` |
| `tests/e2e/package.json`, `package-lock.json` (modify) | `parity`, `parity:update`, `parity:inventory` scripts; `tsx` dev dependency |
| `tests/e2e/support/global-setup.ts` (modify) | Refuse the live domain; reset the DB |
| `tests/e2e/support/fixtures.ts` (modify) | Add `pngFixture()` for CMS image uploads |
| `tests/e2e/support/network.ts` | `blockProduction()` for browser contexts |
| `tests/e2e/parity/inventory.ts` | `InventoryEntry`, `loadInventory()`, `snapshotName()` |
| `tests/e2e/parity/generate-inventory.ts` | Deterministic DB-driven URL sample → `url-inventory.json` |
| `tests/e2e/parity/url-inventory.json` | Committed URL list (paths only) |
| `tests/e2e/parity/normalize.ts` | Masks, tie-order rules, whitespace canonicalisation |
| `tests/e2e/parity/admin-urls.ts` | Admin sections list, permissions-matrix URL list and exclusions |
| `tests/e2e/specs/parity/inventory.spec.ts` | Inventory invariants |
| `tests/e2e/specs/parity/normalize.spec.ts` | Normaliser unit tests |
| `tests/e2e/specs/parity/golden-master.spec.ts` | Layer 1 |
| `tests/e2e/specs/parity/behaviour.spec.ts` | Layer 2 |
| `tests/e2e/specs/parity/admin-sections.spec.ts` | Layer 3: sections, DataTables shapes, failed login, logout |
| `tests/e2e/specs/parity/content-lifecycle.spec.ts` | Layer 3: create/edit/delete with image; landing-page render |
| `tests/e2e/specs/parity/permissions-matrix.spec.ts` | Layer 4 |
| `tests/e2e/specs/parity/visual.spec.ts` | Layer 5 |
| `tests/e2e/snapshots/parity/<spec file>/…` | Committed baselines (generated) |
| `tests/e2e/PARITY.md` | Runbook: run, update, add an order rule, known baseline oddities |

**How to run things locally (used throughout):**

```bash
docker compose ps                                                     # app, db, mailpit up; site at http://localhost:8080
(cd tests/e2e && npx playwright test specs/parity/x.spec.ts)          # one spec; global setup resets the DB first
(cd tests/e2e && SKIP_DB_RESET=1 npx playwright test specs/parity/x.spec.ts)   # quick rerun on the current DB
(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/x.spec.ts)   # write missing/changed baselines
```

---

### Task 1: Parity project and URL inventory

**Files:**
- Modify: `tests/e2e/playwright.config.ts`
- Modify: `tests/e2e/package.json`, `tests/e2e/package-lock.json`
- Create: `tests/e2e/parity/inventory.ts`
- Create: `tests/e2e/parity/generate-inventory.ts`
- Create: `tests/e2e/parity/url-inventory.json` (generated)
- Test: `tests/e2e/specs/parity/inventory.spec.ts`

**Interfaces:**
- Consumes (Phase H harness): `sql(query: string): string` from `support/docker.ts`.
- Produces:
  - Playwright project `parity` (matches `specs/parity/**/*.spec.ts`); project `chromium` now ignores `parity/**`.
  - `UPDATE_PARITY=1` sets `updateSnapshots: 'changed'`; otherwise `'none'`.
  - Snapshot files at `tests/e2e/snapshots/{testFilePath}/{arg}{ext}`; screenshots at `tests/e2e/snapshots/{testFilePath}/{platform}/{arg}{ext}`.
  - npm scripts `parity`, `parity:update`, `parity:inventory`.
  - `interface InventoryEntry { kind: string; path: string }`, `INVENTORY_FILE: string`, `loadInventory(): InventoryEntry[]`, `snapshotName(urlPath: string): string[]` (in `parity/inventory.ts`).
  - `localePaths(): InventoryEntry[]`, `buildInventory(): InventoryEntry[]` (in `parity/generate-inventory.ts`).
  - Inventory kinds: `static`, `search`, `property`, `opportunity`, `listing`, `filter-page`, `service`, `page`, `story`, `article`, `article-category`, `landing-page`, `not-found`.

- [ ] **Step 1: Confirm the branch and the stack**

Run: `git branch --show-current && docker compose ps --format '{{.Service}} {{.State}}'`
Expected: `test/parity-baseline`, then `app running`, `db running`, `mailpit running`.

- [ ] **Step 2: Split the Playwright config into projects**

Replace `tests/e2e/playwright.config.ts` with:

```ts
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './specs',
  // One worker: the specs share one database, one file cache and one rate limiter.
  workers: 1,
  fullyParallel: false,
  retries: 0,
  timeout: 60_000,
  reporter: [['list']],
  globalSetup: './support/global-setup.ts',
  // Parity baselines live in tests/e2e/snapshots/<spec path>/. UPDATE_PARITY=1 rewrites only the ones that differ.
  snapshotPathTemplate: '{testDir}/../snapshots/{testFilePath}/{arg}{ext}',
  updateSnapshots: process.env.UPDATE_PARITY === '1' ? 'changed' : 'none',
  expect: {
    toHaveScreenshot: { pathTemplate: '{testDir}/../snapshots/{testFilePath}/{platform}/{arg}{ext}' },
  },
  use: {
    baseURL: process.env.BASE_URL ?? 'http://localhost:8080',
    trace: 'retain-on-failure',
  },
  projects: [
    // Parity first: it must see the freshly reset database before the security specs change it.
    { name: 'parity', testMatch: 'parity/**/*.spec.ts', use: { ...devices['Desktop Chrome'] } },
    { name: 'chromium', testIgnore: 'parity/**', use: { ...devices['Desktop Chrome'] } },
  ],
});
```

Run: `(cd tests/e2e && npx playwright test --list --project=chromium | tail -1)`
Expected: `Total: 47 tests in 8 files` (the Phase H specs, unchanged).

- [ ] **Step 3: Write the failing inventory spec**

`tests/e2e/specs/parity/inventory.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loadInventory, snapshotName } from '../../parity/inventory';

const inventory = loadInventory();

test('the inventory holds 150-250 URLs, half English and half Arabic', () => {
  expect(inventory.length).toBeGreaterThanOrEqual(150);
  expect(inventory.length).toBeLessThanOrEqual(250);
  const en = inventory.filter(e => e.path.startsWith('/en')).map(e => e.path.slice(3));
  const ar = inventory.filter(e => e.path.startsWith('/ar')).map(e => e.path.slice(3));
  expect(en).toEqual(ar);
});

test('every URL is unique and maps to a unique snapshot file', () => {
  expect(new Set(inventory.map(e => e.path)).size).toBe(inventory.length);
  expect(new Set(inventory.map(e => snapshotName(e.path).join('/'))).size).toBe(inventory.length);
});

test('entries are slug paths only, never personal data', () => {
  for (const { path } of inventory) expect(path).toMatch(/^\/(en|ar)(\/[a-z0-9_-]+)*(\?[a-z_]+=[a-z0-9_]+(&[a-z_]+=[a-z0-9_]+)*)?$/);
});

test('every kind the spec names is covered', () => {
  const kinds = new Set(inventory.map(e => e.kind));
  for (const kind of ['static', 'search', 'property', 'opportunity', 'listing', 'filter-page', 'service', 'page', 'story', 'article', 'article-category', 'landing-page', 'not-found']) {
    expect(kinds, kind).toContain(kind);
  }
});
```

- [ ] **Step 4: Run it to see it fail**

Run: `(cd tests/e2e && SKIP_DB_RESET=1 npx playwright test specs/parity/inventory.spec.ts)`
Expected: FAIL with `Error: Cannot find module '../../parity/inventory'`.

- [ ] **Step 5: Add tsx and the npm scripts**

Run:

```bash
cd tests/e2e
npm install --save-dev tsx@^4.23.13
npm pkg set scripts.parity="playwright test --project=parity"
npm pkg set scripts.parity:update="UPDATE_PARITY=1 playwright test --project=parity"
npm pkg set scripts.parity:inventory="tsx parity/generate-inventory.ts"
cd ../..
```

Expected: `npm pkg get scripts` in `tests/e2e` lists `parity`, `parity:update` and `parity:inventory` beside the three existing scripts.

- [ ] **Step 6: Write the inventory module**

`tests/e2e/parity/inventory.ts`:

```ts
import { readFileSync } from 'node:fs';
import path from 'node:path';

export interface InventoryEntry {
  kind: string;
  path: string;
}

export const INVENTORY_FILE = path.resolve(__dirname, 'url-inventory.json');

export function loadInventory(): InventoryEntry[] {
  return JSON.parse(readFileSync(INVENTORY_FILE, 'utf8')) as InventoryEntry[];
}

/** '/en/search?q=villa&type=projects' becomes ['en', 'search@q=villa_type=projects.html']. */
export function snapshotName(urlPath: string): string[] {
  const [pathname, query] = urlPath.split('?');
  const segments = pathname.split('/').filter(Boolean);
  const last = segments.pop() ?? 'root';
  const suffix = query ? `@${query.replace(/[^A-Za-z0-9=_-]+/g, '_')}` : '';
  return [...segments, `${last}${suffix}.html`];
}
```

- [ ] **Step 7: Write the generator**

`tests/e2e/parity/generate-inventory.ts`:

```ts
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
```

The sampling rules, all by id or by name so the output never depends on query plans:
- **Project pages:** per top-level type (`top_type` category), the first three projects by id plus every 15th.
- **Listings:** every live city, every property type in use, the three Istanbul areas with most projects (ties by id), sort, paging and rent.
- **Content:** every live filter page, service, page (except `contact-us`, already static) and story.
- **Articles:** every 5th article by id, and every article category.
- **Landing pages:** every row, deleted or not.
- **Not found:** three missing-page probes.

- [ ] **Step 8: Generate the inventory from a fresh database**

Run: `scripts/e2e/db-reset.sh && (cd tests/e2e && npm run parity:inventory)`
Expected, ending with:

```
194 URLs written to parity/url-inventory.json
{"static":12,"search":6,"opportunity":2,"property":44,"listing":48,"filter-page":20,"service":8,"page":2,"story":10,"article":22,"article-category":12,"landing-page":2,"not-found":6}
```

- [ ] **Step 9: Prove the generator is deterministic**

Run: `shasum tests/e2e/parity/url-inventory.json && (cd tests/e2e && npm run parity:inventory >/dev/null) && shasum tests/e2e/parity/url-inventory.json`
Expected: the same hash printed twice.

- [ ] **Step 10: Check every URL answers as recorded**

Run:

```bash
node -e 'for (const e of require("./tests/e2e/parity/url-inventory.json")) console.log(e.path)' \
  | while read -r p; do curl -s -o /dev/null -w "%{http_code} $p\n" "http://localhost:8080$p"; done > /tmp/parity-status.txt
cut -d' ' -f1 /tmp/parity-status.txt | sort | uniq -c
grep -v '^200' /tmp/parity-status.txt
```

Expected (about 40 s): `182 200` and `12 404`, and the twelve non-200 lines are exactly:

```
404 /en/apartments-for-sale-in-turkey
404 /en/shop
404 /en/landing-page/new
404 /en/parity-missing-page
404 /en/articles/parity-missing-article
404 /en/apartments/parity-missing-project
404 /ar/apartments-for-sale-in-turkey
404 /ar/shop
404 /ar/landing-page/new
404 /ar/parity-missing-page
404 /ar/articles/parity-missing-article
404 /ar/apartments/parity-missing-project
```

The two filter pages and the landing page are real Laravel 7 behaviour (see Evidence); keep them. Any other non-200 line means the database is not the reset dump: rerun Step 8.

- [ ] **Step 11: Run the inventory spec**

Run: `(cd tests/e2e && SKIP_DB_RESET=1 npx playwright test specs/parity/inventory.spec.ts)`
Expected: `4 passed`.

- [ ] **Step 12: Commit**

```bash
git add tests/e2e/playwright.config.ts tests/e2e/package.json tests/e2e/package-lock.json \
  tests/e2e/parity/inventory.ts tests/e2e/parity/generate-inventory.ts tests/e2e/parity/url-inventory.json \
  tests/e2e/specs/parity/inventory.spec.ts
git commit -m "Add the parity Playwright project and a 194-URL inventory

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 2: Normaliser and golden-master HTML

**Files:**
- Create: `tests/e2e/parity/normalize.ts`
- Modify: `tests/e2e/support/global-setup.ts`
- Create: `tests/e2e/specs/parity/golden-master.spec.ts`
- Create: `tests/e2e/snapshots/parity/golden-master.spec.ts/**` (194 generated files)
- Test: `tests/e2e/specs/parity/normalize.spec.ts`

**Interfaces:**
- Consumes: `loadInventory()`, `snapshotName()` (Task 1); `BASE_URL` from `support/env.ts`.
- Produces (in `parity/normalize.ts`):
  - `interface OrderRule { reason: string; kinds?: string[]; container: string; child: string; mode: 'sort' | 'count'; renumber?: string }`.
  - `ORDER_RULES: OrderRule[]`.
  - `maskVolatileValues(html: string, baseUrl: string): string`.
  - `collapseWhitespace(html: string): string`.
  - `normalizeHtml(page: Page, html: string, kind: string, baseUrl: string): Promise<string>`.
  - Global setup throws `Refusing to run against the live site` for any `BASE_URL` on `aldar-emlak.com`.

**What the normaliser does, and why** (evidence in "Evidence gathered while planning"):

| Step | Masks or reorders | Why |
|---|---|---|
| `_token` inputs, `c_token` JS variable, `csrf-token` meta → `{csrf}` | Values | New per session |
| `http(s)://<BASE_URL host>` → `{origin}`, `//<host>` → `//{host}` | Values | Same snapshot on another host; other hosts (for example `https://aldar-emlak.com/...` links stored in content) stay |
| `.css/.js?id=<20 hex>` → `{mix-hash}` | Values | Mix manifest hash ("asset version hashes"); literal `?v=6.3` stays, because it is markup |
| `&copy; </span>YYYY` → `{year}` | Values | `date('Y')` in the landing-page footer |
| `.nav-footer ul > li`, `#navigation ul > li` | Sorted | Tie-ordered menu queries |
| `.faq [role=tablist] > .panel` on `/faqs`, `tab-N-M` ids → `{n}` | Sorted | FAQs all have `sort_order` NULL |
| Article cards on `/articles/{category}` | Replaced by a count comment | The set on page 1 is not stable; the card markup stays covered by `/articles`, which orders by id |
| Parse with the browser's `DOMParser` (no scripts run), serialise, collapse whitespace to one tag or text run per line | Formatting | Whitespace-only Blade changes do not count; diffs stay line-based |

Nothing else is masked. Snapshots start with `status:` and `content-type:` lines, so status changes show up too.

- [ ] **Step 1: Write the failing normaliser tests**

`tests/e2e/specs/parity/normalize.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { snapshotName } from '../../parity/inventory';
import { collapseWhitespace, maskVolatileValues, normalizeHtml } from '../../parity/normalize';

const BASE = 'http://localhost:8080';

test.describe('maskVolatileValues', () => {
  test('masks per-request tokens', () => {
    const html = '<input type="hidden" name="_token" value="6p5MRvpIjcaeglxVwUcTAj9UV2i4KCn8JoRrSn56"> let c_token = "bhjaEkyW";';
    expect(maskVolatileValues(html, BASE)).toBe('<input type="hidden" name="_token" value="{csrf}"> let c_token = "{csrf}";');
  });

  test('masks the origin but keeps other hosts', () => {
    const html = '<a href="http://localhost:8080/en"><img src="//localhost:8080/a.svg"><a href="https://aldar-emlak.com/en/villa">';
    expect(maskVolatileValues(html, BASE)).toBe('<a href="{origin}/en"><img src="//{host}/a.svg"><a href="https://aldar-emlak.com/en/villa">');
  });

  test('masks Mix hashes and the copyright year, not literal ?v= versions', () => {
    const html = '<script src="/js/frontend.min.js?id=1ef7f9ff9741408c0f9f"></script><link href="/css/main.css?v=7.02"> &copy; </span>2026 -';
    expect(maskVolatileValues(html, BASE)).toBe('<script src="/js/frontend.min.js?id={mix-hash}"></script><link href="/css/main.css?v=7.02"> &copy; </span>{year} -');
  });
});

test('collapseWhitespace puts one tag or text run on each line', () => {
  expect(collapseWhitespace('<p>\n   Hello   <b>world</b>\n</p>')).toBe('<p>\nHello\n<b>\nworld\n</b>\n</p>');
});

test.describe('normalizeHtml', () => {
  test('sorts footer menu items so a tie-order swap gives the same output', async ({ page }) => {
    const a = '<div class="nav-footer"><ul><li><a href="/b">B</a></li><li><a href="/a">A</a></li></ul></div>';
    const b = '<div class="nav-footer"><ul><li><a href="/a">A</a></li><li><a href="/b">B</a></li></ul></div>';
    expect(await normalizeHtml(page, a, 'static', BASE)).toBe(await normalizeHtml(page, b, 'static', BASE));
  });

  test('keeps the order of lists that are not tie-ordered', async ({ page }) => {
    const a = '<ul class="pagination"><li>2</li><li>1</li></ul>';
    expect(await normalizeHtml(page, a, 'static', BASE)).toContain('<li>\n2\n</li>\n<li>\n1\n</li>');
  });

  test('sorts FAQ panels and replaces their position-based ids', async ({ page }) => {
    const panel = (i: number, q: string) => `<div class="panel"><a href="#tab-0-${i}">${q}</a><div id="tab-0-${i}">x</div></div>`;
    const a = `<article class="faq"><div role="tablist">${panel(0, 'Q1')}${panel(1, 'Q2')}</div></article>`;
    const b = `<article class="faq"><div role="tablist">${panel(0, 'Q2')}${panel(1, 'Q1')}</div></article>`;
    const out = await normalizeHtml(page, a, 'static', BASE);
    expect(out).toBe(await normalizeHtml(page, b, 'static', BASE));
    expect(out).toContain('href="#{n}"');
  });

  test('reduces article-category cards to a count, and only on article-category pages', async ({ page }) => {
    const html = '<div class="blog-section"><div class="row"><div class="col-lg-8"><div class="row"><div>A</div><div>B</div></div><div class="row">pages</div></div></div></div>';
    expect(await normalizeHtml(page, html, 'article-category', BASE)).toContain('<!-- parity: 2 x div -->');
    expect(await normalizeHtml(page, html, 'static', BASE)).toContain('<div>\nA\n</div>');
  });
});

test('snapshotName maps URL paths to file names', () => {
  expect(snapshotName('/en')).toEqual(['en.html']);
  expect(snapshotName('/ar/articles/turkey')).toEqual(['ar', 'articles', 'turkey.html']);
  expect(snapshotName('/en/search?q=villa&type=projects')).toEqual(['en', 'search@q=villa_type=projects.html']);
});
```

- [ ] **Step 2: Run them to see them fail**

Run: `(cd tests/e2e && SKIP_DB_RESET=1 npx playwright test specs/parity/normalize.spec.ts)`
Expected: FAIL with `Error: Cannot find module '../../parity/normalize'`.

- [ ] **Step 3: Write the normaliser**

`tests/e2e/parity/normalize.ts`:

```ts
import type { Page } from '@playwright/test';

/**
 * A region whose order is not stable between requests on the unchanged Laravel 7 app.
 * Every rule must cite the evidence (the query behind it and the probe that showed it).
 */
export interface OrderRule {
  reason: string;
  /** Inventory kinds the rule applies to. Omit to apply to every page. */
  kinds?: string[];
  /** CSS selector for the element whose children are reordered. */
  container: string;
  /** CSS selector a child must match to take part. */
  child: string;
  /** sort: order children by their markup. count: replace them with a count. */
  mode: 'sort' | 'count';
  /** Position-derived attribute values to rewrite before sorting (regex source). */
  renumber?: string;
}

export const ORDER_RULES: OrderRule[] = [
  {
    reason: 'Footer menus come from the cached footer_menu_items query (MenuComposer), ordered by sort_order with ties; the tie order changed in 1 of 5 cache-cleared rounds.',
    container: '.nav-footer ul',
    child: 'li',
    mode: 'sort',
  },
  {
    reason: 'Header submenus come from the same query shape as the footer (MenuComposer headerMenuItems: eager-loaded filter contents ordered by tied sort_order). Not seen flipping yet; kept because the footer did.',
    container: '#navigation ul',
    child: 'li',
    mode: 'sort',
  },
  {
    reason: 'FAQ answers are eager-loaded category contents ordered by sort_order, which is NULL for all 7 FAQs; /en/faqs alternated between two orders within one cache lifetime.',
    kinds: ['static'],
    container: '.faq [role="tablist"]',
    child: '.panel',
    mode: 'sort',
    renumber: 'tab-\\d+-\\d+',
  },
  {
    reason: 'Article category pages paginate 10 articles ordered by sort_order, NULL for all 53 articles; with 15-31 articles in a category even the set shown on page 1 changes between requests.',
    kinds: ['article-category'],
    container: '.blog-section > .row > .col-lg-8 > .row:first-child',
    child: 'div',
    mode: 'count',
  },
];

/** String-level masks for values that change on every request or every environment. */
export function maskVolatileValues(html: string, baseUrl: string): string {
  const host = new URL(baseUrl).host.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return html
    .replace(new RegExp(`https?://${host}`, 'g'), '{origin}')
    .replace(new RegExp(`//${host}`, 'g'), '//{host}')
    .replace(/(name="_token"\s+value=")[^"]*"/g, '$1{csrf}"')
    .replace(/(c_token\s*=\s*")[^"]*"/g, '$1{csrf}"')
    .replace(/(<meta\s+name="csrf-token"\s+content=")[^"]*"/g, '$1{csrf}"')
    .replace(/(\.(?:css|js)\?id=)[0-9a-f]{20}/g, '$1{mix-hash}')
    .replace(/(&copy;\s*(?:<\/span>)?\s*)\d{4}/g, '$1{year}');
}

type BrowserRule = Pick<OrderRule, 'container' | 'child' | 'mode' | 'renumber'>;

/** Runs inside the browser: parse without executing scripts, reorder, serialise. */
function canonicalizeInBrowser(arg: { html: string; rules: BrowserRule[] }): string {
  const doc = new DOMParser().parseFromString(arg.html, 'text/html');
  for (const rule of arg.rules) {
    const renumber = rule.renumber ? new RegExp(rule.renumber, 'g') : null;
    // Deepest containers first, so a parent list sorts on already-sorted children.
    for (const container of Array.from(doc.querySelectorAll(rule.container)).reverse()) {
      const children = Array.from(container.children).filter(c => c.matches(rule.child));
      if (children.length === 0) continue;
      children.forEach(c => c.remove());
      if (rule.mode === 'count') {
        container.appendChild(doc.createComment(` parity: ${children.length} x ${rule.child} `));
        continue;
      }
      if (renumber) {
        for (const el of children.flatMap(c => [c, ...Array.from(c.querySelectorAll('*'))])) {
          for (const attr of Array.from(el.attributes)) el.setAttribute(attr.name, attr.value.replace(renumber, '{n}'));
        }
      }
      children
        .map(el => ({ el, key: el.outerHTML.replace(/\s+/g, ' ') }))
        .sort((a, b) => (a.key < b.key ? -1 : a.key > b.key ? 1 : 0))
        .forEach(({ el }) => container.appendChild(el));
    }
  }
  const doctype = doc.doctype ? `<!DOCTYPE ${doc.doctype.name}>` : '';
  return doctype + doc.documentElement.outerHTML;
}

/** One tag or text run per line, whitespace collapsed, so snapshot diffs stay readable. */
export function collapseWhitespace(html: string): string {
  return html
    .replace(/\s+/g, ' ')
    .replace(/\s*(<[^>]+>)\s*/g, '\n$1\n')
    .split('\n')
    .map(line => line.trim())
    .filter(Boolean)
    .join('\n');
}

export async function normalizeHtml(page: Page, html: string, kind: string, baseUrl: string): Promise<string> {
  const rules = ORDER_RULES
    .filter(r => !r.kinds || r.kinds.includes(kind))
    .map(({ container, child, mode, renumber }) => ({ container, child, mode, renumber }));
  const canonical = await page.evaluate(canonicalizeInBrowser, { html: maskVolatileValues(html, baseUrl), rules });
  return collapseWhitespace(canonical) + '\n';
}
```

- [ ] **Step 4: Run the normaliser tests**

Run: `(cd tests/e2e && SKIP_DB_RESET=1 npx playwright test specs/parity/normalize.spec.ts)`
Expected: `9 passed`.

- [ ] **Step 5: Refuse the live site in global setup**

Replace `tests/e2e/support/global-setup.ts` with:

```ts
import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { BASE_URL, IS_LOCAL } from './env';

export default function globalSetup(): void {
  // Phase 0 never talks to the live site, not even read-only.
  if (/(^|\.)aldar-emlak\.com$/i.test(new URL(BASE_URL).hostname)) {
    throw new Error(`Refusing to run against the live site (BASE_URL=${BASE_URL})`);
  }
  if (!IS_LOCAL || process.env.SKIP_DB_RESET === '1') return;
  execFileSync('bash', [path.resolve(__dirname, '../../../scripts/e2e/db-reset.sh')], { stdio: 'inherit' });
}
```

Run: `(cd tests/e2e && BASE_URL=https://aldar-emlak.com npx playwright test specs/parity/normalize.spec.ts 2>&1 | grep -m1 Refusing)`
Expected: `Error: Refusing to run against the live site (BASE_URL=https://aldar-emlak.com)`. Global setup runs before any test, so nothing is sent.

- [ ] **Step 6: Write the golden-master spec**

`tests/e2e/specs/parity/golden-master.spec.ts`:

```ts
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
```

- [ ] **Step 7: Run one URL without a baseline to see it fail**

Run: `(cd tests/e2e && SKIP_DB_RESET=1 npx playwright test specs/parity/golden-master.spec.ts -g "static /en/faqs")`
Expected: `1 failed` with `Error: A snapshot doesn't exist at …/tests/e2e/snapshots/parity/golden-master.spec.ts/en/faqs.html.` (and no file written).

- [ ] **Step 8: Record the baseline from a fresh database**

Run: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/golden-master.spec.ts)`
Expected: `Local DB reset; …`, then `194 passed` (about 1 minute).
Then run: `find tests/e2e/snapshots/parity/golden-master.spec.ts -type f | wc -l`
Expected: `194`.

- [ ] **Step 9: Prove the baseline is stable across cache rebuilds**

The tie-ordered regions are decided when a cache entry is first built, so clear the cache between rounds:

```bash
cd tests/e2e
for i in 1 2 3 4 5; do
  docker compose -f ../../docker-compose.yml exec -T app php artisan cache:clear >/dev/null
  SKIP_DB_RESET=1 npx playwright test specs/parity/golden-master.spec.ts --reporter=line | tail -1
done
cd ../..
```

Expected: five lines of `194 passed`.

If a URL fails in one round only:
1. Read its diff: the `-actual.html` file under `tests/e2e/test-results/`, compared with the snapshot.
2. Decide what caused it:
   - **A region whose order swapped** (the same items in a different order, or a different subset of tied items): add an `ORDER_RULES` entry whose `reason` names the query and this evidence, add a matching case to `normalize.spec.ts`, then rerun Step 4 and this step.
   - **A query-string URL whose sort ties:** replace the URL in `generate-inventory.ts` with a deterministic one (as with `sort=price_asc` → `sort=date_desc`), then rerun Task 1 Steps 8 and 11 and this task's Step 8.
3. Never mask anything else.

- [ ] **Step 10: Check the snapshots hold public content only**

Run: `grep -rhoE '[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}' tests/e2e/snapshots/parity/golden-master.spec.ts | sort -u`
Expected: only addresses on `@aldar-emlak.com` (the company's published contacts: `info@`, and two agent addresses shown on public pages). Anything else must be investigated before committing.

Run: `du -sh tests/e2e/snapshots/parity/golden-master.spec.ts`
Expected: about `11M`.

- [ ] **Step 11: Commit**

```bash
git add tests/e2e/parity/normalize.ts tests/e2e/support/global-setup.ts \
  tests/e2e/specs/parity/normalize.spec.ts tests/e2e/specs/parity/golden-master.spec.ts \
  tests/e2e/snapshots/parity/golden-master.spec.ts
git commit -m "Add the golden-master HTML layer with a documented normaliser

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 3: Behaviour layer

**Files:**
- Create: `tests/e2e/specs/parity/behaviour.spec.ts`
- Create: `tests/e2e/snapshots/parity/behaviour.spec.ts/**` (15 generated JSON files)

**Interfaces:**
- Consumes: `AJAX_HEADERS`, `anonymousCsrfToken(request)` (`support/csrf.ts`); `artisan(...)`, `sqlScalar(query)` (`support/docker.ts`); `BASE_URL`, `requireLocal(what)` (`support/env.ts`).
- Produces: snapshot files `en|ar/{store,store-inner,subscribe}-{empty,invalid}.json`, `en/store-inner-valid.json`, `regions-18.json`, `installments-518.json`.

Coverage map for the spec's behaviour list:

| Spec item | Where |
|---|---|
| Valid input creates a row: `store` | existing `specs/smoke/critical-flows.spec.ts` |
| Valid input creates a row: `subscribe` | existing `specs/security/contact-forms.spec.ts` |
| Valid input creates a row: `store-inner` | here |
| Invalid input returns the existing validation JSON, for all three endpoints, ar and en | here |
| `set_currency`, `/cookies`, `listing/regions-by-city-id/{id}`, `installments-by-payments/{id}` | here |
| `/` → `/en` and the locale prefix redirect | here |
| Trailing-slash redirects (the `.htaccess` 301 rule) | here |
| `img/{size}/{path}` | existing `specs/security/images.spec.ts` |
| The 404 page | golden master, kind `not-found` |

Laravel 7 facts this spec records:
- Validation failures answer **HTTP 200** with a flat `{field: message}` object.
- `store-visit` answers 500.
- `/cookies` without a CSRF token redirects to `/authenticate/login`, through the app's exception handler.
- The `default-currency` cookie is not encrypted.

- [ ] **Step 1: Write the spec**

`tests/e2e/specs/parity/behaviour.spec.ts`:

```ts
import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { artisan, sqlScalar } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';

const location = (headers: Record<string, string>) => (headers['location'] ?? '').replace(BASE_URL, '');

async function postContact(request: APIRequestContext, locale: string, endpoint: string, fields: Record<string, string>) {
  const token = await anonymousCsrfToken(request);
  return request.post(`/${locale}/contact-us/${endpoint}`, { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token }, multipart: fields });
}

test.describe('contact endpoints', () => {
  const INVALID: Record<string, Record<string, string>> = {
    store: { fullname: 'ab', phone: 'abc', email: 'not-an-email', description: 'x', country_code: '+90' },
    'store-inner': { fullname: 'ab', phone: 'abc', email: 'not-an-email', description: 'x', country_code: '+90', language: '', time_from: '', time_to: '' },
    subscribe: { emails: 'not-an-email' },
  };

  test.beforeEach(() => {
    requireLocal('contact endpoint tests clear the cache and write leads');
    artisan('cache:clear'); // resets the contact.guard limiter between tests
  });

  for (const locale of ['en', 'ar']) {
    for (const endpoint of Object.keys(INVALID)) {
      test(`${locale} ${endpoint} answers empty input with the validation JSON`, async ({ request }) => {
        const response = await postContact(request, locale, endpoint, {});
        expect(response.status()).toBe(200);
        expect(JSON.stringify(await response.json(), null, 2)).toMatchSnapshot([locale, `${endpoint}-empty.json`]);
      });

      test(`${locale} ${endpoint} answers invalid input with the validation JSON`, async ({ request }) => {
        const response = await postContact(request, locale, endpoint, INVALID[endpoint]);
        expect(response.status()).toBe(200);
        expect(JSON.stringify(await response.json(), null, 2)).toMatchSnapshot([locale, `${endpoint}-invalid.json`]);
      });
    }
  }

  test('store-inner stores a valid submission', async ({ request }) => {
    const before = sqlScalar('SELECT COUNT(*) FROM contact_us');
    const response = await postContact(request, 'en', 'store-inner', {
      fullname: 'Parity Inner', phone: '5551234567', email: 'parity-inner@aldar.test', description: 'Parity suite lead',
      country_code: '+90', language: 'English', time_from: '10:00', time_to: '12:00',
    });
    expect(JSON.stringify(await response.json(), null, 2)).toMatchSnapshot(['en', 'store-inner-valid.json']);
    expect(sqlScalar('SELECT COUNT(*) FROM contact_us')).toBe(before + 1);
  });

  test('store-visit still has no controller method and answers 500', async ({ request }) => {
    const response = await postContact(request, 'en', 'store-visit', {});
    expect(response.status()).toBe(500);
  });
});

test('set_currency stores the choice in a cookie and redirects back', async ({ request }) => {
  const token = await anonymousCsrfToken(request);
  const symbol = async () => (await (await request.get('/en/contact-us')).text()).match(/id="currency-form"[\s\S]*?id="dropdownlang"[^>]*>([\s\S]*?)<\/button>/)![1].trim();
  expect(await symbol()).toBe('₺');

  const response = await request.post('/en/set_currency', {
    form: { _token: token, currency: 'USD' },
    headers: { Referer: `${BASE_URL}/en/contact-us` },
    maxRedirects: 0,
  });
  expect(response.status()).toBe(302);
  expect(location(response.headers())).toBe('/en/contact-us');
  expect(response.headersArray().filter(h => h.name.toLowerCase() === 'set-cookie').map(h => h.value.split(';')[0])).toContain('default-currency=USD');
  expect(await symbol()).toBe('$');
});

test('/cookies records consent as JSON and refuses a missing CSRF token', async ({ request, playwright }) => {
  const token = await anonymousCsrfToken(request);
  const accepted = await request.post('/cookies', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(accepted.status()).toBe(200);
  expect(await accepted.json()).toEqual({ success: true });
  expect(accepted.headersArray().some(h => h.name.toLowerCase() === 'set-cookie' && h.value.startsWith('cookies='))).toBe(true);

  const fresh = await playwright.request.newContext({ baseURL: BASE_URL });
  const refused = await fresh.post('/cookies', { maxRedirects: 0 });
  expect(refused.status()).toBe(302);
  expect(location(refused.headers())).toBe('/authenticate/login');
  await fresh.dispose();
});

interface Row { id: number; translations?: Array<{ id: number }> }
const byId = (rows: Row[]) => rows
  .map(row => ({ ...row, translations: row.translations?.slice().sort((a, b) => a.id - b.id) }))
  .sort((a, b) => a.id - b.id);

test('listing/regions-by-city-id returns the areas of a city', async ({ request }) => {
  const antalya = await request.get('/en/listing/regions-by-city-id/18');
  expect(antalya.status()).toBe(200);
  const body = await antalya.json();
  expect(JSON.stringify({ ...body, data: byId(body.data) }, null, 2)).toMatchSnapshot('regions-18.json');

  const turkey = await (await request.get('/en/listing/regions-by-city-id/turkey')).json();
  expect({ success: turkey.success, count: turkey.data.length, keys: Object.keys(turkey.data[0]).sort() })
    .toEqual({ success: true, count: 57, keys: expect.arrayContaining(['id', 'city_id', 'native_name', 'name', 'translations']) });
});

test('installments-by-payments returns the child payment categories', async ({ request }) => {
  const cash = await (await request.get('/en/installments-by-payments/518')).json();
  expect(JSON.stringify({ ...cash, data: byId(cash.data) }, null, 2)).toMatchSnapshot('installments-518.json');
  expect((await (await request.get('/en/installments-by-payments/519')).json()).data).toHaveLength(15);
  expect(await (await request.get('/en/installments-by-payments/999999')).json()).toEqual({ success: true, data: [] });
});

test.describe('redirects', () => {
  test('/ goes to /en for a new visitor and to /ar after an Arabic page', async ({ request }) => {
    const first = await request.get('/', { maxRedirects: 0 });
    expect([first.status(), location(first.headers())]).toEqual([302, '/en']);

    await request.get('/ar');
    const again = await request.get('/', { maxRedirects: 0 });
    expect([again.status(), location(again.headers())]).toEqual([302, '/ar']);
  });

  test('a path without a locale gets the /en prefix', async ({ request }) => {
    const response = await request.get('/articles', { maxRedirects: 0 });
    expect([response.status(), location(response.headers())]).toEqual([302, '/en/articles']);
  });

  for (const [from, to] of [['/en/', '/en'], ['/en/articles/', '/en/articles'], ['/ar/faqs/', '/ar/faqs'], ['/en/articles/?page=2', '/en/articles?page=2']]) {
    test(`trailing slash ${from} redirects permanently to ${to}`, async ({ request }) => {
      const response = await request.get(from, { maxRedirects: 0 });
      expect([response.status(), location(response.headers())]).toEqual([301, to]);
    });
  }
});
```

- [ ] **Step 2: Run it before any baseline exists**

Run: `(cd tests/e2e && npx playwright test specs/parity/behaviour.spec.ts)`
Expected: `15 failed` (every snapshot test: `A snapshot doesn't exist`) and `9 passed` (store-visit, set_currency, cookies, the two locale redirects, the four trailing-slash redirects).

- [ ] **Step 3: Record the baselines**

Run: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/behaviour.spec.ts)`
Expected: `24 passed`.

Run: `cat tests/e2e/snapshots/parity/behaviour.spec.ts/en/store-invalid.json`
Expected:

```json
{
  "fullname": "The fullname must be at least 3 characters.",
  "phone": "The phone must be between 1 and 15 digits.",
  "email": "The email must be a valid email address.",
  "description": "The description must be at least 3 characters."
}
```

- [ ] **Step 4: Rerun against the baselines**

Run: `(cd tests/e2e && npx playwright test specs/parity/behaviour.spec.ts)`
Expected: `24 passed` (about 20 s).

- [ ] **Step 5: Commit**

```bash
git add tests/e2e/specs/parity/behaviour.spec.ts tests/e2e/snapshots/parity/behaviour.spec.ts
git commit -m "Add the parity behaviour layer: contact validation, currency, cookies, JSON endpoints, redirects

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 4: Admin sections and authentication

**Files:**
- Create: `tests/e2e/parity/admin-urls.ts`
- Create: `tests/e2e/specs/parity/admin-sections.spec.ts`
- Create: `tests/e2e/snapshots/parity/admin-sections.spec.ts/**` (45 generated JSON files)

**Interfaces:**
- Consumes: `loginAs(page, 'parity-superadmin' | 'parity-admin')` (`support/auth.ts`); `AJAX_HEADERS`; `requireLocal`.
- Produces (in `parity/admin-urls.ts`): `CONTENT_TYPES: string[]` (17), `CATEGORY_TYPES: string[]` (12), `interface AdminSection { name: string; url: string; structureOnly?: boolean }`, `ADMIN_SECTIONS: AdminSection[]` (44).

Each section records:
- the page status and the table's column headers;
- the DataTables request (endpoint, method, status) and its top-level keys, minus the debug-only `input` and `queries`;
- the union of row keys, and the action kinds offered (`edit`, `delete`, …), which follow Bouncer;
- for public data only, `recordsTotal` and the row count.

It never records row values. `structureOnly` sections (users, requests, property forms) omit the counts too.

Playwright sanitises string snapshot names, so `categories-playlist_videos.json` is stored as `categories-playlist-videos.json`. That is expected.

- [ ] **Step 1: Write the admin URL module**

`tests/e2e/parity/admin-urls.ts`:

```ts
// Admin URLs for the parity suite. Model ids come from the committed dump
// (_db-backup/aldar-db-20260914-1704.sql.gz) and name public records only.

export const CONTENT_TYPES = [
  'sliders', 'stories', 'articles', 'faqs', 'playlist_videos', 'agents', 'testimonials', 'services', 'achievements',
  'pages', 'offices', 'filters', 'advertisements', 'currencies', 'first_banners', 'second_banners', 'balance',
];

export const CATEGORY_TYPES = [
  'articles', 'agents', 'faqs', 'filters', 'playlist_videos', 'contracts', 'property_classifications',
  'opportunity_classifications', 'property_status', 'property_features', 'facilities', 'payments',
];

export interface AdminSection {
  name: string;
  url: string;
  /** The table lists customer or staff data: record its shape, never its size. */
  structureOnly?: boolean;
}

export const ADMIN_SECTIONS: AdminSection[] = [
  { name: 'projects', url: '/en/admin/projects' },
  { name: 'opportunities', url: '/en/admin/opportunity' },
  { name: 'project-requests', url: '/en/admin/projects/requests', structureOnly: true },
  { name: 'project-property-forms', url: '/en/admin/projects/properties', structureOnly: true },
  { name: 'opportunity-requests', url: '/en/admin/opportunity/requests', structureOnly: true },
  { name: 'opportunity-property-forms', url: '/en/admin/opportunity/properties', structureOnly: true },
  ...CONTENT_TYPES.map(type => ({ name: `contents-${type}`, url: `/en/admin/contents/${type}` })),
  ...CATEGORY_TYPES.map(type => ({ name: `categories-${type}`, url: `/en/admin/categories/${type}` })),
  { name: 'cities', url: '/en/admin/cities' },
  { name: 'areas', url: '/en/admin/areas' },
  { name: 'countries', url: '/en/admin/countries' },
  { name: 'tags', url: '/en/admin/tags' },
  { name: 'configs', url: '/en/admin/configs' },
  { name: 'users', url: '/en/admin/users', structureOnly: true },
  { name: 'roles', url: '/en/admin/roles' },
  { name: 'landing-pages', url: '/en/admin/landing_pages' },
  { name: 'notifications', url: '/en/admin/notification' },
];
```

The 44 sections are:
- projects, opportunities, and their requests and property-form lists;
- every content type and every category type the CMS defines (`Content::types()`, `Category::types()`);
- cities, areas, countries, tags, configs, users, roles, landing pages and notifications.

- [ ] **Step 2: Write the spec**

`tests/e2e/specs/parity/admin-sections.spec.ts`:

```ts
import { test, expect, Page } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { requireLocal } from '../../support/env';
import { ADMIN_SECTIONS } from '../../parity/admin-urls';

// yajra/laravel-datatables adds these keys only while APP_DEBUG is true.
const DEBUG_ONLY_KEYS = new Set(['input', 'queries']);

interface DataTableJson {
  recordsTotal: number;
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
      shape.columns = (await page.locator('#datatable thead th').allInnerTexts()).map(text => text.replace(/\s+/g, ' ').trim());
      shape.table = {
        endpoint: new URL(table.url()).pathname,
        method: table.request().method(),
        status: table.status(),
        keys: Object.keys(json).filter(key => !DEBUG_ONLY_KEYS.has(key)).sort(),
        ...(section.structureOnly ? {} : { recordsTotal: json.recordsTotal, rows: rows.length }),
        rowKeys: [...new Set(rows.flatMap(row => Object.keys(row)))].sort(),
        actions: [...new Set(rows.flatMap(row => [...(row.actions?.icons ?? []), ...(row.actions?.dropdown ?? [])].map(a => a.action)))].sort(),
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
```

- [ ] **Step 3: Run it before any baseline exists**

Run: `(cd tests/e2e && npx playwright test specs/parity/admin-sections.spec.ts)`
Expected: `45 failed` (`A snapshot doesn't exist`) and `1 passed` (logout).

- [ ] **Step 4: Record the baselines**

Run: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/admin-sections.spec.ts)`
Expected: `46 passed` (about 70 s).

Run: `cat tests/e2e/snapshots/parity/admin-sections.spec.ts/notifications.json tests/e2e/snapshots/parity/admin-sections.spec.ts/login-failed.json`
Expected:

```
{
  "status": 500
}{
  "success": false,
  "type": "validation_error",
  "title": "cms::messages.login_failed.title",
  "description": "cms::messages.login_failed.description",
  "errors": []
}
```

- [ ] **Step 5: Check the PII-bearing tables recorded structure only**

Run: `grep -l recordsTotal tests/e2e/snapshots/parity/admin-sections.spec.ts/{users,project-requests,opportunity-requests,project-property-forms,opportunity-property-forms}.json; grep -rhoE '[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}' tests/e2e/snapshots/parity/admin-sections.spec.ts | wc -l`
Expected: no file names, then `0`.

- [ ] **Step 6: Rerun against the baselines**

Run: `(cd tests/e2e && npx playwright test specs/parity/admin-sections.spec.ts)`
Expected: `46 passed`.

- [ ] **Step 7: Commit**

```bash
git add tests/e2e/parity/admin-urls.ts tests/e2e/specs/parity/admin-sections.spec.ts tests/e2e/snapshots/parity/admin-sections.spec.ts
git commit -m "Add parity checks for every admin section, DataTables shape, failed login and logout

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 5: Content lifecycle and landing-page render

**Files:**
- Modify: `tests/e2e/support/fixtures.ts`
- Create: `tests/e2e/support/network.ts`
- Create: `tests/e2e/specs/parity/content-lifecycle.spec.ts`
- Create: `tests/e2e/snapshots/parity/content-lifecycle.spec.ts/{en,ar}/landing-page-new.html` (generated)

**Interfaces:**
- Consumes: `loginAs`, `AJAX_HEADERS`, `appShell`, `artisan`, `sql`, `BASE_URL`, `requireLocal`, `normalizeHtml(page, html, kind, baseUrl)` (Task 2).
- Produces: `pngFixture(width = 120, height = 90): Buffer` (`support/fixtures.ts`); `blockProduction(context: BrowserContext): Promise<void>` (`support/network.ts`).

**The flow.** Articles are the simplest content type with an image:

- **Create** through the real form at `/en/admin/contents/articles/create`:
  - pick the `Turkish Citizenship` category in the select2 box (AJAX from `categories/articles/get-categories`);
  - fill `slug` (required, unique) and `title_en`, then `title_ar` on the `#tab_ar` tab (`title_ar` is always required);
  - upload `image_en`: at least 50×50 JPEG or PNG, stored by `ContentController@store` under `storage/app/public/uploads/articles/`;
  - submit with the toolbar link `a.submit_form.btn-success`. `onFormSubmit` posts `FormData` with AJAX and receives `{success, redirect_url, model: {model_id}}`.
- **See it** on `/en/articles/{slug}` and as the first card on `/en/articles`. Its image comes through `/img/1000x750/articles/<name>.png`, served as `image/jpeg`.
- **Edit** at `/en/admin/contents/articles/{id}/edit` (the form id is also `addNewForm`).
- **Delete** with the request the index page's delete action sends: `POST /en/admin/contents/destroy/{id}` with `_token` and AJAX headers. That soft-deletes, so the page 404s.
- **Clean up:** `afterAll` hard-deletes the row, translations, category links and image files, then clears the cache, because the home page caches the latest articles.

- [ ] **Step 1: Add the PNG fixture**

Replace `tests/e2e/support/fixtures.ts` with:

```ts
import { appShell } from './docker';

/** A real 8x8 JPEG produced by the app container's GD, so finfo and getimagesize accept it. */
export function jpegFixture(): Buffer {
  const base64 = appShell(`php -r '$i = imagecreatetruecolor(8, 8); ob_start(); imagejpeg($i); echo base64_encode(ob_get_clean());'`);
  return Buffer.from(base64, 'base64');
}

/** A solid-colour PNG of the given size, large enough for the CMS image rules (min 50x50). */
export function pngFixture(width = 120, height = 90): Buffer {
  const base64 = appShell(`php -r '$i = imagecreatetruecolor(${width}, ${height}); imagefill($i, 0, 0, imagecolorallocate($i, 30, 120, 200)); ob_start(); imagepng($i); echo base64_encode(ob_get_clean());'`);
  return Buffer.from(base64, 'base64');
}
```

- [ ] **Step 2: Add the production blocker**

`tests/e2e/support/network.ts`:

```ts
import { BrowserContext } from '@playwright/test';

/** Abort every browser request to the live site (article bodies embed images from it). */
export async function blockProduction(context: BrowserContext): Promise<void> {
  await context.route(/^https?:\/\/([^/]+\.)?aldar-emlak\.com(\/|$)/, route => route.abort());
}
```

- [ ] **Step 3: Write the spec**

`tests/e2e/specs/parity/content-lifecycle.spec.ts`:

```ts
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

function removeArticle(): void {
  for (const id of sql(`SELECT id FROM cms_contents WHERE slug = '${SLUG}'`).split('\n').filter(Boolean).map(Number)) {
    for (const image of sql(`SELECT image FROM cms_content_translations WHERE content_id = ${id} AND image IS NOT NULL`).split('\n').filter(Boolean)) {
      appShell(`rm -rf storage/app/public/uploads/${image} storage/app/public/uploads/.cache/${image}`);
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
```

- [ ] **Step 4: Run it before the landing-page baseline exists**

Run: `ls storage/app/public/uploads/articles | wc -l` and note the number.
Run: `(cd tests/e2e && npx playwright test specs/parity/content-lifecycle.spec.ts)`
Expected: `1 passed` (the lifecycle) and `1 failed` (`A snapshot doesn't exist at …/content-lifecycle.spec.ts/en/landing-page-new.html`).

- [ ] **Step 5: Record the landing-page baseline and rerun**

Run: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/content-lifecycle.spec.ts)`
Expected: `2 passed`.
Run: `(cd tests/e2e && npx playwright test specs/parity/content-lifecycle.spec.ts)`
Expected: `2 passed` (about 15 s).

- [ ] **Step 6: Check the clean-up**

Run:

```bash
docker compose exec -T db mariadb -ualdar -paldar aldar -N -B -e \
  "SELECT COUNT(*) FROM cms_contents WHERE slug = 'parity-lifecycle-article'; SELECT deleted_at FROM landing_pages WHERE slug = 'new';"
ls storage/app/public/uploads/articles | wc -l
```

Expected: `0`, then `2022-01-21 13:39:27`, then the same file count as in Step 4.

- [ ] **Step 7: Commit**

```bash
git add tests/e2e/support/fixtures.ts tests/e2e/support/network.ts \
  tests/e2e/specs/parity/content-lifecycle.spec.ts tests/e2e/snapshots/parity/content-lifecycle.spec.ts
git commit -m "Add the parity content lifecycle and landing-page render checks

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 6: Permissions matrix

**Files:**
- Modify: `tests/e2e/parity/admin-urls.ts` (append)
- Create: `tests/e2e/specs/parity/permissions-matrix.spec.ts`
- Create: `tests/e2e/snapshots/parity/permissions-matrix.spec.ts/matrix.json` (generated)

**Interfaces:**
- Consumes: `CONTENT_TYPES`, `CATEGORY_TYPES` (Task 4); `AJAX_HEADERS`; `BASE_URL`, `E2E_PASSWORD`, `requireLocal`.
- Produces: `MATRIX_URLS: string[]` (170), `MATRIX_EXCLUDED: Record<string, string>` (in `parity/admin-urls.ts`).

**How outcomes are recorded.**
- **Coverage:** every admin GET route across the Cms, Backend, Permissions and Notification modules, including data, create, edit, show and summary endpoints. The model ids are public records from the dump:
  - city 12, area 98, country 2, tag 145, config 49;
  - article 251, category 318;
  - project 133, opportunity 320;
  - the `parity-admin` user, id 31;
  - role 3 (ADMIN), landing page 11.
- **Status:** a non-redirect response records its status.
- **Redirects:** `app/Exceptions/Handler.php` turns every non-404 `HttpException` and `AuthenticationException` into a redirect to `route('login')`, and an `AuthorizationException` (policy denial) into `redirect()->back()`. Each request carries a fixed `Referer`, so:
  - a login redirect is recorded as `302 login`;
  - a denial (`redirect()->back()`) is recorded as `302 back`;
  - any other redirect records its path.
- **Principals:** anonymous, `parity-admin` (ADMIN) and `parity-superadmin` (SUPERADMIN), each with its own cookie jar.

- [ ] **Step 1: Append the matrix URLs**

Append to `tests/e2e/parity/admin-urls.ts`:

```ts

const crud = (base: string, id: number) => [base, `${base}/data`, `${base}/create`, `${base}/${id}/edit`];

/** Every admin GET route, with ids filled in. */
export const MATRIX_URLS: string[] = [
  '/en/admin',
  '/en/admin/users', '/en/admin/users/data', '/en/admin/users/create', '/en/admin/users/summary?model=31',
  '/en/admin/users/show', '/en/admin/users/myprofile', '/en/admin/users/31/edit',
  '/en/admin/users/identity/validate?name=username&keyword=parity-probe',
  '/en/admin/users/identity/validate_?name=username&keyword=parity-probe', '/en/admin/users/get-user-select2',
  ...CONTENT_TYPES.flatMap(type => [`/en/admin/contents/${type}`, `/en/admin/contents/${type}/data`, `/en/admin/contents/${type}/create`]),
  '/en/admin/contents/articles/251/edit',
  ...CATEGORY_TYPES.flatMap(type => [`/en/admin/categories/${type}`, `/en/admin/categories/${type}/data`, `/en/admin/categories/${type}/create`, `/en/admin/categories/${type}/get-categories`]),
  '/en/admin/categories/contracts/318/edit',
  ...crud('/en/admin/landing_pages', 11),
  '/en/admin/tags/list', ...crud('/en/admin/tags', 145),
  ...crud('/en/admin/areas', 98),
  ...crud('/en/admin/cities', 12),
  ...crud('/en/admin/countries', 2),
  ...crud('/en/admin/configs', 49), '/en/admin/configs/49/edit-config',
  ...(['projects', 'opportunity'] as const).flatMap(section => [
    ...crud(`/en/admin/${section}`, section === 'projects' ? 133 : 320),
    `/en/admin/${section}/show`, `/en/admin/${section}/requests`, `/en/admin/${section}/data_requests`, `/en/admin/${section}/request_summary`,
    `/en/admin/${section}/properties`, `/en/admin/${section}/data_properties`, `/en/admin/${section}/properties_summary`, `/en/admin/${section}/show_details/0`,
  ]),
  '/en/admin/roles', '/en/admin/roles/data', '/en/admin/roles/create', '/en/admin/roles/show', '/en/admin/roles/3/edit',
  '/en/admin/notification', '/en/admin/notification/config', '/en/admin/notification/create',
];

/** Admin GET routes deliberately left out of the matrix, and why. */
export const MATRIX_EXCLUDED: Record<string, string> = {
  'GET admin/users/login_as/{model}': 'switches the session to another user',
  'GET admin/projects/update_prices': 'rewrites project prices',
  'GET admin/categories/asdwadwadwdaw': 'creates categories and filter pages',
  'GET admin/clear-cache': 'flushes the cache mid-run; covered by specs/security/maintenance-routes.spec.ts',
};
```

- [ ] **Step 2: Write the spec**

`tests/e2e/specs/parity/permissions-matrix.spec.ts`:

```ts
import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS } from '../../support/csrf';
import { BASE_URL, E2E_PASSWORD, requireLocal } from '../../support/env';
import { MATRIX_URLS } from '../../parity/admin-urls';

// A fixed Referer makes redirect()->back() (the app's "permission denied" response) predictable.
const REFERER = `${BASE_URL}/en/parity-referer`;
const PRINCIPALS = ['anonymous', 'parity-admin', 'parity-superadmin'] as const;

async function signIn(request: APIRequestContext, username: string): Promise<void> {
  const html = await (await request.get('/en/authenticate/login')).text();
  const token = html.match(/name="_token" value="([^"]+)"/)![1];
  const response = await request.post('/en/authenticate/login', {
    headers: AJAX_HEADERS,
    form: { _token: token, identity: username, password: E2E_PASSWORD },
  });
  expect(response.status(), `login as ${username}`).toBe(200);
}

/** 200, 404, 500 ... or "302 login" / "302 back" / "302 <path>" for redirects. */
async function outcome(request: APIRequestContext, url: string): Promise<string> {
  const response = await request.get(url, { maxRedirects: 0, headers: { Referer: REFERER }, timeout: 60_000 });
  const status = response.status();
  if (status < 300 || status >= 400) return String(status);
  const location = response.headers()['location'] ?? '';
  if (/\/authenticate\/login$/.test(location)) return `${status} login`;
  if (location === REFERER) return `${status} back`;
  return `${status} ${location.replace(BASE_URL, '')}`;
}

test('admin GET routes give each role the same outcome as the Laravel 7 baseline', async ({ playwright }) => {
  requireLocal('uses the local parity accounts');
  test.setTimeout(10 * 60_000);

  const contexts: Record<string, APIRequestContext> = {};
  for (const principal of PRINCIPALS) {
    contexts[principal] = await playwright.request.newContext({ baseURL: BASE_URL });
    if (principal !== 'anonymous') await signIn(contexts[principal], principal);
  }

  const matrix: Record<string, Record<string, string>> = {};
  for (const url of MATRIX_URLS) {
    matrix[url] = {};
    for (const principal of PRINCIPALS) matrix[url][principal] = await outcome(contexts[principal], url);
  }
  for (const context of Object.values(contexts)) await context.dispose();

  expect(JSON.stringify(matrix, null, 2)).toMatchSnapshot('matrix.json');
});
```

- [ ] **Step 3: Run it before the baseline exists**

Run: `(cd tests/e2e && npx playwright test specs/parity/permissions-matrix.spec.ts)`
Expected: `1 failed` with `A snapshot doesn't exist at …/permissions-matrix.spec.ts/matrix.json` (about 3.5 minutes; the requests run before the comparison).

- [ ] **Step 4: Record the baseline**

Run: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/permissions-matrix.spec.ts)`
Expected: `1 passed`.

- [ ] **Step 5: Check the matrix matches what was measured while planning**

Run:

```bash
cd tests/e2e && node -e '
const m = require("./snapshots/parity/permissions-matrix.spec.ts/matrix.json");
const groups = {};
for (const r of Object.values(m)) { const k = [r.anonymous, r["parity-admin"], r["parity-superadmin"]].join(" | "); groups[k] = (groups[k] || 0) + 1; }
console.log(Object.keys(m).length, JSON.stringify(groups));
console.log(Object.entries(m).filter(([, r]) => r["parity-admin"] !== r["parity-superadmin"]).map(([u]) => u).join("\n"));' && cd ../..
```

Expected, first line:

```
170 {"302 login | 200 | 200":130,"302 login | 302 back | 302 back":3,"302 login | 500 | 500":8,"302 login | 302 back | 200":22,"302 login | 404 | 404":6,"200 | 200 | 200":1}
```

The next 22 lines are the URLs where only SUPERADMIN is allowed:
- `/en/admin/contents/achievements`, plus its `/data` and `/create`;
- `/en/admin/categories/{agents,filters,opportunity_classifications}`, each plus `/data` and `/create`;
- `/en/admin/landing_pages`, plus `/data`, `/create` and `/11/edit`;
- `/en/admin/{projects,opportunity}/{requests,properties}`;
- `/en/admin/roles/3/edit`;
- `/en/admin/notification/create`.

Any other result means the database or the accounts differ from the reset dump; rerun from a reset before recording.

- [ ] **Step 6: Rerun against the baseline**

Run: `(cd tests/e2e && npx playwright test specs/parity/permissions-matrix.spec.ts)`
Expected: `1 passed`.

- [ ] **Step 7: Commit**

```bash
git add tests/e2e/parity/admin-urls.ts tests/e2e/specs/parity/permissions-matrix.spec.ts tests/e2e/snapshots/parity/permissions-matrix.spec.ts
git commit -m "Add the parity permissions matrix for anonymous, ADMIN and SUPERADMIN

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 7: Visual layer

**Files:**
- Create: `tests/e2e/specs/parity/visual.spec.ts`
- Create: `tests/e2e/snapshots/parity/visual.spec.ts/darwin/{desktop,mobile}/{en,ar}/*.png` (32 generated)

**Interfaces:**
- Consumes: `blockProduction(context)` (Task 5).
- Produces: 32 PNG baselines named `{device}/{locale}/{page}.png`.

**The design.**
- **Pages:** home, a property, a listing, the articles list, an article without live-site images, contact us, services, who we are.
- **Viewports:** 1366×800 desktop and 390×844 mobile (touch), `deviceScaleFactor: 1`.
- **Shot:** viewport-sized, not full-page. Full pages of the home page alone run to several MB per PNG.
- **Settling** (measured stable over 3 compare runs):
  - wait for `networkidle`;
  - stop swiper, owl and slick carousels on slide 0 (the hero image is compared rather than masked);
  - wait for fonts and for the images in view;
  - `animations: 'disabled'`;
  - mask only `iframe` (Google Maps).
- **Tolerance:** `maxDiffPixelRatio: 0.01`.
- **Network:** requests to `aldar-emlak.com` are aborted. CDN assets (bootstrapcdn, jsdelivr, Google Fonts) load normally, so the machine needs internet access.

- [ ] **Step 1: Write the spec**

`tests/e2e/specs/parity/visual.spec.ts`:

```ts
import { test, expect, Page } from '@playwright/test';
import { blockProduction } from '../../support/network';

// Eight key pages x ar/en x desktop/mobile. Viewport-sized shots: they catch missing CSS,
// fonts and images without committing multi-megabyte full-page PNGs.
const PAGES: Record<string, string> = {
  home: '',
  property: '/apartments/rose-marine-butik',
  listing: '/properties/for-sale/istanbul',
  articles: '/articles',
  article: '/articles/realestate-index',
  'contact-us': '/contact-us',
  services: '/services',
  'who-we-are': '/who-we-are',
};
const DEVICES = {
  desktop: { viewport: { width: 1366, height: 800 }, isMobile: false, hasTouch: false },
  mobile: { viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true },
};

/** Stop carousels on their first slide, then wait for fonts and the images in view. */
async function settle(page: Page): Promise<void> {
  await page.waitForLoadState('networkidle');
  await page.evaluate(async () => {
    const w = window as unknown as { jQuery?: any };
    document.querySelectorAll<HTMLElement & { swiper?: any }>('.swiper-container').forEach(el => {
      el.swiper?.autoplay?.stop();
      el.swiper?.slideTo(0, 0, false);
    });
    if (w.jQuery) {
      w.jQuery('.owl-carousel').trigger('stop.owl.autoplay').trigger('to.owl.carousel', [0, 0]);
      w.jQuery('.slick-initialized').slick('slickPause').slick('slickGoTo', 0, true);
    }
    await document.fonts.ready;
    const inView = (el: Element) => { const r = el.getBoundingClientRect(); return r.width > 0 && r.bottom > 0 && r.top < innerHeight; };
    await Promise.all(Array.from(document.images).filter(img => inView(img) && !img.complete)
      .map(img => new Promise(done => { img.onload = img.onerror = done; })));
  });
}

for (const [device, options] of Object.entries(DEVICES)) {
  for (const locale of ['en', 'ar']) {
    for (const [name, path] of Object.entries(PAGES)) {
      test(`${device} ${locale} ${name}`, async ({ browser }) => {
        const context = await browser.newContext({ ...options, deviceScaleFactor: 1 });
        await blockProduction(context);
        const page = await context.newPage();
        try {
          expect((await page.goto(`/${locale}${path}`))?.status()).toBe(200);
          await settle(page);
          await expect(page).toHaveScreenshot([device, locale, `${name}.png`], {
            animations: 'disabled',
            maxDiffPixelRatio: 0.01,
            mask: [page.locator('iframe')], // Google Maps embeds
            timeout: 30_000,
          });
        } finally {
          await context.close();
        }
      });
    }
  }
}
```

- [ ] **Step 2: Run it before any baseline exists**

Run: `(cd tests/e2e && npx playwright test specs/parity/visual.spec.ts)`
Expected: `32 failed` with `A snapshot doesn't exist at …/snapshots/parity/visual.spec.ts/darwin/…`.

- [ ] **Step 3: Record the baselines**

Run: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/visual.spec.ts)`
Expected: `32 passed` (about 1 minute).
Run: `find tests/e2e/snapshots/parity/visual.spec.ts -name '*.png' | wc -l && du -sh tests/e2e/snapshots/parity/visual.spec.ts`
Expected: `32`, about `7.8M`.

- [ ] **Step 4: Look at two baselines**

Open `tests/e2e/snapshots/parity/visual.spec.ts/darwin/desktop/en/home.png` and `.../mobile/ar/property.png` with the Read tool.
Expected:
- **Desktop home:** the blue top bar, the logo, the menu from Home to Contact Us, the "Find your property" filter on the left, and the hero slide image on the right, not blank or masked.
- **Mobile property:** Arabic, right-to-left, with price, title and gallery buttons.
- **Both:** the cookie banner is visible at the bottom. That is expected, because every context is fresh.

- [ ] **Step 5: Prove the screenshots are stable**

Run: `(cd tests/e2e && for i in 1 2 3; do SKIP_DB_RESET=1 npx playwright test specs/parity/visual.spec.ts --reporter=line | tail -1; done)`
Expected: three lines of `32 passed`.

- [ ] **Step 6: Commit**

```bash
git add tests/e2e/specs/parity/visual.spec.ts tests/e2e/snapshots/parity/visual.spec.ts
git commit -m "Add parity screenshots for eight key pages in ar/en on desktop and mobile

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 8: Runbook, two green runs, pull request

**Files:**
- Create: `tests/e2e/PARITY.md`

**Interfaces:**
- Consumes: everything above.
- Produces: the exit-criterion evidence (two consecutive green `npm run parity` runs) and the PR.

- [ ] **Step 1: Write the runbook**

`tests/e2e/PARITY.md`:

````markdown
# Parity suite

Black-box checks that the site behaves the same before and after an upgrade. Baselines were recorded on Laravel 7.3 / PHP 7.4 from `_db-backup/aldar-db-20260914-1704.sql.gz`.

## Run

```bash
docker compose up -d
cd tests/e2e
npm run parity            # resets the local DB, then 312 tests, about 7 minutes
```

`npm test` runs the parity project first and then the Phase H smoke and security specs.

## Layers

| Spec | Checks |
|---|---|
| `specs/parity/golden-master.spec.ts` | Normalised server HTML for the 194 URLs in `parity/url-inventory.json` |
| `specs/parity/behaviour.spec.ts` | Contact validation JSON (ar/en), `store-inner` lead, `set_currency`, `/cookies`, regions and installments JSON, locale and trailing-slash redirects |
| `specs/parity/admin-sections.spec.ts` | Every admin section's status, columns and DataTables shape; failed login; logout |
| `specs/parity/content-lifecycle.spec.ts` | Article create (with image) → front end and `/img` → edit → delete; landing-page render |
| `specs/parity/permissions-matrix.spec.ts` | 170 admin GET routes × anonymous, ADMIN, SUPERADMIN |
| `specs/parity/visual.spec.ts` | 8 pages × ar/en × desktop/mobile screenshots (`maxDiffPixelRatio` 0.01) |

Specs that write data or sign in with the local parity accounts (behaviour, admin sections, lifecycle, matrix) refuse to run unless `BASE_URL` is the local Docker stack. Global setup refuses any `aldar-emlak.com` URL.

## When a parity test fails after a change

1. Open the diff: Playwright prints it. The actual output is in `test-results/**/*-actual.*`, next to the expected file in `snapshots/`.
2. Fix the regression, or decide the difference is intended.
3. For intended differences, run `npm run parity:update`. It rewrites only the snapshots that differ.
4. Review `git diff tests/e2e/snapshots`, commit the snapshots with the change, and list each accepted difference with its reason in the PR description.

Never edit a snapshot by hand and never widen the normaliser to hide a real difference.

## Normaliser (`parity/normalize.ts`)

**Masked values:**
- CSRF tokens (`_token` inputs, `c_token`, `csrf-token` meta);
- the test origin (`{origin}`, `//{host}`);
- Laravel Mix `?id=` hashes;
- the landing-page footer year.

**Formatting:** the HTML is parsed by the browser's `DOMParser` (no scripts run), serialised, and whitespace-collapsed to one tag or text run per line.

**Order rules.** MariaDB returns rows with equal `sort_order` in a plan-dependent order, so these regions change between requests even on Laravel 7. They are reordered, never removed:
- footer and header menus: sorted;
- FAQ panels: sorted, with position ids renumbered;
- article-category card grids: replaced by a count, because even the set shown changes.

**Adding an order rule** is allowed only with evidence:
1. Reproduce the swap on the unchanged code, with `artisan cache:clear` between two runs.
2. Name the query in `reason`.
3. Add a test to `specs/parity/normalize.spec.ts`.

A URL whose query string sorts on a tied column is replaced in `parity/generate-inventory.ts` instead.

## Regenerating the inventory

Only when the dump changes:

```bash
scripts/e2e/db-reset.sh
(cd tests/e2e && npm run parity:inventory && UPDATE_PARITY=1 npx playwright test specs/parity/golden-master.spec.ts)
```

## Visual baselines

Screenshots depend on the OS font renderer. They are stored per platform (`snapshots/parity/visual.spec.ts/<platform>/`); the committed set is `darwin`. On another platform, record that platform's set first: `UPDATE_PARITY=1 npx playwright test specs/parity/visual.spec.ts`. The pages load CDN assets, so the machine needs internet access.

## Laravel 7 behaviour recorded as-is

These are baseline facts, not bugs to fix inside a parity change:
- `/{en,ar}/apartments-for-sale-in-turkey` and `/{en,ar}/shop` return 404: their filter link names a missing category.
- `/landing-page/new` returns 404 because the row is soft-deleted; the lifecycle spec restores it for one test.
- Admin GET routes that answer 500 for staff: `notification`, `users/show`, `users/identity/validate_`, `tags/list`, `roles/show`, `projects/show`, `opportunity/show`, and `projects/data` without DataTables parameters.
- `POST contact-us/store-visit` answers 500: the controller method is commented out.
- `GET admin/notification/config` answers 200 to anonymous visitors (Phase 2 candidate).
- Left out of the matrix because they change state on GET: `users/login_as/{model}`, `projects/update_prices`, `categories/asdwadwadwdaw`, `clear-cache` (see `MATRIX_EXCLUDED`).
````

- [ ] **Step 2: First full run**

Run: `(cd tests/e2e && npm run parity)`
Expected: `Local DB reset; test accounts: parity-superadmin, parity-admin`, then `312 passed` (about 7 minutes).

- [ ] **Step 3: Second full run, straight after**

Run: `(cd tests/e2e && npm run parity)`
Expected: `312 passed` again. Both runs green in a row is the Phase 0 exit criterion. If either run fails, fix the cause (Task 2 Step 9 explains flaky URLs), commit, and restart from Step 2.

- [ ] **Step 4: Confirm the Phase H specs are unaffected**

Run: `(cd tests/e2e && npx playwright test --project=chromium)`
Expected: `47 passed`.

- [ ] **Step 5: Confirm nothing outside the test tree changed**

Run: `git diff --stat 73cb9a1 -- . ':!tests/e2e' ':!docs'`
Expected: no output.

Run: `git status --short`
Expected: only `tests/e2e/PARITY.md` untracked. No `test-results/` entries, which `tests/e2e/.gitignore` excludes.

- [ ] **Step 6: Commit the runbook**

```bash
git add tests/e2e/PARITY.md
git commit -m "Document how to run and update the parity suite

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

- [ ] **Step 7: Open the pull request (owner approval required)**

Ask the owner before pushing. `test/parity-baseline` was cut from `hotfix/security`, so merge the hotfix PR first; otherwise this PR also carries the Phase H commits. Once approved:

```bash
git push -u origin test/parity-baseline
gh pr create --base main --head test/parity-baseline \
  --title "Phase 0: parity safety net on Laravel 7" \
  --body "$(cat <<'EOF'
Black-box parity suite for the Laravel 13 upgrade, recorded on Laravel 7.3.

- 312 tests in the `parity` Playwright project (`cd tests/e2e && npm run parity`, about 7 minutes).
- Golden-master HTML for 194 URLs, behaviour, admin sections, content lifecycle, permissions matrix (170 routes × 3 principals), 32 screenshots.
- Green twice in a row from a fresh `scripts/e2e/db-reset.sh`.
- Baselines update only through `UPDATE_PARITY=1`; see `tests/e2e/PARITY.md`.
- No application code changes. Laravel 7 behaviour recorded as-is, including the findings listed in PARITY.md.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
EOF
)"
```

Expected: the PR URL. Merge it before any dependency change (Phase 1).

---

## Self-review

**1. Spec coverage** (Phase 0 section):

| Spec requirement | Task |
|---|---|
| Playwright (TypeScript) in `tests/e2e/`, `npm run parity` | 1 |
| Deterministic data from the reset script, parity accounts only | 1 (reuses `scripts/e2e/db-reset.sh` through global setup) |
| Layer 1: generated inventory, 150–250 URLs, ar/en, with its URL types | 1 |
| Layer 1: normalise (CSRF, session fragments, timestamps, asset hashes, whitespace), store under `tests/e2e/snapshots/` | 2 |
| Layer 1: diffs fixed or accepted in the PR | 2 (update workflow), 8 (runbook) |
| Layer 2: contact endpoints valid/invalid | 3 (with existing smoke/security for `store`/`subscribe` rows) |
| Layer 2: `set_currency`, `/cookies`, regions and installments JSON | 3 |
| Layer 2: `img/{size}/{path}`, `/` → `/en`, trailing slashes | Existing `images.spec.ts`; 3 |
| Layer 3: login, logout, failed login | Existing smoke; 4 |
| Layer 3: every admin section loads with the same DataTables shape | 4 (44 sections, a superset of the spec's list) |
| Layer 3: content lifecycle with image upload, front end, `/img`, edit, delete | 5 |
| Layer 4: permissions matrix ADMIN vs SUPERADMIN, asserted identical | 6 (anonymous added) |
| Layer 5: 8 key pages × ar/en × desktop/mobile, small tolerance | 7 |
| Privacy: public snapshots committed, admin PII structure only | 2 Step 10, 4 Step 5 |
| Exit: green twice in a row on Laravel 7, merged to `main` first | 8 |

No gaps.

**2. Placeholder scan.** Every code step includes the full file content, and Task 6 shows the full appended block. There are no "TBD", "similar to" or "add validation" steps. Commands carry expected outputs measured while planning.

**3. Type consistency.**
- `InventoryEntry`, `loadInventory`, `snapshotName` (Task 1) are used with the same signatures in Tasks 2 and 8.
- `normalizeHtml(page, html, kind, baseUrl)` (Task 2) matches its use in Task 5.
- `ADMIN_SECTIONS`, `CONTENT_TYPES`, `CATEGORY_TYPES` (Task 4) are used in Task 6.
- `blockProduction(context)` (Task 5) is used in Tasks 5 and 7; `pngFixture()` (Task 5) is used in Task 5.
- Test counts add up to 312: 4 + 9 + 194 + 24 + 46 + 2 + 1 + 32.
