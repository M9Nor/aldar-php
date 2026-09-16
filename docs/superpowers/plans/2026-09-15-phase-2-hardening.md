# Phase 2: Remaining Hardening Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Close the spec's Phase 2 hardening items S1–S21 on the Laravel 13 / PHP 8.4 code. Each item gets its own new test. Every feature and every page stays as it is, apart from the differences listed and accepted below.

**Architecture:**
- Thirteen sequential tasks on the existing Laravel 7-style application: Kernel classes, service providers, string `Controller@method` routes, nwidart modules.
- Every task writes its failing test first: a Playwright spec in `tests/e2e/specs/security/` (project `chromium`) or a PHPUnit test in `tests/Unit` / `tests/Feature`. It then makes the smallest code change and runs the gate.
- Intended baseline changes are named cell by cell in the task that causes them:
  - the permissions matrix;
  - the `notifications` admin-sections snapshot;
  - `tests/upgrade/routes.json` and `tests/upgrade/config.json`.
- The controller accepts each of them in an isolated commit and verifies it with a script (see "Baseline acceptance").

**Tech Stack:**
- Laravel 13.31 and PHP 8.4 (`php:8.4-apache` image `aldar-php84`), with nwidart/laravel-modules 13, silber/bouncer 1.0.4, yajra/laravel-datatables-oracle 13.3, league/flysystem 3, laravel/ui 4 and mcamara/laravel-localization 2.4.
- PHPUnit 12 and Larastan 3.
- The Playwright suite in `tests/e2e` (projects `parity` and `chromium`).
- MariaDB 11.8 and Mailpit in Docker.

**Spec:** `docs/superpowers/specs/2026-09-14-aldar-security-hotfix-and-laravel-13-upgrade-design.md`. Its section "Phase 2 — Remaining hardening" (S1–S21) binds this plan; "Constraints", "Non-goals" and "Observations outside this scope" apply too.

**Branch:** `security/hardening`, cut from `upgrade/laravel-13` at 9ec5ee4 (ruling P2-R1). Rebase it onto `main` after PR #4 merges.

**Controller rulings that bind this plan:** P2-R1 to P2-R9 in `.superpowers/sdd/2026-09-15-phase-2-hardening/progress.md`.

**Research used while planning** (git-ignored, under `.superpowers/research/phase-2/`):
- `A-request-config.md` (S1, S2, S3, S6, S8, S9)
- `B-routes-state.md` (S7, S10, S11, S12, S13, P1-R39; the findings behind S20 and S21)
- `C-unescaped-output.md` (S5)
- `D-deps-secrets-tests.md` (S4, S14–S19, test conventions)

## Global Constraints

These apply to every task. An implementer sees them together with their own task.

**Scope and structure**
- **No visual or UX change.** "Compiled CSS/JS in `public/` stays byte-identical, and there is no move from Mix to Vite." (spec, Non-goals.)
  - The check is `git diff --stat main...HEAD -- public/css public/js`, which must print nothing.
  - The only allowed change under `public/modules` is the Task 10 (S16) deletion. `git diff --name-status main...HEAD -- public/modules` prints nothing before Task 10, and after it exactly:
    ```
    D	public/modules/frontend/form/process-contact.php
    D	public/modules/frontend/form/quote-contact.php
    ```
  - Blade and PHP changes that alter visible markup are listed in the task and in the PR.
- **Keep every feature, including dormant ones** (Firebase push, `finance`, `property_form`, landing pages).
- **Keep the Laravel 7-style application structure:** `app/Http/Kernel.php`, `app/Console/Kernel.php`, the providers, and string `'Controller@method'` routes. Do not move to the Laravel 11+ slim structure.
- **Front-end libraries are reported, never upgraded** (S4, Non-goals).
- **Line endings.** Many PHP and Blade files use CRLF line endings (for example `ProjectController.php`, `UserController.php`, `aside.blade.php`, `config/session.php`). Keep each file's existing line endings. After editing, `git diff --stat` and `git diff --ignore-cr-at-eol --stat` must print the same numbers. If they differ, you converted line endings: undo that and redo the edit.

**Parity (1:1)**
- "Any intentional output change must be listed and accepted explicitly in its PR." (spec, Constraints.)
- **Implementers never run `UPDATE_PARITY=1`.** They never edit a file under `tests/e2e/snapshots/` or `tests/upgrade/*.json`, and never run `structure-snapshot.php` into those files.
- **A task that changes a baseline names it exactly.** It gives the files and the expected changed cells in the output format of the script in "Baseline acceptance". The controller accepts that change in an isolated commit that holds only those cells, verified by the script.
- **Any other parity or structure difference is a regression.** Fix it in application code. If you cannot, report DONE_WITH_CONCERNS with the smallest diff excerpt. Never add a normaliser mask, never widen a tolerance, and never weaken an existing assertion to hide a difference.
- **Existing security specs may change only as a task says**, and only when the behaviour they pin is the one S-item changes, for example `maintenance-routes.spec.ts` in Task 4 and `module-scripts.spec.ts` in Task 10. The PR lists each such change.

**Data, secrets and environments**
- **No PII in git.** DB dumps, lead data, logs and `.env` files stay out.
  - Tests use only synthetic rows they create themselves: addresses under `@aldar.test`, and markers such as `s21-probe`. Tests delete those rows again.
  - Never print, snapshot or commit real lead, contact or user content. Counts and column names are fine.
- **Never print or read `.env`.** A `grep -q '^KEY=' .env` that prints no value is allowed. Never write a secret, API key, password, token or IP address value into code, tests, commit messages or reports: say "the hard-coded key" or "the hard-coded IP".
- **Never contact aldar-emlak.com.** Specs that load pages with `page.goto` call `await blockProduction(page.context())` from `tests/e2e/support/network.ts` first.
- **Tests that write data run only against the local Docker stack.** Such a spec calls `requireLocal(reason)` from `tests/e2e/support/env.ts` in `test.beforeAll` or `test.beforeEach`. The `sql`, `artisan` and `appShell` helpers call it too.
- **Each S item gets a test added to the suite** (spec). The task names it.

**The gate** (every task runs it before committing; Task 13 runs it for the exit evidence)

```bash
cd /Users/mohammedelkasim/Desktop/aldar-php
grep -q '^LOG_DEPRECATIONS_CHANNEL=deprecations' .env && echo "deprecation channel on"
rm -f storage/logs/deprecations.log
(cd tests/e2e && npm test)
docker compose exec -T app php vendor/bin/phpunit
scripts/upgrade/check-structure.sh
docker compose exec -T app php -d memory_limit=2G vendor/bin/phpstan analyse --no-progress
test ! -s storage/logs/deprecations.log && echo "no deprecations"
git diff --stat main...HEAD -- public/css public/js
git diff --name-status main...HEAD -- public/modules
```

- `npm test` runs the `parity` project and then the `chromium` project. It always starts from the global-setup DB reset: never gate on a `SKIP_DB_RESET=1` run.
- `npm test` takes about 15 minutes. Run it with `nohup` into a log file under `.superpowers/sdd/2026-09-15-phase-2-hardening/`, and poll the log with separate foreground `tail -3` calls.
- Each task states its expected `npm test` and PHPUnit counts. phpstan must print `[OK] No errors`. The deprecation log must stay empty.
- Before the controller accepts a task's baseline change, the gate shows exactly that task's expected failures and nothing else. After the acceptance commit, the gate is fully green.

**Frozen test toolchain**
- Install `tests/e2e` with `npm ci`, and do not bump `@playwright/test` or its Chromium.
- Keep the `mariadb:11.8` image and the pinned Composer image in `docker/php/Dockerfile`.
- Run the visual spec on the same macOS machine the `darwin` baselines came from.

**Delivery**
- One commit per task, plus the controller's acceptance commits. Stage explicit paths, never `git add -A` at the repository root.
- **Commit trailer:** every commit message ends with a `Co-Authored-By: Claude <model> <noreply@anthropic.com>` line naming the model that wrote it, for example `Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>`. In the commit steps below, `<model>` stands for that name.
- **Out of scope** (Phase 3 or owner work), so no task does them:
  - production deployment and `composer install --no-dev`;
  - `config:cache` and `route:cache`;
  - setting production `.env` values;
  - rotating the currconv key;
  - purging the production logs.

  Phase 2 makes the code ready for them, and Task 13 writes them into the PR material.

---

## Working environment

- Work in place in `/Users/mohammedelkasim/Desktop/aldar-php`, the checkout the Docker stack bind-mounts. The site runs at http://localhost:8080, MariaDB on port 3307 and Mailpit on 8025. `docker compose ps` must show `app`, `db` and `mailpit` up.
- Commands inside the app container: `docker compose exec -T app <cmd>`.
- PHPUnit: `docker compose exec -T app php vendor/bin/phpunit`. Run one file with `docker compose exec -T app php vendor/bin/phpunit tests/Unit/<File>.php`.
- One Playwright spec: `(cd tests/e2e && npx playwright test specs/security/<file>.spec.ts --reporter=line)`. This also resets the DB first. While iterating, `SKIP_DB_RESET=1` may skip the reset, but a gate run never does.
- **Fixture accounts:** `parity-superadmin` (SUPERADMIN) and `parity-admin` (ADMIN), created by `scripts/e2e/db-reset.sh`, with password `E2E_PASSWORD` (default `e2e-local-password`).
  - Helpers in `tests/e2e/support/`:
    - `auth.ts`: `loginAs(page, 'parity-superadmin' | 'parity-admin')`, `attemptLogin`;
    - `csrf.ts`: `AJAX_HEADERS`, `csrfToken(page)`, `anonymousCsrfToken(request)`;
    - `docker.ts`: `sql`, `sqlScalar`, `artisan`, `appShell`;
    - `env.ts`: `BASE_URL`, `IS_LOCAL`, `E2E_PASSWORD`, `requireLocal`;
    - `network.ts`: `blockProduction`;
    - `fixtures.ts`: `jpegFixture`, `pngFixture`.
- **How this app renders errors** (`app/Exceptions/Handler.php`); the expected statuses below follow from it:
  - `NotFoundHttpException` and `ModelNotFoundException` give 404.
  - `TokenMismatchException`, any other `HttpException` and `AuthenticationException` give a 302 to `/en/authenticate/login`.
  - `AuthorizationException` gives `redirect()->back()`, even for AJAX. With a fixed `Referer` that is "302 back".
  - `ValidationException` gives a 302 back for a plain request and a 422 JSON response when the request expects JSON.
- **CSRF on Laravel 13.** `PreventRequestForgery` skips GET/HEAD/OPTIONS and accepts `Sec-Fetch-Site: same-origin` without a token. Playwright's API requests send no `Sec-Fetch-Site`, so they must send `_token` (or `X-CSRF-TOKEN`). Adding `Sec-Fetch-Site: cross-site` without a token simulates a hostile page.
- **PHPUnit shares the `aldar` database** with the Docker stack. There is no test database: DB-touching tests use `Illuminate\Foundation\Testing\DatabaseTransactions`.
  - `phpunit.xml` sets `APP_ENV=testing`, `CACHE_DRIVER=array` and `SESSION_DRIVER=array`.
  - Plain source or config guards extend `PHPUnit\Framework\TestCase`, like `tests/Unit/FirebaseTlsTest.php`. Tests that need `config()`, facades or `response()` extend `Tests\TestCase`.
  - `RedirectToHttps` redirects every non-`local` request, so PHPUnit tests call middleware and controllers directly instead of sending HTTP requests.
  - **A source guard must never print file contents on failure.** `assertStringContainsString` prints the whole haystack, which could print a secret. Use `assertTrue(str_contains(...), 'message')` or `assertSame(0, preg_match(...), 'message')`.

## Baseline acceptance (controller only)

Implementers do not run this section; it shows how the controller accepts the baseline changes each task names.

Save this script once as `.superpowers/sdd/2026-09-15-phase-2-hardening/json-cell-diff.js`. The folder is git-ignored through `.git/info/exclude`.

```js
#!/usr/bin/env node
// Usage: node json-cell-diff.js <before.json> <after.json>
// Prints every changed leaf, one line each, sorted:  <path>: <before> -> <after>
// Objects recurse by key and arrays by index. An array of route records (tests/upgrade/routes.json)
// is keyed by "<METHODS> <uri>", so an added or removed route prints as one line.
const fs = require('fs');
const [beforeFile, afterFile] = process.argv.slice(2);
const read = file => JSON.parse(fs.readFileSync(file, 'utf8'));
const isRouteList = v => Array.isArray(v) && v.length > 0
  && v.every(e => e && typeof e === 'object' && Array.isArray(e.methods) && typeof e.uri === 'string');
const normalise = v => (isRouteList(v) ? Object.fromEntries(v.map(e => [`${e.methods.join('|')} ${e.uri}`, e])) : v);
const isContainer = v => v !== null && typeof v === 'object';
const show = v => (v === undefined ? '(absent)' : JSON.stringify(v));
const out = [];
function walk(a, b, path) {
  a = normalise(a);
  b = normalise(b);
  if (isContainer(a) && isContainer(b) && Array.isArray(a) === Array.isArray(b)) {
    for (const key of new Set([...Object.keys(a), ...Object.keys(b)])) {
      walk(key in a ? a[key] : undefined, key in b ? b[key] : undefined, path.concat(key));
    }
    return;
  }
  if (show(a) !== show(b)) out.push(`${path.join(' | ')}: ${show(a)} -> ${show(b)}`);
}
walk(read(beforeFile), read(afterFile), []);
if (out.length) console.log(out.sort().join('\n'));
```

For each acceptance named in a task:

1. **Regenerate only the named file, from a fresh DB reset.**
   - `permissions-matrix`: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/permissions-matrix.spec.ts)`
   - `admin-sections` notifications: `(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/admin-sections.spec.ts -g "admin section notifications")`
   - `routes.json`: `docker compose exec -T app php scripts/upgrade/structure-snapshot.php routes > tests/upgrade/routes.json`
   - `config.json`: `docker compose exec -T app php scripts/upgrade/structure-snapshot.php config > tests/upgrade/config.json`
2. **Verify the cells.** Write the task's "Expected cell diff" block to `/tmp/expected-cells.txt`, then run:
   ```bash
   F=tests/upgrade/routes.json   # or the snapshot path named by the task
   node .superpowers/sdd/2026-09-15-phase-2-hardening/json-cell-diff.js <(git show HEAD:$F) $F > /tmp/actual-cells.txt
   diff /tmp/expected-cells.txt /tmp/actual-cells.txt && echo "cells match"
   git status --short -- tests/e2e/snapshots tests/upgrade
   ```
   Expected: `cells match`, and `git status` lists only the named file. If anything else changed, restore it with `git checkout -- <path>` and rule on it separately.
3. **Commit that file alone**, for example: `git commit -m "Accept the staff-only notification config route in the permissions matrix (S9)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"`.
4. **Re-run the gate part that failed.** It must now pass.

---

## File structure

| Path | Task | Responsibility |
|---|---|---|
| `Modules/Backend/Http/Controllers/Admin/ProjectController.php` | 1, 5, 6, 7 | Lead list JSON escaping (S5), lead endpoint authorization and child-row delete authorization (S21), `show()` 404 (S11), uncapped lead "All" (S1) |
| `Modules/Backend/Http/Controllers/Admin/OpportunityController.php` | 1, 5, 6, 7 | Same as ProjectController, plus the P1-R39 `link` column removal |
| `Modules/Backend/Resources/views/admin/requests.blade.php` | 1 | Link column renders only http(s) as a link (S5 C3) |
| `Modules/Cms/Classes/ResponseHandler.php`, `config/datatables.php` | 1, 7 | Invalid UTF-8 substitution (S12); `max_length` (S1) |
| `app/Providers/AppServiceProvider.php` | 2 | Debug-IP block removed (S15) |
| `Modules/Frontend/Http/Controllers/FrontendController.php`, `config/services.php`, `.env.example` | 2 | currconv key from config (S14) |
| `Modules/Cms/Http/Controllers/Admin/UserController.php` | 2, 6, 7 | No request-body logging (S20), `validateIdentity_` and `show()` (S11), Select2 page size (S1) |
| `app/Http/Middleware/EnsureStaff.php` | 3 | JSON 403 (S8) |
| `Modules/Notification/Http/Controllers/NotificationController.php` | 3, 5, 6 | `postWebToken` validation, limiter and generic error (S6); `postIndex`/`getList` authorization (S21); `index` 404 (S11) |
| `Modules/Notification/Routes/web.php` | 3 | `config` behind `staff` (S9) |
| `Modules/Cms/Routes/web.php`, `Modules/Backend/Routes/web.php` | 4 | clear-cache and login_as become POST; update_prices and asdwadwadwdaw removed (S7, S10) |
| `Modules/Frontend/Http/Controllers/HomeController.php` | 4 | clear-cache requires `tags.requests` (S7) |
| `Modules/Backend/start.php`, `Modules/Cms/Resources/views/includes/aside.blade.php` | 4 | Aside clear-cache item as a hidden CSRF form (S7) |
| `Modules/Cms/Resources/views/dashboard.blade.php` | 4 | Currency button fires a native `form.submit()` (S7, P2-R6) |
| `Modules/Cms/Resources/views/includes/header.blade.php` | 4 | Login-back link as a hidden CSRF form (S10) |
| `Modules/Cms/Http/Controllers/Admin/CategoryController.php` | 4, 7 | Seeding method removed (S10); Select2 page size (S1) |
| `Modules/Cms/Http/Controllers/Admin/{ContentController,LandingPageController,TagController}.php` | 5 (and 6 for TagController) | Page abilities on S21 POST routes; `tags/list` locale (S11) |
| `Modules/Permissions/Http/Controllers/Admin/RoleController.php` | 6 | `show()` 404 (S11) |
| `Modules/Cms/Classes/PageSize.php` (new), `Modules/Cms/Http/Controllers/CmsController.php` | 7 | Select2 page-size cap (S1) |
| `app/Http/Middleware/SecurityHeaders.php` (new), `app/Http/Kernel.php` | 8 | Security headers and Report-Only CSP (S3) |
| `config/app.php`, `config/session.php` | 9 | Safe debug default, JSON sessions (S2) |
| `config/filesystems.php`, `Modules/Cms/Providers/CmsServiceProvider.php`, `Modules/Cms/Http/Controllers/Admin/TinymceController.php` | 10 | Failed writes throw; TinyMCE reports them (S17) |
| `Modules/Frontend/Resources/assets/form/*.php`, `public/modules/frontend/form/*.php` | 10 | Deleted (S16) |
| `app/Pagination/LengthAwarePaginator.php`, `app/DataTables/{EloquentDataTable,DataProcessor}.php`, `app/Glide/Encoder.php` | 11 | `#[\Override]` (S18) |
| `scripts/check-composer-audit.sh` (new), `docs/security/front-end-libraries.md` (new) | 13 | S4 gate and report |
| `tests/e2e/specs/security/*.spec.ts` | 1–12 | New and updated security specs |
| `tests/Unit/*.php`, `tests/Feature/*.php` | 1–3, 7–11, 13 | New PHPUnit tests |
| `tests/e2e/parity/admin-urls.ts` | 4 | `MATRIX_EXCLUDED` emptied (no state-changing admin GET routes remain) |
| `tests/e2e/PARITY.md` | 1, 3, 4, 5, 6, 9, 12, 13 | Runbook facts and counts |

**Running totals** (each task's gate expects these):

| After task | `npm test` (parity + chromium) | PHPUnit tests |
|---|---|---|
| start | 368 (321 + 47) | 19 |
| 1 | 371 (321 + 50) | 22 |
| 2 | 372 (321 + 51) | 27 |
| 3 | 375 (321 + 54) | 30 |
| 4 | 380 (321 + 59) | 30 |
| 5 | 383 (321 + 62) | 30 |
| 6 | 387 (321 + 66) | 30 |
| 7 | 389 (321 + 68) | 33 |
| 8 | 391 (321 + 70) | 35 |
| 9 | 392 (321 + 71) | 38 |
| 10 | 393 (321 + 72) | 40 |
| 11 | 393 (321 + 72) | 43 |
| 12 | 394 (321 + 73) | 43 |
| 13 | 394 (321 + 73) | 46 |

---

### Task 1: Lead lists: stored XSS, invalid UTF-8 and the opportunity link column (S5, S12, P1-R39)

Business-critical: this is the admin view of the 3,700 leads.

- **S5 (research C: C1, C2, C3).**
  - An anonymous visitor's contact message is stored in `contact_us.description`. `{Project,Opportunity}Controller::data_requests` return it as the **raw** column `breef`, and DataTables writes it with `innerHTML` in every staff session (C1, C2).
  - The `link` column is the visitor's `Referer` header. `requests.blade.php` renders it as an `<a href>` whatever its scheme, so `javascript:` links are possible (C3).
- **S12 (research B).** Four dump leads hold CESU-8 bytes that `json_encode` rejects. Their list pages, and the lead summary modal, fail with "Malformed UTF-8".
- **P1-R39.** `OpportunityController::data_requests` adds a `link` column that calls `route()` with a `slug` that `contact_us` does not have. It throws on every page.

**Files:**
- Modify: `Modules/Backend/Http/Controllers/Admin/ProjectController.php` (`data_requests`: `rawColumns`)
- Modify: `Modules/Backend/Http/Controllers/Admin/OpportunityController.php` (`data_requests`: `rawColumns` and the `link` column)
- Modify: `Modules/Backend/Resources/views/admin/requests.blade.php` (the `link` column render, around line 264)
- Modify: `config/datatables.php` (`json.options`)
- Modify: `Modules/Cms/Classes/ResponseHandler.php` (AJAX JSON encoding)
- Modify: `tests/e2e/PARITY.md` (the Malformed UTF-8 bullet)
- Create: `tests/e2e/specs/security/lead-lists.spec.ts`
- Create: `tests/Unit/InvalidUtf8JsonTest.php`
- Existing tests that observe this change, none of which needs a baseline change:
  - `specs/parity/admin-sections.spec.ts`, "admin section project-requests" and "admin section opportunity-requests". Both are `structureOnly` (headers and row keys only; the key set does not change).
  - `specs/parity/behaviour.spec.ts`, the contact validation JSON, and `admin-sections.spec.ts` `login-failed.json`. Both come from `ResponseHandler`, and valid UTF-8 encodes byte for byte the same.

**Interfaces:**
- Consumes: nothing from earlier tasks.
- Produces:
  - `config('datatables.json.options')` includes `JSON_INVALID_UTF8_SUBSTITUTE`. Task 7 adds `max_length` to the same file.
  - The `data_requests` methods still start with `$list = ContactUS::query();`. Task 5 adds an `authorize` call above it, and Task 7 changes the `DataTables::of($list)` line.
  - Fixture addresses `s5-probe@aldar.test`, `s5-link@aldar.test` and `s12-probe@aldar.test`, created and deleted by the spec.

- [ ] **Step 1: Write the failing Playwright spec**

Create `tests/e2e/specs/security/lead-lists.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { sql } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S5, S12, P1-R39. Synthetic leads only (aldar.test addresses): planted here, removed in afterAll.
const XSS_EMAIL = 's5-probe@aldar.test';
const LINK_EMAIL = 's5-link@aldar.test';
const UTF8_EMAIL = 's12-probe@aldar.test';
const MARKER = `S5-${Date.now()}`;
const MESSAGE = `${MARKER} <img src=x onerror="window.__s5=1"> & "q" 'a'`;
const JS_LINK = 'javascript://%0Awindow.__s5link=1';
const HTTP_LINK = 'https://example.invalid/s5-page';
// U+1F600 as a CESU-8 surrogate pair: MariaDB's utf8mb4 stores these bytes, json_encode rejects them.
const INVALID_UTF8_HEX = 'EDA0BDEDB880';
// No dump lead is this old, so the list's own date filter isolates the planted lead.
const UTF8_DAY = { 'filter[0][name]': 'daterange', 'filter[0][value]': '01/01/2001 - 01/01/2001' };

const quote = (value: string) => `'${value.replace(/\\/g, '\\\\').replace(/'/g, "''")}'`;
const removeFixtures = () =>
  sql(`DELETE FROM contact_us WHERE email IN (${[XSS_EMAIL, LINK_EMAIL, UTF8_EMAIL].map(quote).join(', ')})`);

test.beforeAll(() => {
  requireLocal('plants lead fixtures in the local database');
  removeFixtures();
  // A future date puts both S5 leads on page 1: the list sorts by date, newest first.
  sql(`INSERT INTO contact_us (sender, email, phone, description, link, created_at, updated_at) VALUES
    ('S5 Probe', ${quote(XSS_EMAIL)}, '0', ${quote(MESSAGE)}, ${quote(JS_LINK)}, '2030-01-01 12:00:00', '2030-01-01 12:00:00'),
    ('S5 Link', ${quote(LINK_EMAIL)}, '0', ${quote(`${MARKER} plain`)}, ${quote(HTTP_LINK)}, '2030-01-01 12:00:01', '2030-01-01 12:00:01')`);
  sql(`INSERT INTO contact_us (sender, email, phone, description, created_at, updated_at) VALUES
    ('S12 Probe', ${quote(UTF8_EMAIL)}, '0', CONCAT('S12 probe ', UNHEX('${INVALID_UTF8_HEX}'), ' end'), '2001-01-01 12:00:00', '2001-01-01 12:00:00')`);
  // Precondition: the invalid bytes really reached the column, so the S12 test cannot pass vacuously.
  expect(sql(`SELECT HEX(description) FROM contact_us WHERE email = ${quote(UTF8_EMAIL)}`)).toContain(INVALID_UTF8_HEX);
});
test.afterAll(() => removeFixtures());

test('the lead list JSON escapes a visitor message (S5)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const table = page.waitForResponse(r => r.url().includes('/en/admin/projects/data_requests'));
  await page.goto('/en/admin/projects/requests');
  const json = (await (await table).json()) as { data: Array<Record<string, string>> };
  const row = json.data.find(r => r.email === XSS_EMAIL);
  expect(row, 'the planted lead is on page 1').toBeDefined();
  expect(row!.breef).toBe(`${MARKER} &lt;img src=x onerror=&quot;window.__s5=1&quot;&gt; &amp; &quot;q&quot; &#039;a&#039;`);
});

test('the lead list shows the message as text and links only http(s) addresses (S5)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  await page.goto('/en/admin/projects/requests');
  const body = page.locator('#datatable tbody');
  await expect(body).toContainText(MARKER);
  await expect(body).toContainText(MESSAGE);
  await expect(page.locator('#datatable img[src="x"]')).toHaveCount(0);
  await page.waitForLoadState('networkidle');
  expect(await page.evaluate(() => (window as unknown as { __s5?: number }).__s5)).toBeUndefined();
  // The sender cells legitimately use href="javascript:;" — only a javascript:// link is the attack.
  await expect(page.locator('#datatable a[href^="javascript://"]')).toHaveCount(0);
  await expect(body).toContainText(JS_LINK);
  await expect(page.locator(`#datatable a[href="${HTTP_LINK}"]`)).toHaveCount(1);
});

test('both lead lists and both lead summaries answer valid JSON for a lead with invalid UTF-8 (S12, P1-R39)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const id = sql(`SELECT id FROM contact_us WHERE email = ${quote(UTF8_EMAIL)}`);
  for (const section of ['projects', 'opportunity']) {
    const list = await page.request.get(`/en/admin/${section}/data_requests`, {
      headers: AJAX_HEADERS,
      params: { draw: '1', start: '0', length: '50', ...UTF8_DAY },
    });
    expect(list.status(), `${section} list status`).toBe(200);
    const json = JSON.parse(await list.text()) as { error?: string; recordsFiltered: number; data: Array<Record<string, string>> };
    expect(json.error, `${section} list error`).toBeUndefined();
    expect(json.recordsFiltered, `${section} filtered rows`).toBe(1);
    expect(json.data[0].breef).toContain('S12 probe');
    expect(json.data[0].breef).toContain('�');

    const summary = await page.request.get(`/en/admin/${section}/request_summary`, { headers: AJAX_HEADERS, params: { model: id } });
    expect(summary.status(), `${section} summary status`).toBe(200);
    expect((JSON.parse(await summary.text()) as { success: boolean }).success).toBe(true);
  }
});
```

- [ ] **Step 2: Write the failing PHPUnit test**

Create `tests/Unit/InvalidUtf8JsonTest.php`:

```php
<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Modules\Cms\Classes\ResponseHandler;
use Tests\TestCase;

/** S12: JSON answers substitute U+FFFD for invalid UTF-8 instead of failing, and valid output is unchanged. */
class InvalidUtf8JsonTest extends TestCase
{
    private function ajaxRequest(): Request
    {
        return Request::create('/', 'GET', [], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
    }

    public function test_an_ajax_response_handler_substitutes_invalid_utf8(): void
    {
        $response = (new ResponseHandler(['description' => "probe \xED\xA0\xBD\xED\xB8\x80 end"]))->toResponse($this->ajaxRequest());

        $decoded = json_decode($response->getContent(), true);
        $this->assertIsArray($decoded);
        $this->assertStringStartsWith('probe ', $decoded['description']);
        $this->assertStringContainsString("\u{FFFD}", $decoded['description']);
    }

    public function test_valid_utf8_encodes_exactly_as_before(): void
    {
        $data = ['title' => 'عقارات "Aldar" & <b>/path</b>', 'count' => 3];

        $this->assertSame(json_encode($data), (new ResponseHandler($data))->toResponse($this->ajaxRequest())->getContent());
    }

    public function test_datatables_json_substitutes_invalid_utf8(): void
    {
        $this->assertSame(JSON_INVALID_UTF8_SUBSTITUTE, config('datatables.json.options') & JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
```

- [ ] **Step 3: Run both and confirm they fail for the right reasons**

```bash
(cd tests/e2e && npx playwright test specs/security/lead-lists.spec.ts --reporter=line | tail -20)
docker compose exec -T app php vendor/bin/phpunit tests/Unit/InvalidUtf8JsonTest.php
```

Expected:
- Playwright: `3 failed`.
  - Test 1 receives the raw `<img …>` string in `breef`.
  - Test 2 finds `img[src="x"]` (count 1) or a `javascript://` link.
  - Test 3 fails on `projects list error` (a `Malformed UTF-8` message).
- PHPUnit:
  - `test_an_ajax_response_handler_substitutes_invalid_utf8` errors with `InvalidArgumentException: Malformed UTF-8 characters`.
  - `test_datatables_json_substitutes_invalid_utf8` fails (0 is not 2097152).
  - `test_valid_utf8_encodes_exactly_as_before` passes.
- If the `beforeAll` precondition fails (no `EDA0BD` in the stored hex), MariaDB refused the bytes. Stop and report: do not change the fixture.

- [ ] **Step 4: Escape `breef` in ProjectController (C1)**

In `Modules/Backend/Http/Controllers/Admin/ProjectController.php`, replace:

```php
        $rawColumns = [];
        $rawColumns[] = 'breef';
        $rawColumns[] = 'actions';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function request_summary(Request $request)
```

with:

```php
        $rawColumns = [];
        // 'breef' is the visitor's message: it is never raw, so the DataProcessor escapes it (S5).
        $rawColumns[] = 'actions';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function request_summary(Request $request)
```

Leave `data_properties` alone: its `$rawColumns[] = 'breef';` names a column that method never adds.

- [ ] **Step 5: Escape `breef` and drop the broken `link` column in OpportunityController (C2, P1-R39)**

In `Modules/Backend/Http/Controllers/Admin/OpportunityController.php`, apply the same replacement as Step 4. The block that ends with `public function request_summary(Request $request)` is unique in this file too.

Then replace this block. It is the one indented with 8 spaces and followed by `->addColumn('breef'`; `data_properties` has a differently indented copy that stays.

```php
        ->addColumn('link', function($model){
            $category = $model->category;
            return route('OpportunityController@single', ['type' => (!is_null($category) ? $category->slug : 'unknown'), 'slug' => $model->slug]);
        })
        ->addColumn('breef', function($model){
```

with:

```php
        // No computed 'link' column: contact_us has no slug or category, so route() threw on every page (P1-R39).
        // Each row keeps its own contact_us.link, as in ProjectController::data_requests.
        ->addColumn('breef', function($model){
```

- [ ] **Step 6: Substitute invalid UTF-8 in DataTables JSON and in ResponseHandler (S12)**

In `config/datatables.php`, replace:

```php
    'json'           => [
        'header'  => [],
        'options' => 0,
    ],
```

with:

```php
    'json'           => [
        'header'  => [],
        // Spam leads hold invalid UTF-8: substitute U+FFFD instead of failing the whole page (S12).
        // Valid UTF-8 encodes byte for byte as it did with 0.
        'options' => JSON_INVALID_UTF8_SUBSTITUTE,
    ],
```

In `Modules/Cms/Classes/ResponseHandler.php`, replace:

```php
            return response()->json($this->data, $this->code);
```

with:

```php
            // Invalid UTF-8 in stored leads becomes U+FFFD instead of a 500 (S12).
            return response()->json($this->data, $this->code, [], JSON_INVALID_UTF8_SUBSTITUTE);
```

- [ ] **Step 7: Link only http(s) addresses in the lead list (C3)**

In `Modules/Backend/Resources/views/admin/requests.blade.php`, replace the one line:

```js
                            return row.link ? `<a href="${row.link}" target="_blank" rel="noopener">${row.link}</span>` : '';
```

with these two lines. The Blade comment renders nothing, and the stray `</span>` stays so the DOM for http(s) links is unchanged.

```js
                            {{-- The link is the visitor's Referer header: only http(s) becomes a link (S5). --}}
                            return row.link ? (/^https?:\/\//i.test(row.link) ? `<a href="${row.link}" target="_blank" rel="noopener">${row.link}</span>` : row.link) : '';
```

- [ ] **Step 8: Run the new tests and confirm they pass**

```bash
(cd tests/e2e && npx playwright test specs/security/lead-lists.spec.ts --reporter=line | tail -5)
docker compose exec -T app php vendor/bin/phpunit tests/Unit/InvalidUtf8JsonTest.php
```

Expected: `3 passed`; PHPUnit `OK (3 tests`.

- [ ] **Step 9: Update the runbook fact**

In `tests/e2e/PARITY.md`, section "Laravel 7 behaviour recorded as-is", replace the bullet that starts with "- The admin project and opportunity request lists" with:

```markdown
- The admin project and opportunity request lists (`admin/{projects,opportunity}/data_requests`) read the same unfiltered `contact_us` rows. On Laravel 7 and 13, pages whose window held spam submissions with invalid UTF-8 answered a DataTables `Malformed UTF-8` error (4 of the 74 pages per list at the page's 50-row length). Since Phase 2 (S12) the lists and the lead summary substitute U+FFFD for invalid bytes, so every lead stays reachable; `specs/security/lead-lists.spec.ts` plants such a lead.
```

- [ ] **Step 10: Run the gate** (Global Constraints, "The gate")

Expected:
- `npm test`: `371 passed` (321 parity + 50 chromium).
- PHPUnit: `OK (22 tests`.
- `structure matches tests/upgrade/`.
- phpstan: `[OK] No errors`.
- `deprecation channel on` and `no deprecations`.
- Both `git diff` commands print nothing.
- No baseline acceptance is needed. A diff in `admin-sections` project-requests or opportunity-requests is a regression to report.

- [ ] **Step 11: Commit**

```bash
git add Modules/Backend/Http/Controllers/Admin/ProjectController.php Modules/Backend/Http/Controllers/Admin/OpportunityController.php \
  Modules/Backend/Resources/views/admin/requests.blade.php config/datatables.php Modules/Cms/Classes/ResponseHandler.php \
  tests/e2e/specs/security/lead-lists.spec.ts tests/Unit/InvalidUtf8JsonTest.php tests/e2e/PARITY.md
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Escape visitor text in the admin lead lists and keep their JSON valid (S5, S12, P1-R39)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

---

### Task 2: Secrets and logging hygiene (S20, S15, S14)

- **S20.** `UserController::store`, `update` and `updateProfile` call `\Log::debug($request->all())`, so plaintext passwords reach `storage/logs`.
- **S15.** `AppServiceProvider::boot()` turns on `app.debug` and Debugbar for one hard-coded IP address. Under `--no-dev` it would also answer 500 for that address.
- **S14.** A hard-coded currconv API key sits in `FrontendController`. It moves to `config('services.currconv.key')` / `CURRCONV_API_KEY`. With no key configured, the refresh does nothing, keeps the stored rates, and logs a warning.

Visitors never call currconv: prices read `cms_contents.currency_value`, and only the `update_currency` command refreshes it.

**Files:**
- Modify: `Modules/Cms/Http/Controllers/Admin/UserController.php` (delete the three `\Log::debug($request->all());` lines)
- Modify: `app/Providers/AppServiceProvider.php` (whole file)
- Modify: `Modules/Frontend/Http/Controllers/FrontendController.php` (key property, `getCurrencyPrice`, `storeCurrencies`)
- Modify: `config/services.php`, `.env.example`
- Create: `tests/Unit/SecretsAndLoggingGuardTest.php`, `tests/Feature/CurrencyApiKeyTest.php`, `tests/e2e/specs/security/secrets-and-logs.spec.ts`
- Existing tests that observe this change: none. The currency route specs in `maintenance-routes.spec.ts` never reach `storeCurrencies()`.
- Baseline impact: none.

**Interfaces:**
- Consumes: nothing from earlier tasks.
- Produces:
  - `FrontendController::currconvKey(): ?string` (protected).
  - With `CURRCONV_API_KEY` unset (the local default), `php artisan update_currency` makes no HTTP call. Task 4 relies on this as a second safety net behind its request interception.
  - `app/Providers/AppServiceProvider.php` no longer references Debugbar. Task 9's require-dev guard relies on this.

- [ ] **Step 1: Write the failing source guards**

Create `tests/Unit/SecretsAndLoggingGuardTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Source guards for S14 (currconv key), S15 (IP-gated debug switch) and S20 (request bodies in logs).
 * They read files only, and their failure messages never print file contents, so no secret is echoed.
 */
class SecretsAndLoggingGuardTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public function test_the_currconv_key_is_read_from_config(): void
    {
        $source = file_get_contents($this->root() . '/Modules/Frontend/Http/Controllers/FrontendController.php');

        $this->assertTrue(str_contains($source, "config('services.currconv.key')"), 'FrontendController must read services.currconv.key');
        $this->assertFalse(str_contains($source, '$apiKey'), 'FrontendController still declares the $apiKey property');
        $this->assertSame(0, preg_match("/['\"][0-9a-f]{20,}['\"]/", $source), 'FrontendController contains a key-shaped hex literal');
    }

    public function test_the_currconv_key_comes_from_the_environment(): void
    {
        $services = file_get_contents($this->root() . '/config/services.php');
        $example = file_get_contents($this->root() . '/.env.example');

        $this->assertTrue(str_contains($services, "env('CURRCONV_API_KEY')"), 'config/services.php must read CURRCONV_API_KEY');
        $this->assertSame(1, preg_match('/^CURRCONV_API_KEY=$/m', $example), '.env.example must list an empty CURRCONV_API_KEY');
    }

    public function test_no_ip_address_turns_on_debug_mode(): void
    {
        $source = file_get_contents($this->root() . '/app/Providers/AppServiceProvider.php');

        $this->assertFalse(str_contains($source, 'ipAddresses'), 'AppServiceProvider still has an IP allow-list');
        $this->assertFalse(str_contains($source, 'Debugbar'), 'AppServiceProvider still references Debugbar');
        $this->assertFalse(str_contains($source, "'app.debug'"), 'AppServiceProvider still changes app.debug');
    }

    public function test_request_bodies_are_never_logged(): void
    {
        $offenders = [];
        foreach (['app', 'Modules'] as $dir) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root() . '/' . $dir, RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                $path = $file->getPathname();
                if (! str_ends_with($path, '.php') || str_contains($path, '/Resources/assets/')) {
                    continue;
                }
                if (preg_match('/(Log::\w+|logger)\s*\(\s*\$request->(all|input|post|except|only)\s*\(/', file_get_contents($path))) {
                    $offenders[] = substr($path, strlen($this->root()) + 1);
                }
            }
        }

        $this->assertSame([], $offenders, 'Request bodies can hold passwords: never log them (S20).');
    }
}
```

- [ ] **Step 2: Write the failing Playwright test for S20**

Create `tests/e2e/specs/security/secrets-and-logs.spec.ts`:

```ts
import { randomBytes } from 'node:crypto';
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken, csrfToken } from '../../support/csrf';
import { appShell, sql } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S20: a password sent to the user forms never reaches storage/logs.
test('user store, update and profile update never write the password to the logs', async ({ page, request }) => {
  requireLocal('reads storage/logs in the app container');
  const linesMatching = (text: string) => Number(appShell(`grep -rh -- '${text}' storage/logs 2>/dev/null | wc -l`));

  // Non-vacuous: prove that a web request's log lines land in storage/logs (the honeypot logs at info).
  const honeypotBefore = linesMatching('dropped by the honeypot');
  const anonymousToken = await anonymousCsrfToken(request);
  await request.post('/en/contact-us/subscribe', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': anonymousToken },
    multipart: { emails: 's20-canary@aldar.test', aldar_hp: 'filled' },
  });
  expect(linesMatching('dropped by the honeypot')).toBe(honeypotBefore + 1);

  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const token = await csrfToken(page);
  const adminId = sql("SELECT id FROM users WHERE username = 'parity-admin'");
  const superadminId = sql("SELECT id FROM users WHERE username = 'parity-superadmin'");
  // 15 characters, inside the 8-20 rule; the confirmation differs, so validation fails and nothing is saved.
  const secret = `S20${randomBytes(6).toString('hex')}`;
  const body = { _token: token, password: secret, password_confirmation: `${secret}x` };

  for (const url of ['/en/admin/users/store', `/en/admin/users/${adminId}/update`, `/en/admin/users/${superadminId}/update_profile`]) {
    const response = await page.request.post(url, { headers: AJAX_HEADERS, form: body });
    expect(response.status(), url).toBe(422);
  }

  expect(linesMatching(secret)).toBe(0);
});
```

- [ ] **Step 3: Run both and confirm they fail**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/SecretsAndLoggingGuardTest.php
(cd tests/e2e && npx playwright test specs/security/secrets-and-logs.spec.ts --reporter=line | tail -10)
```

Expected:
- PHPUnit: `FAILURES!` with 4 failures, whose messages are the ones in the test. They print no file content.
- Playwright: `1 failed`, at the last `expect`: 3 matching lines instead of 0. Before the fix, `\Log::debug` wrote the password once per endpoint.

Do not run `tests/Feature/CurrencyApiKeyTest.php` before Step 7. On the old code, `storeCurrencies()` would call the API and end in `dd()`.

- [ ] **Step 4: Stop logging request bodies (S20)**

```bash
perl -ni -e 'print unless /^\s*\\Log::debug\(\$request->all\(\)\);\s*$/' Modules/Cms/Http/Controllers/Admin/UserController.php
grep -c 'Log::debug' Modules/Cms/Http/Controllers/Admin/UserController.php
git diff --stat -- Modules/Cms/Http/Controllers/Admin/UserController.php
```

Expected: `0`, then `1 file changed, 3 deletions(-)`.

- [ ] **Step 5: Remove the IP-gated debug switch (S15)**

Replace the whole content of `app/Providers/AppServiceProvider.php` with:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Laravel 7 pagination links: see App\Pagination\LengthAwarePaginator.
        $this->app->bind(\Illuminate\Pagination\LengthAwarePaginator::class, \App\Pagination\LengthAwarePaginator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();
        Carbon::setLocale(app()->getLocale());
        setlocale(LC_TIME,'ar_BH');
        Schema::defaultStringLength(191);
    }
}
```

`git diff -- app/Providers/AppServiceProvider.php` must show only deletions: the property line, the `if` block and its blank lines.

- [ ] **Step 6: Read the currconv key from config (S14)**

1. In `config/services.php`, after the `'ses' => [ … ],` entry and before the closing `];`, add:

```php

    // currconv.com free currency API, used only by the daily `update_currency` command (S14).
    'currconv' => [
        'key' => env('CURRCONV_API_KEY'),
    ],
```

2. Append the empty key to `.env.example`:

```bash
printf '\nCURRCONV_API_KEY=\n' >> .env.example
```

3. Delete the hard-coded key property from `FrontendController` without printing it:

```bash
perl -ni -e 'print unless /^\s*protected \$apiKey = /' Modules/Frontend/Http/Controllers/FrontendController.php
grep -c 'protected \$apiKey' Modules/Frontend/Http/Controllers/FrontendController.php
```

Expected: `0`.

4. In the same file, replace:

```php
    public function getCurrencyPrice($currency, $defaultCurrency = 'TRY')
    {
        $url = "https://free.currconv.com/api/v7/convert?q=" . $defaultCurrency . "_" . $currency . "&compact=ultra&apiKey=" . $this->apiKey;
```

with:

```php
    /**
     * The currconv.com API key from config/services.php (CURRCONV_API_KEY), or null when it is not set (S14).
     */
    protected function currconvKey(): ?string
    {
        $key = config('services.currconv.key');

        return is_string($key) && $key !== '' ? $key : null;
    }

    public function getCurrencyPrice($currency, $defaultCurrency = 'TRY')
    {
        $url = "https://free.currconv.com/api/v7/convert?q=" . $defaultCurrency . "_" . $currency . "&compact=ultra&apiKey=" . $this->currconvKey();
```

5. Replace:

```php
    public  function storeCurrencies() {
        $url = 'https://free.currconv.com/api/v7/currencies?apiKey=' . $this->apiKey;
```

with:

```php
    public  function storeCurrencies() {
        // Without a key there is nothing to ask: keep the stored rates and say why (S14).
        if ($this->currconvKey() === null) {
            \Log::warning('Currency rates not refreshed: CURRCONV_API_KEY is not set.');

            return;
        }
        $url = 'https://free.currconv.com/api/v7/currencies?apiKey=' . $this->currconvKey();
```

Leave the rest of the file unchanged, including `currencyApi()` and the `catch` block.

- [ ] **Step 7: Write the behaviour test for an empty key**

Create `tests/Feature/CurrencyApiKeyTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Modules\Cms\Entities\Content;
use Modules\Frontend\Http\Controllers\FrontendController;
use Tests\TestCase;

/** S14: with no currconv key configured, the refresh makes no API call and keeps the stored rates. */
class CurrencyApiKeyTest extends TestCase
{
    use DatabaseTransactions;

    /** @var array<string, string|false> */
    private $proxies = [];

    protected function setUp(): void
    {
        parent::setUp();
        // Belt and braces: even if the guard regressed, curl could not leave the container.
        foreach (['http_proxy', 'https_proxy', 'HTTPS_PROXY'] as $name) {
            $this->proxies[$name] = getenv($name);
            putenv("{$name}=http://127.0.0.1:9");
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->proxies as $name => $value) {
            putenv($value === false ? $name : "{$name}={$value}");
        }
        parent::tearDown();
    }

    private function storedRates(): array
    {
        return Content::withDisabled()->where('type', 'currencies')->orderBy('id')->pluck('currency_value', 'id')->all();
    }

    public function test_an_empty_key_keeps_the_stored_rates_and_logs_why(): void
    {
        config(['services.currconv.key' => '']);
        $before = $this->storedRates();
        Log::spy();

        (new FrontendController)->storeCurrencies();

        $this->assertNotSame([], $before);
        $this->assertSame($before, $this->storedRates());
        Log::shouldHaveReceived('warning')->once()->with('Currency rates not refreshed: CURRCONV_API_KEY is not set.');
    }
}
```

- [ ] **Step 8: Run the tests and confirm they pass**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/SecretsAndLoggingGuardTest.php tests/Feature/CurrencyApiKeyTest.php
(cd tests/e2e && npx playwright test specs/security/secrets-and-logs.spec.ts --reporter=line | tail -5)
docker compose exec -T app php artisan update_currency; echo "exit $?"
```

Expected:
- PHPUnit `OK (5 tests`.
- Playwright `1 passed`.
- `exit 0`: the local `.env` has no `CURRCONV_API_KEY`, so the command only logs the warning.
- If `update_currency` shows a debug dump instead, the local `.env` already sets `CURRCONV_API_KEY`. Report it and do not print the value.

- [ ] **Step 9: Run the gate** (Global Constraints, "The gate")

Expected:
- `npm test`: `372 passed` (321 + 51).
- PHPUnit: `OK (27 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.
- No baseline change.

- [ ] **Step 10: Commit**

```bash
git add Modules/Cms/Http/Controllers/Admin/UserController.php app/Providers/AppServiceProvider.php \
  Modules/Frontend/Http/Controllers/FrontendController.php config/services.php .env.example \
  tests/Unit/SecretsAndLoggingGuardTest.php tests/Feature/CurrencyApiKeyTest.php tests/e2e/specs/security/secrets-and-logs.spec.ts
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Stop logging user request bodies, drop the IP debug switch, and read the currconv key from config (S20, S15, S14)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

Before committing, check that `git diff --cached` contains no 20-character hex literal. The command prints a count only:

```bash
git diff --cached | grep -cE "^\+.*['\"][0-9a-f]{20}['\"]"
```

Expected: `0`.

---

### Task 3: Notification and staff JSON endpoints (S6, S8, S9)

- **S6.** `POST admin/notification/postWebToken` is public. It accepts any non-empty `data_token`, has no rate limit, and its `catch` returns the exception message, code, line and file.
  - Fix:
    - validate `required|string|max:191` (`notif_tokens.token` is `varchar(191)`);
    - allow 10 attempts per IP per minute, answering a JSON 429 beyond that;
    - return a generic error body.
  - The limit lives in the controller: `app/Exceptions/Handler.php` renders every `HttpException`, including the `throttle` middleware's `ThrottleRequestsException`, as a 302 to the login page, so route throttling could never answer 429.
- **S8.** `EnsureStaff` answers a signed-in but denied JSON caller with a plain-text 403. JSON callers now get `{"success": false, "message": "Forbidden."}`, the same shape as its 401 branch.
- **S9.** `GET admin/notification/config` answers 200 to anonymous visitors. No page's JavaScript requests it: `firebase_scripts.blade.php` inlines the values and is itself commented out in both layouts. It goes behind `staff`.

**Files:**
- Modify: `app/Http/Middleware/EnsureStaff.php`
- Modify: `Modules/Notification/Http/Controllers/NotificationController.php` (imports, `$validationsRules`, `postWebToken`)
- Modify: `Modules/Notification/Routes/web.php` (line 22)
- Modify: `tests/e2e/PARITY.md` (the notification config bullet)
- Create: `tests/Unit/EnsureStaffJsonTest.php`, `tests/Feature/WebTokenErrorResponseTest.php`, `tests/e2e/specs/security/notification-endpoints.spec.ts`
- Existing tests that observe this change:
  - `specs/parity/permissions-matrix.spec.ts`: **baseline change, 1 cell.**
  - `scripts/upgrade/check-structure.sh` (`tests/upgrade/routes.json`): **baseline change, 1 cell.**
  - `specs/security/attachments.spec.ts` and `maintenance-routes.spec.ts` use the 401 branch and non-JSON 403s. Those stay unchanged.

**Interfaces:**
- Consumes: nothing from earlier tasks.
- Produces:
  - `NotificationController::WEB_TOKEN_MAX_ATTEMPTS = 10`.
  - `EnsureStaff` JSON 403 body `{"success":false,"message":"Forbidden."}`.
  - The route `NotificationsController@getConfig` with route middleware `staff`.
  - Task 5 edits `postIndex` and `getList` in the same controller, and Task 6 edits `index`.

**Expected cell diffs** (the controller accepts them after the commit; see "Baseline acceptance"):

`tests/e2e/snapshots/parity/permissions-matrix.spec.ts/matrix.json`:
```
/en/admin/notification/config | anonymous: "200" -> "302 login"
```

`tests/upgrade/routes.json`:
```
GET admin/notification/config | middleware | 4: (absent) -> "staff"
```

- [ ] **Step 1: Write the failing EnsureStaff test (S8)**

Create `tests/Unit/EnsureStaffJsonTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureStaff;
use App\User;
use Illuminate\Http\Request;
use Tests\TestCase;

/** S8: a signed-in but denied caller that expects JSON gets a JSON 403; browsers keep the plain-text body. */
class EnsureStaffJsonTest extends TestCase
{
    private function deniedRequest(string $accept): Request
    {
        $request = Request::create('/en/admin/tinymce/uploader', 'POST', [], [], [], ['HTTP_ACCEPT' => $accept]);
        $user = new User();
        $user->disabled_at = now();
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    public function test_a_json_caller_gets_a_json_403(): void
    {
        $response = (new EnsureStaff)->handle($this->deniedRequest('application/json'), fn () => response('next'));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame(['success' => false, 'message' => 'Forbidden.'], json_decode($response->getContent(), true));
    }

    public function test_a_browser_still_gets_the_plain_text_403(): void
    {
        $response = (new EnsureStaff)->handle($this->deniedRequest('text/html'), fn () => response('next'));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Forbidden.', $response->getContent());
    }
}
```

- [ ] **Step 2: Write the failing exception-detail test (S6)**

Create `tests/Feature/WebTokenErrorResponseTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Modules\Notification\Entities\FirebaseToken;
use Modules\Notification\Http\Controllers\NotificationController;
use Tests\TestCase;

/** S6: a storage failure in postWebToken never returns exception details to the client. */
class WebTokenErrorResponseTest extends TestCase
{
    use DatabaseTransactions;

    public function test_a_storage_failure_returns_a_generic_error(): void
    {
        FirebaseToken::saving(function () {
            throw new \RuntimeException('simulated failure in /var/www/html/secret-path.php');
        });
        $request = Request::create('/en/admin/notification/postWebToken', 'POST', ['data_token' => 'phpunit-s6-token'], [], [], [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'HTTP_ACCEPT'           => 'application/json',
            'REMOTE_ADDR'           => '203.0.113.9',
        ]);
        $this->app->instance('request', $request);

        $response = (new NotificationController)->postWebToken($request)->toResponse($request);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse(str_contains($response->getContent(), 'simulated failure'), 'the exception message leaked');
        $this->assertFalse(str_contains($response->getContent(), '/var/www'), 'a server path leaked');
        $this->assertSame([], json_decode($response->getContent(), true)['errors']);
    }
}
```

- [ ] **Step 3: Write the failing Playwright spec (S6, S9)**

Create `tests/e2e/specs/security/notification-endpoints.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { artisan, sql, sqlScalar } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

const WEB_TOKEN = '/en/admin/notification/postWebToken';
const PROBE_TOKENS = "SELECT COUNT(*) FROM notif_tokens WHERE token LIKE 'e2e-s6-%'";

test.beforeAll(() => requireLocal('uses the local parity accounts and writes notif_tokens rows'));

test('the Firebase web config is for staff only (S9)', async ({ page, request }) => {
  const anonymous = await request.get('/en/admin/notification/config', { maxRedirects: 0 });
  expect(anonymous.status()).toBe(302);
  expect(anonymous.headers()['location']).toMatch(/\/authenticate\/login$/);
  expect((await request.get('/en/admin/notification/config', { headers: AJAX_HEADERS, maxRedirects: 0 })).status()).toBe(401);

  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const staff = await page.request.get('/en/admin/notification/config', { headers: AJAX_HEADERS });
  expect(staff.status()).toBe(200);
  expect(Object.keys(await staff.json()).sort()).toEqual([
    'apiKey', 'appId', 'authDomain', 'databaseURL', 'measurementId', 'messagingSenderId', 'projectId',
  ]);
});

test.describe('postWebToken (S6)', () => {
  test.beforeEach(() => {
    artisan('cache:clear'); // resets the per-IP limiter
  });
  test.afterAll(() => sql("DELETE FROM notif_tokens WHERE token LIKE 'e2e-s6-%'"));

  test('an oversized token is refused and stores nothing', async ({ request }) => {
    const token = await anonymousCsrfToken(request);
    const before = sqlScalar(PROBE_TOKENS);
    const response = await request.post(WEB_TOKEN, {
      headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
      form: { data_token: `e2e-s6-${'x'.repeat(300)}` },
    });
    expect(response.status()).toBe(422);
    expect(sqlScalar(PROBE_TOKENS)).toBe(before);
  });

  test('the eleventh attempt within a minute is refused with a generic JSON 429', async ({ request }) => {
    const token = await anonymousCsrfToken(request);
    const attempt = () => request.post(WEB_TOKEN, { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token }, form: {} });
    for (let i = 1; i <= 10; i++) {
      expect((await attempt()).status(), `attempt ${i}`).toBe(422);
    }
    const refused = await attempt();
    expect(refused.status()).toBe(429);
    const body = await refused.json();
    expect(body.success).toBe(false);
    expect(body.errors).toEqual([]);
  });
});
```

- [ ] **Step 4: Run the three files and confirm they fail**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/EnsureStaffJsonTest.php tests/Feature/WebTokenErrorResponseTest.php
(cd tests/e2e && npx playwright test specs/security/notification-endpoints.spec.ts --reporter=line | tail -15)
```

Expected:
- PHPUnit:
  - `test_a_json_caller_gets_a_json_403` fails: `null` is not the array.
  - `test_a_storage_failure_returns_a_generic_error` fails with "the exception message leaked".
  - `test_a_browser_still_gets_the_plain_text_403` passes.
- Playwright: `3 failed`.
  - The config test gets 200.
  - The oversized token answers 200 and stores a row (removed by `afterAll`).
  - The eleventh attempt answers 422.

- [ ] **Step 5: JSON 403 in EnsureStaff (S8)**

In `app/Http/Middleware/EnsureStaff.php`, replace:

```php
            return response('Forbidden.', 403);
```

with:

```php
            // JSON callers get the same {success, message} shape as the 401 branch above (S8).
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Forbidden.'], 403)
                : response('Forbidden.', 403);
```

- [ ] **Step 6: Validate, rate-limit and genericise postWebToken (S6)**

In `Modules/Notification/Http/Controllers/NotificationController.php` (CRLF file):

1. Replace `use Illuminate\Routing\Controller;` with:

```php
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\RateLimiter;
```

2. Replace:

```php
    public static $validationsRules = [
        'postWebToken' => [
            'data_token' => 'required',
        ],
    ];
```

with:

```php
    /** Attempts per client IP per minute on the public postWebToken endpoint (S6). */
    public const WEB_TOKEN_MAX_ATTEMPTS = 10;

    public static $validationsRules = [
        'postWebToken' => [
            // notif_tokens.token is varchar(191).
            'data_token' => 'required|string|max:191',
        ],
    ];
```

3. Replace:

```php
    public function postWebToken(Request $request)
    {
        $this->data['locale']      = $request->locale ? $request->locale : app()->getLocale();
```

with:

```php
    public function postWebToken(Request $request)
    {
        // Public endpoint: count every attempt per client IP and refuse with a JSON 429 over the limit (S6).
        // Not the throttle middleware: app/Exceptions/Handler.php turns its ThrottleRequestsException,
        // like every HttpException, into a redirect to the login page.
        $throttleKey = 'notification-web-token:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, self::WEB_TOKEN_MAX_ATTEMPTS)) {
            return response()->json([
                'success'             => false,
                'type'                => 'toastr',
                'message_type'        => 'error',
                'message_title'       => __('admin::strings.error.title'),
                'message_description' => __('admin::strings.error.description'),
                'errors'              => [],
            ], 429);
        }
        RateLimiter::hit($throttleKey, 60);

        $this->data['locale']      = $request->locale ? $request->locale : app()->getLocale();
```

4. Replace:

```php
        catch (Exception $e) {
            return new CrudResponse([
                'success'             => false,
                'type'                => 'toastr',
                'message_type'        => 'error',
                'message_title'       => __('admin::strings.error.title'),
                'message_description' => __('admin::strings.error.description'),
                'errors'              => [
                    'exception' => [
                        'message' => $e->getMessage(),
                        'code'    => $e->getCode(),
                        'line'    => $e->getLine(),
                        'file'    => $e->getFile(),
                    ],
                ]
            ], 500);
        }
```

with:

```php
        catch (Exception $e) {
            // Logged server-side only: the client never sees exception details (S6).
            report($e);

            return new CrudResponse([
                'success'             => false,
                'type'                => 'toastr',
                'message_type'        => 'error',
                'message_title'       => __('admin::strings.error.title'),
                'message_description' => __('admin::strings.error.description'),
                'errors'              => [],
            ], 500);
        }
```

Leave the constructor's `$this->middleware('auth')->except(['getConfig', 'postWebToken']);` unchanged. The route's `staff` middleware (Step 7) now guards `getConfig`, and it answers anonymous AJAX callers with a JSON 401, which the blanket `auth` would pre-empt with a redirect. This is the same pattern as `DashboardController@updateCurrency`.

- [ ] **Step 7: Put the Firebase config behind staff (S9)**

In `Modules/Notification/Routes/web.php` (CRLF file), replace:

```php
            Route::get('config'          ,'NotificationController@getConfig')->name('NotificationsController@getConfig');
```

with:

```php
            Route::get('config'          ,'NotificationController@getConfig')->middleware('staff')->name('NotificationsController@getConfig');
```

- [ ] **Step 8: Run the three files and confirm they pass**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/EnsureStaffJsonTest.php tests/Feature/WebTokenErrorResponseTest.php
(cd tests/e2e && npx playwright test specs/security/notification-endpoints.spec.ts --reporter=line | tail -5)
```

Expected: PHPUnit `OK (3 tests`; Playwright `3 passed`.

- [ ] **Step 9: Update the runbook fact**

In `tests/e2e/PARITY.md`, replace the bullet "- `GET admin/notification/config` answers 200 to anonymous visitors with the Firebase web client config (Phase 2 candidate)." with:

```markdown
- `GET admin/notification/config` answered 200 to anonymous visitors on Laravel 7 and 13. Since Phase 2 (S9) it carries the `staff` middleware: anonymous visitors are sent to login (the matrix records "302 login"), and staff still get the Firebase web client config.
```

- [ ] **Step 10: Run the gate** (Global Constraints, "The gate")

Expected before the controller's acceptance:
- `npm test`: `374 passed, 1 failed`. The failure is `specs/parity/permissions-matrix.spec.ts`, and its diff is exactly the one cell in "Expected cell diffs".
- PHPUnit: `OK (30 tests`.
- `check-structure.sh` exits 1, with one routes diff: `"localeViewPath"` becomes `"localeViewPath",` followed by `"staff"` for `admin/notification/config`. The config and glide sections match.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.

Check the matrix diff from the saved actual file:

```bash
ACTUAL=$(find tests/e2e/test-results -name 'matrix-actual.json' | head -1)
node -e '
const fs = require("fs");
const a = JSON.parse(fs.readFileSync("tests/e2e/snapshots/parity/permissions-matrix.spec.ts/matrix.json", "utf8"));
const b = JSON.parse(fs.readFileSync(process.argv[1], "utf8"));
for (const url of Object.keys(b)) for (const who of Object.keys(b[url]))
  if (a[url]?.[who] !== b[url][who]) console.log(`${url} | ${who}: ${JSON.stringify(a[url]?.[who])} -> ${JSON.stringify(b[url][who])}`);
' "$ACTUAL"
```

Expected output, exactly:

```
/en/admin/notification/config | anonymous: "200" -> "302 login"
```

Any other difference is a regression: fix it before committing.

- [ ] **Step 11: Commit** (code and tests only; the controller commits the two acceptances)

```bash
git add app/Http/Middleware/EnsureStaff.php Modules/Notification/Http/Controllers/NotificationController.php Modules/Notification/Routes/web.php \
  tests/Unit/EnsureStaffJsonTest.php tests/Feature/WebTokenErrorResponseTest.php tests/e2e/specs/security/notification-endpoints.spec.ts tests/e2e/PARITY.md
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Validate and rate-limit postWebToken, answer JSON 403s, and put the Firebase config behind staff (S6, S8, S9)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

In the report, ask the controller to accept the matrix cell and the routes cell above.

---

### Task 4: CSRF on state-changing admin actions (S7, S10, P2-R6)

- **S7.**
  - `GET admin/clear-cache` flushes the cache, so a hostile page can trigger it in a staff browser. It becomes a POST with CSRF.
  - Its authorization aligns with the aside menu item, which only `tags.requests` holders see (P2-R6 a).
  - The menu item submits a hidden `@csrf` form with native `form.submit()`, like `#logoutForm`. A global jQuery handler (`Modules/Cms/Resources/views/includes/scripts.blade.php:196-201`) cancels every submit event of forms present at page load.
  - The dashboard "Update currency" button (Phase H) is such a form, so it is probably inert. Step 3 checks it in a browser and fixes it with a native submit only if it is inert (P2-R6 b).
- **S10.**
  - `GET admin/projects/update_prices` already answers 500 before writing: its method needs an id the route never passes. The route is removed; the method stays for its two internal callers.
  - `GET admin/categories/asdwadwadwdaw` is a one-off seeding URL that nothing calls. It rewrites 2 categories and creates or overwrites 12 filter pages. The route and the method are removed.
  - `GET admin/users/login_as/{model}` becomes a POST, and the header's "login back as root" link becomes a hidden CSRF form.

All changes are Blade and PHP; no file under `public/` changes.

**Files:**
- Modify: `Modules/Cms/Routes/web.php` (lines 25, 42, 79)
- Modify: `Modules/Backend/Routes/web.php` (line 40)
- Modify: `Modules/Frontend/Http/Controllers/HomeController.php` (`clearCache`)
- Modify: `Modules/Cms/Http/Controllers/Admin/CategoryController.php` (delete `addCategoriesAndFilters`)
- Modify: `Modules/Backend/start.php` (the clear-cache menu item, around line 584)
- Modify: `Modules/Cms/Resources/views/includes/aside.blade.php` (around lines 97–108)
- Modify: `Modules/Cms/Resources/views/includes/header.blade.php` (lines 175–177)
- Modify, only if Step 3 shows the button is inert: `Modules/Cms/Resources/views/dashboard.blade.php` (line 25)
- Modify: `tests/e2e/specs/security/maintenance-routes.spec.ts` (rewritten below)
- Modify: `tests/e2e/parity/admin-urls.ts` (`MATRIX_EXCLUDED`)
- Modify: `tests/e2e/PARITY.md` (the state-changing GET bullet)
- Create: `tests/e2e/specs/security/state-changing-gets.spec.ts`
- Existing tests that observe this change:
  - `specs/security/maintenance-routes.spec.ts`: rewritten here, because it pinned GET clear-cache.
  - `scripts/upgrade/check-structure.sh` (`routes.json`): **baseline change, 6 cells.**
  - `permissions-matrix`: unchanged, because none of these routes is in `MATRIX_URLS`.
  - `golden-master` and `visual`: public pages only, unchanged.

**Interfaces:**
- Consumes (Task 2): with `CURRCONV_API_KEY` unset, `update_currency` makes no HTTP call. The button test below also intercepts the request, so it never reaches the app.
- Produces:
  - Form ids `clearCacheForm` (aside) and `loginBackForm` (header).
  - Aside menu item keys `form` (the action URL) and `form_id`, read by `aside.blade.php`.
  - Route names unchanged: `cache.clear` (POST) and `UserController@loginAs` (POST).
  - Cache sentinel key `e2e-s7-sentinel`.

**Expected cell diff** in `tests/upgrade/routes.json` (the controller accepts it after the commit):

```
GET admin/categories/asdwadwadwdaw: {"methods":["GET"],"uri":"admin/categories/asdwadwadwdaw","name":"CategoryController@addCategoriesAndFilters","action":"Modules\\Cms\\Http\\Controllers\\Admin\\CategoryController@addCategoriesAndFilters","middleware":["web","localeSessionRedirect","localizationRedirect","localeViewPath"]} -> (absent)
GET admin/clear-cache: {"methods":["GET"],"uri":"admin/clear-cache","name":"cache.clear","action":"Modules\\Frontend\\Http\\Controllers\\HomeController@clearCache","middleware":["web","localeSessionRedirect","localizationRedirect","localeViewPath","staff"]} -> (absent)
GET admin/projects/update_prices: {"methods":["GET"],"uri":"admin/projects/update_prices","name":"ProjectController@updatePrices","action":"Modules\\Backend\\Http\\Controllers\\Admin\\ProjectController@updatePrices","middleware":["web","localeSessionRedirect","localizationRedirect","localeViewPath"]} -> (absent)
GET admin/users/login_as/{model}: {"methods":["GET"],"uri":"admin/users/login_as/{model}","name":"UserController@loginAs","action":"Modules\\Cms\\Http\\Controllers\\Admin\\UserController@loginAs","middleware":["web","localeSessionRedirect","localizationRedirect","localeViewPath"]} -> (absent)
POST admin/clear-cache: (absent) -> {"methods":["POST"],"uri":"admin/clear-cache","name":"cache.clear","action":"Modules\\Frontend\\Http\\Controllers\\HomeController@clearCache","middleware":["web","localeSessionRedirect","localizationRedirect","localeViewPath","staff"]}
POST admin/users/login_as/{model}: (absent) -> {"methods":["POST"],"uri":"admin/users/login_as/{model}","name":"UserController@loginAs","action":"Modules\\Cms\\Http\\Controllers\\Admin\\UserController@loginAs","middleware":["web","localeSessionRedirect","localizationRedirect","localeViewPath"]}
```

- [ ] **Step 1: Rewrite the maintenance-routes spec (failing)**

Replace the whole content of `tests/e2e/specs/security/maintenance-routes.spec.ts` with the code below. The four "is not publicly reachable" tests and "the dashboard still offers the currency update to staff" are unchanged apart from `blockProduction`. The anonymous test and the clear-cache tests now expect a POST with CSRF and the `tags.requests` ability, and three tests are new.

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken, csrfToken } from '../../support/csrf';
import { artisan, sql } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

test.beforeAll(() => requireLocal('GET /seed runs the seeders on unpatched code'));

// A cache entry only a real flush removes. CACHE_DRIVER=file is shared by the CLI and the web server.
const SENTINEL = 'e2e-s7-sentinel';
const setSentinel = () => artisan('tinker', '--execute', `\\Illuminate\\Support\\Facades\\Cache::forever('${SENTINEL}', 1);`);
const sentinelKept = () =>
  artisan('tinker', '--execute', `echo \\Illuminate\\Support\\Facades\\Cache::has('${SENTINEL}') ? 'kept' : 'flushed';`).includes('kept');

for (const url of ['/seed', '/migrate', '/update_currency', '/en/clear-cache']) {
  test(`${url} is not publicly reachable`, async ({ request }) => {
    expect((await request.get(url)).status()).toBe(404);
  });
}

test('anonymous visitors cannot use the admin maintenance routes', async ({ request }) => {
  const token = await anonymousCsrfToken(request);
  const update = await request.post('/en/admin/update-currency', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(update.status()).toBe(401);

  expect((await request.get('/en/admin/clear-cache', { maxRedirects: 0 })).status()).toBe(404);
  const clearAjax = await request.post('/en/admin/clear-cache', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(clearAjax.status()).toBe(401);
  const clear = await request.post('/en/admin/clear-cache', { form: { _token: token }, maxRedirects: 0 });
  expect(clear.status()).toBe(302);
  expect(clear.headers()['location']).toContain('/authenticate/login');
});

test('the dashboard still offers the currency update to staff', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const form = page.locator('form[action$="/en/admin/update-currency"]');
  await expect(form).toHaveCount(1);
  await expect(form.locator('input[name="_token"]')).toHaveCount(1);
  await expect(form.locator('button.btn-success')).toBeVisible();
});

test('the dashboard currency button really submits its form (P2-R6)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const methods: string[] = [];
  // The request never reaches the app: the command it triggers calls an external currency API.
  await page.context().route('**/en/admin/update-currency', async route => {
    methods.push(route.request().method());
    await route.abort();
  });
  await page.locator('form[action$="/en/admin/update-currency"] button').click();
  await expect.poll(() => methods.length, { timeout: 5_000 }).toBe(1);
  expect(methods[0]).toBe('POST');
});

test('cache holders clear the cache only with a CSRF POST; a GET, a forged POST or a disabled account cannot', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const token = await csrfToken(page);

  setSentinel();
  expect((await page.request.get('/en/admin/clear-cache', { maxRedirects: 0 })).status()).toBe(404);
  const forged = await page.request.post('/en/admin/clear-cache', { headers: { 'Sec-Fetch-Site': 'cross-site' }, maxRedirects: 0 });
  expect(forged.status()).toBe(302);
  expect(forged.headers()['location']).toContain('/authenticate/login');
  expect(sentinelKept()).toBe(true);

  const ok = await page.request.post('/en/admin/clear-cache', { form: { _token: token }, maxRedirects: 0 });
  expect(ok.status()).toBe(302);
  expect(sentinelKept()).toBe(false);

  sql("UPDATE users SET disabled_at = NOW() WHERE username = 'parity-superadmin'");
  try {
    setSentinel();
    const denied = await page.request.post('/en/admin/clear-cache', { form: { _token: token }, maxRedirects: 0 });
    expect(denied.status()).toBe(403);
    expect(sentinelKept()).toBe(true);
  } finally {
    sql("UPDATE users SET disabled_at = NULL WHERE username = 'parity-superadmin'");
  }
});

test('ADMIN, who does not see the menu item, cannot clear the cache (P2-R6)', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const token = await csrfToken(page);
  setSentinel();
  const referer = `${BASE_URL}/en/parity-referer`;
  const denied = await page.request.post('/en/admin/clear-cache', { form: { _token: token }, headers: { Referer: referer }, maxRedirects: 0 });
  expect(denied.status()).toBe(302);
  expect(denied.headers()['location']).toBe(referer);
  expect(sentinelKept()).toBe(true);
});

test('the aside clear-cache item submits a hidden CSRF form', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const form = page.locator('form#clearCacheForm');
  await expect(form).toHaveAttribute('method', 'POST');
  await expect(form).toHaveAttribute('action', /\/en\/admin\/clear-cache$/);
  await expect(form.locator('input[name="_token"]')).toHaveCount(1);

  setSentinel();
  const posted = page.waitForRequest(r => r.url().endsWith('/en/admin/clear-cache') && r.method() === 'POST');
  await page.locator('#kt_aside_menu a[onclick*="clearCacheForm"]').click();
  await posted;
  await page.waitForLoadState('load');
  await expect.poll(sentinelKept).toBe(false);
});
```

- [ ] **Step 2: Write the failing S10 spec**

Create `tests/e2e/specs/security/state-changing-gets.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { csrfToken } from '../../support/csrf';
import { sql, sqlScalar } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S10: no admin GET route changes state.
test.beforeAll(() => requireLocal('uses the local parity accounts'));

// The filter pages the removed seeding method created or overwrote.
const SEEDED_FILTERS = `SELECT COUNT(*) FROM cms_contents WHERE type = 'filters' AND slug IN (
  'apartments-for-sale-in-turkey', 'installments-apartments-for-sale-in-turkey', 'sea-view-apartments-for-sale-in-turkey',
  'cheap-apartments-for-sale-in-turkey', 'shops-for-sale-in-turkey', 'villas-for-sale-in-turkey', 'cheap-villas-for-sale-in-turkey',
  'sea-view-villas-for-sale-in-turkey', 'installments-apartments-for-sale-in-istanbul', 'cheap-villas-for-sale-in-istanbul',
  'sea-view-villas-for-sale-in-istanbul', 'apartments-for-sale-in-istanbul')`;

test('update_prices and the category seeding URL are gone and seed nothing', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const before = sqlScalar(SEEDED_FILTERS);
  // update_prices first: on unpatched code it answers 500 and this test stops here, before the seeding GET could write.
  expect((await page.request.get('/en/admin/projects/update_prices', { maxRedirects: 0 })).status()).toBe(404);
  expect((await page.request.get('/en/admin/categories/asdwadwadwdaw', { maxRedirects: 0 })).status()).toBe(404);
  expect(sqlScalar(SEEDED_FILTERS)).toBe(before);
});

test('login_as needs a CSRF POST and never switches a non-ROOT session', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');
  const target = sql("SELECT id FROM users WHERE username = 'parity-superadmin'");

  expect((await page.request.get(`/en/admin/users/login_as/${target}`, { maxRedirects: 0 })).status()).toBe(404);
  const forged = await page.request.post(`/en/admin/users/login_as/${target}`, { headers: { 'Sec-Fetch-Site': 'cross-site' }, maxRedirects: 0 });
  expect(forged.status()).toBe(302);
  expect(forged.headers()['location']).toContain('/authenticate/login');

  const token = await csrfToken(page);
  const posted = await page.request.post(`/en/admin/users/login_as/${target}`, { form: { _token: token }, maxRedirects: 0 });
  expect(posted.status()).toBe(200);

  // Still ADMIN: the leads page (tags.requests, SUPERADMIN only) still redirects back.
  const referer = `${BASE_URL}/en/parity-referer`;
  const leads = await page.request.get('/en/admin/projects/requests', { headers: { Referer: referer }, maxRedirects: 0 });
  expect(leads.status()).toBe(302);
  expect(leads.headers()['location']).toBe(referer);
});
```

- [ ] **Step 3: Run both specs on unchanged code**

```bash
(cd tests/e2e && npx playwright test specs/security/maintenance-routes.spec.ts specs/security/state-changing-gets.spec.ts --reporter=line | tail -25)
```

Of the 12 tests, 5 always pass on unchanged code: the four "is not publicly reachable" tests and "the dashboard still offers the currency update to staff".

These 6 always fail:
- "anonymous visitors cannot use the admin maintenance routes": the GET answers 302, not 404.
- "cache holders clear the cache only with a CSRF POST…": the GET answers 302.
- "ADMIN, who does not see the menu item…": the POST reaches no route and redirects to login, not back.
- "the aside clear-cache item submits a hidden CSRF form": there is no `#clearCacheForm`.
- "update_prices and the category seeding URL are gone and seed nothing": 500.
- "login_as needs a CSRF POST…": the GET answers 200.

**Record the result of "the dashboard currency button really submits its form (P2-R6)".**
- If it **fails** (no POST within 5 s), the button is inert. The summary is `5 passed, 7 failed`: do Step 7.
- If it **passes**, the button works. The summary is `6 passed, 6 failed`: skip Step 7, and say so in the report.

- [ ] **Step 4: Routes (S7, S10)**

In `Modules/Cms/Routes/web.php` (LF file), replace:

```php
        Route::get('clear-cache',                       '\Modules\Frontend\Http\Controllers\HomeController@clearCache')->middleware('staff')->name('cache.clear');
```

with:

```php
        Route::post('clear-cache',                      '\Modules\Frontend\Http\Controllers\HomeController@clearCache')->middleware('staff')->name('cache.clear');
```

Replace:

```php
            Route::get('/login_as/{model}',             'UserController@loginAs')->name('UserController@loginAs');
```

with:

```php
            Route::post('/login_as/{model}',            'UserController@loginAs')->name('UserController@loginAs');
```

Then delete the seeding route and the update_prices route:

```bash
perl -ni -e 'print unless /CategoryController\@addCategoriesAndFilters/' Modules/Cms/Routes/web.php
perl -ni -e 'print unless /ProjectController\@updatePrices/' Modules/Backend/Routes/web.php
grep -c 'addCategoriesAndFilters' Modules/Cms/Routes/web.php; grep -c 'updatePrices' Modules/Backend/Routes/web.php
```

Expected: `0` and `0`. `ProjectController::updatePrices()` stays: `store` and `update` call it with `$this->updatePrices($id)`.

- [ ] **Step 5: Remove the seeding method (S10)**

```bash
perl -ni -e 'if (/^\s*public function addCategoriesAndFilters\(\)\{/) { $skip = 1 } if ($skip && /^\s*public \$attributeNames = \[\];/) { $skip = 0 } print unless $skip' Modules/Cms/Http/Controllers/Admin/CategoryController.php
grep -c 'addCategoriesAndFilters\|asdwadwadwdaw' Modules/Cms/Http/Controllers/Admin/CategoryController.php
git diff --stat -- Modules/Cms/Http/Controllers/Admin/CategoryController.php
```

Expected: `0`, then `1 file changed, 211 deletions(-)`. The class now starts with `public $attributeNames = [];` and `__construct()`.

- [ ] **Step 6: Clear-cache authorization and the aside form (S7)**

1. In `Modules/Frontend/Http/Controllers/HomeController.php` (CRLF file), replace:

```php
    public function clearCache()
    {
```

with:

```php
    public function clearCache()
    {
        // Only holders of the aside menu item's ability (tags.requests) may flush the cache (S7).
        \Illuminate\Support\Facades\Gate::authorize('requests', \Modules\Cms\Entities\Tag::class);
```

2. In `Modules/Backend/start.php` (CRLF file), replace:

```php
            'label'     => __('cms::includes.aside.clear_cache'),
            'link'      => route('cache.clear'),
```

with:

```php
            'label'     => __('cms::includes.aside.clear_cache'),
            // A POST with CSRF (S7): aside.blade.php renders a hidden form and submits it natively.
            'link'      => 'javascript:;',
            'form'      => route('cache.clear'),
            'form_id'   => 'clearCacheForm',
```

3. In `Modules/Cms/Resources/views/includes/aside.blade.php` (CRLF file), replace:

```blade
                        <a href="{!! $menu_item['link'] !!}" class="kt-menu__link {{ ($menu_item['items']) ? 'kt-menu__toggle' : '' }}">
```

with:

```blade
                        <a href="{!! $menu_item['link'] !!}" class="kt-menu__link {{ ($menu_item['items']) ? 'kt-menu__toggle' : '' }}"@if(!empty($menu_item['form_id'])) onclick="document.getElementById('{{ $menu_item['form_id'] }}').submit();"@endif>
```

Items without `form_id` render exactly the same bytes as before.

Then replace:

```blade
                        </a>
                        @if($menu_item['items'])
                            <div class="kt-menu__submenu">
```

with:

```blade
                        </a>
                        @if(!empty($menu_item['form_id']))
                            {{-- Native form.submit() bypasses the global jQuery submit blocker, like #logoutForm (S7). --}}
                            <form id="{{ $menu_item['form_id'] }}" action="{{ $menu_item['form'] }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endif
                        @if($menu_item['items'])
                            <div class="kt-menu__submenu">
```

- [ ] **Step 7: Fire the dashboard currency form natively (only if Step 3 showed it inert)**

In `Modules/Cms/Resources/views/dashboard.blade.php`, replace:

```blade
                            <button type="submit" class="submit_form btn btn-success btn-bold">
```

with:

```blade
                            <button type="submit" class="submit_form btn btn-success btn-bold" onclick="event.preventDefault(); this.form.submit();">
```

The click handler cancels the submit event that the global jQuery blocker would swallow. Native `submit()` then posts the form with its `@csrf` token and `target="_blank"`. The markup gains only the `onclick` attribute.

- [ ] **Step 8: Login-back link as a CSRF form (S10)**

In `Modules/Cms/Resources/views/includes/header.blade.php` (CRLF file), replace:

```blade
                    @if(session()->get('temporaryLoginUser'))
                        <a href="{{ route('UserController@loginAs', ['model' => auth()->user()->id]) }}" class="btn btn-clean btn-sm btn-bold">{{ __('cms::header.login_back_as_root') }}</a>
                    @endif
```

with:

```blade
                    @if(session()->get('temporaryLoginUser'))
                        <form id="loginBackForm" action="{{ route('UserController@loginAs', ['model' => auth()->user()->id]) }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="javascript:;" onclick="getElementById('loginBackForm').submit();" class="btn btn-clean btn-sm btn-bold">{{ __('cms::header.login_back_as_root') }}</a>
                    @endif
```

Leave the ROOT-only "login as" item in the users table (`UserController.php`, around line 197) unchanged. It renders only for a ROOT viewer, no account holds ROOT, and after this task its plain GET link answers 404. Report this as a dormant-feature degradation for a controller ruling. Keeping it working would need an AJAX form, a confirmation dialog and a JSON response in `loginAs()`.

- [ ] **Step 9: Empty `MATRIX_EXCLUDED` and update the runbook**

In `tests/e2e/parity/admin-urls.ts`, replace:

```ts
/** Admin GET routes deliberately left out of the matrix, and why. */
export const MATRIX_EXCLUDED: Record<string, string> = {
  'GET admin/users/login_as/{model}': 'switches the session to another user',
  'GET admin/projects/update_prices': 'rewrites project prices',
  'GET admin/categories/asdwadwadwdaw': 'creates categories and filter pages',
  'GET admin/clear-cache': 'flushes the cache mid-run; covered by specs/security/maintenance-routes.spec.ts',
};
```

with:

```ts
/**
 * Admin GET routes deliberately left out of the matrix, and why. Empty since Phase 2: the four admin GET routes
 * that changed state are POST-only (clear-cache, users/login_as/{model}; S7, S10) or removed
 * (projects/update_prices, categories/asdwadwadwdaw; S10). See specs/security/maintenance-routes.spec.ts and
 * specs/security/state-changing-gets.spec.ts.
 */
export const MATRIX_EXCLUDED: Record<string, string> = {};
```

`MATRIX_URLS` does not change, so the matrix does not change.

In `tests/e2e/PARITY.md`, replace the bullet "- Left out of the matrix because they change state on GET: `users/login_as/{model}`, `projects/update_prices`, `categories/asdwadwadwdaw`, `clear-cache` (see `MATRIX_EXCLUDED`)." with:

```markdown
- Four admin GET routes changed state on Laravel 7 and were left out of the matrix. Since Phase 2 none is a GET route: `clear-cache` (which now also requires `tags.requests`) and `users/login_as/{model}` are CSRF-protected POST routes (S7, S10), and `projects/update_prices` and `categories/asdwadwadwdaw` are removed (S10). A GET to any of them answers 404. `specs/security/maintenance-routes.spec.ts` and `specs/security/state-changing-gets.spec.ts` cover them.
```

- [ ] **Step 10: Run both specs and confirm they pass**

```bash
(cd tests/e2e && npx playwright test specs/security/maintenance-routes.spec.ts specs/security/state-changing-gets.spec.ts --reporter=line | tail -5)
grep -rn "route('cache.clear')\|UserController@loginAs" Modules --include='*.php' | grep -v Routes/web.php
```

Expected:
- `12 passed`.
- The grep lists exactly three sites:
  - `Modules/Backend/start.php` (`'form' => route('cache.clear')`);
  - `includes/header.blade.php` (the form `action`);
  - `UserController.php` (the ROOT-only dropdown item).

- [ ] **Step 11: Run the gate** (Global Constraints, "The gate")

Expected before the controller's acceptance:
- `npm test`: `380 passed` (321 + 59).
- PHPUnit: `OK (30 tests`.
- `check-structure.sh` exits 1 with a routes-only diff: the four removed GET entries and the two added POST entries in "Expected cell diff".
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.

- [ ] **Step 12: Commit**

```bash
git add Modules/Cms/Routes/web.php Modules/Backend/Routes/web.php Modules/Frontend/Http/Controllers/HomeController.php \
  Modules/Cms/Http/Controllers/Admin/CategoryController.php Modules/Backend/start.php \
  Modules/Cms/Resources/views/includes/aside.blade.php Modules/Cms/Resources/views/includes/header.blade.php \
  tests/e2e/specs/security/maintenance-routes.spec.ts tests/e2e/specs/security/state-changing-gets.spec.ts \
  tests/e2e/parity/admin-urls.ts tests/e2e/PARITY.md
git add Modules/Cms/Resources/views/dashboard.blade.php   # only if Step 7 changed it
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Make clear-cache and login_as CSRF POSTs, remove the state-changing admin GETs, and fire the currency form natively (S7, S10)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

In the report:
- say whether Step 7 was needed;
- list the visible markup changes: the SUPERADMIN aside item (`href`, `onclick`, hidden form), the header login-back form, and the dashboard button `onclick` if applied;
- ask for the `routes.json` acceptance and for a ruling on the ROOT-only users-table dropdown.

---

### Task 5: Admin endpoints that authorize nothing (S21)

S21 has two parts. Every added check reuses the ability the matching admin page already requires, so SUPERADMIN flows stay the same (ruling P2-R4).

- **Lead data endpoints.** `data_requests`, `data_properties`, `request_summary`, `properties_summary` and `showDetails` in both `ProjectController` and `OpportunityController` return every lead to any signed-in account. Their pages (`requests`, `properties`) require `tags.requests`. Each endpoint now authorizes `requests` on `Tag` **before** loading anything, so a denied caller learns nothing about which ids exist.
- **POST routes with no ability check.** Each now enforces its page's ability:

| Route | Page it belongs to | Check added |
|---|---|---|
| `contents/delete-attachemnt` | content edit (`contents.<type>.edit`) | `authorize('update', [$content, $content->type])` on the attachment's content |
| `landing_pages/delete-timeline` | landing page edit (`landingpages.edit`) | `authorize('update', $landingPage)` |
| `{projects,opportunity}/delete-payment`, `delete-price` | project/opportunity edit (`projects.edit`) | `authorize('update', $project)` |
| `tags/save` | tag creation (`tags.create`, as `TagController@create`) | `authorize('create', Tag::class)` before validation |
| notification `POST /` and `getList` | notifications (`notifications.view`) | `authorize('view', FirebaseNotification::class)` |

`getList` feeds the header notification list in `firebase_scripts.blade.php`, which both layouts currently comment out. `notifications.view` is the Notification module's read ability.

Who is affected: ADMIN lacks `tags.requests`, `landingpages.*` and `notifications.view`, so ADMIN loses those direct URLs. ADMIN holds `projects.edit`, `contents.<type>.edit` (all but achievements) and `tags.create`, so for those routes only accounts without a staff role are affected, for example CLIENT. The spec below creates a temporary CLIENT fixture account to prove it.

**Files:**
- Modify: `Modules/Backend/Http/Controllers/Admin/ProjectController.php`
- Modify: `Modules/Backend/Http/Controllers/Admin/OpportunityController.php`
- Modify: `Modules/Cms/Http/Controllers/Admin/ContentController.php` (`deleteAttachment`)
- Modify: `Modules/Cms/Http/Controllers/Admin/LandingPageController.php` (`deleteTimeline`)
- Modify: `Modules/Cms/Http/Controllers/Admin/TagController.php` (`save`)
- Modify: `Modules/Notification/Http/Controllers/NotificationController.php` (`postIndex`, `getList`)
- Modify: `tests/e2e/PARITY.md` ("When the dump changes")
- Create: `tests/e2e/specs/security/admin-authorization.spec.ts`
- Existing tests that observe this change:
  - `specs/parity/permissions-matrix.spec.ts`: **baseline change, 10 cells**, all in the `parity-admin` column.
  - `admin-sections.spec.ts`: signs in as SUPERADMIN, so unchanged.

**Interfaces:**
- Consumes:
  - Task 1's `data_requests` bodies, which start with `$list = ContactUS::query();`.
  - Task 3's `NotificationController` (the `postWebToken` changes are elsewhere in the file).
- Produces:
  - The five lead-endpoint methods in each controller start with `$this->authorize('requests', Tag::class);`. Task 7 edits the `DataTables::of($list)` line in `data_requests` and `data_properties`.
  - Six of the nine matrix rows that had no role signal gain one; Task 12 documents that.
  - Fixture account `parity-client-s21` (role CLIENT) and `s21-probe` rows, created and deleted by the spec.

**Expected cell diff** in `tests/e2e/snapshots/parity/permissions-matrix.spec.ts/matrix.json`:

```
/en/admin/opportunity/data_properties | parity-admin: "200" -> "302 back"
/en/admin/opportunity/data_requests | parity-admin: "200" -> "302 back"
/en/admin/opportunity/properties_summary | parity-admin: "404" -> "302 back"
/en/admin/opportunity/request_summary | parity-admin: "404" -> "302 back"
/en/admin/opportunity/show_details/0 | parity-admin: "404" -> "302 back"
/en/admin/projects/data_properties | parity-admin: "200" -> "302 back"
/en/admin/projects/data_requests | parity-admin: "200" -> "302 back"
/en/admin/projects/properties_summary | parity-admin: "404" -> "302 back"
/en/admin/projects/request_summary | parity-admin: "404" -> "302 back"
/en/admin/projects/show_details/0 | parity-admin: "404" -> "302 back"
```

- [ ] **Step 1: Write the failing spec**

Create `tests/e2e/specs/security/admin-authorization.spec.ts`:

```ts
import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { appShell, sql, sqlScalar } from '../../support/docker';
import { BASE_URL, E2E_PASSWORD, requireLocal } from '../../support/env';

// S21: lead data endpoints and the named admin POST routes enforce the ability of their admin page.
// Every row this spec touches is a synthetic fixture (s21-probe, aldar.test), deleted in afterAll.
const REFERER = `${BASE_URL}/en/parity-referer`;
const CLIENT = 'parity-client-s21';
const LEAD_EMAIL = 's21-lead@aldar.test';
const FORM_EMAIL = 's21-form@aldar.test';
const MARK = 's21-probe';
// Dump rows the fixtures hang off (PARITY.md, "When the dump changes").
const PROJECT_ID = 133;
const OPPORTUNITY_ID = 320;
const LANDING_PAGE_ID = 11;
const ARTICLE_ID = 251;

type FixtureId = 'lead' | 'form' | 'projectPayment' | 'opportunityPayment' | 'projectPrice' | 'opportunityPrice' | 'timeline' | 'attachment';
const ids = {} as Record<FixtureId, string>;

async function signIn(request: APIRequestContext, username: string): Promise<void> {
  const html = await (await request.get('/en/authenticate/login')).text();
  const token = html.match(/name="_token" value="([^"]+)"/)![1];
  const response = await request.post('/en/authenticate/login', {
    headers: AJAX_HEADERS,
    form: { _token: token, identity: username, password: E2E_PASSWORD },
  });
  expect(response.status(), `login as ${username}`).toBe(200);
}

/** "200", "404", "422" ... or "302 login" / "302 back" / "302 <path>", as in the permissions matrix. */
async function outcome(request: APIRequestContext, method: 'GET' | 'POST', url: string, form: Record<string, string> = {}): Promise<string> {
  const options = { headers: { ...AJAX_HEADERS, Referer: REFERER }, maxRedirects: 0 };
  const response = method === 'GET'
    ? await request.get(url, options)
    : await request.post(url, { ...options, form: { _token: await anonymousCsrfToken(request), ...form } });
  const status = response.status();
  if (status < 300 || status >= 400) return String(status);
  const location = response.headers()['location'] ?? '';
  if (/\/authenticate\/login$/.test(location)) return `${status} login`;
  if (location === REFERER) return `${status} back`;
  return `${status} ${location.replace(BASE_URL, '')}`;
}

function removeFixtures(): void {
  sql(`DELETE FROM contact_us WHERE email = '${LEAD_EMAIL}'`);
  sql(`DELETE FROM property_form WHERE advertisers_email = '${FORM_EMAIL}'`);
  sql(`DELETE FROM paying_method WHERE type = '${MARK}'`);
  sql(`DELETE FROM price WHERE balance = '${MARK}'`);
  sql(`DELETE FROM timeline WHERE title = '${MARK}'`);
  sql(`DELETE FROM cms_external_attachments WHERE name = '${MARK}'`);
  sql(`DELETE a FROM perms_assigned_roles a JOIN users u ON u.id = a.entity_id WHERE u.username = '${CLIENT}' AND a.entity_type = CONCAT('App', CHAR(92), 'User')`);
  sql(`DELETE FROM users WHERE username = '${CLIENT}'`);
}

const fixturesLeft = () => sqlScalar(`SELECT
  (SELECT COUNT(*) FROM paying_method WHERE type = '${MARK}') +
  (SELECT COUNT(*) FROM price WHERE balance = '${MARK}') +
  (SELECT COUNT(*) FROM timeline WHERE title = '${MARK}') +
  (SELECT COUNT(*) FROM cms_external_attachments WHERE name = '${MARK}' AND deleted_at IS NULL)`);

const leadReads = (section: 'projects' | 'opportunity') => [
  `/en/admin/${section}/data_requests?draw=1&start=0&length=1`,
  `/en/admin/${section}/data_properties?draw=1&start=0&length=1`,
  `/en/admin/${section}/request_summary?model=${ids.lead}`,
  `/en/admin/${section}/properties_summary?model=${ids.form}`,
  `/en/admin/${section}/show_details/${ids.form}`,
];

const childDeletes = () => [
  { url: '/en/admin/contents/delete-attachemnt', form: { attachment_id: ids.attachment } },
  { url: '/en/admin/landing_pages/delete-timeline', form: { timeline_id: ids.timeline } },
  { url: '/en/admin/projects/delete-payment', form: { payment_id: ids.projectPayment } },
  { url: '/en/admin/projects/delete-price', form: { price_id: ids.projectPrice } },
  { url: '/en/admin/opportunity/delete-payment', form: { payment_id: ids.opportunityPayment } },
  { url: '/en/admin/opportunity/delete-price', form: { price_id: ids.opportunityPrice } },
];
// Empty bodies: tags/save fails validation, and the two notification endpoints only read (getList marks the caller's own rows seen).
const otherPosts = ['/en/admin/tags/save', '/en/admin/notification', '/en/admin/notification/getList'];

test.beforeAll(() => {
  requireLocal('creates fixture rows and a fixture account in the local database');
  removeFixtures();
  const hash = appShell(`php -r 'echo password_hash("${E2E_PASSWORD}", PASSWORD_BCRYPT);'`);
  sql(`INSERT INTO users (username, email, status, verification_code, password, email_verified_at, created_at, updated_at)
       VALUES ('${CLIENT}', '${CLIENT}@aldar.test', 'ACTIVE', 'VERIFIED', '${hash}', NOW(), NOW(), NOW())`);
  sql(`INSERT INTO perms_assigned_roles (role_id, entity_id, entity_type)
       SELECT r.id, u.id, CONCAT('App', CHAR(92), 'User') FROM users u JOIN perms_roles r ON r.name = 'CLIENT' WHERE u.username = '${CLIENT}'`);
  sql(`INSERT INTO contact_us (sender, email, phone, description, created_at, updated_at)
       VALUES ('S21 Probe', '${LEAD_EMAIL}', '0', 'S21 probe lead', NOW(), NOW())`);
  sql(`INSERT INTO property_form (advertisers_name, advertisers_email, advertisers_phone, property_explanation, created_at, updated_at)
       VALUES ('S21 Probe', '${FORM_EMAIL}', '0', 'S21 probe form', NOW(), NOW())`);
  sql(`INSERT INTO paying_method (project_id, type, first_pay, created_at, updated_at)
       VALUES (${PROJECT_ID}, '${MARK}', '0', NOW(), NOW()), (${OPPORTUNITY_ID}, '${MARK}', '0', NOW(), NOW())`);
  sql(`INSERT INTO price (project_id, balance_id, balance, is_sold, created_at, updated_at)
       SELECT p.id, c.id, '${MARK}', 'no', NOW(), NOW()
       FROM be_projects p JOIN (SELECT MIN(id) AS id FROM cms_contents WHERE type = 'currencies') c
       WHERE p.id IN (${PROJECT_ID}, ${OPPORTUNITY_ID})`);
  sql(`INSERT INTO timeline (landing_id, sort_order, language, title, created_at) VALUES (${LANDING_PAGE_ID}, 0, 'en', '${MARK}', NOW())`);
  sql(`INSERT INTO cms_external_attachments (type, name, link, attachable_type, attachable_id, created_at, updated_at)
       VALUES ('file', '${MARK}', 'https://example.invalid/${MARK}', CONCAT('Modules', CHAR(92), 'Cms', CHAR(92), 'Entities', CHAR(92), 'Content'), ${ARTICLE_ID}, NOW(), NOW())`);
  ids.lead = sql(`SELECT id FROM contact_us WHERE email = '${LEAD_EMAIL}'`);
  ids.form = sql(`SELECT id FROM property_form WHERE advertisers_email = '${FORM_EMAIL}'`);
  ids.projectPayment = sql(`SELECT id FROM paying_method WHERE type = '${MARK}' AND project_id = ${PROJECT_ID}`);
  ids.opportunityPayment = sql(`SELECT id FROM paying_method WHERE type = '${MARK}' AND project_id = ${OPPORTUNITY_ID}`);
  ids.projectPrice = sql(`SELECT id FROM price WHERE balance = '${MARK}' AND project_id = ${PROJECT_ID}`);
  ids.opportunityPrice = sql(`SELECT id FROM price WHERE balance = '${MARK}' AND project_id = ${OPPORTUNITY_ID}`);
  ids.timeline = sql(`SELECT id FROM timeline WHERE title = '${MARK}'`);
  ids.attachment = sql(`SELECT id FROM cms_external_attachments WHERE name = '${MARK}'`);
});
test.afterAll(() => removeFixtures());

test('only holders of the leads page ability read leads through the data endpoints', async ({ playwright }) => {
  const admin = await playwright.request.newContext({ baseURL: BASE_URL });
  const superadmin = await playwright.request.newContext({ baseURL: BASE_URL });
  try {
    await signIn(admin, 'parity-admin');
    await signIn(superadmin, 'parity-superadmin');
    const urls = [...leadReads('projects'), ...leadReads('opportunity')];
    // SUPERADMIN first: on unchanged code these must already pass, so a failure here is not an S21 effect.
    for (const url of urls) {
      expect(await outcome(superadmin, 'GET', url), `parity-superadmin ${url}`).toBe('200');
    }
    for (const url of urls) {
      expect(await outcome(admin, 'GET', url), `parity-admin ${url}`).toBe('302 back');
    }
  } finally {
    await admin.dispose();
    await superadmin.dispose();
  }
});

test('accounts without the page ability cannot use the admin POST routes S21 names', async ({ playwright }) => {
  const client = await playwright.request.newContext({ baseURL: BASE_URL });
  const admin = await playwright.request.newContext({ baseURL: BASE_URL });
  try {
    await signIn(client, CLIENT);
    await signIn(admin, 'parity-admin');
    expect(fixturesLeft()).toBe(6);
    for (const { url, form } of childDeletes()) {
      expect(await outcome(client, 'POST', url, form), `${CLIENT} ${url}`).toBe('302 back');
    }
    for (const url of otherPosts) {
      expect(await outcome(client, 'POST', url), `${CLIENT} ${url}`).toBe('302 back');
    }
    // ADMIN lacks landingpages.* and notifications.view; it holds the abilities of the other routes.
    expect(await outcome(admin, 'POST', '/en/admin/landing_pages/delete-timeline', { timeline_id: ids.timeline })).toBe('302 back');
    expect(await outcome(admin, 'POST', '/en/admin/notification')).toBe('302 back');
    expect(await outcome(admin, 'POST', '/en/admin/notification/getList')).toBe('302 back');
    expect(fixturesLeft()).toBe(6);
  } finally {
    await client.dispose();
    await admin.dispose();
  }
});

test('SUPERADMIN still deletes payments, prices, timelines and attachments, and still reaches the tag and notification endpoints', async ({ playwright }) => {
  const superadmin = await playwright.request.newContext({ baseURL: BASE_URL });
  try {
    await signIn(superadmin, 'parity-superadmin');
    for (const { url, form } of childDeletes()) {
      expect(await outcome(superadmin, 'POST', url, form), url).toBe('200');
    }
    expect(fixturesLeft()).toBe(0);
    expect(await outcome(superadmin, 'POST', '/en/admin/tags/save'), 'tags/save with an empty body').toBe('422');
    expect(await outcome(superadmin, 'POST', '/en/admin/notification'), 'notification list').toBe('200');
    expect(await outcome(superadmin, 'POST', '/en/admin/notification/getList'), 'notification feed').toBe('200');
  } finally {
    await superadmin.dispose();
  }
});
```

- [ ] **Step 2: Run it on unchanged code**

```bash
(cd tests/e2e && npx playwright test specs/security/admin-authorization.spec.ts --reporter=line | tail -20)
```

Expected: `3 failed`.
- Test 1 passes all ten SUPERADMIN reads, then fails at `parity-admin /en/admin/projects/data_requests?draw=1&start=0&length=1`, which answers `200`.
- Test 2 fails at its first expectation: `parity-client-s21 /en/admin/contents/delete-attachemnt` answers `200`. The unpatched route has just soft-deleted that fixture.
- Test 3 fails on that already deleted attachment (`404`).

Stop and report instead of implementing if either of these happens:
- `beforeAll` fails (a fixture insert is refused): report the SQL error.
- Test 1's failure message names `parity-superadmin`. A SUPERADMIN read that is not `200` on unchanged code is a pre-existing defect of that endpoint, not an S21 effect.

- [ ] **Step 3: Authorize the lead endpoints in ProjectController**

In `Modules/Backend/Http/Controllers/Admin/ProjectController.php` (CRLF file), make five replacements. Each old text is unique in the file.

1. Replace:

```php
    public function data_requests(Request $request)
    {
        $list = ContactUS::query();
```

with:

```php
    public function data_requests(Request $request)
    {
        // Lead data is for holders of the leads page ability, checked before anything loads (S21).
        $this->authorize('requests', Tag::class);
        $list = ContactUS::query();
```

2. Replace:

```php
    public function request_summary(Request $request)
    {
        $this->data['model'] = ContactUS::findOrFail($request->model);
```

with:

```php
    public function request_summary(Request $request)
    {
        // Lead data is for holders of the leads page ability, checked before anything loads (S21).
        $this->authorize('requests', Tag::class);
        $this->data['model'] = ContactUS::findOrFail($request->model);
```

3. Replace:

```php
    public function data_properties(Request $request)
    {
        $list = PropertyForm::query();
```

with:

```php
    public function data_properties(Request $request)
    {
        // Property submissions are for holders of the properties page ability, checked before anything loads (S21).
        $this->authorize('requests', Tag::class);
        $list = PropertyForm::query();
```

4. Replace:

```php
    public function properties_summary(Request $request)
    {
        $this->data['model'] = PropertyForm::with('attachments')->findOrFail($request->model);
```

with:

```php
    public function properties_summary(Request $request)
    {
        // Property submissions are for holders of the properties page ability, checked before anything loads (S21).
        $this->authorize('requests', Tag::class);
        $this->data['model'] = PropertyForm::with('attachments')->findOrFail($request->model);
```

5. Replace:

```php
    public function showDetails(Request $request)
    {
        $this->data['model']    = PropertyForm::with('attachments')->findOrFail($request->model);
```

with:

```php
    public function showDetails(Request $request)
    {
        // Property submissions are for holders of the properties page ability, checked before anything loads (S21).
        $this->authorize('requests', Tag::class);
        $this->data['model']    = PropertyForm::with('attachments')->findOrFail($request->model);
```

Then add the edit page's ability to the two child-row deletes. Replace:

```php
        $this->data['model'] = PayingMethod::findOrFail($request->payment_id);
```

with:

```php
        $this->data['model'] = PayingMethod::findOrFail($request->payment_id);
        // The ability of the edit page this delete button sits on (S21).
        $this->authorize('update', CrudModel::withDisabled()->findOrFail($this->data['model']->project_id));
```

and replace:

```php
        $this->data['model'] = Price::findOrFail($request->price_id);
```

with:

```php
        $this->data['model'] = Price::findOrFail($request->price_id);
        // The ability of the edit page this delete button sits on (S21).
        $this->authorize('update', CrudModel::withDisabled()->findOrFail($this->data['model']->project_id));
```

- [ ] **Step 4: The same seven changes in OpportunityController**

Apply exactly the seven replacements of Step 3 to `Modules/Backend/Http/Controllers/Admin/OpportunityController.php` (CRLF file). The old and new texts are identical there: the same method bodies, the same `Tag`, `PayingMethod` and `Price` imports, and `CrudModel` is `Modules\Backend\Entities\Project`.

Verify both files:

```bash
grep -c "authorize('requests', Tag::class)" Modules/Backend/Http/Controllers/Admin/ProjectController.php Modules/Backend/Http/Controllers/Admin/OpportunityController.php
grep -c "findOrFail(\$this->data\['model'\]->project_id)" Modules/Backend/Http/Controllers/Admin/ProjectController.php Modules/Backend/Http/Controllers/Admin/OpportunityController.php
```

Expected:
- `…ProjectController.php:7` and `…OpportunityController.php:7`: the 5 new checks plus the existing `requests()` and `properties()`.
- Then `:2` for each file.

- [ ] **Step 5: Content attachments, landing page timelines, tags and notifications**

1. In `Modules/Cms/Http/Controllers/Admin/ContentController.php` (CRLF file), replace:

```php
        $this->data['model'] = ExternalAttachments::findOrFail($request->attachment_id);
        // Check if the authenticated user is allowed to proceed farther.
        // $this->authorize('enable', [$this->data['model'],$this->data['type']]);
```

with:

```php
        $this->data['model'] = ExternalAttachments::findOrFail($request->attachment_id);
        // The content edit page's ability, for the attachment's own content and type (S21).
        $content = CrudModel::withDisabled()->findOrFail($this->data['model']->attachable_id);
        $this->authorize('update', [$content, $content->type]);
```

2. In `Modules/Cms/Http/Controllers/Admin/LandingPageController.php` (CRLF file), replace:

```php
        $this->data['model'] = Timeline::findOrFail($request->timeline_id);
```

with:

```php
        $this->data['model'] = Timeline::findOrFail($request->timeline_id);
        // The landing page edit page's ability (S21).
        $this->authorize('update', CrudModel::withTrashed()->findOrFail($this->data['model']->landing_id));
```

3. In `Modules/Cms/Http/Controllers/Admin/TagController.php` (CRLF file), replace:

```php
    public function save(Request $request)
    {
```

with:

```php
    public function save(Request $request)
    {
        // Saving a keyword creates a tag: the tag create page's ability, before validation (S21).
        $this->authorize('create', CrudModel::class);
```

4. In `Modules/Notification/Http/Controllers/NotificationController.php` (CRLF file), replace:

```php
    public function getList(Request $request)
    {
```

with:

```php
    public function getList(Request $request)
    {
        // The notifications ability (S21).
        $this->authorize('view', FirebaseNotification::class);
```

and replace:

```php
    public function postIndex(Request $request)
    {
```

with:

```php
    public function postIndex(Request $request)
    {
        // The notifications ability (S21).
        $this->authorize('view', FirebaseNotification::class);
```

- [ ] **Step 6: Run the spec and confirm it passes**

```bash
(cd tests/e2e && npx playwright test specs/security/admin-authorization.spec.ts --reporter=line | tail -5)
```

Expected: `3 passed`.

If test 3 fails only at "notification list" or "notification feed" with `500`, do not change the expectation. Report the exception class and message from the newest `storage/logs/laravel-*.log` entry: those two endpoints had no caller before, so a failure there may predate S21.

- [ ] **Step 7: Record the dump ids**

In `tests/e2e/PARITY.md`, section "When the dump changes", after the bullet that starts with "- `parity/admin-urls.ts`: model ids", add:

```markdown
- `specs/security/admin-authorization.spec.ts`: project id `133`, opportunity id `320`, landing page id `11` and article id `251`. The spec attaches fixture payments, prices, a timeline and an external attachment to them, and deletes them again.
```

- [ ] **Step 8: Run the gate** (Global Constraints, "The gate")

Expected before the controller's acceptance:
- `npm test`: `382 passed, 1 failed`. The failure is `permissions-matrix.spec.ts`, with exactly the 10 cells in "Expected cell diff". Check them with the node snippet from Task 3, Step 10, which prints `url | principal: before -> after` lines; the output must equal the block above.
- PHPUnit: `OK (30 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.

- [ ] **Step 9: Commit**

```bash
git add Modules/Backend/Http/Controllers/Admin/ProjectController.php Modules/Backend/Http/Controllers/Admin/OpportunityController.php \
  Modules/Cms/Http/Controllers/Admin/ContentController.php Modules/Cms/Http/Controllers/Admin/LandingPageController.php \
  Modules/Cms/Http/Controllers/Admin/TagController.php Modules/Notification/Http/Controllers/NotificationController.php \
  tests/e2e/specs/security/admin-authorization.spec.ts tests/e2e/PARITY.md
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Enforce the admin page abilities on lead data endpoints and on unauthorized admin POST routes (S21)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

In the report, ask for the 10-cell matrix acceptance.

---

### Task 6: Admin GET routes that answer 500 (S11)

Seven admin GET routes answer 500 for staff. On Laravel 13, `projects/data` already answers 200 (P1-R19).
- `notification` extends a view namespace that does not exist.
- `users/show`, `roles/show`, `projects/show` and `opportunity/show` point at `show()` methods that do not exist.
- `users/identity/validate_` reads an undefined `$this->id`.
- `tags/list` without a `locale` dereferences a missing translation.

The first five now answer 404, and the last two a validation error. Every route and route name stays, so `tests/upgrade/routes.json` does not change. The `users/show` name must stay because the user summary modal links to it.

**Files:**
- Modify: `Modules/Notification/Http/Controllers/NotificationController.php` (`index`)
- Modify: `Modules/Cms/Http/Controllers/Admin/UserController.php` (add `show`; fix `validateIdentity_`)
- Modify: `Modules/Cms/Http/Controllers/Admin/TagController.php` (`list`)
- Modify: `Modules/Backend/Http/Controllers/Admin/ProjectController.php`, `Modules/Backend/Http/Controllers/Admin/OpportunityController.php`, `Modules/Permissions/Http/Controllers/Admin/RoleController.php` (add `show`)
- Modify: `tests/e2e/PARITY.md` (the 500 bullet)
- Create: `tests/e2e/specs/security/admin-error-routes.spec.ts`
- Existing tests that observe this change:
  - `specs/parity/permissions-matrix.spec.ts`: **baseline change, 14 cells.**
  - `specs/parity/admin-sections.spec.ts` "admin section notifications": **baseline change, 1 cell.**

**Interfaces:**
- Consumes: Task 5's `NotificationController` and `TagController` (other methods of the same files).
- Produces: `show()` on UserController, ProjectController, OpportunityController and RoleController, each `abort(404)`.
- The live uniqueness hint on the user edit forms stays as silent as it is today: its callers send no `model` id and now get 422 instead of 500.

**Expected cell diffs:**

`tests/e2e/snapshots/parity/permissions-matrix.spec.ts/matrix.json`:
```
/en/admin/notification | parity-admin: "500" -> "404"
/en/admin/notification | parity-superadmin: "500" -> "404"
/en/admin/opportunity/show | parity-admin: "500" -> "404"
/en/admin/opportunity/show | parity-superadmin: "500" -> "404"
/en/admin/projects/show | parity-admin: "500" -> "404"
/en/admin/projects/show | parity-superadmin: "500" -> "404"
/en/admin/roles/show | parity-admin: "500" -> "404"
/en/admin/roles/show | parity-superadmin: "500" -> "404"
/en/admin/tags/list | parity-admin: "500" -> "302 back"
/en/admin/tags/list | parity-superadmin: "500" -> "302 back"
/en/admin/users/identity/validate_?name=username&keyword=parity-probe | parity-admin: "500" -> "302 back"
/en/admin/users/identity/validate_?name=username&keyword=parity-probe | parity-superadmin: "500" -> "302 back"
/en/admin/users/show | parity-admin: "500" -> "404"
/en/admin/users/show | parity-superadmin: "500" -> "404"
```

`tests/e2e/snapshots/parity/admin-sections.spec.ts/notifications.json`:
```
status: 500 -> 404
```

- [ ] **Step 1: Write the failing spec**

Create `tests/e2e/specs/security/admin-error-routes.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { sql } from '../../support/docker';
import { requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S11: admin GET routes that answered 500 for staff answer 404 or a validation error.
test.beforeAll(() => requireLocal('uses the local parity accounts'));

const GONE = ['/en/admin/notification', '/en/admin/users/show', '/en/admin/roles/show', '/en/admin/projects/show', '/en/admin/opportunity/show'];

for (const who of ['parity-superadmin', 'parity-admin'] as const) {
  test(`dead admin GET routes answer 404 to ${who}, never 500`, async ({ page }) => {
    await blockProduction(page.context());
    await loginAs(page, who);
    for (const url of GONE) {
      expect((await page.request.get(url, { maxRedirects: 0 })).status(), url).toBe(404);
    }
  });
}

test('tags/list requires a locale and still lists tags with one', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  expect((await page.request.get('/en/admin/tags/list', { headers: AJAX_HEADERS })).status()).toBe(422);
  const listed = await page.request.get('/en/admin/tags/list?locale=en&q=&count=0', { headers: AJAX_HEADERS });
  expect(listed.status()).toBe(200);
  expect((JSON.parse(await listed.text()) as unknown[]).length).toBeGreaterThan(1);
});

test('validate_ needs the edited user id, ignores that user, and the user summary still links to users/show', async ({ page }) => {
  await blockProduction(page.context());
  await loginAs(page, 'parity-superadmin');
  const probe = await page.request.get('/en/admin/users/identity/validate_?name=username&keyword=parity-probe', { headers: AJAX_HEADERS });
  expect(probe.status()).toBe(422);

  const id = sql("SELECT id FROM users WHERE username = 'parity-admin'");
  const own = await page.request.get(`/en/admin/users/identity/validate_?name=username&keyword=parity-admin&model=${id}`, { headers: AJAX_HEADERS });
  expect(own.status()).toBe(200);
  expect((await own.json()).is_valid).toBe(true);

  const summary = await page.request.get(`/en/admin/users/summary?model=${id}`, { headers: AJAX_HEADERS });
  expect(summary.status()).toBe(200);
  expect((await summary.json()).summary).toContain('/en/admin/users/show?model=');
});
```

- [ ] **Step 2: Run it and confirm it fails**

```bash
(cd tests/e2e && npx playwright test specs/security/admin-error-routes.spec.ts --reporter=line | tail -15)
```

Expected: `4 failed`. The first `expect` of each test gets `500`.

- [ ] **Step 3: 404 for the five dead pages**

1. In `Modules/Notification/Http/Controllers/NotificationController.php` (CRLF file), replace:

```php
    public function index(Request $request)
    {

        $this->data['roles'] = Role::with('translations')->get();
        return view('notification::notifications.index', $this->data);
    }
```

with:

```php
    public function index(Request $request)
    {
        // The page extends a view namespace (admin::) that does not exist, so it could only answer 500, and its
        // menu entry is commented out. 404 instead (S11); the route name stays for notifications/create.blade.php.
        abort(404);
    }
```

2. In each of `Modules/Backend/Http/Controllers/Admin/ProjectController.php`, `Modules/Backend/Http/Controllers/Admin/OpportunityController.php` and `Modules/Permissions/Http/Controllers/Admin/RoleController.php` (all CRLF), replace the single line:

```php
    public function create(Request $request)
```

with:

```php
    /**
     * The show route exists but no detail page does: 404 instead of 500 (S11).
     */
    public function show()
    {
        abort(404);
    }

    public function create(Request $request)
```

- [ ] **Step 4: users/show and validateIdentity_**

In `Modules/Cms/Http/Controllers/Admin/UserController.php` (CRLF file), replace:

```php
    public function validateIdentity_(Request $request)
    {
        $validator = Validator::make([
            $request->name => $request->keyword
        ], [
            'username'  => ['nullable', 'min:4', 'max:191', new Username, 'unique:users,username' .$this->id],
            'email'     => ['nullable', 'max:191', 'email', 'unique:users,email'  .$this->id]
        ]);
```

with:

```php
    /**
     * No user detail page exists, but the user summary modal links here: 404 instead of 500 (S11).
     */
    public function show()
    {
        abort(404);
    }

    public function validateIdentity_(Request $request)
    {
        // The id of the user being edited is required: the unique rules must ignore that user's own username
        // and email. This used to read an undefined $this->id and answered 500 (S11).
        $request->validate(['model' => 'required|integer']);

        $validator = Validator::make([
            $request->name => $request->keyword
        ], [
            'username'  => ['nullable', 'min:4', 'max:191', new Username, 'unique:users,username,' . $request->model],
            'email'     => ['nullable', 'max:191', 'email', 'unique:users,email,' . $request->model]
        ]);
```

- [ ] **Step 5: tags/list requires a locale**

In `Modules/Cms/Http/Controllers/Admin/TagController.php` (CRLF file), replace:

```php
    public function list(Request $request)
    {
        $term   = trim((string) $request->q);
```

with:

```php
    public function list(Request $request)
    {
        // The taggable widget always sends a locale. Without one, translate() returned null for tags
        // missing the current locale and the list answered 500 (S11).
        $request->validate(['locale' => 'required|string']);

        $term   = trim((string) $request->q);
```

- [ ] **Step 6: Run the spec and confirm it passes**

```bash
(cd tests/e2e && npx playwright test specs/security/admin-error-routes.spec.ts --reporter=line | tail -5)
```

Expected: `4 passed`.

- [ ] **Step 7: Update the runbook fact**

In `tests/e2e/PARITY.md`, replace the bullet that starts with "- Admin GET routes that answer 500 for staff:" with:

```markdown
- Admin GET routes that answered 500 for staff on Laravel 7 no longer do (Phase 2, S11). `notification`, `users/show`, `roles/show`, `projects/show` and `opportunity/show` answer 404. `users/identity/validate_` without a `model` id and `tags/list` without a `locale` answer a validation error ("302 back" in the matrix, 422 for AJAX). `projects/data` without DataTables parameters answered 500 on Laravel 7 and answers 200 on Laravel 13 (accepted difference P1-R19).
```

- [ ] **Step 8: Run the gate** (Global Constraints, "The gate")

Expected before the controller's acceptance:
- `npm test`: `385 passed, 2 failed`.
  - `permissions-matrix.spec.ts` shows exactly the 14 cells; check with the node snippet from Task 3, Step 10.
  - "admin section notifications" expects `"status": 500` and receives `"status": 404`.
- PHPUnit: `OK (30 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.

- [ ] **Step 9: Commit**

```bash
git add Modules/Notification/Http/Controllers/NotificationController.php Modules/Cms/Http/Controllers/Admin/UserController.php \
  Modules/Cms/Http/Controllers/Admin/TagController.php Modules/Backend/Http/Controllers/Admin/ProjectController.php \
  Modules/Backend/Http/Controllers/Admin/OpportunityController.php Modules/Permissions/Http/Controllers/Admin/RoleController.php \
  tests/e2e/specs/security/admin-error-routes.spec.ts tests/e2e/PARITY.md
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Answer 404 or a validation error instead of 500 on seven admin GET routes (S11)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

In the report, ask for the 14-cell matrix acceptance and the one-cell `notifications.json` acceptance.

---

### Task 7: Page-size caps (S1)

Two separate mechanisms take the page size from the request, and neither caps it:

- **Select2 JSON endpoints: `->paginate($request->items_per_page)`.** There are six call sites. Four are public: `/{locale}/get-countries`, `get-cities`, `get-areas` and `get-contents`. Two need any signed-in account: `admin/users/get-user-select2` and `admin/categories/{type}/get-categories`. Every Blade caller sends 20.
  - Today `?items_per_page=999999` returns the whole table.
  - `abc` or `-3` answer 500.
  - They now go through `Modules\Cms\Classes\PageSize::fromRequest()`. It clamps to 20, and a missing or invalid value gives `null`, meaning the model default of 15, which is what a missing value always gave.
- **Admin DataTables: `length`.** yajra reads `length` unbounded, and `config/datatables.php` has no `max_length`. The base tables offer at most 500, so `max_length` is 500.
  - With `max_length` set, yajra pages every request, including a `length=-1` "All". The leads list and the property-forms list (`data_requests` and `data_properties` in both controllers) offer "All". Capping those would silently show 500 of 3,708 leads under "All", so those four endpoints call `ignoreMaxLength()` and stay exactly as they are.
  - Since Task 5 only `tags.requests` holders (SUPERADMIN) reach them.

A static helper is used instead of a `Request` macro, so Larastan and editors resolve the call without macro reflection. The behaviour is the one a macro would have.

**Files:**
- Create: `Modules/Cms/Classes/PageSize.php`
- Modify: `Modules/Cms/Http/Controllers/CmsController.php` (4 call sites and an import)
- Modify: `Modules/Cms/Http/Controllers/Admin/UserController.php` (`getUsersSelect2` and an import)
- Modify: `Modules/Cms/Http/Controllers/Admin/CategoryController.php` (`getCategoriesSelect2` and an import)
- Modify: `config/datatables.php` (`max_length`)
- Modify: `Modules/Backend/Http/Controllers/Admin/ProjectController.php`, `Modules/Backend/Http/Controllers/Admin/OpportunityController.php` (`ignoreMaxLength()` in `data_requests` and `data_properties`)
- Create: `tests/Unit/PageSizeCapTest.php`, `tests/e2e/specs/security/page-size-caps.spec.ts`
- Existing tests that observe this change, none of which needs a baseline change:
  - `permissions-matrix.spec.ts` records statuses only. `get-user-select2`, `get-categories` and `…/data` without parameters still answer 200, now with a first page instead of every row.
  - `admin-sections.spec.ts` and `admin-table-values.spec.ts` walk at the page's own 50-row length.

**Interfaces:**
- Consumes:
  - Task 1's `config/datatables.php`, where `json.options` stays.
  - Task 5's lead endpoint bodies:
    ```php
    $this->authorize(...);
    $list = ContactUS::query();
    $datatables = DataTables::of($list);
    ```
- Produces:
  - `Modules\Cms\Classes\PageSize::fromRequest(Illuminate\Http\Request $request, int $max = PageSize::SELECT2_MAX): ?int`, with `SELECT2_MAX = 20`.
  - `config('datatables.max_length') === 500`.

- [ ] **Step 1: Write the failing PHPUnit test**

Create `tests/Unit/PageSizeCapTest.php`:

```php
<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Modules\Cms\Classes\PageSize;
use Tests\TestCase;
use Yajra\DataTables\Utilities\Request as DataTablesRequest;

/** S1: request-controlled page sizes are capped at the largest size the UI uses. */
class PageSizeCapTest extends TestCase
{
    public function test_select2_page_sizes_are_capped_at_twenty(): void
    {
        $size = fn (array $query) => PageSize::fromRequest(Request::create('/', 'GET', $query));

        $this->assertSame(20, $size(['items_per_page' => '999999']));
        $this->assertSame(20, $size(['items_per_page' => '20']));
        $this->assertSame(7, $size(['items_per_page' => '7']));
        $this->assertNull($size([]));
        $this->assertNull($size(['items_per_page' => '0']));
        $this->assertNull($size(['items_per_page' => '-3']));
        $this->assertNull($size(['items_per_page' => 'abc']));
        $this->assertNull($size(['items_per_page' => ['20']]));
    }

    public function test_datatables_lengths_are_capped_at_five_hundred(): void
    {
        $this->assertSame(500, config('datatables.max_length'));

        foreach ([['100000', 500], ['-1', 500], ['500', 500], ['50', 50]] as [$asked, $served]) {
            request()->merge(['start' => '0', 'length' => $asked]);
            $this->assertSame($served, (new DataTablesRequest)->length(), "length={$asked}");
        }
    }

    public function test_the_lead_and_property_form_lists_keep_their_all_option(): void
    {
        foreach (['Project', 'Opportunity'] as $name) {
            $source = file_get_contents(base_path("Modules/Backend/Http/Controllers/Admin/{$name}Controller.php"));
            $this->assertSame(2, substr_count($source, 'DataTables::of($list)->ignoreMaxLength()'), "{$name}Controller: data_requests and data_properties");
        }
    }
}
```

- [ ] **Step 2: Write the Playwright spec**

Create `tests/e2e/specs/security/page-size-caps.spec.ts`:

```ts
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
```

- [ ] **Step 3: Run both and confirm the expected results**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/PageSizeCapTest.php
(cd tests/e2e && npx playwright test specs/security/page-size-caps.spec.ts --reporter=line | tail -10)
```

Expected:
- PHPUnit: 1 error (`Class "Modules\Cms\Classes\PageSize" not found`) and 2 failures (`null` is not 500; `0` is not `2`).
- Playwright: `1 failed, 1 passed`.
  - The Select2 test gets `57`.
  - The lead-list test passes: it guards that the lead list keeps "All", which is true before and after this task.

- [ ] **Step 4: The Select2 page-size helper**

Create `Modules/Cms/Classes/PageSize.php`:

```php
<?php

namespace Modules\Cms\Classes;

use Illuminate\Http\Request;

/**
 * Page size for the Select2 JSON endpoints (S1).
 *
 * Every Blade caller sends items_per_page=20, so 20 is the cap: a larger value is clamped to it. A missing,
 * zero, negative or non-numeric value returns null, which makes paginate() use the model's default page
 * size (15), exactly as a missing value always did.
 */
class PageSize
{
    public const SELECT2_MAX = 20;

    public static function fromRequest(Request $request, int $max = self::SELECT2_MAX): ?int
    {
        $value = $request->input('items_per_page');

        if (! is_numeric($value) || (int) $value < 1) {
            return null;
        }

        return min((int) $value, $max);
    }
}
```

- [ ] **Step 5: Use it at the six call sites**

```bash
perl -pi -e 's/->paginate\(\$request->items_per_page\)/->paginate(PageSize::fromRequest(\$request))/g' \
  Modules/Cms/Http/Controllers/CmsController.php Modules/Cms/Http/Controllers/Admin/UserController.php Modules/Cms/Http/Controllers/Admin/CategoryController.php
grep -c 'PageSize::fromRequest' Modules/Cms/Http/Controllers/CmsController.php Modules/Cms/Http/Controllers/Admin/UserController.php Modules/Cms/Http/Controllers/Admin/CategoryController.php
grep -rn 'items_per_page)' Modules app --include='*.php'
```

Expected: counts `4`, `1`, `1`, and no output from the last grep.

Add the import in each file, keeping its CRLF line endings:
- In `Modules/Cms/Http/Controllers/CmsController.php`, replace `use Modules\Cms\Entities\Content;` with:

```php
use Modules\Cms\Entities\Content;
use Modules\Cms\Classes\PageSize;
```

- In `Modules/Cms/Http/Controllers/Admin/UserController.php` and `Modules/Cms/Http/Controllers/Admin/CategoryController.php`, replace `use Modules\Cms\Classes\ResponseHandler;` with:

```php
use Modules\Cms\Classes\ResponseHandler;
use Modules\Cms\Classes\PageSize;
```

- [ ] **Step 6: The DataTables cap, with the lead lists' "All" kept**

In `config/datatables.php`, replace:

```php
    'index_column'   => 'DT_RowIndex',
```

with:

```php
    'index_column'   => 'DT_RowIndex',

    /*
     * Largest page a request may ask for (S1). 500 is the largest page size the admin tables offer, so a
     * larger length, or length=-1, gets 500 rows. The lead and property-form lists offer "All" and keep it
     * with ignoreMaxLength() in their controllers.
     */
    'max_length'     => 500,
```

In both `Modules/Backend/Http/Controllers/Admin/ProjectController.php` and `Modules/Backend/Http/Controllers/Admin/OpportunityController.php` (CRLF files), replace:

```php
        $list = ContactUS::query();
        $datatables = DataTables::of($list);
```

with:

```php
        $list = ContactUS::query();
        // The leads list offers "All" (length=-1): it stays uncapped (S1); only tags.requests holders reach it (S21).
        $datatables = DataTables::of($list)->ignoreMaxLength();
```

and replace:

```php
        $list = PropertyForm::query();
        $datatables = DataTables::of($list);
```

with:

```php
        $list = PropertyForm::query();
        // The property-forms list offers "All" (length=-1): it stays uncapped (S1); only tags.requests holders reach it (S21).
        $datatables = DataTables::of($list)->ignoreMaxLength();
```

- [ ] **Step 7: Run both and confirm they pass**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/PageSizeCapTest.php
(cd tests/e2e && npx playwright test specs/security/page-size-caps.spec.ts --reporter=line | tail -5)
for q in 999999 abc -3; do curl -s -o /dev/null -w "items_per_page=$q %{http_code}\n" "http://localhost:8080/en/get-areas?items_per_page=$q"; done
```

Expected:
- PHPUnit `OK (3 tests`.
- Playwright `2 passed`.
- `items_per_page=999999 200`, `items_per_page=abc 200`, `items_per_page=-3 200`. The last two answered 500 before.

- [ ] **Step 8: Run the gate** (Global Constraints, "The gate")

Expected:
- `npm test`: `389 passed` (321 + 68).
- PHPUnit: `OK (33 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.
- No baseline change.

- [ ] **Step 9: Commit**

```bash
git add Modules/Cms/Classes/PageSize.php Modules/Cms/Http/Controllers/CmsController.php Modules/Cms/Http/Controllers/Admin/UserController.php \
  Modules/Cms/Http/Controllers/Admin/CategoryController.php config/datatables.php \
  Modules/Backend/Http/Controllers/Admin/ProjectController.php Modules/Backend/Http/Controllers/Admin/OpportunityController.php \
  tests/Unit/PageSizeCapTest.php tests/e2e/specs/security/page-size-caps.spec.ts
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Cap request-controlled page sizes: Select2 at 20, DataTables at 500, lead lists keep All (S1)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

---

### Task 8: Security headers and a Report-Only CSP (S3)

No response carries security headers today. A new global middleware sets these on **every response the application sends**:
- `Strict-Transport-Security: max-age=31536000`
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Content-Security-Policy-Report-Only`

"Every response" covers pages, redirects, JSON, `/img` images, and 404 and 500 pages. The middleware is registered first in `app/Http/Kernel.php` `$middleware`, so it wraps even responses rendered from exceptions or redirects made by `RedirectToHttps`.

Decisions:
- **HSTS without `includeSubDomains`.** The spec asks for HSTS only. The Hostinger account also serves other hosts, and a subdomain that is not HTTPS would break for a year.
- **Report-Only CSP with no `report-uri`.** The app has no endpoint or worker to collect reports. Browsers log violations to the console and block nothing, so no page changes. Revisit `report-uri` and enforcement on staging (Phase 3).
- **The CSP origin list is what the views load today** (research A):
  - amcharts, jsdelivr, unpkg, cdnjs and bootstrapcdn (maxcdn and stackpath);
  - ampproject and ampbyexample (AMP stories);
  - the rocketcdn icon host;
  - YouTube (lightbox iframes);
  - Google Fonts;
  - Google Maps and gstatic (admin);
  - TinyMCE Cloud (admin).

  `'unsafe-inline'` and `'unsafe-eval'` are needed because the views use inline scripts without nonces.
- **Static files served directly by Apache/LiteSpeed are out of the middleware's reach**, and this task does not add `.htaccess` headers for them. `nosniff` on static files would block any asset the production server serves with a wrong MIME type, and that cannot be verified locally. The PR lists this for Phase 3 staging.

**Files:**
- Create: `app/Http/Middleware/SecurityHeaders.php`
- Modify: `app/Http/Kernel.php` (`$middleware`)
- Create: `tests/Unit/SecurityHeadersTest.php`, `tests/e2e/specs/security/headers.spec.ts`
- Existing tests that observe this change, none of which needs a baseline change:
  - `golden-master.spec.ts` records only `content-type`.
  - The `images`, `module-scripts`, `tinymce` and `maintenance-routes` security specs assert `content-type` or `location` only.
  - The visual spec is unaffected: Report-Only blocks nothing.

**Interfaces:**
- Consumes: nothing.
- Produces: `App\Http\Middleware\SecurityHeaders::HEADERS` (`array<string, string>`) and `SecurityHeaders::CONTENT_SECURITY_POLICY_REPORT_ONLY` (`string`).

- [ ] **Step 1: Write the failing PHPUnit test**

Create `tests/Unit/SecurityHeadersTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Tests\TestCase;

/** S3: security headers on every application response; the CSP is Report-Only. */
class SecurityHeadersTest extends TestCase
{
    public function test_a_response_gets_every_security_header(): void
    {
        $response = (new SecurityHeaders)->handle(Request::create('/'), fn () => response('ok'));

        $this->assertSame('max-age=31536000', $response->headers->get('Strict-Transport-Security'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertSame(SecurityHeaders::CONTENT_SECURITY_POLICY_REPORT_ONLY, $response->headers->get('Content-Security-Policy-Report-Only'));
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
    }

    public function test_the_middleware_is_global(): void
    {
        $this->assertTrue($this->app->make(Kernel::class)->hasMiddleware(SecurityHeaders::class));
    }
}
```

- [ ] **Step 2: Write the failing Playwright spec**

Create `tests/e2e/specs/security/headers.spec.ts`:

```ts
import { test, expect, APIResponse } from '@playwright/test';

// S3: security headers on every response the application sends. Read-only.
const EXPECTED: Record<string, string> = {
  'strict-transport-security': 'max-age=31536000',
  'x-content-type-options': 'nosniff',
  'x-frame-options': 'SAMEORIGIN',
  'referrer-policy': 'strict-origin-when-cross-origin',
};

function expectSecurityHeaders(response: APIResponse, label: string): void {
  const headers = response.headersArray();
  for (const [name, value] of Object.entries(EXPECTED)) {
    expect(headers.filter(h => h.name.toLowerCase() === name).map(h => h.value), `${label}: ${name}`).toEqual([value]);
  }
  expect(headers.filter(h => h.name.toLowerCase() === 'content-security-policy-report-only').length, `${label}: CSP report-only`).toBe(1);
  expect(headers.some(h => h.name.toLowerCase() === 'content-security-policy'), `${label}: no enforcing CSP`).toBe(false);
}

test('pages, redirects, JSON, images and error pages carry the security headers once', async ({ request }) => {
  const urls: Array<[string, string]> = [
    ['English home', '/en'],
    ['Arabic home', '/ar'],
    ['admin login', '/en/authenticate/login'],
    ['Select2 JSON', '/en/get-areas?items_per_page=2'],
    ['image', '/img/85x85/defaults/base.png'],
    ['not found', '/en/zz-no-such-type/zz-no-such-page'],
    ['locale redirect', '/'],
  ];
  for (const [label, url] of urls) {
    expectSecurityHeaders(await request.get(url, { maxRedirects: 0 }), label);
  }
});

test('the Content-Security-Policy is report-only and names the CDNs the pages load', async ({ request }) => {
  const policy = (await request.get('/en')).headers()['content-security-policy-report-only'] ?? '';
  for (const origin of [
    'www.amcharts.com', 'cdn.jsdelivr.net', 'unpkg.com', 'cdnjs.cloudflare.com', 'maxcdn.bootstrapcdn.com',
    'stackpath.bootstrapcdn.com', 'cdn.ampproject.org', 'kq9v7r75.rocketcdn.com', 'www.youtube.com',
    'fonts.googleapis.com', 'fonts.gstatic.com',
  ]) {
    expect(policy, origin).toContain(origin);
  }
});
```

- [ ] **Step 3: Run both and confirm they fail**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/SecurityHeadersTest.php
(cd tests/e2e && npx playwright test specs/security/headers.spec.ts --reporter=line | tail -10)
```

Expected: PHPUnit 2 errors (`Class "App\Http\Middleware\SecurityHeaders" not found`); Playwright `2 failed` (`English home: strict-transport-security` gets `[]`).

- [ ] **Step 4: The middleware**

Create `app/Http/Middleware/SecurityHeaders.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Security headers on every response the application sends (spec S3).
 *
 * Registered first in the global middleware stack, so pages, redirects, JSON, /img images and rendered
 * exceptions all carry them. Files the web server serves directly (compiled CSS/JS, uploads) never reach PHP.
 *
 * HSTS has no includeSubDomains: other hosts share the Hostinger account. The Content-Security-Policy is
 * Report-Only, so browsers log violations and block nothing. It has no report-uri, because the app has no
 * endpoint or worker to collect reports. It lists the external origins the views load today; the views use
 * inline scripts without nonces, hence 'unsafe-inline' and 'unsafe-eval'.
 */
class SecurityHeaders
{
    public const HEADERS = [
        'Strict-Transport-Security' => 'max-age=31536000',
        'X-Content-Type-Options'    => 'nosniff',
        'X-Frame-Options'           => 'SAMEORIGIN',
        'Referrer-Policy'           => 'strict-origin-when-cross-origin',
    ];

    public const CONTENT_SECURITY_POLICY_REPORT_ONLY =
        "default-src 'self'; "
        . "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com www.amcharts.com "
        . "maxcdn.bootstrapcdn.com stackpath.bootstrapcdn.com cdn.ampproject.org cdn.tiny.cloud maps.google.com www.gstatic.com; "
        . "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net unpkg.com maxcdn.bootstrapcdn.com stackpath.bootstrapcdn.com "
        . "fonts.googleapis.com ampbyexample.com; "
        . "font-src 'self' data: fonts.gstatic.com; "
        . "img-src 'self' data: kq9v7r75.rocketcdn.com ampbyexample.com; "
        . "frame-src 'self' www.youtube.com maps.google.com; "
        . "connect-src 'self'";

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        foreach (self::HEADERS as $name => $value) {
            $response->headers->set($name, $value);
        }
        $response->headers->set('Content-Security-Policy-Report-Only', self::CONTENT_SECURITY_POLICY_REPORT_ONLY);

        return $response;
    }
}
```

- [ ] **Step 5: Register it first in the global stack**

In `app/Http/Kernel.php`, replace:

```php
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
```

with:

```php
    protected $middleware = [
        // First, so every response gets the headers, including redirects and rendered exceptions (S3).
        \App\Http\Middleware\SecurityHeaders::class,
        \App\Http\Middleware\TrustProxies::class,
```

- [ ] **Step 6: Run both and confirm they pass**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/SecurityHeadersTest.php
(cd tests/e2e && npx playwright test specs/security/headers.spec.ts --reporter=line | tail -5)
curl -sI http://localhost:8080/en | grep -ic 'strict-transport-security\|x-content-type-options\|x-frame-options\|referrer-policy\|content-security-policy-report-only'
```

Expected: PHPUnit `OK (2 tests`; Playwright `2 passed`; `5`.

- [ ] **Step 7: Run the gate** (Global Constraints, "The gate")

Expected:
- `npm test`: `391 passed` (321 + 70). The `visual` spec must pass unchanged: Report-Only changes nothing on screen.
- PHPUnit: `OK (35 tests`.
- `structure matches tests/upgrade/`. Global middleware is not part of the route table.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.
- No baseline change.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Middleware/SecurityHeaders.php app/Http/Kernel.php tests/Unit/SecurityHeadersTest.php tests/e2e/specs/security/headers.spec.ts
git commit -m "Send HSTS, nosniff, frame, referrer and Report-Only CSP headers on every application response (S3)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

---

### Task 9: Production-readiness config (S2)

Scope, per the spec's S2 and the Phase 3 split:
- **`config/app.php`:** `'debug' => env('APP_DEBUG', false)`. Today a missing `APP_DEBUG` means debug on.
- **`config/session.php`:** `serialization` becomes `json`, the move Phase 1 deferred.
  - No session value holds a PHP object: flash arrays, old input, user ids, the CSRF token and the locale. Laravel's store marshals the `errors` bag for JSON.
  - The one-time effect is that every existing session file becomes unreadable, so everyone is logged out once.
  - `secure` keeps reading `SESSION_SECURE_COOKIE`, with the local default `false`: the local stack is plain http.
- **No application code references a `require-dev` class,** so `composer install --no-dev` is safe. Task 2 removed the only such reference (`\Debugbar::enable()`); this task adds the guard test.
- **The production `.env` values are a Phase 3 checklist item, not a Phase 2 change:** `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true` and `CURRCONV_API_KEY`, together with `composer install --no-dev`. Task 13 writes them into the PR material.

**Files:**
- Modify: `config/app.php` (line 42)
- Modify: `config/session.php` (the `serialization` block, and a comment on `secure`)
- Modify: `tests/e2e/PARITY.md` ("Environment the baselines assume")
- Create: `tests/Unit/ProductionConfigTest.php`, `tests/e2e/specs/security/session-config.spec.ts`
- Existing tests that observe this change:
  - `scripts/upgrade/check-structure.sh` (`tests/upgrade/config.json`): **baseline change, 1 cell.**
  - Every signed-in spec exercises sessions (login, CSRF, flashes) and must stay green.

**Interfaces:**
- Consumes (Task 2): `app/Providers/AppServiceProvider.php` no longer references Debugbar. Without that, the require-dev guard below fails.
- Produces: `config('session.serialization') === 'json'`.

**Expected cell diff** in `tests/upgrade/config.json` (the controller accepts it after the commit):

```
session.serialization: "php" -> "json"
```

- [ ] **Step 1: Check the local debug setting without printing `.env`**

```bash
grep -q '^APP_DEBUG=true' .env && echo "APP_DEBUG=true is set"
```

Expected: `APP_DEBUG=true is set`. If nothing prints, stop and report. The parity baselines assume debug on (yajra answers DataTables errors as HTTP 200 JSON only in debug mode), and without the key the new safe default would turn debug off locally. The owner adds the key; do not edit `.env` yourself.

- [ ] **Step 2: Write the failing PHPUnit guard**

Create `tests/Unit/ProductionConfigTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * S2 production readiness, checked in source. The production .env values themselves (APP_DEBUG=false,
 * SESSION_SECURE_COOKIE=true) are Phase 3 work; the code must default safely and run under --no-dev.
 */
class ProductionConfigTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public function test_debug_mode_is_off_unless_the_environment_turns_it_on(): void
    {
        $source = file_get_contents($this->root() . '/config/app.php');

        $this->assertTrue(str_contains($source, "'debug' => env('APP_DEBUG', false),"), 'config/app.php must default app.debug to false');
    }

    public function test_sessions_are_json_and_the_secure_flag_comes_from_the_environment(): void
    {
        $source = file_get_contents($this->root() . '/config/session.php');

        $this->assertTrue(str_contains($source, "'serialization' => 'json',"), 'config/session.php must serialize sessions as JSON');
        $this->assertTrue(str_contains($source, "'secure' => env('SESSION_SECURE_COOKIE', false),"), 'config/session.php must read SESSION_SECURE_COOKIE');
    }

    public function test_application_code_references_no_require_dev_package(): void
    {
        // Legacy factory closures that only type-hint Faker; no request loads them.
        $allowed = ['database/factories/UserFactory.php', 'Modules/Permissions/Database/factories/UserFactory.php'];
        $pattern = '/\b(Barryvdh|Fruitcake\\\\LaravelDebugbar|Debugbar::|Mockery|PHPUnit\\\\|RectorLaravel|Rector\\\\|Larastan|NunoMaduro|Faker\\\\)/';
        $offenders = [];
        foreach (['app', 'bootstrap', 'config', 'database', 'Modules', 'routes'] as $dir) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root() . '/' . $dir, RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                $relative = substr($file->getPathname(), strlen($this->root()) + 1);
                if (! str_ends_with($relative, '.php')
                    || str_contains($relative, '/Resources/assets/')
                    || str_contains($relative, '/Tests/')
                    || str_starts_with($relative, 'bootstrap/cache/')
                    || in_array($relative, $allowed, true)) {
                    continue;
                }
                if (preg_match($pattern, file_get_contents($file->getPathname()))) {
                    $offenders[] = $relative;
                }
            }
        }

        $this->assertSame([], $offenders, 'These files reference a require-dev package, which composer install --no-dev removes.');
    }
}
```

- [ ] **Step 3: Write the failing session spec**

Create `tests/e2e/specs/security/session-config.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { appShell } from '../../support/docker';
import { BASE_URL, requireLocal } from '../../support/env';
import { blockProduction } from '../../support/network';

// S2: sessions are stored as JSON, and flashed messages still survive a redirect.
test('sessions are stored as JSON and a flashed message still survives a redirect', async ({ page }) => {
  requireLocal('reads the local session files');
  await blockProduction(page.context());
  await loginAs(page, 'parity-admin');

  // ADMIN lacks tags.requests: the leads page redirects back with a flashed "permission error" toastr.
  await page.goto('/en/admin/projects/requests', { referer: `${BASE_URL}/en/admin` });
  expect(new URL(page.url()).pathname).toBe('/en/admin');
  expect(await page.content()).toContain("show_toastr('warning'");

  // Only the first byte of the newest session file is read: "{" for JSON, "a" for PHP serialization.
  const newest = appShell("ls -t storage/framework/sessions | grep -v '^\\.gitignore$' | head -n 1");
  expect(appShell(`head -c 1 storage/framework/sessions/${newest}`)).toBe('{');
});
```

- [ ] **Step 4: Run both and confirm they fail**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/ProductionConfigTest.php
(cd tests/e2e && npx playwright test specs/security/session-config.spec.ts --reporter=line | tail -10)
```

Expected:
- PHPUnit: 2 failures, the debug default and the serialization.
  - `test_application_code_references_no_require_dev_package` already passes after Task 2.
  - If it lists offenders, report them.
- Playwright: `1 failed`. The flash assertions pass; the last `expect` receives `a`.

- [ ] **Step 5: Change the two config values**

In `config/app.php` (CRLF file), replace:

```php
    'debug' => env('APP_DEBUG', true),
```

with:

```php
    // Off unless the environment turns it on: a missing APP_DEBUG must never mean debug output (S2).
    'debug' => env('APP_DEBUG', false),
```

In `config/session.php` (CRLF file), replace:

```php
    /*
    | Laravel 13 option. Kept at "php" so Laravel 7 session files stay readable; Phase 2 moves to "json".
    */
    'serialization' => 'php',
```

with:

```php
    /*
    | Laravel 13 option. JSON since Phase 2 (S2): no session value holds a PHP object. Switching from "php"
    | makes every earlier session file unreadable, so every signed-in user is logged out once.
    */
    'serialization' => 'json',
```

and replace:

```php
    'secure' => env('SESSION_SECURE_COOKIE', false),
```

with:

```php
    // Production sets SESSION_SECURE_COOKIE=true (Phase 3 checklist); the local http stack keeps false (S2).
    'secure' => env('SESSION_SECURE_COOKIE', false),
```

- [ ] **Step 6: Run both and confirm they pass**

```bash
docker compose exec -T app php artisan about --only=environment | grep -i 'debug'
docker compose exec -T app php vendor/bin/phpunit tests/Unit/ProductionConfigTest.php
(cd tests/e2e && npx playwright test specs/security/session-config.spec.ts --reporter=line | tail -5)
```

Expected: a `Debug Mode` line showing `ENABLED`; PHPUnit `OK (3 tests`; Playwright `1 passed`.

- [ ] **Step 7: Document the debug assumption**

In `tests/e2e/PARITY.md`, section "Environment the baselines assume", after the bullet "- `SESSION_DRIVER=file`.", add:

```markdown
- `APP_DEBUG=true`. Since Phase 2 (S2), `config/app.php` defaults debug to off when the key is missing, but the baselines were recorded with debug on: yajra answers a DataTables error as HTTP 200 JSON only in debug mode, for example. Check it without printing `.env`: `docker compose exec -T app php artisan about --only=environment | grep -i debug` must show `ENABLED`.
```

- [ ] **Step 8: Run the gate** (Global Constraints, "The gate")

Expected before the controller's acceptance:
- `npm test`: `392 passed` (321 + 71).
- PHPUnit: `OK (38 tests`.
- `check-structure.sh` exits 1 with one config diff: `"session.serialization": "php"` becomes `"json"`. Routes and glide match.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks print nothing.

- [ ] **Step 9: Commit**

```bash
git add config/app.php config/session.php tests/Unit/ProductionConfigTest.php tests/e2e/specs/security/session-config.spec.ts tests/e2e/PARITY.md
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Default debug off, store sessions as JSON, and guard against require-dev references (S2)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

In the report, ask for the `config.json` acceptance. Note for the PR: the JSON switch logs every signed-in user out once at deploy, and the production `.env` values belong to the Phase 3 checklist.

---

### Task 10: Dead contact scripts and silent failed writes (S16, S17)

- **S16 (ruling P2-R8).** Delete `Modules/Frontend/Resources/assets/form/{process,quote}-contact.php` and their `public/modules/frontend/form/` copies.
  - They are the H9 mail header-injection scripts. Nothing reaches them: `forms.js` is not loaded, and `forms-2.js` binds to a `#contactform` no view has.
  - Apache already denies PHP under `/modules`, but `module:publish` would copy the lint-fixed, parseable sources back.
  - `public/modules/frontend/landingpage/libs/{contact,quote}-form-process.php` stay: they are wired to soft-deleted landing-page forms (P2-R8), and `.htaccess` still refuses them.
  - Once the `form/` directory is gone, a request for those names falls through to Laravel's locale redirect and then answers 404, no longer 403. The existing `module-scripts.spec.ts` test that pinned 403 for them is rewritten.
- **S17.** Flysystem 3 returns `false` instead of throwing when a write fails, so the TinyMCE uploader reports success for a file that was never written. Two changes:
  - `'throw' => true` on the `public` disk (the default disk, used by every `->store()` call) and on the `graph` disk. The admin CRUD `store()` sites already sit inside `try { … } catch (\Exception $e)` blocks that answer an error.
  - A `try`/`catch` in `TinymceController::uploader()`, the one bare `put()`, answering with the same failure shape its validation branches use.
  - Only failed operations change behaviour. Deleting a missing file stays a silent no-op on the local adapter.

**Files:**
- Delete: `Modules/Frontend/Resources/assets/form/process-contact.php`, `Modules/Frontend/Resources/assets/form/quote-contact.php`, `public/modules/frontend/form/process-contact.php`, `public/modules/frontend/form/quote-contact.php`
- Modify: `config/filesystems.php` (`public` disk), `Modules/Cms/Providers/CmsServiceProvider.php` (`graph` disk), `Modules/Cms/Http/Controllers/Admin/TinymceController.php` (`uploader`)
- Modify: `tests/e2e/specs/security/module-scripts.spec.ts` (rewritten below)
- Create: `tests/Feature/TinymceWriteFailureTest.php`
- Existing tests that observe this change:
  - `module-scripts.spec.ts`: rewritten.
  - `tinymce.spec.ts` and `attachments.spec.ts`: the success path only, unchanged.
  - `content-lifecycle.spec.ts`: an image upload, success path, unchanged.
  - `scripts/upgrade/check-structure.sh`: records the disk roots, not `throw`, so no change.

**Interfaces:**
- Consumes: nothing.
- Produces:
  - `config('filesystems.disks.public.throw') === true` and `config('filesystems.disks.graph.throw') === true`.
  - The only `public/modules` change of the phase (see "Global Constraints").

- [ ] **Step 1: Rewrite the module-scripts spec (failing)**

Replace the whole content of `tests/e2e/specs/security/module-scripts.spec.ts` with:

```ts
import { existsSync } from 'node:fs';
import path from 'node:path';
import { randomBytes } from 'node:crypto';
import { test, expect } from '@playwright/test';

// Read-only: safe against any environment. Template mail scripts under public/modules put request input into
// mail() headers (H9). The dead contact-form copies are deleted (S16); the web server refuses every other PHP file there.
const REPO_ROOT = path.resolve(__dirname, '../../../..');

test('the deleted contact-form scripts are not served, and the landing-page mail scripts are refused', async ({ request }) => {
  for (const name of ['process-contact.php', 'quote-contact.php']) {
    // With form/ gone the request falls through to Laravel's locale redirect, which ends in a 404.
    expect((await request.get(`/modules/frontend/form/${name}`)).status(), name).toBe(404);
  }
  for (const name of ['contact-form-process.php', 'quote-form-process.php']) {
    expect((await request.get(`/modules/frontend/landingpage/libs/${name}`)).status(), name).toBe(403);
  }
});

test('the dead contact-form scripts are gone from the module sources and from public/modules (S16)', async () => {
  for (const relative of [
    'Modules/Frontend/Resources/assets/form/process-contact.php',
    'Modules/Frontend/Resources/assets/form/quote-contact.php',
    'public/modules/frontend/form/process-contact.php',
    'public/modules/frontend/form/quote-contact.php',
  ]) {
    expect(existsSync(path.join(REPO_ROOT, relative)), relative).toBe(false);
  }
});

test('any PHP file name under /modules is refused, even one that does not exist', async ({ request }) => {
  expect((await request.get(`/modules/verify-${randomBytes(8).toString('hex')}.php`)).status()).toBe(403);
});

test('static assets under /modules are still served', async ({ request }) => {
  const response = await request.get('/modules/frontend/css/font-awesome.min.css');
  expect(response.status()).toBe(200);
  expect(response.headers()['content-type']).toMatch(/^text\/css/);
});
```

- [ ] **Step 2: Write the failing PHPUnit test (S17)**

Create `tests/Feature/TinymceWriteFailureTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Modules\Cms\Http\Controllers\Admin\TinymceController;
use Tests\TestCase;

/** S17: a failed write is an error, never a success pointing at a file that does not exist. */
class TinymceWriteFailureTest extends TestCase
{
    public function test_a_failed_write_is_reported_as_a_failed_upload(): void
    {
        $disk = \Mockery::mock(Filesystem::class);
        $disk->shouldReceive('put')->once()->andThrow(UnableToWriteFile::atLocation('tinymce/probe.jpg', 'simulated full disk'));
        Storage::shouldReceive('disk')->with('graph')->andReturn($disk);

        $image = imagecreatetruecolor(8, 8);
        ob_start();
        imagejpeg($image);
        $jpeg = ob_get_clean();
        $request = Request::create('/en/admin/tinymce/uploader', 'POST', ['tinymce' => ['filename' => 'photo.jpg', 'base64' => base64_encode($jpeg)]]);

        $result = (new TinymceController)->uploader($request);

        $this->assertFalse($result['success']);
        $this->assertArrayNotHasKey('location', $result);
    }

    public function test_the_public_and_graph_disks_throw_on_failed_operations(): void
    {
        $this->assertTrue(config('filesystems.disks.public.throw'));
        $this->assertTrue(config('filesystems.disks.graph.throw'));
    }
}
```

- [ ] **Step 3: Run both and confirm they fail**

```bash
(cd tests/e2e && npx playwright test specs/security/module-scripts.spec.ts --reporter=line | tail -10)
docker compose exec -T app php vendor/bin/phpunit tests/Feature/TinymceWriteFailureTest.php
```

Expected:
- Playwright: `2 failed, 2 passed`. The first test gets `403`; the second finds the files.
- PHPUnit:
  - `test_a_failed_write_is_reported_as_a_failed_upload` errors with `League\Flysystem\UnableToWriteFile`.
  - `test_the_public_and_graph_disks_throw_on_failed_operations` fails (`null` is not true).

- [ ] **Step 4: Delete the dead scripts (S16)**

```bash
git rm Modules/Frontend/Resources/assets/form/process-contact.php Modules/Frontend/Resources/assets/form/quote-contact.php \
  public/modules/frontend/form/process-contact.php public/modules/frontend/form/quote-contact.php
ls Modules/Frontend/Resources/assets/form public/modules/frontend/form 2>&1 | grep -c 'No such file'
git diff --cached --name-status -- public/modules Modules/Frontend/Resources/assets
```

Expected: `2` (both directories are gone), then exactly four `D` lines, for the four paths above.

- [ ] **Step 5: Failed writes throw (S17)**

In `config/filesystems.php` (CRLF file), replace:

```php
            'url' => env('APP_URL').'/storage/uploads',
            'visibility' => 'public',
        ],
```

with:

```php
            'url' => env('APP_URL').'/storage/uploads',
            'visibility' => 'public',
            // Flysystem 3 returns false on a failed write unless the disk throws (S17).
            'throw' => true,
        ],
```

In `Modules/Cms/Providers/CmsServiceProvider.php` (CRLF file), replace:

```php
            'root' => public_path('graph/uploads/original'),
        ];
```

with:

```php
            'root' => public_path('graph/uploads/original'),
            // A failed write throws instead of returning false (S17).
            'throw' => true,
        ];
```

In `Modules/Cms/Http/Controllers/Admin/TinymceController.php` (LF file), replace:

```php
        \Storage::disk('graph')->put( 'tinymce/' . $filename, $binary );
```

with:

```php
        try {
            \Storage::disk('graph')->put( 'tinymce/' . $filename, $binary );
        } catch (\League\Flysystem\FilesystemException $e) {
            // The graph disk throws on a failed write (S17): never report success for a file that is not there.
            report($e);

            return [
                'success' => false,
                'type'    => 'danger',
                'strong'  => __('cms::app.crud_messages.upload_error.title'),
                'msg'     => __('cms::app.crud_messages.upload_error.description'),
            ];
        }
```

- [ ] **Step 6: Run both and confirm they pass**

```bash
(cd tests/e2e && npx playwright test specs/security/module-scripts.spec.ts specs/security/tinymce.spec.ts specs/security/attachments.spec.ts --reporter=line | tail -5)
docker compose exec -T app php vendor/bin/phpunit tests/Feature/TinymceWriteFailureTest.php
```

Expected: `10 passed` (4 module-scripts + 3 tinymce + 3 attachments); PHPUnit `OK (2 tests`.

- [ ] **Step 7: Run the gate** (Global Constraints, "The gate")

Expected:
- `npm test`: `393 passed` (321 + 72).
- PHPUnit: `OK (40 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `no deprecations`.
- `git diff --stat main...HEAD -- public/css public/js` prints nothing.
- `git diff --name-status main...HEAD -- public/modules` prints exactly the two `D` lines in Global Constraints. This check sees the change only after the commit, so run it again after Step 8.

- [ ] **Step 8: Commit**

```bash
git add config/filesystems.php Modules/Cms/Providers/CmsServiceProvider.php Modules/Cms/Http/Controllers/Admin/TinymceController.php \
  tests/e2e/specs/security/module-scripts.spec.ts tests/Feature/TinymceWriteFailureTest.php
git diff --cached --stat; git diff --cached --ignore-cr-at-eol --stat
git commit -m "Delete the dead contact-form mail scripts and make failed uploads fail loudly (S16, S17)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
git diff --name-status main...HEAD -- public/modules
```

Expected last output: exactly `D	public/modules/frontend/form/process-contact.php` and `D	public/modules/frontend/form/quote-contact.php`.

In the report, note for the PR that the Phase H expectation "`/modules/frontend/form/process-contact.php` answers 403" became "answers 404": the file is deleted.

---

### Task 11: Guards for the Laravel 7 restorations, and a deterministic raw-socket test (S18, S19)

- **S18.** Phase 1 restored Laravel 7 behaviour by overriding package methods:
  - the paginator URL window;
  - the DataTables escaping engine and processor;
  - the Glide encoder quality;
  - several laravel/ui auth trait methods.

  A package update inside the composer constraints could rename or remove an overridden method and silently drop a restoration.
  - The four class-method overrides get `#[\Override]`, so PHP refuses to load the class when the parent method disappears.
  - The trait-method overrides cannot use `#[\Override]`, because PHP fatals when the only same-named method comes from a trait. A PHPUnit reflection test checks instead that every trait method the six auth controllers override still exists with the same visibility, and that the list is complete.
- **S19.** `specs/security/images.spec.ts` "an empty segment returns 404 and caches nothing" once failed with a socket hang-up. `rawGet()` uses Node's shared keep-alive agent between slow `docker compose exec` calls, so a socket the server already closed can be reused. Each raw request gets its own non-pooling agent and `Connection: close`. The assertions do not change.

**Files:**
- Modify: `app/Pagination/LengthAwarePaginator.php`, `app/DataTables/EloquentDataTable.php`, `app/DataTables/DataProcessor.php`, `app/Glide/Encoder.php` (one attribute line each)
- Modify: `tests/e2e/specs/security/images.spec.ts` (`rawGet` only)
- Create: `tests/Unit/RestorationOverrideGuardTest.php`
- Existing tests that observe this change:
  - `admin-sections`, `admin-table-values`, `golden-master` pagination and `images.spec.ts`: behaviour unchanged.
  - `tests/upgrade/glide-cache-paths.json`: unchanged.

**Interfaces:**
- Consumes: nothing.
- Produces: `Tests\Unit\RestorationOverrideGuardTest::TRAIT_OVERRIDES`, the authoritative list of laravel/ui trait overrides.

- [ ] **Step 1: Write the guard test**

Create `tests/Unit/RestorationOverrideGuardTest.php`:

```php
<?php

namespace Tests\Unit;

use App\DataTables\DataProcessor;
use App\DataTables\EloquentDataTable;
use App\Glide\Encoder;
use App\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Modules\Cms\Http\Controllers\Auth\ConfirmPasswordController;
use Modules\Cms\Http\Controllers\Auth\ForgotPasswordController;
use Modules\Cms\Http\Controllers\Auth\LoginController;
use Modules\Cms\Http\Controllers\Auth\RegisterController;
use Modules\Cms\Http\Controllers\Auth\ResetPasswordController;
use Modules\Cms\Http\Controllers\Auth\VerificationController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * S18: the Laravel 7 behaviour restorations fail loudly when a package update inside the composer
 * constraints removes or renames what they override.
 */
class RestorationOverrideGuardTest extends TestCase
{
    /** Class-method overrides: #[\Override] makes PHP refuse to load the class if the parent method goes away. */
    private const CLASS_OVERRIDES = [
        [LengthAwarePaginator::class, 'elements'],
        [EloquentDataTable::class, 'processResults'],
        [DataProcessor::class, 'escapeRow'],
        [Encoder::class, 'getQuality'],
    ];

    /**
     * Trait-method overrides in the laravel/ui auth controllers: controller => [method => trait declaring it].
     * #[\Override] cannot guard these (PHP fatals when the only same-named method comes from a trait).
     */
    public const TRAIT_OVERRIDES = [
        LoginController::class => [
            'showLoginForm'           => AuthenticatesUsers::class,
            'login'                   => AuthenticatesUsers::class,
            'attemptLogin'            => AuthenticatesUsers::class,
            'sendLoginResponse'       => AuthenticatesUsers::class,
            'sendFailedLoginResponse' => AuthenticatesUsers::class,
            'username'                => AuthenticatesUsers::class,
            'sendLockoutResponse'     => ThrottlesLogins::class,
        ],
        RegisterController::class => [
            'showRegistrationForm' => RegistersUsers::class,
            'register'             => RegistersUsers::class,
        ],
        ResetPasswordController::class => [
            'showResetForm'           => ResetsPasswords::class,
            'reset'                   => ResetsPasswords::class,
            'rules'                   => ResetsPasswords::class,
            'sendResetResponse'       => ResetsPasswords::class,
            'sendResetFailedResponse' => ResetsPasswords::class,
        ],
        ForgotPasswordController::class => [
            'showLinkRequestForm'         => SendsPasswordResetEmails::class,
            'validateEmail'               => SendsPasswordResetEmails::class,
            'sendResetLinkResponse'       => SendsPasswordResetEmails::class,
            'sendResetLinkFailedResponse' => SendsPasswordResetEmails::class,
        ],
        VerificationController::class    => [],
        ConfirmPasswordController::class => [],
    ];

    public function test_class_method_overrides_carry_the_override_attribute(): void
    {
        foreach (self::CLASS_OVERRIDES as [$class, $method]) {
            $reflection = new ReflectionMethod($class, $method);
            $this->assertSame($class, $reflection->getDeclaringClass()->getName(), "{$class}::{$method} must be declared in the class");
            $this->assertCount(1, $reflection->getAttributes(\Override::class), "{$class}::{$method} needs #[\\Override]");
        }
    }

    public function test_the_auth_controllers_trait_overrides_are_complete_and_still_match_their_traits(): void
    {
        foreach (self::TRAIT_OVERRIDES as $controller => $expected) {
            $controllerFile = (new ReflectionClass($controller))->getFileName();
            $overridden = [];
            foreach (class_uses_recursive($controller) as $trait) {
                foreach ((new ReflectionClass($trait))->getMethods() as $traitMethod) {
                    if ((new ReflectionMethod($controller, $traitMethod->getName()))->getFileName() === $controllerFile) {
                        $overridden[$traitMethod->getName()] = true;
                    }
                }
            }
            $actualNames = array_keys($overridden);
            sort($actualNames);
            $expectedNames = array_keys($expected);
            sort($expectedNames);
            $this->assertSame($expectedNames, $actualNames, "{$controller}: its trait overrides changed; update TRAIT_OVERRIDES after checking each one");

            foreach ($expected as $method => $trait) {
                $inTrait = new ReflectionMethod($trait, $method);
                $inController = new ReflectionMethod($controller, $method);
                $this->assertSame($inTrait->isPublic(), $inController->isPublic(), "{$controller}::{$method} visibility differs from {$trait}");
                $this->assertSame($inTrait->isProtected(), $inController->isProtected(), "{$controller}::{$method} visibility differs from {$trait}");
                $this->assertCount(0, $inController->getAttributes(\Override::class), "{$controller}::{$method} overrides a trait method: #[\\Override] would be fatal");
            }
        }
    }

    public function test_redirect_path_still_reads_the_controllers_redirect_to_method(): void
    {
        $method = new ReflectionMethod(RedirectsUsers::class, 'redirectPath');
        $lines = array_slice(file($method->getFileName()), $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        $this->assertStringContainsString("method_exists(\$this, 'redirectTo')", implode('', $lines));
    }
}
```

- [ ] **Step 2: Run it and confirm the attribute test fails**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/RestorationOverrideGuardTest.php
```

Expected:
- `test_class_method_overrides_carry_the_override_attribute` fails with "App\Pagination\LengthAwarePaginator::elements needs #[\Override]".
- The other two tests pass. They guard the current trait overrides.
- If the trait test fails, the list above is wrong for the current code. Report the actual method names; do not edit the list silently.

- [ ] **Step 3: Add `#[\Override]` to the four class methods**

In `app/Pagination/LengthAwarePaginator.php`, replace `    protected function elements()` with:

```php
    #[\Override]
    protected function elements()
```

In `app/DataTables/EloquentDataTable.php`, replace `    protected function processResults($results, $object = false): array` with:

```php
    #[\Override]
    protected function processResults($results, $object = false): array
```

In `app/DataTables/DataProcessor.php`, replace `    protected function escapeRow(array $row): array` with:

```php
    #[\Override]
    protected function escapeRow(array $row): array
```

In `app/Glide/Encoder.php`, replace `    public function getQuality(): int` with:

```php
    #[\Override]
    public function getQuality(): int
```

- [ ] **Step 4: Make the raw socket requests deterministic (S19)**

In `tests/e2e/specs/security/images.spec.ts`, replace the function:

```ts
function rawGet(path: string): Promise<number> {
  const base = new URL(BASE_URL);
  const client = base.protocol === 'https:' ? https : http;
  return new Promise((resolve, reject) => {
    const req = client.request({ host: base.hostname, port: base.port || undefined, path, method: 'GET' }, (res) => {
      res.resume();
      res.on('end', () => resolve(res.statusCode ?? 0));
    });
    req.on('error', reject);
    req.end();
  });
}
```

with:

```ts
function rawGet(path: string): Promise<number> {
  const base = new URL(BASE_URL);
  const isHttps = base.protocol === 'https:';
  const client = isHttps ? https : http;
  // A fresh agent without keep-alive and `Connection: close` for every call: no socket is ever reused,
  // so the server can never have half-closed one between two calls (S19).
  const agent = isHttps ? new https.Agent({ keepAlive: false }) : new http.Agent({ keepAlive: false });
  return new Promise((resolve, reject) => {
    const req = client.request(
      { host: base.hostname, port: base.port || undefined, path, method: 'GET', agent, headers: { Connection: 'close' } },
      (res) => {
        res.resume();
        res.on('end', () => resolve(res.statusCode ?? 0));
      },
    );
    req.on('error', reject);
    req.end();
  });
}
```

Leave the comment above the function and every test in the file unchanged.

- [ ] **Step 5: Run the guard and the raw-socket tests repeatedly**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/RestorationOverrideGuardTest.php
(cd tests/e2e && npx playwright test specs/security/images.spec.ts --repeat-each=10 --reporter=line | tail -5)
```

Expected: PHPUnit `OK (3 tests`; Playwright `120 passed` (12 tests × 10).

- [ ] **Step 6: Run the gate** (Global Constraints, "The gate")

Expected:
- `npm test`: `393 passed` (321 + 72).
- PHPUnit: `OK (43 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `no deprecations`.
- `git diff --stat main...HEAD -- public/css public/js` prints nothing.
- `public/modules` shows only the two Task 10 `D` lines.

- [ ] **Step 7: Commit**

```bash
git add app/Pagination/LengthAwarePaginator.php app/DataTables/EloquentDataTable.php app/DataTables/DataProcessor.php app/Glide/Encoder.php \
  tests/Unit/RestorationOverrideGuardTest.php tests/e2e/specs/security/images.spec.ts
git commit -m "Guard the Laravel 7 restorations against silent package changes and stop reusing raw test sockets (S18, S19)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

---

### Task 12: Write-permission probes (S13)

The Phase 0 matrix sends GETs only. This spec sends admin POST probes as anonymous, ADMIN and SUPERADMIN, and asserts both each principal's outcome and that nothing changed. Tasks 5 and 6 settled the matrix, so this task runs after both.

How the probes change nothing:
- **An ability ADMIN lacks, with an empty body.** ADMIN gets "302 back" and SUPERADMIN gets 422, because validation fails.
- **A no-op write.** `projects/special/139` is already special and `projects/not_special/133` is already not special. SUPERADMIN gets 200 and no UPDATE runs.
- **Read-only POSTs.** The notification DataTables list.
- **Controls.** A destroy on a missing id answers 404 for both roles, and `tags/save` with an empty body answers 422 for both.

A `CHECKSUM TABLE` over every table a probe could touch must be identical before and after.

Never probed:
- `users/{model}/update` and `update_profile`: an empty body overwrites the user;
- `copy`, and `destroy`, `mass_*` or `restore` with real ids;
- `attachments/*` and `tinymce/uploader`;
- `update-currency`: it calls an external API;
- `postWebToken`.

The expected outcomes follow from the app's exception rendering and are **not yet observed**. If a cell differs, report the full actual table and do not edit the expectations: the controller rules.

The spec also records the nine GET rows without a role signal in `PARITY.md`, per ruling P2-R7. S21 (Task 5) gave six of them a signal, and three remain.

**Files:**
- Create: `tests/e2e/specs/security/write-permissions.spec.ts`
- Modify: `tests/e2e/PARITY.md` (a new section, and "When the dump changes")
- Existing tests that observe this change: none. No baseline change.

**Interfaces:**
- Consumes:
  - Task 5: `notification` POST `/` authorizes `notifications.view`; `tags/save` authorizes `tags.create` before validation.
  - Task 6: none of the probed routes.
- Produces: nothing used later.

- [ ] **Step 1: Write the spec**

Create `tests/e2e/specs/security/write-permissions.spec.ts`:

```ts
import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { sql } from '../../support/docker';
import { BASE_URL, E2E_PASSWORD, requireLocal } from '../../support/env';

// S13: admin POST routes give each role the expected outcome and change nothing.
// Ids name rows in the committed dump (PARITY.md, "When the dump changes").
const REFERER = `${BASE_URL}/en/parity-referer`;
const PRINCIPALS = ['anonymous', 'parity-admin', 'parity-superadmin'] as const;
type Principal = (typeof PRINCIPALS)[number];
type Outcomes = Record<Principal, string>;

const adminDenied = (superadmin: string): Outcomes => ({ anonymous: '302 login', 'parity-admin': '302 back', 'parity-superadmin': superadmin });
const bothRoles = (both: string): Outcomes => ({ anonymous: '302 login', 'parity-admin': both, 'parity-superadmin': both });

const PROBES: Array<{ url: string; form?: Record<string, string>; expected: Outcomes }> = [
  // Abilities ADMIN lacks; empty bodies fail validation for SUPERADMIN.
  { url: '/en/admin/categories/agents/store', expected: adminDenied('422') },
  { url: '/en/admin/categories/filters/store', expected: adminDenied('422') },
  { url: '/en/admin/categories/opportunity_classifications/store', expected: adminDenied('422') },
  { url: '/en/admin/categories/agents/608/update', expected: adminDenied('422') },
  { url: '/en/admin/categories/filters/598/update', expected: adminDenied('422') },
  { url: '/en/admin/contents/achievements/store', expected: adminDenied('422') },
  { url: '/en/admin/landing_pages/store', expected: adminDenied('422') },
  { url: '/en/admin/notification/postCreate', expected: adminDenied('422') },
  { url: '/en/admin/roles/update', form: { model: '3' }, expected: adminDenied('422') },
  // No-op writes: 139 is already special, 133 is already not special.
  { url: '/en/admin/projects/special/139', expected: adminDenied('200') },
  { url: '/en/admin/projects/not_special/133', expected: adminDenied('200') },
  // Read-only POST: the notification DataTables list (notifications.view since S21).
  { url: '/en/admin/notification', expected: adminDenied('200') },
  // Controls: reachable and never 500.
  { url: '/en/admin/tags/destroy/999999999', expected: bothRoles('404') },
  { url: '/en/admin/tags/save', expected: bothRoles('422') },
];

// Every table the probes above could write to.
const CHECKSUM = 'CHECKSUM TABLE cms_categories, cms_category_translations, cms_categorizables, cms_contents, cms_content_translations, '
  + 'landing_pages, landing_page_translations, perms_roles, perms_role_translations, be_projects, be_projects_translations, '
  + 'notif_notifications, notif_notification_translations, notif_notification_receivers, cms_tags, cms_tag_translations';

async function signIn(request: APIRequestContext, username: string): Promise<void> {
  const html = await (await request.get('/en/authenticate/login')).text();
  const token = html.match(/name="_token" value="([^"]+)"/)![1];
  const response = await request.post('/en/authenticate/login', {
    headers: AJAX_HEADERS,
    form: { _token: token, identity: username, password: E2E_PASSWORD },
  });
  expect(response.status(), `login as ${username}`).toBe(200);
}

/** Same mapping as permissions-matrix.spec.ts: "200", "404", "422" ... or "302 login" / "302 back" / "302 <path>". */
async function postOutcome(request: APIRequestContext, url: string, form: Record<string, string> = {}): Promise<string> {
  const response = await request.post(url, {
    form: { _token: await anonymousCsrfToken(request), ...form },
    headers: { ...AJAX_HEADERS, Referer: REFERER },
    maxRedirects: 0,
  });
  const status = response.status();
  if (status < 300 || status >= 400) return String(status);
  const location = response.headers()['location'] ?? '';
  if (/\/authenticate\/login$/.test(location)) return `${status} login`;
  if (location === REFERER) return `${status} back`;
  return `${status} ${location.replace(BASE_URL, '')}`;
}

test('admin POST routes give each role its expected outcome and change nothing', async ({ playwright }) => {
  requireLocal('uses the local parity accounts');
  test.setTimeout(5 * 60_000);

  const contexts = {} as Record<Principal, APIRequestContext>;
  for (const principal of PRINCIPALS) {
    contexts[principal] = await playwright.request.newContext({ baseURL: BASE_URL });
    if (principal !== 'anonymous') await signIn(contexts[principal], principal);
  }

  const before = sql(CHECKSUM);
  const actual: Record<string, Outcomes> = {};
  const expected: Record<string, Outcomes> = {};
  for (const probe of PROBES) {
    expected[probe.url] = probe.expected;
    actual[probe.url] = {} as Outcomes;
    for (const principal of PRINCIPALS) {
      actual[probe.url][principal] = await postOutcome(contexts[principal], probe.url, probe.form);
    }
  }
  const after = sql(CHECKSUM);
  for (const context of Object.values(contexts)) await context.dispose();

  expect(actual).toEqual(expected);
  expect(after).toBe(before);
});
```

- [ ] **Step 2: Run it**

```bash
(cd tests/e2e && npx playwright test specs/security/write-permissions.spec.ts --reporter=line | tail -40)
```

Expected: `1 passed`. This spec records behaviour that Tasks 5 and 6 settled; it has no failing phase of its own.
- If `toEqual` fails, copy the whole printed diff into the report as the actual table, and do not change `PROBES`.
- If the checksum differs, a probe wrote something. Report which, by running the probes one at a time, and remove nothing.

- [ ] **Step 3: Record the rows without a role signal and the new dump ids**

In `tests/e2e/PARITY.md`:

1. In "When the dump changes", after the bullet for `specs/security/admin-authorization.spec.ts`, add:

```markdown
- `specs/security/write-permissions.spec.ts`: category ids `608` (an `agents` category) and `598` (a `filters` category), project ids `139` (`is_special = 1`) and `133` (`is_special = 0`), and role id `3` (ADMIN).
```

2. Before the section "## Visual baselines", add:

```markdown
## Rows without a role signal (S13, P2-R7)

The permissions matrix sends GETs only. Nine of its rows gave ADMIN and SUPERADMIN the same outcome on Laravel 7, so they carried no role signal:

| Row | parity-admin / parity-superadmin | Why |
|---|---|---|
| `/en/admin/users/summary?model=31` | 302 back / 302 back | A non-AJAX `ResponseHandler` answer redirects back, and `UserController::summary` authorizes nothing (both roles hold `users.view`). |
| `/en/admin/users/identity/validate?name=username&keyword=parity-probe` | 302 back / 302 back | The same: a `ResponseHandler` answer and no authorization. |
| `/en/admin/configs/49/edit` | 302 back / 302 back | `ConfigController::edit` returns `back()` before any other code. The real page is `/en/admin/configs/49/edit-config` (200 / 200). |
| `/en/admin/{projects,opportunity}/request_summary` (2 rows) | 302 back / 404 since S21 (404 / 404 before) | S21 authorizes `tags.requests` before the lookup. |
| `/en/admin/{projects,opportunity}/properties_summary` (2 rows) | 302 back / 404 since S21 (404 / 404 before) | Same. |
| `/en/admin/{projects,opportunity}/show_details/0` (2 rows) | 302 back / 404 since S21 (404 / 404 before) | Same. |

Per ruling P2-R7, the first three stay without a role signal: giving them one would need an application change (an authorization call, or a real edit page), which Phase 2 does not make. S21 gave the other six a signal. Write permissions are covered by `specs/security/write-permissions.spec.ts` (S13): ADMIN POST probes that change nothing, with ids that give SUPERADMIN a real answer, checked against a table checksum.
```

- [ ] **Step 4: Run the gate** (Global Constraints, "The gate")

Expected:
- `npm test`: `394 passed` (321 + 73).
- PHPUnit: `OK (43 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `no deprecations`.
- The asset checks as in Task 11.

- [ ] **Step 5: Commit**

```bash
git add tests/e2e/specs/security/write-permissions.spec.ts tests/e2e/PARITY.md
git commit -m "Probe admin write permissions per role without changing data, and document the rows without a role signal (S13)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

---

### Task 13: Dependency audit gate, front-end report, runbook counts, exit evidence and PR material (S4)

- **S4.**
  - `composer audit` must report zero known vulnerabilities before any deploy. It reports zero today (research D, `composer audit --locked`). `scripts/check-composer-audit.sh` turns that into a gate that exits non-zero on any advisory; Phase 3's deploy wires it in.
  - Front-end libraries are reported only, in `docs/security/front-end-libraries.md`.
  - `npm audit` is not a gate: `tests/e2e/package.json` is test tooling, and the root `package.json` is Mix build tooling whose output in `public/` never changes.
- **This task runs last.** It updates the runbook counts, produces the exit evidence, and writes the PR material, including every accepted difference and the owner actions.

**Files:**
- Create: `scripts/check-composer-audit.sh`
- Create: `tests/Unit/ComposerAuditGateTest.php`
- Create: `docs/security/front-end-libraries.md`
- Modify: `tests/e2e/PARITY.md` (test counts, a "Dependency audit" section)
- Existing tests that observe this change: none.

**Interfaces:**
- Consumes: every earlier task and every controller acceptance commit.
- Produces: `scripts/check-composer-audit.sh [report.json]`, which exits 0 with no advisories, 1 when it finds advisories (listed on stderr), and 2 when the report cannot be read. Phase 3 calls it.

- [ ] **Step 1: Write the failing gate test**

Create `tests/Unit/ComposerAuditGateTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/** S4: the composer audit gate exits non-zero on any advisory and on an unreadable report. */
class ComposerAuditGateTest extends TestCase
{
    /** @return array{0: int, 1: string} the exit code and the combined output */
    private function runGate(string $report): array
    {
        $file = tempnam(sys_get_temp_dir(), 'composer-audit-');
        file_put_contents($file, $report);
        exec('bash ' . escapeshellarg(dirname(__DIR__, 2) . '/scripts/check-composer-audit.sh') . ' ' . escapeshellarg($file) . ' 2>&1', $output, $code);
        unlink($file);

        return [$code, implode("\n", $output)];
    }

    public function test_a_report_without_advisories_passes(): void
    {
        [$code, $output] = $this->runGate(json_encode(['advisories' => [], 'abandoned' => []]));

        $this->assertSame(0, $code, $output);
        $this->assertStringContainsString('composer audit: no known advisories', $output);
    }

    public function test_one_advisory_fails_the_gate_and_names_the_package(): void
    {
        [$code, $output] = $this->runGate(json_encode(['advisories' => ['vendor/package' => [[
            'advisoryId' => 'PKSA-test-0001', 'packageName' => 'vendor/package', 'title' => 'Test advisory', 'cve' => 'CVE-0000-0000',
        ]]]]));

        $this->assertSame(1, $code, $output);
        $this->assertStringContainsString('vendor/package', $output);
    }

    public function test_an_unreadable_report_fails_closed(): void
    {
        [$code] = $this->runGate('not json');

        $this->assertSame(2, $code);
    }
}
```

- [ ] **Step 2: Run it and confirm it fails**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/ComposerAuditGateTest.php
```

Expected: 3 failures. The script does not exist, so `bash` exits 127.

- [ ] **Step 3: The gate script**

Create `scripts/check-composer-audit.sh`:

```bash
#!/usr/bin/env bash
# S4 gate: exits 0 only when composer.lock has no known security advisory.
#
#   scripts/check-composer-audit.sh                audits composer.lock (needs composer and access to packagist.org)
#   scripts/check-composer-audit.sh <report.json>  checks a saved `composer audit --format=json` report instead
#
# Exit codes: 0 no advisories, 1 advisories found (listed on stderr), 2 the report could not be read.
# Run it where composer lives, for example `docker compose exec -T app bash scripts/check-composer-audit.sh`.
# Phase 3's deploy runs it right after `composer install --no-dev`, before any migration.
set -euo pipefail
cd "$(dirname "$0")/.."

report="$(mktemp)"
trap 'rm -f "$report"' EXIT

if [ "$#" -ge 1 ]; then
  cp "$1" "$report"
else
  # composer audit exits non-zero when it finds advisories or abandoned packages; the JSON decides.
  composer audit --locked --no-interaction --format=json > "$report" || true
fi

status=0
php -r '
  $data = json_decode((string) file_get_contents($argv[1]), true);
  if (! is_array($data) || ! array_key_exists("advisories", $data)) {
      fwrite(STDERR, "composer audit: the report could not be read\n");
      exit(2);
  }
  $count = 0;
  foreach ((array) $data["advisories"] as $package => $advisories) {
      foreach ((array) $advisories as $advisory) {
          $count++;
          fwrite(STDERR, sprintf("%s: %s %s %s\n", $package, $advisory["advisoryId"] ?? "", $advisory["cve"] ?? "", $advisory["title"] ?? ""));
      }
  }
  if ($count > 0) {
      fwrite(STDERR, "composer audit: $count known advisories\n");
      exit(1);
  }
  echo "composer audit: no known advisories\n";
' "$report" || status=$?

exit "$status"
```

```bash
chmod +x scripts/check-composer-audit.sh
```

- [ ] **Step 4: Run the test and the real audit**

```bash
docker compose exec -T app php vendor/bin/phpunit tests/Unit/ComposerAuditGateTest.php
docker compose exec -T app bash scripts/check-composer-audit.sh; echo "exit $?"
```

Expected:
- PHPUnit `OK (3 tests`.
- `composer audit: no known advisories`, then `exit 0`.
- If advisories are listed instead, stop and report them with their package and advisory ids. Do not update dependencies in this task.

- [ ] **Step 5: The front-end library report**

Create `docs/security/front-end-libraries.md`:

```markdown
# Front-end libraries served by aldar-emlak.com

Spec item S4: "Front-end libraries are listed in a report only." Per the spec's non-goals they are **not upgraded** in this project. Versions come from each served file's own banner (Phase 2 research, 2026-09-15), not from `bower.json` or `package.json`.

## Public pages

Loaded on every public page by `Modules/Frontend/Resources/views/partials/scripts.blade.php` and `partials/css.blade.php`.

| Library | Version served | Served from |
|---|---|---|
| jQuery | 3.2.1 | `public/modules/frontend/js/jquery.min.js` |
| Bootstrap (JS) | 4.1.0 | `https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js` |
| Bootstrap (CSS) | 4.0.0-alpha.6 | `public/modules/frontend/css/bootstrap.css` |
| bootstrap-select (JS) | 1.13.14 | `public/modules/frontend/bootstrapselect/bootstrap-select.js` |
| bootstrap-select (CSS) | 1.13.18 | `https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css` |
| Swiper | 4.5.0 | `public/modules/frontend/js/swiper.min.js`, `public/modules/frontend/css/swiper.min.css` |
| Owl Carousel | 2.2.1 | `public/modules/frontend/js/owl.carousel.js` |
| Slick | no version banner (source directory `libs/slick-1.8.1`) | `public/modules/frontend/js/slick.min.js` |
| mmenu | 6.1.8 | `public/modules/frontend/js/mmenu.min.js` |
| vanilla-lazyload | 17.5.0 | `https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.5.0/dist/lazyload.min.js` |

## Staff-only admin pages

| Library | Version served | Served from |
|---|---|---|
| jQuery (Metronic plugin bundle) | 3.4.1 | `public/modules/cms/metronic/plugins/global/plugins.bundle.js` |
| DataTables | 1.10.21 | `public/modules/cms/metronic/plugins/custom/datatables/datatables.bundle.js` |
| TinyMCE | 4.7.9 | `public/modules/cms/js/tinymce/tinymce.min.js` |

## Notes

- Most of these are several major versions behind current releases. Releases of that age have had published XSS advisories, for example jQuery before 3.5.0. The advisories for each pinned version were not cross-checked against an advisory database: that belongs to a front-end upgrade project, which this project's scope excludes.
- Visitor text reaches these libraries escaped: see spec S5 and `tests/e2e/specs/security/lead-lists.spec.ts`.
- The Report-Only Content-Security-Policy (S3, `app/Http/Middleware/SecurityHeaders.php`) lists the CDN origins above.
- `npm audit` is not a gate. `tests/e2e/package.json` holds test tooling only, and the root `package.json` is Laravel Mix build tooling whose output in `public/` stays byte-identical.
- PHP dependencies are gated by `scripts/check-composer-audit.sh`: zero known advisories before any deploy.
```

- [ ] **Step 6: Document the audit in the runbook**

In `tests/e2e/PARITY.md`, before the section "## When a parity test fails after a change", add:

```markdown
## Dependency audit (S4)

`scripts/check-composer-audit.sh` exits 0 only when `composer.lock` has no known security advisory. It exits 1 and lists the advisories otherwise, and exits 2 if the audit report cannot be read. Run it from the repository root with `docker compose exec -T app bash scripts/check-composer-audit.sh`; it needs access to packagist.org. It is not part of `npm test`: Phase 3's deploy runs it after `composer install --no-dev`. Front-end libraries are reported in `docs/security/front-end-libraries.md` and are not upgraded in this project.
```

- [ ] **Step 7: Commit the gate, the report and the runbook section**

```bash
git add scripts/check-composer-audit.sh tests/Unit/ComposerAuditGateTest.php docs/security/front-end-libraries.md tests/e2e/PARITY.md
git commit -m "Add the composer audit gate and the front-end library report (S4)" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

- [ ] **Step 8: Exit evidence, run 1**

The controller must have committed every acceptance first. Check that the tree is clean and list the acceptance commits:

```bash
git status --short
git log --oneline 9ec5ee4..HEAD -- tests/e2e/snapshots tests/upgrade
```

Expected:
- `git status` prints nothing.
- The log lists only controller acceptance commits, each naming its S id:
  - the S9 matrix cell;
  - the S9 routes cell;
  - the S7/S10 routes;
  - the S21 matrix;
  - the S11 matrix;
  - the S11 notifications snapshot;
  - the S2 config cell.

  The controller may have combined some of these per file. Anything else in the list is a finding to report.

Then run the gate (Global Constraints, "The gate") with `docker compose exec -T app bash scripts/check-composer-audit.sh` added before the last two lines. Record the duration Playwright prints at the end of `npm test`.

Expected:
- `npm test`: `394 passed` (321 parity + 73 chromium).
- PHPUnit: `OK (46 tests`.
- `structure matches tests/upgrade/`.
- phpstan `[OK] No errors`.
- `composer audit: no known advisories`.
- `no deprecations`.
- `git diff --stat main...HEAD -- public/css public/js` prints nothing.
- `git diff --name-status main...HEAD -- public/modules` prints exactly the two Task 10 `D` lines.

- [ ] **Step 9: Update the runbook counts**

In `tests/e2e/PARITY.md`, replace `368 tests in all (321 parity + 47 chromium, about 14 minutes)` with `394 tests in all (321 parity + 73 chromium, about N minutes)`. N is the Step 8 duration rounded up to a whole minute. Then:

```bash
git add tests/e2e/PARITY.md
git commit -m "Update the parity runbook test counts for the Phase 2 security specs" -m "Co-Authored-By: Claude <model> <noreply@anthropic.com>"
```

This commit changes documentation only, so runs 1 and 2 test the same code.

- [ ] **Step 10: Exit evidence, run 2**

Run the gate again, exactly as in Step 8, including the audit line.

Expected: the same results as Step 8. Together with Step 8, this is `394 passed` twice in a row with no code change between.

- [ ] **Step 11: PR material (the controller opens the PR)**

Write the PR body in the report. It covers:

1. **Summary.** S1–S21 closed on `security/hardening`, each with its new test. The branch was cut from `upgrade/laravel-13` at 9ec5ee4 and rebased onto `main` once PR #4 merged (P2-R1).
2. **Accepted baseline differences**, each with its S id:
   - S9, `matrix.json`: `/en/admin/notification/config` anonymous `200 → 302 login`. S9, `routes.json`: `staff` added to `admin/notification/config`.
   - S7/S10, `routes.json`: `admin/clear-cache` and `admin/users/login_as/{model}` GET → POST; `admin/projects/update_prices` and `admin/categories/asdwadwadwdaw` removed.
   - S21, `matrix.json`: `parity-admin` gets `302 back` on `{projects,opportunity}/{data_requests,data_properties,request_summary,properties_summary,show_details/0}` (was 200 or 404).
   - S11, `matrix.json`: `notification`, `users/show`, `roles/show`, `projects/show`, `opportunity/show` `500 → 404`, and `users/identity/validate_` and `tags/list` `500 → 302 back`, for both staff roles. S11, `admin-sections` `notifications.json`: status `500 → 404`.
   - S2, `config.json`: `session.serialization` `php → json`.
3. **Changed security-spec expectations:**
   - `maintenance-routes.spec.ts`: clear-cache is a CSRF POST that needs `tags.requests`, and a GET answers 404.
   - `module-scripts.spec.ts`: the deleted `form/{process,quote}-contact.php` answer 404 (previously 403), and the landing-page mail scripts still answer 403.
4. **Visible changes, all admin-only:**
   - The SUPERADMIN aside "clear cache" item: `href="javascript:;"`, an `onclick`, and a hidden form.
   - The dashboard currency button gains `onclick` (only if Task 4 Step 7 was needed; state whether it was).
   - The header "login back as root" is a hidden form.
   - Lead list cells show visitor markup as text (20 dump leads contain tag-like text), and non-http(s) lead links show as text.
5. **Behaviour changes users could notice:**
   - Every signed-in user is logged out once at deploy (JSON sessions).
   - ADMIN loses direct URLs it could not reach from its menu: lead data, clear-cache, landing-page timeline deletes, and the notification list.
   - Select2 endpoints return at most 20 items.
   - Admin tables return at most 500 rows per page, except the lead and property-form "All".
   - `postWebToken` allows 10 attempts per IP per minute.
6. **Items needing a ruling, if not already ruled:**
   - The ROOT-only users-table "login as" item now links to a POST-only route (Task 4 Step 8).
   - Any S13 probe cell that differed from the plan's expectation.
7. **Owner actions:**
   - Rotate the currconv API key and set the new one as `CURRCONV_API_KEY` in the production `.env`. The old key joins the N8 history-rewrite scope (S14).
   - Check the production `storage/logs` for logged user request bodies (lines written by `UserController` `\Log::debug`), and delete those lines or files (S20). Nobody should read the values.
8. **Phase 3 checklist items this phase prepared:**
   - Production `.env`: `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `CURRCONV_API_KEY`.
   - `composer install --no-dev`, then `scripts/check-composer-audit.sh` before migrations.
   - Verify the security headers on staging, and decide whether static files served by LiteSpeed need them.
   - Decide on a CSP `report-uri` and on HSTS `includeSubDomains`.
   - Expect one logout at cutover.

---

## Self-review

### 1. Spec coverage (Phase 2, S1–S21)

| Spec item | Task | New test(s) |
|---|---|---|
| S1 page-size caps | 7 | `tests/Unit/PageSizeCapTest.php`, `specs/security/page-size-caps.spec.ts` |
| S2 production config (debug default, JSON sessions, secure cookie from env, no require-dev references) | 9 (production `.env` values: Phase 3, listed in Task 13) | `tests/Unit/ProductionConfigTest.php`, `specs/security/session-config.spec.ts` |
| S3 security headers, Report-Only CSP | 8 | `tests/Unit/SecurityHeadersTest.php`, `specs/security/headers.spec.ts` |
| S4 composer audit gate, front-end report | 13 | `tests/Unit/ComposerAuditGateTest.php` |
| S5 unescaped visitor output (C1, C2, C3) | 1 | `specs/security/lead-lists.spec.ts` (tests 1–2) |
| S6 postWebToken validation, rate limit, generic error | 3 | `tests/Feature/WebTokenErrorResponseTest.php`, `specs/security/notification-endpoints.spec.ts` |
| S7 clear-cache POST + CSRF, `tags.requests`, currency button check | 4 (P2-R6) | `specs/security/maintenance-routes.spec.ts` (rewritten, +3 tests) |
| S8 EnsureStaff JSON 403 | 3 | `tests/Unit/EnsureStaffJsonTest.php` |
| S9 notification config behind staff | 3 | `specs/security/notification-endpoints.spec.ts` (test 1) |
| S10 update_prices, asdwadwadwdaw, login_as | 4 | `specs/security/state-changing-gets.spec.ts` |
| S11 seven admin GET 500s | 6 | `specs/security/admin-error-routes.spec.ts` |
| S12 invalid UTF-8 in lead lists and summary | 1 | `specs/security/lead-lists.spec.ts` (test 3), `tests/Unit/InvalidUtf8JsonTest.php` |
| S13 write-permission probes; the 9 no-signal rows (P2-R7) | 12 | `specs/security/write-permissions.spec.ts`; `PARITY.md` section |
| S14 currconv key from config, empty-key behaviour | 2 | `tests/Unit/SecretsAndLoggingGuardTest.php`, `tests/Feature/CurrencyApiKeyTest.php` |
| S15 debug-IP block removed | 2 | `tests/Unit/SecretsAndLoggingGuardTest.php` |
| S16 dead contact scripts deleted (P2-R8 scope) | 10 | `specs/security/module-scripts.spec.ts` (rewritten, +1 test) |
| S17 failed writes throw; TinyMCE reports them | 10 | `tests/Feature/TinymceWriteFailureTest.php` |
| S18 `#[\Override]` and the trait-override reflection guard | 11 | `tests/Unit/RestorationOverrideGuardTest.php` |
| S19 deterministic raw-socket image test | 11 | `specs/security/images.spec.ts` (`rawGet`), verified with `--repeat-each=10` |
| S20 no request bodies (passwords) in logs | 2 | `specs/security/secrets-and-logs.spec.ts`, `tests/Unit/SecretsAndLoggingGuardTest.php` |
| S21 lead endpoints and nine POST routes enforce page abilities | 5 | `specs/security/admin-authorization.spec.ts` |
| P1-R39 opportunity `data_requests` link column | 1 | `specs/security/lead-lists.spec.ts` (test 3, opportunity loop) |
| "Each item gets a test added to the suite" | all | the column above |
| No visual/UX change; compiled assets byte-identical | Global Constraints; every gate | `git diff --stat main...HEAD -- public/css public/js`; `public/modules` allowed deletions |
| Parity 1:1, accepted differences listed in the PR | Global Constraints, "Baseline acceptance", Task 13 Step 11 | cell-diff script |

**Decomposition.** The plan keeps the controller's 13 tasks in the proposed order.
- Tasks 5 and 6 come before Task 12, which records the settled matrix. Task 2 comes before Task 4, whose currency button test relies on the empty-key behaviour as a second safety net, and before Task 9, whose require-dev guard needs Debugbar gone. Task 13 is last.
- No task was merged or split. Tasks 1, 5 and 7 all edit the lead endpoints, but each is separately rejectable: escaping and UTF-8, authorization, page-size cap.
- Two implementation choices differ from the research proposals, with reasons given in the tasks:
  - S1 uses a static `PageSize` helper instead of a `Request` macro, so Larastan resolves it without macro reflection.
  - S6 uses a controller-level `RateLimiter` instead of `throttle:10,1`: this app's Handler renders every `HttpException` as a login redirect, so the middleware could never answer 429.

### 2. Placeholder scan

- No "TBD", "TODO", "implement later", "add validation" or "handle edge cases" text remains. Every code step gives the full new file or an exact old → new replacement.
- `<model>` in commit trailers is defined once in Global Constraints: the committing model's name. It is the spec's own trailer format.
- `N` in Task 13 Step 9 is defined as a measured value: the Step 8 duration, rounded up.
- `/tmp/expected-cells.txt` in "Baseline acceptance" is a named scratch file with a stated source.
- Conditional steps give both branches, for example Task 4 Step 7 (only if the button is inert). Expectations that research could not verify say what to do when they do not hold: Task 5 Step 2 (SUPERADMIN reads) and Task 12 Step 2 (probe outcomes).

### 3. Name and type consistency

- **Specs and test counts.**
  - Spec files: `lead-lists`, `secrets-and-logs`, `notification-endpoints`, `state-changing-gets`, `admin-authorization`, `admin-error-routes`, `page-size-caps`, `headers`, `session-config` and `write-permissions`, plus the rewritten `maintenance-routes`, `module-scripts` and `images`.
  - Chromium test counts: 3, 1, 3, 5, 3, 4, 2, 2, 1, 1, 0, 1, for 47 + 26 = 73. `npm test`: 321 + 73 = 394.
  - PHPUnit test counts: 19 + 3 + 5 + 3 + 3 + 2 + 3 + 2 + 3 + 3 = 46.
  - These match the "Running totals" table and every task's gate.
- **Code names.**
  - `PageSize::fromRequest(Request, int = PageSize::SELECT2_MAX): ?int` with `SELECT2_MAX = 20` (Task 7), matching its test.
  - `SecurityHeaders::HEADERS` and `SecurityHeaders::CONTENT_SECURITY_POLICY_REPORT_ONLY` (Task 8), matching its test.
  - `NotificationController::WEB_TOKEN_MAX_ATTEMPTS = 10` (Task 3); the e2e test expects attempts 1–10 to answer 422 and attempt 11 to answer 429.
  - `FrontendController::currconvKey(): ?string` (Task 2), with the warning text `Currency rates not refreshed: CURRCONV_API_KEY is not set.` identical in code and test.
- **Blade and markup names.**
  - Form ids `clearCacheForm` (menu item `form_id`, `aside.blade.php`, spec selector `#kt_aside_menu a[onclick*="clearCacheForm"]`) and `loginBackForm` (header).
  - Cache sentinel `e2e-s7-sentinel`.
- **Fixtures.**
  - `s5-probe@aldar.test`, `s5-link@aldar.test` and `s12-probe@aldar.test` (Task 1).
  - `parity-client-s21`, `s21-lead@aldar.test`, `s21-form@aldar.test` and marker `s21-probe` (Task 5).
  - `e2e-s6-` token prefix (Task 3).
  - Every spec that creates fixtures deletes them by the same marker.
- **Config values.**
  - `datatables.json.options = JSON_INVALID_UTF8_SUBSTITUTE` (Task 1) and `datatables.max_length = 500` (Task 7): same file, different keys.
  - `filesystems.disks.{public,graph}.throw = true` (Task 10).
  - `session.serialization = 'json'` (Task 9) and its `config.json` cell.
- **Expected cell diffs.** They use the script's exact output format, `path | …: before -> after`, sorted: Task 3 (1 + 1), Task 4 (6), Task 5 (10), Task 6 (14 + 1), Task 9 (1).
- **Authorization abilities.** `requests` on `Tag` (`tags.requests`), `update` on `Project`, `LandingPage` and `Content` with type, `create` on `Tag`, and `view` on `FirebaseNotification`. All of them exist in the registered policies (`Modules\Backend\Policies\ProjectPolicy`, `Modules\Cms\Policies\{TagPolicy,LandingPagePolicy,ContentPolicy}`, `Modules\Notification\Policies\NotificationPolicy`).

### 4. Task pairs that share files or interfaces (for the controller's pre-flight scan)

| Tasks | Shared file or interface | How they meet | Consistent? |
|---|---|---|---|
| 1 → 5 → 7 | `ProjectController.php`, `OpportunityController.php` `data_requests` | 1 edits `rawColumns` and removes the Opportunity `link` column. 5 inserts `authorize` above `$list = ContactUS::query();`. 7 changes the `DataTables::of($list)` line below it. Each old text still exists after the earlier tasks. | yes |
| 5 → 7 | same controllers, `data_properties` | 5 inserts `authorize` above `$list = PropertyForm::query();`; 7 edits the next line | yes |
| 5 → 6 | same controllers | 5 edits the lead methods and `deletePayment`/`deletePrice`; 6 inserts `show()` above `create()` | yes, disjoint regions |
| 1 → 7 | `config/datatables.php` | 1 changes `json.options`; 7 adds `max_length` after `index_column` | yes |
| 2 → 6 → 7 | `UserController.php` | 2 deletes three `\Log::debug` lines; 6 replaces the head of `validateIdentity_` and adds `show()`; 7 changes the `paginate` call and adds an import | yes, disjoint regions |
| 4 → 7 | `CategoryController.php` | 4 deletes `addCategoriesAndFilters` (lines 24–234); 7 edits `getCategoriesSelect2` and adds an import after `use Modules\Cms\Classes\ResponseHandler;` (line 11, above the deleted block) | yes |
| 5 → 6 | `TagController.php` | 5 adds `authorize` in `save`; 6 adds `validate` in `list` | yes |
| 3 → 5 → 6 | `NotificationController.php` | 3: imports, `$validationsRules`, `postWebToken`. 5: `getList`, `postIndex`. 6: `index`. The `auth` except-list is left unchanged by all three. | yes |
| 3, 4 → controller | `tests/upgrade/routes.json` | two separate acceptances: 1 cell after Task 3, 6 cells after Task 4 | yes |
| 3, 5, 6 → controller → 12 | `permissions-matrix` `matrix.json` | acceptances of 1, 10 and 14 disjoint cells, applied in order; Task 12 documents the settled rows | yes |
| 6 → controller | `admin-sections` `notifications.json` | 1 cell | yes |
| 9 → controller | `tests/upgrade/config.json` | 1 cell | yes |
| 2 → 9 | `AppServiceProvider.php` has no Debugbar reference; `ProductionConfigTest` require-dev guard | 9's guard passes only after 2 | yes, 2 runs first |
| 2 → 4 | `CURRCONV_API_KEY` unset makes `update_currency` a no-op | 4's button test also aborts the request in the browser | yes |
| 1, 3, 4, 5, 6, 9, 12, 13 | `tests/e2e/PARITY.md` | each edits a different bullet or section: the UTF-8 bullet, the config bullet, the state-changing GET bullet, "When the dump changes" (5 and 12 add separate bullets), the 500 bullet, the environment list, a new no-signal section, the audit section, the count line | yes, disjoint |
| 4 | `tests/e2e/parity/admin-urls.ts` `MATRIX_EXCLUDED` | emptied; `MATRIX_URLS` untouched, so no matrix change | yes |
| 3, 4, 8, 10 | `EnsureStaff` 401/403 bodies, route `staff` middleware, global `SecurityHeaders`, `module-scripts` 404 | 4's anonymous clear-cache AJAX POST expects 3's unchanged JSON 401; 8's headers do not affect status assertions elsewhere | yes |
| 5, 12 | admin POST outcomes | 12's probes use the notification POST (`notifications.view` since 5) and `tags/save` (authorize before validation since 5) | yes |

### 5. Spec conflicts and research corrections found while planning

- **S3 "every response".** The middleware covers every response PHP produces. Static files served directly by Apache/LiteSpeed never reach it, and the plan adds no `.htaccess` headers: `nosniff` on static files could block assets that production serves with a wrong MIME type, which cannot be verified locally. Listed for Phase 3.
- **S2 production values.** The spec's `APP_DEBUG=false` and `SESSION_SECURE_COOKIE=true` are production `.env` values, so they are Phase 3. Locally `secure` keeps the `false` default, as instructed.
- **S6 (research A).** `throttle:10,1` with an expected 429 cannot work in this app: the Handler turns `ThrottleRequestsException` into a login redirect. A controller-level limiter is used instead.
- **S9 and S3 (research A).** `firebase_scripts` is commented out in `Modules/Cms/Resources/views/layouts/master.blade.php` too, not only in the frontend layout. `postWebToken`, `getList` and gstatic have no live caller today. The fixes are unchanged.
- **S1 (research A).**
  - With `datatables.max_length` set, yajra pages every request and caps `length=-1` at 500. That would silently truncate the leads "All" option (3,708 leads), so the four lead/property-form endpoints keep "All" with `ignoreMaxLength()`.
  - The research's OOM premise dates from Laravel 7's 128M limit; the PHP 8.4 image has `memory_limit = 512M`.
- **S10 (research B).** `asdwadwadwdaw` seeds 12 filter pages (10 of them missing from the dump), not 6.
- **S10 vs "keep every feature".** Making `login_as` POST-only breaks the ROOT-only users-table "login as" link. No account holds ROOT, but this needs a ruling.
- **S13 and P2-R7.** P2-R7 predates S21. Authorizing the lead endpoints before their lookup gives 6 of the 9 no-signal rows a role signal, and 3 remain.
- **S16 (research D).** Deleting the scripts turns their `/modules` URL from 403 into 404: a PHP name under a directory that no longer exists falls through to Laravel. This was checked with a GET for a PHP name under a directory that does not exist (302, then 404). The `module-scripts.spec.ts` expectation changes.
- **S18 (research D).** Its trait list names `RegistersUsers::validator` and `create`, which are not trait methods. It misses `ThrottlesLogins::sendLockoutResponse` and the four `SendsPasswordResetEmails` overrides in `ForgotPasswordController`. The guard in Task 11 uses the verified list and checks its completeness by reflection.
- **S21 `getList`.** No admin page requires an ability for the header notification feed, which is dormant, so the plan uses the module's `notifications.view`.
- **Outside the S items.** `OpportunityController::data_properties` has the same slug-less `link` column bug as P1-R39 (`property_form` has no slug). No UI calls it. It is reported, not fixed.
- **S14.** `FrontendController::currencyApi()` also sends a hard-coded Cloudflare `__cfduid` cookie header. It is not a credential and stays unchanged.
- **Tests and line endings.** PHPUnit cannot send HTTP requests to this app: `RedirectToHttps` redirects every non-`local` request. The PHPUnit tests therefore call middleware and controllers directly. Many edited files use CRLF, so every task checks `git diff --ignore-cr-at-eol --stat`.
