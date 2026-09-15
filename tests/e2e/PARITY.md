# Parity suite

Black-box checks that the site behaves the same before and after an upgrade. Baselines were recorded on Laravel 7.3 / PHP 7.4 from `_db-backup/aldar-db-20260914-1704.sql.gz`.

## Run

```bash
docker compose up -d
cd tests/e2e
npm run parity            # resets the local DB, then 313 tests, about 7 minutes
```

`npm test` runs the parity project first and then the Phase H smoke and security specs.

## Layers

| Spec | Checks |
|---|---|
| `specs/parity/golden-master.spec.ts` | Normalised server HTML for the 194 URLs in `parity/url-inventory.json` |
| `specs/parity/behaviour.spec.ts` | Contact validation JSON (ar/en), `store-inner` lead, `set_currency`, `/cookies`, regions and installments JSON, locale and trailing-slash redirects |
| `specs/parity/admin-sections.spec.ts` | Every admin section's status, columns and DataTables shape; failed login; logout; row keys and action kinds over every row of public tables (walked page by page; structureOnly lead, user and form tables use page 1 and record no counts) |
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

The Mix-hash, year and csrf-token meta masks match nothing on today's pages (assets use literal `?v=` versions and the footer year is literal); they stay in place for future templates.

**Formatting:** the HTML is parsed by the browser's `DOMParser` (no scripts run), serialised, and whitespace-collapsed to one tag or text run per line.

**Order rules.** MariaDB returns rows with equal `sort_order` in a plan-dependent order, so these regions change between requests even on Laravel 7. They are reordered, never removed:
- footer and header menus: sorted;
- FAQ panels: sorted, with position ids renumbered;
- article-category card grids: replaced by a count, because even the set shown changes.
- Sort keys ignore whitespace between tags, like the output, so whitespace-only template changes never reorder tied items.

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
- `GET admin/notification/config` answers 200 to anonymous visitors with the Firebase web client config (Phase 2 candidate).
- The admin project and opportunity request lists (`admin/{projects,opportunity}/data_requests`) read the same unfiltered `contact_us` rows, and pages whose window holds spam submissions with invalid UTF-8 answer a DataTables `Malformed UTF-8` error, so staff cannot page past them.
- `admin/opportunity/properties` loads its table from the projects endpoint `admin/projects/data_properties`.
- The `store-inner` behaviour test leaves one synthetic `contact_us` row (`parity-inner@aldar.test`) until the next DB reset.
- Left out of the matrix because they change state on GET: `users/login_as/{model}`, `projects/update_prices`, `categories/asdwadwadwdaw`, `clear-cache` (see `MATRIX_EXCLUDED`).
