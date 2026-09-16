# Parity suite

Black-box checks that the site behaves the same before and after an upgrade. Baselines were recorded on Laravel 7.3 / PHP 7.4 from `_db-backup/aldar-db-20260914-1704.sql.gz`. The suite now runs against Laravel 13 / PHP 8.4; the baselines are still the Laravel 7.3 recordings, apart from the differences accepted in the Phase 1 upgrade PR.

## Run

```bash
docker compose up -d
cd tests/e2e
npm run parity            # resets the local DB, then 321 tests, about 10 minutes
```

**The upgrade gate is `npm test`, not `npm run parity`.** `npm test` runs the `parity` project first and then the Phase H `chromium` project (smoke and security specs), 368 tests in all (321 parity + 47 chromium, about 14 minutes), and both must pass. Several Phase 0 requirements live only in the `chromium` project: the `img/{size}/{path}` route including the H4 size whitelist (`specs/security/images.spec.ts`), leads stored for valid `store`/`subscribe` submissions (`specs/smoke/critical-flows.spec.ts`, `specs/security/contact-forms.spec.ts`), and admin login (`specs/smoke/critical-flows.spec.ts`, "admin can log in and reach the dashboard"). A `parity`-only run does not check any of these.

## Environment the baselines assume

The baselines depend on local `.env` settings that are not in `.env.example`. A fresh `.env`, or a Phase 1 config merge, can silently break them:

- `DEBUGBAR_ENABLED=false`. `.env.example` has no `DEBUGBAR_ENABLED` key and `APP_DEBUG=true`; barryvdh/laravel-debugbar (`config/debugbar.php:17`) enables itself whenever debug is true and would inject markup into every HTML response, failing about 228 snapshots with no pointer to the cause. Global setup fails fast on this — see "Guards" below.
- `CACHE_DRIVER=file`. The `cache:clear` limiter reset and the file-cache warm-up both rely on the file driver. Laravel 11+ renamed this setting `CACHE_STORE`; a config merge that only renames the key without preserving the `file` value changes caching behaviour.
- `APP_URL=http://localhost:8080`. `.env.example` has `http://localhost` (no port).
- `SESSION_DRIVER=file`.
- The Docker services must stay named `app` and `db`: `scripts/e2e/db-reset.sh` execs into both by name. The `app` image needs the PHP CLI and the GD extension: `support/fixtures.ts`'s `pngFixture`/`jpegFixture` (used by `content-lifecycle.spec.ts` and the security attachment specs) shell into it to render real images with GD.

No secret values (`APP_KEY`, DB or mail credentials) are listed here or ever printed by the suite.

## Guards

- Global setup refuses to run against anything but `localhost`/`127.0.0.1` (any port), unless `PARITY_ALLOWED_HOST` names the host exactly — an explicit opt-in reserved for a future read-only staging run in Phase 3. `aldar-emlak.com` (and subdomains, and a trailing-dot FQDN) gets its own "live site" message; anything else unlisted gets a generic refusal.
- Specs that write data or sign in with the local parity accounts (behaviour, admin sections, lifecycle, matrix) additionally call `requireLocal` and refuse to run unless `BASE_URL` is exactly the local Docker stack — `PARITY_ALLOWED_HOST` does not open those up, since a staging run must stay read-only.
- After a DB reset, on a local run, global setup fetches `${BASE_URL}/en` and fails if the response contains `phpdebugbar` (see "Environment the baselines assume" above).

## Layers

| Spec | Checks |
|---|---|
| `specs/parity/golden-master.spec.ts` | Normalised server HTML for the 194 URLs in `parity/url-inventory.json` |
| `specs/parity/behaviour.spec.ts` | Contact validation JSON (ar/en), `store-inner` lead, `set_currency`, `/cookies`, regions and installments JSON, locale and trailing-slash redirects |
| `specs/parity/admin-table-values.spec.ts` | Row values of four public admin tables (countries, cities, areas, FAQs), all pages, sorted by id |
| `specs/parity/admin-sections.spec.ts` | Every admin section's status, columns and DataTables shape; failed login; logout; row keys and action kinds over every row of public tables (walked page by page; structureOnly lead, user and form tables use page 1 and record no counts) |
| `specs/parity/content-lifecycle.spec.ts` | Article create (with image) → front end and `/img` → edit → delete; landing-page render |
| `specs/parity/permissions-matrix.spec.ts` | 170 admin GET routes × anonymous, ADMIN, SUPERADMIN |
| `specs/parity/visual.spec.ts` | 8 pages × ar/en × desktop/mobile screenshots (`maxDiffPixelRatio` 0.01) |

Only the `contact endpoints` `describe` block in `behaviour.spec.ts` calls `requireLocal` (it clears the cache limiter and writes leads), so it refuses to run unless `BASE_URL` is the local Docker stack — as do all of `admin-sections.spec.ts`, `content-lifecycle.spec.ts` and `permissions-matrix.spec.ts`, which sign in with the local parity accounts. `behaviour.spec.ts`'s `set_currency`, `/cookies`, regions/installments JSON and redirect tests are not gated: they only set cookies and can run against any host the guards in "Guards" above allow. See "Guards" above for the live-site and allowlist checks that apply to every spec.

## Structural checks

`scripts/upgrade/check-structure.sh` compares routes, key config values and Glide cache paths with `tests/upgrade/*.json` (`routes.json`, `config.json`, `glide-cache-paths.json`), which were recorded on Laravel 7. It prints `structure matches tests/upgrade/` and exits 0 when all three match, and prints a diff and exits 1 otherwise. Run it from the repository root with the Docker stack up; it is not part of `npm test`.

```bash
scripts/upgrade/check-structure.sh
```

## Deprecation check

The Phase 1 exit criterion "no PHP deprecation entries are logged during a full parity run" reads `storage/logs/deprecations.log`. It needs `LOG_DEPRECATIONS_CHANNEL=deprecations` in the local `.env` (not in `.env.example`). Without it, `config/logging.php` sends deprecations to the `null` channel, nothing is ever written, and an empty or missing log proves nothing. Check the key without printing `.env`, then run the gate from the repository root:

```bash
grep -q '^LOG_DEPRECATIONS_CHANNEL=deprecations' .env || echo 'add LOG_DEPRECATIONS_CHANNEL=deprecations to .env first'
rm -f storage/logs/deprecations.log
(cd tests/e2e && npm test)
test ! -s storage/logs/deprecations.log && echo 'no deprecations'
```

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

The Mix-hash and csrf-token meta masks match nothing on today's pages (assets use literal `?v=` versions); they stay in place for future templates. The year mask is not dormant: `content-lifecycle.spec.ts`'s `[locale, 'landing-page-new.html']` snapshot contains `&copy; {year}`, sourced from `date('Y')` in `Modules/Frontend/Resources/views/landingpage/footer.blade.php:41`.

**Formatting:** the HTML is parsed by the browser's `DOMParser` (no scripts run), serialised, and whitespace-collapsed to one tag or text run per line.

**Order rules.** MariaDB returns rows with equal `sort_order` in a plan-dependent order, so these regions change between requests even on Laravel 7. Each rule is scoped to only the elements whose order actually comes from such a tied query — never a list whose order the template fixes:
- header submenus (`#responsive > li > ul`: the buy-properties and opportunity submenus) and footer menus (`.col-lg-4 .nav-footer ul`: the `footer_menu_items` list): sorted. The top-level `#responsive` list and the footer's `.col-lg-2` "Pages" list are template-ordered, not tied, and are left alone.
- FAQ panels: sorted, with position ids renumbered;
- article-category card grids: replaced by a count, not reordered, because even the set of cards shown changes between requests.
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

## When the dump changes

Regenerating the inventory (above) only replaces `url-inventory.json` and re-records the golden master. It does not touch these other hardcoded ids and slugs, which name specific rows in `_db-backup/aldar-db-20260914-1704.sql.gz` and will start naming the wrong thing, or a now-missing thing, if the dump changes:

- `parity/admin-urls.ts`: model ids `31` (a user), `251` (an article), `318` (a contract category), `11` (a landing page), `145` (a tag), `98` (an area), `12` (a city), `2` (a country), `49` (a config), `133` (a project), `320` (an opportunity), `3` (a role).
- `specs/parity/behaviour.spec.ts`: city id `18` (Antalya) and its area `count: 57`; payment category ids `518`/`519`.
- `specs/parity/visual.spec.ts`: the `rose-marine-butik` property slug, the `istanbul` city slug, and the `realestate-index` article slug.
- `specs/parity/content-lifecycle.spec.ts`: the "Turkish Citizenship" category picked in the article-create select2.
- the `new` landing-page slug (soft-deleted; restored for one test).

After a dump change, re-verify each one still resolves (the admin ids and the behaviour ids/counts against the new dump; the visual and lifecycle slugs by loading their pages), update whichever no longer match, then regenerate the inventory and re-record. The orphan-snapshot test in `inventory.spec.ts` catches golden-master snapshots left behind by URLs the new inventory dropped, but it cannot catch a stale id or slug that still happens to resolve to a different record — that only shows up as an unexpected diff in the affected spec, or not at all if you don't look.

## Visual baselines

Screenshots depend on the OS font renderer. They are stored per platform (`snapshots/parity/visual.spec.ts/<platform>/`); the committed set is `darwin`. On another platform, record that platform's set first: `UPDATE_PARITY=1 npx playwright test specs/parity/visual.spec.ts`. The pages load CDN assets, so the machine needs internet access.

## Laravel 7 behaviour recorded as-is

These are baseline facts, not bugs to fix inside a parity change:
- `/{en,ar}/apartments-for-sale-in-turkey` and `/{en,ar}/shop` return 404: their filter link names a missing category.
- `/landing-page/new` returns 404 because the row is soft-deleted; the lifecycle spec restores it for one test.
- Admin GET routes that answer 500 for staff: `notification`, `users/show`, `users/identity/validate_`, `tags/list`, `roles/show`, `projects/show` and `opportunity/show`. `projects/data` without DataTables parameters also answered 500 on Laravel 7; on Laravel 13 it answers 200 for staff (accepted difference P1-R19, recorded in the matrix baseline).
- `POST contact-us/store-visit` answers 500: the controller method is commented out.
- `GET admin/notification/config` answers 200 to anonymous visitors with the Firebase web client config (Phase 2 candidate).
- The admin project and opportunity request lists (`admin/{projects,opportunity}/data_requests`) read the same unfiltered `contact_us` rows, and pages whose window holds spam submissions with invalid UTF-8 answer a DataTables `Malformed UTF-8` error, so staff cannot page past them. Laravel 13 behaves the same: walking both lists as `parity-superadmin` at the page's own 50-row length, 4 of the 74 pages answer HTTP 200 with `Malformed UTF-8 characters, possibly incorrectly encoded`. Tracked as S12 for Phase 2.
- `admin/opportunity/properties` loads its table from the projects endpoint `admin/projects/data_properties`.
- The `store-inner` behaviour test leaves one synthetic `contact_us` row (`parity-inner@aldar.test`) until the next DB reset.
- Left out of the matrix because they change state on GET: `users/login_as/{model}`, `projects/update_prices`, `categories/asdwadwadwdaw`, `clear-cache` (see `MATRIX_EXCLUDED`).
- Admin `/en` pages recorded as-is with untranslated module translation keys, for example `permissions::roles.datatable.id` in many admin sections' `columns` arrays and `cms::messages.login_failed.title` in `login-failed.json`. If a Phase 1 change alters how or when translations load, the diff on these keys is the signal to look at, not a snapshot to update blindly.
