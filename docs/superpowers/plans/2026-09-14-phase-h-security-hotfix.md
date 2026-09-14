# Phase H — Urgent Security Hotfix Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Close every confirmed vulnerability on the live Laravel 7 site (H1–H8), remove all access paths for the former vendor (N1–N6), and ship the fixes to production with a backup and a one-command rollback.

**Architecture:**
- Targeted patches on the existing Laravel 7.3 code base; no framework or dependency changes.
- Two new route middleware: `staff` gates admin-only endpoints, `contact.guard` adds a honeypot and a per-IP limit on the public contact forms.
- Two new artisan commands: `aldar:offboard-vendor` and `aldar:set-password`.
- A Playwright suite (`tests/e2e`) proves each fix against the local Docker stack; PHPUnit covers the commands and two source-level guards.
- A small bash toolkit patches production in place, with a drift check, backups, verification and rollback.

**Tech Stack:** Laravel 7.3, PHP 7.4, MariaDB 11.8, Docker Compose (local), Playwright + TypeScript (Node 24), PHPUnit 8, bash, Hostinger shared hosting over SSH (`codecamb`).

**Spec:** `docs/superpowers/specs/2026-09-14-aldar-security-hotfix-and-laravel-13-upgrade-design.md` (Phase H sections H.1–H.4).

## Global Constraints

- **Parity (1:1).** Any intentional output change must be listed and accepted explicitly in its PR.
- **No PII in git.** DB dumps, lead data, logs and `.env` files stay out.
- **Production runtime:** Laravel 7.3 on `/opt/alt/php74/usr/bin/php`, app dir `~/domains/aldar-emlak.com/public_html` on SSH host `codecamb`.
- **Branching.** All work happens on branch `hotfix/security` and lands on `main` through a PR.
- **Commit trailer.** Every commit message ends with `Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>`.
- **Tests that write data** run only when `BASE_URL` is the local Docker stack (`http://localhost:8080`).
- **Production is read-only until Task 11.** Task 9 only reads file hashes over SSH. Any command that changes production needs explicit owner approval at the moment it runs.
- **Secrets.** Claude never reads, prints or types secret values. The owner enters new DB and admin passwords themselves.

## File Structure

| Path | Responsibility |
|---|---|
| `scripts/e2e/db-reset.sh` | Rebuild the local DB from the dump; create `parity-superadmin` / `parity-admin` |
| `tests/e2e/package.json`, `playwright.config.ts`, `tsconfig.json`, `.gitignore` | Playwright project |
| `tests/e2e/support/env.ts` | Base URL, local-only guard |
| `tests/e2e/support/docker.ts` | SQL, artisan and shell access to the local containers |
| `tests/e2e/support/auth.ts` | Log in through the real login form |
| `tests/e2e/support/csrf.ts` | Read CSRF tokens for page and API contexts |
| `tests/e2e/support/fixtures.ts` | Generate a real JPEG with GD |
| `tests/e2e/support/global-setup.ts` | Reset the DB before each local run |
| `tests/e2e/specs/smoke/critical-flows.spec.ts` | Key pages, images, lead capture, admin login |
| `tests/e2e/specs/security/*.spec.ts` | One spec per fix group |
| `app/Http/Middleware/EnsureStaff.php` | `staff` middleware |
| `app/Http/Middleware/ProtectContactForms.php` | `contact.guard` middleware |
| `app/Console/Commands/OffboardVendor.php` | `aldar:offboard-vendor` |
| `app/Console/Commands/SetUserPassword.php` | `aldar:set-password` |
| `config/image_sizes.php` | Allowed Glide sizes |
| `public/graph/.htaccess` | Block script execution in the TinyMCE upload tree |
| `public/modules/.htaccess` | Block the template mail scripts and any other PHP under `public/modules` (H9, final review) |
| `Modules/Frontend/Resources/views/partials/honeypot.blade.php` | Hidden spam-trap field |
| `tests/Unit/ImageSizesConfigTest.php`, `tests/Unit/FirebaseTlsTest.php` | Source-level regression guards |
| `tests/Feature/VendorOffboardingTest.php`, `tests/Feature/SetUserPasswordCommandTest.php` | Command tests |
| `scripts/server/db-dump.php` | Server-side DB dump using Laravel's own config |
| `scripts/hotfix/deploy.sh`, `rollback.sh`, `verify-production.sh`, `probe-client-ip.sh`, `known-baseline-edits.txt` | Production patching toolkit |

Existing files modified: `app/Http/Kernel.php`, `routes/web.php`, `Modules/Cms/Routes/web.php`, `Modules/Frontend/Routes/web.php`, `Modules/Cms/Http/Controllers/Admin/{DashboardController,AttachmentController,TinymceController}.php`, `Modules/Cms/Http/Controllers/ImageController.php`, `Modules/Cms/Resources/views/dashboard.blade.php`, the six Blade views holding contact forms, `Modules/Frontend/Resources/lang/{ar,en}/main.php`, `Modules/Notification/Entities/FirebaseNotification.php`, `public/service-worker.js`, `public/firebase-messaging-sw.js`, `Modules/Cms/Config/config.php`, `Modules/Cms/Database/Seeders/UsersTableSeeder.php`.

**How to run things locally (used throughout):**

```bash
docker compose up -d                                          # stack at http://localhost:8080
(cd tests/e2e && npx playwright test specs/security/x.spec.ts)   # one e2e spec (resets the DB first)
docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Unit/XTest.php
```

---

### Task 1: E2E harness and baseline smoke tests

**Files:**
- Create: `scripts/e2e/db-reset.sh`
- Create: `tests/e2e/package.json`, `tests/e2e/playwright.config.ts`, `tests/e2e/tsconfig.json`, `tests/e2e/.gitignore`
- Create: `tests/e2e/support/{env,docker,auth,csrf,fixtures,global-setup}.ts`
- Test: `tests/e2e/specs/smoke/critical-flows.spec.ts`

**Interfaces:**
- Produces:
  - `BASE_URL: string`, `IS_LOCAL: boolean`, `E2E_PASSWORD: string`, `requireLocal(what: string): void` (env.ts)
  - `sql(query: string): string`, `sqlScalar(query: string): number`, `artisan(...args: string[]): string`, `appShell(command: string): string` (docker.ts)
  - `attemptLogin(page: Page, identity: string, password: string): Promise<number>`, `loginAs(page: Page, username: 'parity-superadmin' | 'parity-admin'): Promise<void>` (auth.ts)
  - `csrfToken(page: Page): Promise<string>`, `anonymousCsrfToken(request: APIRequestContext): Promise<string>`, `AJAX_HEADERS` (csrf.ts)
  - `jpegFixture(): Buffer` (fixtures.ts)
  - Local test accounts `parity-superadmin` (SUPERADMIN) and `parity-admin` (ADMIN), both with password `E2E_PASSWORD`.

- [ ] **Step 1: Create the branch**

```bash
git checkout main && git pull --ff-only && git checkout -b hotfix/security
```

- [ ] **Step 2: Write the DB reset script**

`scripts/e2e/db-reset.sh`:

```bash
#!/usr/bin/env bash
# Rebuilds the local Docker database from the production dump and adds the
# e2e test accounts. Local only: it talks to the docker compose services.
set -euo pipefail
cd "$(dirname "$0")/../.."

DUMP="${DUMP:-_db-backup/aldar-db-20260914-1704.sql.gz}"
E2E_PASSWORD="${E2E_PASSWORD:-e2e-local-password}"

[ -f "$DUMP" ] || { echo "Dump not found: $DUMP" >&2; exit 1; }

docker compose exec -T db mariadb -uroot -proot -e \
  "DROP DATABASE IF EXISTS aldar; CREATE DATABASE aldar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON aldar.* TO 'aldar'@'%';"
gunzip -c "$DUMP" | docker compose exec -T db mariadb -uroot -proot aldar

HASH="$(docker compose exec -T app php -r 'echo password_hash($argv[1], PASSWORD_BCRYPT);' "$E2E_PASSWORD")"

docker compose exec -T db mariadb -ualdar -paldar aldar <<SQL
INSERT INTO users (username, email, status, verification_code, password, email_verified_at, created_at, updated_at) VALUES
  ('parity-superadmin', 'parity-superadmin@aldar.test', 'ACTIVE', 'VERIFIED', '${HASH}', NOW(), NOW(), NOW()),
  ('parity-admin',      'parity-admin@aldar.test',      'ACTIVE', 'VERIFIED', '${HASH}', NOW(), NOW(), NOW());
INSERT INTO perms_assigned_roles (role_id, entity_id, entity_type)
SELECT r.id, u.id, CONCAT('App', CHAR(92), 'User')
FROM users u
JOIN perms_roles r ON r.name = IF(u.username = 'parity-superadmin', 'SUPERADMIN', 'ADMIN')
WHERE u.username IN ('parity-superadmin', 'parity-admin');
SQL

docker compose exec -T app php artisan cache:clear >/dev/null
echo "Local DB reset; test accounts: parity-superadmin, parity-admin"
```

Run: `chmod +x scripts/e2e/db-reset.sh && scripts/e2e/db-reset.sh`
Expected: ends with `Local DB reset; test accounts: parity-superadmin, parity-admin`.

- [ ] **Step 3: Create the Playwright project**

`tests/e2e/.gitignore`:

```
node_modules/
test-results/
playwright-report/
```

`tests/e2e/tsconfig.json`:

```json
{
  "compilerOptions": {
    "target": "ES2022",
    "module": "commonjs",
    "strict": true,
    "esModuleInterop": true,
    "types": ["node"]
  }
}
```

Run:

```bash
cd tests/e2e
npm init -y >/dev/null
npm pkg set name=aldar-e2e private=true --json
npm pkg set scripts.test="playwright test" scripts.test:security="playwright test specs/security" scripts.test:smoke="playwright test specs/smoke"
npm install --save-dev @playwright/test@latest @types/node@22
npx playwright install chromium
cd ../..
```

`tests/e2e/playwright.config.ts`:

```ts
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './specs',
  // One worker: the specs share one database and one rate limiter.
  workers: 1,
  fullyParallel: false,
  retries: 0,
  timeout: 60_000,
  reporter: [['list']],
  globalSetup: './support/global-setup.ts',
  use: {
    baseURL: process.env.BASE_URL ?? 'http://localhost:8080',
    trace: 'retain-on-failure',
  },
  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
```

- [ ] **Step 4: Write the support modules**

`tests/e2e/support/env.ts`:

```ts
export const BASE_URL = process.env.BASE_URL ?? 'http://localhost:8080';
export const IS_LOCAL = /^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/?$/.test(BASE_URL);
export const E2E_PASSWORD = process.env.E2E_PASSWORD ?? 'e2e-local-password';

export function requireLocal(what: string): void {
  if (!IS_LOCAL) {
    throw new Error(`${what} only runs against the local Docker stack (BASE_URL=${BASE_URL})`);
  }
}
```

`tests/e2e/support/docker.ts`:

```ts
import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { requireLocal } from './env';

const REPO_ROOT = path.resolve(__dirname, '../../..');

function compose(args: string[]): string {
  requireLocal('Docker access');
  return execFileSync('docker', ['compose', ...args], { cwd: REPO_ROOT, encoding: 'utf8' });
}

export function sql(query: string): string {
  return compose(['exec', '-T', 'db', 'mariadb', '-ualdar', '-paldar', 'aldar', '-N', '-B', '-e', query]).trim();
}

export function sqlScalar(query: string): number {
  return Number(sql(query));
}

export function artisan(...args: string[]): string {
  return compose(['exec', '-T', 'app', 'php', 'artisan', ...args]);
}

export function appShell(command: string): string {
  return compose(['exec', '-T', 'app', 'sh', '-c', command]).trim();
}
```

`tests/e2e/support/auth.ts`:

```ts
import { expect, Page } from '@playwright/test';
import { E2E_PASSWORD } from './env';

export async function attemptLogin(page: Page, identity: string, password: string): Promise<number> {
  await page.context().clearCookies();
  await page.goto('/en/authenticate/login');
  await page.fill('#identity', identity);
  await page.fill('#password', password);
  const [response] = await Promise.all([
    page.waitForResponse(r => r.request().method() === 'POST' && r.url().includes('/authenticate/login')),
    page.click('button[type=submit]'),
  ]);
  return response.status();
}

export async function loginAs(page: Page, username: 'parity-superadmin' | 'parity-admin'): Promise<void> {
  expect(await attemptLogin(page, username, E2E_PASSWORD)).toBe(200);
  await expect(page).toHaveURL(/\/en\/admin\/?$/, { timeout: 30_000 });
}
```

`tests/e2e/support/csrf.ts`:

```ts
import { APIRequestContext, Page } from '@playwright/test';

export const AJAX_HEADERS = { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' };

/** Token for the page's own session (works for logged-in pages too). */
export async function csrfToken(page: Page): Promise<string> {
  await page.goto('/en/contact-us');
  const token = await page.locator('input[name="_token"]').first().getAttribute('value');
  if (!token) throw new Error('No CSRF token on /en/contact-us');
  return token;
}

/** Token for a cookie-isolated API context with no login. */
export async function anonymousCsrfToken(request: APIRequestContext): Promise<string> {
  const html = await (await request.get('/en/contact-us')).text();
  const match = html.match(/name="_token" value="([^"]+)"/);
  if (!match) throw new Error('No CSRF token on /en/contact-us');
  return match[1];
}
```

`tests/e2e/support/fixtures.ts`:

```ts
import { appShell } from './docker';

/** A real 8x8 JPEG produced by the app container's GD, so finfo and getimagesize accept it. */
export function jpegFixture(): Buffer {
  const base64 = appShell(`php -r '$i = imagecreatetruecolor(8, 8); ob_start(); imagejpeg($i); echo base64_encode(ob_get_clean());'`);
  return Buffer.from(base64, 'base64');
}
```

`tests/e2e/support/global-setup.ts`:

```ts
import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { IS_LOCAL } from './env';

export default function globalSetup(): void {
  if (!IS_LOCAL || process.env.SKIP_DB_RESET === '1') return;
  execFileSync('bash', [path.resolve(__dirname, '../../../scripts/e2e/db-reset.sh')], { stdio: 'inherit' });
}
```

- [ ] **Step 5: Write the smoke spec**

`tests/e2e/specs/smoke/critical-flows.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { sqlScalar } from '../../support/docker';
import { IS_LOCAL } from '../../support/env';

const PAGES = [
  '/en', '/ar',
  '/en/contact-us', '/ar/contact-us',
  '/en/articles', '/ar/articles',
  '/en/services', '/en/faqs',
  '/en/apartments/for-sale/istanbul',
];

for (const url of PAGES) {
  test(`page ${url} renders and every image on it loads`, async ({ page, request }) => {
    const response = await page.goto(url);
    expect(response?.status()).toBe(200);

    const imageUrls = await page.$$eval('[src*="/img/"], [data-src*="/img/"]', elements =>
      Array.from(new Set(elements.map(e => e.getAttribute('data-src') || e.getAttribute('src') || ''))).filter(Boolean));
    expect(imageUrls.length).toBeGreaterThan(0);

    for (const imageUrl of imageUrls) {
      expect((await request.get(imageUrl)).status(), imageUrl).toBe(200);
    }
  });
}

test('contact form stores a lead', async ({ request }) => {
  test.skip(!IS_LOCAL, 'writes a lead');
  const before = sqlScalar('SELECT COUNT(*) FROM contact_us');
  const token = await anonymousCsrfToken(request);

  const response = await request.post('/en/contact-us/store', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
    multipart: {
      fullname: 'Smoke Test', phone: '5551234567', email: 'smoke@aldar.test',
      description: 'Smoke test lead', country_code: '+90',
    },
  });

  expect(await response.json()).toMatchObject({ success: true });
  expect(sqlScalar('SELECT COUNT(*) FROM contact_us')).toBe(before + 1);
});

test('admin can log in and reach the dashboard', async ({ page }) => {
  test.skip(!IS_LOCAL, 'uses local test accounts');
  await loginAs(page, 'parity-superadmin');
  await expect(page.locator('.kt-widget1').first()).toBeVisible();
});
```

- [ ] **Step 6: Run the smoke spec on the unmodified code**

Run: `docker compose up -d && (cd tests/e2e && npx playwright test specs/smoke)`
Expected: `11 passed` (9 pages, lead, login). This is the baseline; if a page fails here, fix the harness before going on.

- [ ] **Step 7: Commit**

```bash
git add scripts/e2e tests/e2e/.gitignore tests/e2e/package.json tests/e2e/package-lock.json tests/e2e/tsconfig.json tests/e2e/playwright.config.ts tests/e2e/support tests/e2e/specs/smoke
git commit -m "Add Playwright e2e harness and baseline smoke tests

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 2: `staff` middleware and the maintenance routes (H2, H3, H7)

**Files:**
- Create: `app/Http/Middleware/EnsureStaff.php`
- Modify: `app/Http/Kernel.php` (`$routeMiddleware`)
- Modify: `Modules/Cms/Routes/web.php:10-35` (delete), and the admin group (add routes)
- Modify: `routes/web.php:14-16` (delete)
- Modify: `Modules/Frontend/Routes/web.php:20` (delete)
- Modify: `Modules/Cms/Http/Controllers/Admin/DashboardController.php` (add `updateCurrency`)
- Modify: `Modules/Cms/Resources/views/dashboard.blade.php:22-26`
- Test: `tests/e2e/specs/security/maintenance-routes.spec.ts`

**Interfaces:**
- Consumes: `loginAs`, `csrfToken`, `anonymousCsrfToken`, `AJAX_HEADERS`, `sql`, `requireLocal` (Task 1).
- Produces:
  - Route middleware alias `staff` → `App\Http\Middleware\EnsureStaff`. It lets through authenticated users with `disabled_at` and `deleted_at` null who hold role `SUPERADMIN`, `ADMIN` or `Editor`. Unauthenticated users get 401 JSON (AJAX) or a redirect to `route('login')`; everyone else gets 403.
  - `EnsureStaff::ROLES` constant.
  - Routes `POST {locale}/admin/update-currency` (name `DashboardController@updateCurrency`) and `GET {locale}/admin/clear-cache` (name `cache.clear`).

- [ ] **Step 1: Write the failing test**

`tests/e2e/specs/security/maintenance-routes.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { sql } from '../../support/docker';
import { requireLocal } from '../../support/env';

test.beforeAll(() => requireLocal('GET /seed runs the seeders on unpatched code'));

for (const url of ['/seed', '/migrate', '/update_currency', '/en/clear-cache']) {
  test(`${url} is not publicly reachable`, async ({ request }) => {
    expect((await request.get(url)).status()).toBe(404);
  });
}

test('anonymous visitors cannot use the admin maintenance routes', async ({ request }) => {
  const token = await anonymousCsrfToken(request);
  const update = await request.post('/en/admin/update-currency', { headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token } });
  expect(update.status()).toBe(401);

  const clear = await request.get('/en/admin/clear-cache', { maxRedirects: 0 });
  expect(clear.status()).toBe(302);
  expect(clear.headers()['location']).toContain('/authenticate/login');
});

test('the dashboard still offers the currency update to staff', async ({ page }) => {
  await loginAs(page, 'parity-admin');
  const form = page.locator('form[action$="/en/admin/update-currency"]');
  await expect(form).toHaveCount(1);
  await expect(form.locator('input[name="_token"]')).toHaveCount(1);
  await expect(form.locator('button.btn-success')).toBeVisible();
});

test('staff can clear the cache; disabled staff cannot', async ({ page }) => {
  await loginAs(page, 'parity-admin');
  const ok = await page.request.get('/en/admin/clear-cache', { maxRedirects: 0 });
  expect(ok.status()).toBe(302);

  sql("UPDATE users SET disabled_at = NOW() WHERE username = 'parity-admin'");
  try {
    const denied = await page.request.get('/en/admin/clear-cache', { maxRedirects: 0 });
    expect(denied.status()).toBe(403);
  } finally {
    sql("UPDATE users SET disabled_at = NULL WHERE username = 'parity-admin'");
  }
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `(cd tests/e2e && npx playwright test specs/security/maintenance-routes.spec.ts)`
Expected: FAIL. `/seed` returns 200 (it runs the seeders on the local DB, which the next run's reset repairs), and `/en/admin/update-currency` returns 404.

- [ ] **Step 3: Create the middleware**

`app/Http/Middleware/EnsureStaff.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Admin-only endpoints: an active, non-deleted account holding a staff role.
 * ROOT is deliberately not a staff role; it belonged to the former vendor.
 */
class EnsureStaff
{
    public const ROLES = ['SUPERADMIN', 'ADMIN', 'Editor'];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('login'));
        }

        if ($user->disabled_at !== null || $user->deleted_at !== null || ! $user->isAn(...self::ROLES)) {
            abort(403);
        }

        return $next($request);
    }
}
```

In `app/Http/Kernel.php`, add to `$routeMiddleware` after the `'verified'` entry:

```php
        'staff' => \App\Http\Middleware\EnsureStaff::class,
```

- [ ] **Step 4: Remove the public routes and add the admin ones**

In `Modules/Cms/Routes/web.php`, delete everything between the `img/{size}/{path}` route and the `Route::group([` localized group: the commented `migrate_refresh` block, `Route::get('migrate' …)` and `Route::get('seed' …)`.

In the same file, inside the `'prefix' => 'admin', 'namespace' => 'Admin'` group, directly under the `DashboardController@index` route, add:

```php
        Route::post('update-currency',                  'DashboardController@updateCurrency')->middleware('staff')->name('DashboardController@updateCurrency');
        Route::get('clear-cache',                       '\Modules\Frontend\Http\Controllers\HomeController@clearCache')->middleware('staff')->name('cache.clear');
```

In `routes/web.php`, delete the `update_currency` closure (lines 14–16) and keep the header comment.

In `Modules/Frontend/Routes/web.php`, delete line 20: `Route::get('/clear-cache', 'HomeController@clearCache')->name('cache.clear');`

- [ ] **Step 5: Add the controller action and the dashboard form**

In `Modules/Cms/Http/Controllers/Admin/DashboardController.php`, add after `index()`:

```php
    /**
     * Refresh currency rates on demand (the same command the scheduler runs).
     */
    public function updateCurrency()
    {
        \Artisan::call('update_currency');

        return redirect()->route('DashboardController@index');
    }
```

In `Modules/Cms/Resources/views/dashboard.blade.php`, replace:

```blade
                        <a target="_blank" href="https://aldar-emlak.com/update_currency" class="submit_form btn btn-success btn-bold">
                            {{ __('cms::dashboard.update') }}
                        </a>
```

with:

```blade
                        <form method="POST" action="{{ route('DashboardController@updateCurrency') }}" target="_blank" class="d-inline">
                            @csrf
                            <button type="submit" class="submit_form btn btn-success btn-bold">
                                {{ __('cms::dashboard.update') }}
                            </button>
                        </form>
```

- [ ] **Step 6: Run the tests to verify they pass**

Run: `docker compose exec -T app php artisan route:clear && (cd tests/e2e && npx playwright test specs/security/maintenance-routes.spec.ts specs/smoke)`
Expected: all pass (7 security + 11 smoke).

- [ ] **Step 7: Commit**

```bash
git add app/Http/Middleware/EnsureStaff.php app/Http/Kernel.php routes/web.php Modules/Cms/Routes/web.php Modules/Frontend/Routes/web.php Modules/Cms/Http/Controllers/Admin/DashboardController.php Modules/Cms/Resources/views/dashboard.blade.php tests/e2e/specs/security/maintenance-routes.spec.ts
git commit -m "Close public seed, migrate, currency and cache routes

/seed re-activated the former vendor's ROOT account with a known password.
Currency refresh and cache clearing move behind the new staff middleware.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 3: Lock down attachment upload and delete (H8)

**Files:**
- Modify: `Modules/Cms/Routes/web.php` (attachments group)
- Modify: `Modules/Cms/Http/Controllers/Admin/AttachmentController.php:23-71`
- Test: `tests/e2e/specs/security/attachments.spec.ts`

**Interfaces:**
- Consumes: `staff` middleware (Task 2); `loginAs`, `csrfToken`, `anonymousCsrfToken`, `AJAX_HEADERS`, `sql`, `sqlScalar`, `appShell`, `jpegFixture` (Task 1).
- Produces:
  - `AttachmentController::ALLOWED_RULES` (string[]) and `AttachmentController::DEFAULT_RULES` (string).
  - Stored uid format `attachments/{subFolder}/{40 random chars}.{extension}`.

- [ ] **Step 1: Write the failing test**

`tests/e2e/specs/security/attachments.spec.ts`:

```ts
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
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `(cd tests/e2e && npx playwright test specs/security/attachments.spec.ts)`
Expected: FAIL. The anonymous upload returns 409 and leaves `probe.php` under `uploads/projects`; the anonymous delete returns 200 and removes the row.

- [ ] **Step 3: Protect the routes**

In `Modules/Cms/Routes/web.php`, change the attachments group opening line to:

```php
        Route::group([ 'prefix' => 'attachments', 'middleware' => 'staff' ], function() {
```

- [ ] **Step 4: Harden `store()`**

In `Modules/Cms/Http/Controllers/Admin/AttachmentController.php`, add these constants inside the class, above `store()`:

```php
    /**
     * Rule strings the admin views send (dropzone components, Content, projects and
     * opportunities media). Anything else from the client falls back to DEFAULT_RULES.
     */
    public const ALLOWED_RULES = [
        'required|file|max:2048|mimes:jpeg,jpg,png,pdf',
        'required|file|max:2048|mimes:jpeg,jpg,png',
        'required|file|max:1024|mimes:jpeg,jpg,png,pdf',
        'required|image|max:1024|mimes:jpeg,jpg,png',
        'bail|required|image|max:2048|mimes:jpeg,jpg,png|dimensions:min_width=250,min_height=500,max_width=1000,max_height=2000',
    ];

    public const DEFAULT_RULES = 'required|file|max:2048|mimes:jpeg,jpg,png,pdf';
```

Replace the `'attachment' => $request->validation_rules ?? 'required',` rule with:

```php
            'attachment'        => in_array($request->validation_rules, self::ALLOWED_RULES, true) ? $request->validation_rules : self::DEFAULT_RULES,
```

Replace the sub-folder and `storeAs` block:

```php
                $subFolder = $request->sub_folder ?? 'general';
                if(!empty($subFolder))
                {
                    $subFolder = !Str::startsWith($subFolder, '/') ? "/{$subFolder}" : $subFolder;
                }
                if($attachmentUid = $request->attachment->storeAs('attachments'.$subFolder, $request->attachment->getClientOriginalName()))
```

with:

```php
                $subFolder = preg_match('/^[a-z0-9_-]+$/i', (string) $request->sub_folder) ? $request->sub_folder : 'general';
                $storedName = Str::random(40) . '.' . ($request->attachment->guessExtension() ?: 'bin');
                if($attachmentUid = $request->attachment->storeAs("attachments/{$subFolder}", $storedName))
```

Leave `'filename' => $request->attachment->getClientOriginalName()` as it is; the original name is kept for display.

- [ ] **Step 5: Run the tests to verify they pass**

Run: `docker compose exec -T app php artisan route:clear && (cd tests/e2e && npx playwright test specs/security/attachments.spec.ts)`
Expected: `3 passed`.

- [ ] **Step 6: Check that the real admin uploader still works**

Run: `(cd tests/e2e && npx playwright test specs/smoke)` → `11 passed`.

Then open http://localhost:8080/en/admin, log in as `parity-superadmin` (password `e2e-local-password`), edit a project, and upload an image in the media section. Expected: the dropzone shows the upload succeeded and the image appears after saving.

- [ ] **Step 7: Commit**

```bash
git add Modules/Cms/Routes/web.php Modules/Cms/Http/Controllers/Admin/AttachmentController.php tests/e2e/specs/security/attachments.spec.ts
git commit -m "Require staff for attachment upload and delete

Anyone could upload files with client-chosen rules, folder and name,
overwrite existing images, or delete attachments by id. Rules now come
from a server-side allowlist, folders are a single safe segment, and
files get random names.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 4: TinyMCE uploader and script execution under `public/graph` (H1)

**Files:**
- Modify: `Modules/Cms/Routes/web.php` (tinymce group)
- Modify: `Modules/Cms/Http/Controllers/Admin/TinymceController.php`
- Create: `public/graph/.htaccess`
- Test: `tests/e2e/specs/security/tinymce.spec.ts`

**Interfaces:**
- Consumes: `staff` (Task 2); `loginAs`, `csrfToken`, `AJAX_HEADERS`, `appShell`, `jpegFixture` (Task 1).
- Produces:
  - `TinymceController::ALLOWED_MIME_EXTENSIONS` (array<string, string>).
  - Response `{success: true, msg, location: "/graph/uploads/original/tinymce/<40 chars>.<ext>"}` or `{success: false, type, strong, msg}`.

- [ ] **Step 1: Write the failing test**

`tests/e2e/specs/security/tinymce.spec.ts`:

```ts
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
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `(cd tests/e2e && npx playwright test specs/security/tinymce.spec.ts)`
Expected: FAIL. `shell.php` uploads with `success: true`, and `probe-exec.php` returns 200 `executed`.

- [ ] **Step 3: Protect the route and rewrite the uploader**

In `Modules/Cms/Routes/web.php`, change the tinymce group opening line to:

```php
        Route::group(['prefix' => 'tinymce', 'middleware' => 'staff'], function (){
```

Replace the body of `Modules/Cms/Http/Controllers/Admin/TinymceController.php` from line 14 to the end with:

```php
class TinymceController extends CmsController
{
    protected static $UploadValidation = [
        'base_64_kb' => 10241
    ];

    /** Accepted image types, detected from the file bytes, never from the client name. */
    public const ALLOWED_MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    public function __construct(){
        parent::__construct();
        $this->middleware('auth');
    }

    public function uploader (Request $request){
        $base64 = (string) data_get($request->input('tinymce'), 'base64', '');

        $stringLength = strlen( $base64 );
        $byte = 4 * ( $stringLength / 3 ) * 0.5624896334383812;
        $kb = $byte / 1024;
        if ( $kb > static::$UploadValidation['base_64_kb'] ) {
            return [
                'success' => false,
                'type'    => 'danger',
                'strong'  => __('cms::global.upload_size_error.title'),
                'msg'     => __('cms::global.upload_size_error.description', ['size' => static::$UploadValidation['base_64_kb'], 'unit' => 'kb']),
            ];
        }

        $binary = base64_decode($base64, true);
        $mime = $binary === false ? null : (new \finfo(FILEINFO_MIME_TYPE))->buffer($binary);

        if ( ! isset(self::ALLOWED_MIME_EXTENSIONS[$mime]) || @getimagesizefromstring($binary) === false ) {
            return [
                'success' => false,
                'type'    => 'danger',
                'strong'  => __('cms::app.crud_messages.upload_error.title'),
                'msg'     => __('cms::app.crud_messages.upload_error.description'),
            ];
        }

        $filename = Str::random(40) . '.' . self::ALLOWED_MIME_EXTENSIONS[$mime];
        \Storage::disk('graph')->put( 'tinymce/' . $filename, $binary );

        return [
            'success'  => true,
            'msg'      => 'Uploaded Successfully',
            'location' => '/graph/uploads/original/tinymce/' . $filename,
        ];
    }
}
```

Add `use Illuminate\Support\Str;` to the `use` block at the top of the file.

- [ ] **Step 4: Block script execution under `public/graph`**

`public/graph/.htaccess`:

```apache
# Uploaded media only: never serve or execute scripts from this tree.
<FilesMatch "\.(php[0-9]?|phtml|phar|pht)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>
```

- [ ] **Step 5: Run the tests to verify they pass**

Run: `docker compose exec -T app php artisan route:clear && (cd tests/e2e && npx playwright test specs/security/tinymce.spec.ts)`
Expected: `3 passed`.

- [ ] **Step 6: Check the editor manually**

Log in at http://localhost:8080/en/admin as `parity-superadmin`, open any content edit page, and paste or insert an image in the TinyMCE editor. Expected: the image appears in the editor with a `/graph/uploads/original/tinymce/<random>.jpg` URL.

- [ ] **Step 7: Commit**

```bash
git add Modules/Cms/Routes/web.php Modules/Cms/Http/Controllers/Admin/TinymceController.php public/graph/.htaccess tests/e2e/specs/security/tinymce.spec.ts
git commit -m "Accept only real images in the TinyMCE uploader

Any logged-in user could write a .php file under public/graph and run
it. Uploads are now checked by content, get random names, need the
staff role, and PHP under public/graph is denied.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 5: Image size allowlist (H4)

**Files:**
- Create: `config/image_sizes.php`
- Modify: `Modules/Cms/Http/Controllers/ImageController.php:22-66`
- Test: `tests/Unit/ImageSizesConfigTest.php`, `tests/e2e/specs/security/images.spec.ts`

**Interfaces:**
- Consumes: `appShell`, `requireLocal` (Task 1).
- Produces: `config('image_sizes.allowed')` (string[] of `WxH`, where each side is digits or `auto`).

- [ ] **Step 1: Write the failing tests**

`tests/Unit/ImageSizesConfigTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImageSizesConfigTest extends TestCase
{
    public function test_every_size_literal_in_code_is_allowed(): void
    {
        $root = dirname(__DIR__, 2);
        $this->assertFileExists("{$root}/config/image_sizes.php");
        $allowed = (require "{$root}/config/image_sizes.php")['allowed'];

        $found = [];
        foreach (['Modules', 'app', 'config', 'resources'] as $dir) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("{$root}/{$dir}", RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                if (substr($file->getFilename(), -4) !== '.php') {
                    continue;
                }
                preg_match_all('/[\'"]((?:\d{2,4}|auto)x(?:\d{2,4}|auto))[\'"]/', file_get_contents($file->getPathname()), $matches);
                foreach ($matches[1] as $size) {
                    $found[$size] = true;
                }
            }
        }

        $this->assertSame([], array_values(array_diff(array_keys($found), $allowed)), 'Add these sizes to config/image_sizes.php');
    }
}
```

`tests/e2e/specs/security/images.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { appShell } from '../../support/docker';
import { requireLocal } from '../../support/env';

const CACHE_DIR = 'storage/app/public/uploads/.cache/defaults/base.png';
const cachedFiles = () => Number(appShell(`find ${CACHE_DIR} -type f 2>/dev/null | wc -l`));

test('an allowed size is served', async ({ request }) => {
  const response = await request.get('/img/85x85/defaults/base.png');
  expect(response.status()).toBe(200);
  expect(response.headers()['content-type']).toMatch(/^image\//);
});

test('an unknown size with no cached variant is refused', async ({ request }) => {
  // A fresh size on every run, so an earlier run can never have cached it.
  const size = `${1100 + Math.floor(Math.random() * 800)}x${1100 + Math.floor(Math.random() * 800)}`;
  expect((await request.get(`/img/${size}/defaults/base.png`)).status()).toBe(404);
});

test('quality, extension and mark parameters create no new variants', async ({ request }) => {
  requireLocal('reads the cache directory');
  expect((await request.get('/img/85x85/defaults/base.png')).status()).toBe(200);
  const before = cachedFiles();

  const response = await request.get(`/img/85x85/defaults/base.png?quality=${Date.now() % 90 + 5}&extension=gif&mark=x.png`);
  expect(response.status()).toBe(200);
  expect(cachedFiles()).toBe(before);
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Unit/ImageSizesConfigTest.php`
Expected: FAIL, `config/image_sizes.php` does not exist.

Run: `(cd tests/e2e && npx playwright test specs/security/images.spec.ts)`
Expected: FAIL. The unknown size returns 200, and the `quality` request adds a cache file.

- [ ] **Step 3: Create the allowlist**

`config/image_sizes.php`:

```php
<?php

/*
| Sizes the img/{size}/{path} route may generate. Each one appears as a literal
| in the code (entity image dimensions, getImage() calls, route('image') calls).
| tests/Unit/ImageSizesConfigTest.php fails when code uses a size missing here.
*/

return [
    'allowed' => [
        '1000x1000', '1000x750', '100x100', '100x70', '110x110', '1150x598', '1150x700', '1200x848',
        '120x120', '130x85', '150x100', '150x150', '165x130', '180x180', '1920x1079', '1920x1280',
        '1920x540', '1920x600', '1920x960', '192x128', '200x150', '200x200', '250x187', '250x250',
        '250x320', '25x25', '270x270', '300x300', '30x30', '341x218', '349x250', '350x350',
        '360x180', '400x200', '400x400', '40x40', '420x700', '425x250', '450x300', '500x375',
        '500x500', '500x640', '590x330', '600x300', '600x600', '60x30', '640x1066', '640x180',
        '698x500', '730x350', '735x403', '75x75', '795x259', '800x400', '850x500', '85x85',
        '861x825', '90x80', 'autox400',
    ],
];
```

- [ ] **Step 4: Enforce it in the controller**

Replace the `show()` method in `Modules/Cms/Http/Controllers/ImageController.php` with:

```php
    public function show(Server $server, Request $request)
    {
        if(! $server->sourceFileExists($request->path))
        {
            abort(404);
        }

        if($request->size == 'original')
        {
            $server->setDefaults([]);

            $name = $server->makeImage($request->path, []);
            $file = Storage::get($name);
            $type = Storage::mimeType($name);

            return \Response::make($file, 200)->header("Content-Type", $type);
        }

        $size = preg_split('/x/', $request->size);

        $options = [];

        if($size[0] != 'auto') $options['w'] = $size[0];
        if($size[1] != 'auto') $options['h'] = $size[1];

        // Only known sizes are generated. A size that is already cached keeps working,
        // so no URL that rendered before this change can break.
        if(! in_array($request->size, config('image_sizes.allowed'), true) && ! $server->cacheFileExists($request->path, $options))
        {
            abort(404);
        }

        $name = $server->makeImage($request->path, $options);
        $file = Storage::get($name);
        $type = Storage::mimeType($name);

        return \Response::make($file, 200)->header("Content-Type", $type);
    }
```

- [ ] **Step 5: Run the tests to verify they pass**

Run: `docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Unit/ImageSizesConfigTest.php`
Expected: `OK (1 test, 2 assertions)`.

Run: `(cd tests/e2e && npx playwright test specs/security/images.spec.ts specs/smoke)`
Expected: `3 passed` + `11 passed`. The smoke run loads every image on the nine key pages through the new allowlist.

- [ ] **Step 6: Commit**

```bash
git add config/image_sizes.php Modules/Cms/Http/Controllers/ImageController.php tests/Unit/ImageSizesConfigTest.php tests/e2e/specs/security/images.spec.ts
git commit -m "Only generate image sizes the site uses

Any visitor could request unlimited size, quality and format variants
and fill the disk. Unknown sizes now return 404 unless already cached.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 6: Contact form honeypot and limit (H5)

**Files:**
- Create: `app/Http/Middleware/ProtectContactForms.php`
- Create: `Modules/Frontend/Resources/views/partials/honeypot.blade.php`
- Modify: `app/Http/Kernel.php` (`$routeMiddleware`)
- Modify: `Modules/Frontend/Routes/web.php` (contact-us group)
- Modify: `Modules/Frontend/Resources/views/includes/side_contact.blade.php:4`, `includes/side_contact_with_agent.blade.php:28`, `layouts/footer.blade.php:190`, `index/contact.blade.php:109`, `landingpage/main.blade.php:35,123`, `pages/contact_us.blade.php:150`
- Modify: `Modules/Frontend/Resources/lang/en/main.php:127`, `Modules/Frontend/Resources/lang/ar/main.php:123`
- Test: `tests/e2e/specs/security/contact-forms.spec.ts`

**Interfaces:**
- Consumes: `anonymousCsrfToken`, `AJAX_HEADERS`, `sqlScalar`, `artisan`, `requireLocal` (Task 1).
- Produces:
  - Middleware alias `contact.guard` → `App\Http\Middleware\ProtectContactForms`, with constants `HONEYPOT_FIELD = 'aldar_hp'`, `MAX_SUBMISSIONS = 5`, `DECAY_SECONDS = 600`.
  - Translation key `frontend::main.too_many_submissions`.

- [ ] **Step 1: Write the failing test**

`tests/e2e/specs/security/contact-forms.spec.ts`:

```ts
import { test, expect, APIRequestContext } from '@playwright/test';
import { AJAX_HEADERS, anonymousCsrfToken } from '../../support/csrf';
import { artisan, sqlScalar } from '../../support/docker';
import { requireLocal } from '../../support/env';

const leads = () => sqlScalar('SELECT COUNT(*) FROM contact_us');

async function subscribe(request: APIRequestContext, extra: Record<string, string> = {}) {
  const token = await anonymousCsrfToken(request);
  const response = await request.post('/en/contact-us/subscribe', {
    headers: { ...AJAX_HEADERS, 'X-CSRF-TOKEN': token },
    multipart: { emails: `lead-${Date.now()}-${Math.random().toString(36).slice(2)}@aldar.test`, ...extra },
  });
  expect(response.status()).toBe(200);
  return response.json();
}

test.beforeEach(() => {
  requireLocal('contact form tests write leads');
  artisan('cache:clear'); // resets the limiter between tests
});

test('a filled honeypot looks successful but stores nothing', async ({ request }) => {
  const before = leads();
  expect(await subscribe(request, { aldar_hp: 'http://spam.example' })).toMatchObject({ success: true });
  expect(leads()).toBe(before);
});

test('the sixth successful submission within ten minutes is refused', async ({ request }) => {
  const before = leads();
  for (let i = 0; i < 5; i++) {
    expect(await subscribe(request)).toMatchObject({ success: true });
  }
  const refused = await subscribe(request);
  expect(refused.success).toBe(false);
  expect(refused.message).toBeTruthy();
  expect(leads()).toBe(before + 5);
});

test('every contact form renders the honeypot field', async ({ page }) => {
  for (const url of ['/en/contact-us', '/en']) {
    await page.goto(url);
    const forms = page.locator('form[action*="/contact-us/"]');
    const count = await forms.count();
    expect(count).toBeGreaterThan(0);
    for (let i = 0; i < count; i++) {
      await expect(forms.nth(i).locator('input[name="aldar_hp"]')).toHaveCount(1);
      await expect(forms.nth(i).locator('input[name="aldar_hp"]')).not.toBeInViewport();
    }
  }
});

test('a real visitor can still subscribe through the footer form', async ({ page }) => {
  const before = leads();
  await page.goto('/en/contact-us');
  await page.fill('form.bloq-email input[name="emails"]', `footer-${Date.now()}@aldar.test`);
  await page.click('form.bloq-email button[type="submit"]');
  await expect.poll(leads, { timeout: 15_000 }).toBe(before + 1);
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `(cd tests/e2e && npx playwright test specs/security/contact-forms.spec.ts)`
Expected: FAIL. The honeypot test stores a row, the sixth submission succeeds, and there is no `aldar_hp` field.

- [ ] **Step 3: Create the middleware**

`app/Http/Middleware/ProtectContactForms.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Spam protection for the public contact endpoints.
 *
 * Responses keep the endpoints' existing JSON shape and HTTP 200, because the
 * front-end submitFroms() handlers ignore non-2xx responses.
 */
class ProtectContactForms
{
    public const HONEYPOT_FIELD = 'aldar_hp';
    public const MAX_SUBMISSIONS = 5;
    public const DECAY_SECONDS = 600;

    /** @var RateLimiter */
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        if (filled($request->input(self::HONEYPOT_FIELD))) {
            return response()->json([
                'success'  => true,
                'disabled' => true,
                'message'  => trans('frontend::main.sendded'),
            ]);
        }

        $key = 'contact-forms:' . optional($request->route())->getName() . ':' . $request->ip();

        if ($this->limiter->tooManyAttempts($key, self::MAX_SUBMISSIONS)) {
            return response()->json([
                'success' => false,
                'message' => trans('frontend::main.too_many_submissions'),
            ]);
        }

        $response = $next($request);

        // Only stored leads count, so typos and validation errors never lock a visitor out.
        if ($response instanceof JsonResponse && data_get($response->getData(true), 'success') === true) {
            $this->limiter->hit($key, self::DECAY_SECONDS);
        }

        return $response;
    }
}
```

In `app/Http/Kernel.php` `$routeMiddleware`, add after the `'staff'` entry:

```php
        'contact.guard' => \App\Http\Middleware\ProtectContactForms::class,
```

- [ ] **Step 4: Apply it to the three endpoints**

In `Modules/Frontend/Routes/web.php`, replace the `store`, `subscribe` and `store-inner` lines in the `contact-us` group with:

```php
        Route::post('/store'        , 'ContactController@store')->name('store')->middleware('contact.guard');
        Route::post('/subscribe'    , 'ContactController@subscribe')->name('subscribe')->middleware('contact.guard');
        Route::post('/store-inner'  , 'ContactController@storeInner')->name('storeInner')->middleware('contact.guard');
```

- [ ] **Step 5: Add the honeypot partial and include it in every form**

`Modules/Frontend/Resources/views/partials/honeypot.blade.php`:

```blade
{{-- Spam trap: invisible to people, filled in by bots. See App\Http\Middleware\ProtectContactForms. --}}
<div style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
    <label for="aldar-hp-{{ $id }}">Leave this field empty</label>
    <input type="text" id="aldar-hp-{{ $id }}" name="aldar_hp" value="" tabindex="-1" autocomplete="off">
</div>
```

Insert one line directly after each opening `<form …>` tag (each tag is on a single line):

| File:line of the `<form>` tag | Line to insert after it |
|---|---|
| `Modules/Frontend/Resources/views/includes/side_contact.blade.php:4` | `@include('frontend::partials.honeypot', ['id' => 'side'])` |
| `Modules/Frontend/Resources/views/includes/side_contact_with_agent.blade.php:28` | `@include('frontend::partials.honeypot', ['id' => 'agent'])` |
| `Modules/Frontend/Resources/views/layouts/footer.blade.php:190` | `@include('frontend::partials.honeypot', ['id' => 'footer'])` |
| `Modules/Frontend/Resources/views/index/contact.blade.php:109` | `@include('frontend::partials.honeypot', ['id' => 'home'])` |
| `Modules/Frontend/Resources/views/landingpage/main.blade.php:35` | `@include('frontend::partials.honeypot', ['id' => 'landing-top'])` |
| `Modules/Frontend/Resources/views/landingpage/main.blade.php:123` (line 124 after the first insert) | `@include('frontend::partials.honeypot', ['id' => 'landing-bottom'])` |
| `Modules/Frontend/Resources/views/pages/contact_us.blade.php:150` | `@include('frontend::partials.honeypot', ['id' => 'contact-page'])` |

- [ ] **Step 6: Add the translations**

In `Modules/Frontend/Resources/lang/en/main.php`, after the `'some_errors_occurred'` line, add:

```php
    'too_many_submissions'      => 'You have sent several requests. Please try again in a few minutes.',
```

In `Modules/Frontend/Resources/lang/ar/main.php`, after the `'some_errors_occurred'` line, add:

```php
    'too_many_submissions'      => 'لقد أرسلت عدة طلبات. يرجى المحاولة مرة أخرى بعد بضع دقائق.',
```

- [ ] **Step 7: Run the tests to verify they pass**

Run: `docker compose exec -T app php artisan route:clear && docker compose exec -T app php artisan view:clear && (cd tests/e2e && npx playwright test specs/security/contact-forms.spec.ts specs/smoke)`
Expected: `4 passed` + `11 passed`.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Middleware/ProtectContactForms.php app/Http/Kernel.php Modules/Frontend/Routes/web.php Modules/Frontend/Resources/views Modules/Frontend/Resources/lang tests/e2e/specs/security/contact-forms.spec.ts
git commit -m "Add honeypot and per-IP limit to the contact forms

About half of the stored leads are link spam. Bots that fill the hidden
field get a normal success reply and nothing is stored, and each IP can
store five leads per endpoint per ten minutes.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 7: TLS for FCM and the vendor's service workers (H6, N6)

**Files:**
- Modify: `Modules/Notification/Entities/FirebaseNotification.php:181-182`
- Modify: `public/service-worker.js`, `public/firebase-messaging-sw.js` (full replacement)
- Test: `tests/Unit/FirebaseTlsTest.php`, `tests/e2e/specs/security/vendor-access.spec.ts` (service-worker part)

**Interfaces:**
- Produces: service workers that unsubscribe from push and unregister themselves.

- [ ] **Step 1: Write the failing tests**

`tests/Unit/FirebaseTlsTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FirebaseTlsTest extends TestCase
{
    public function test_fcm_request_verifies_the_tls_certificate(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/Modules/Notification/Entities/FirebaseNotification.php');

        $this->assertStringContainsString('CURLOPT_SSL_VERIFYHOST , 2', $source);
        $this->assertStringContainsString('CURLOPT_SSL_VERIFYPEER , true', $source);
        $this->assertStringNotContainsString('CURLOPT_SSL_VERIFYPEER , 0', $source);
    }
}
```

`tests/e2e/specs/security/vendor-access.spec.ts`:

```ts
import { test, expect } from '@playwright/test';

for (const worker of ['/service-worker.js', '/firebase-messaging-sw.js']) {
  test(`${worker} carries no vendor Firebase config and unregisters itself`, async ({ request }) => {
    const response = await request.get(worker);
    expect(response.status()).toBe(200);
    const body = await response.text();
    expect(body).not.toContain('binaa-prod');
    expect(body).not.toContain('firebase');
    expect(body).toContain('registration.unregister()');
  });
}
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Unit/FirebaseTlsTest.php` → FAIL.
Run: `(cd tests/e2e && npx playwright test specs/security/vendor-access.spec.ts)` → FAIL (`binaa-prod` present).

- [ ] **Step 3: Enable TLS verification**

In `Modules/Notification/Entities/FirebaseNotification.php`, replace lines 181–182:

```php
        curl_setopt( $CH, CURLOPT_SSL_VERIFYHOST , 0 );
        curl_setopt( $CH, CURLOPT_SSL_VERIFYPEER , 0 );
```

with:

```php
        curl_setopt( $CH, CURLOPT_SSL_VERIFYHOST , 2 );
        curl_setopt( $CH, CURLOPT_SSL_VERIFYPEER , true );
```

- [ ] **Step 4: Replace both service workers**

Write this exact content to both `public/service-worker.js` and `public/firebase-messaging-sw.js`:

```js
// Web push is no longer used on this site. Browsers that registered an earlier
// worker fetch this version on their next update check; it cancels the push
// subscription and removes itself.
self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        self.registration.pushManager.getSubscription()
            .then(function (subscription) {
                return subscription ? subscription.unsubscribe() : false;
            })
            .catch(function () {
                return false;
            })
            .then(function () {
                return self.registration.unregister();
            })
    );
});
```

- [ ] **Step 5: Run the tests to verify they pass**

Run: `docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Unit/FirebaseTlsTest.php` → `OK (1 test, 3 assertions)`.
Run: `(cd tests/e2e && npx playwright test specs/security/vendor-access.spec.ts)` → `2 passed`.

- [ ] **Step 6: Commit**

```bash
git add Modules/Notification/Entities/FirebaseNotification.php public/service-worker.js public/firebase-messaging-sw.js tests/Unit/FirebaseTlsTest.php tests/e2e/specs/security/vendor-access.spec.ts
git commit -m "Verify TLS for FCM and retire the vendor's service workers

The service workers pointed browsers at the former vendor's Firebase
project. They now unsubscribe and unregister themselves.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 8: Vendor offboarding and password tooling (N1, N2, N5)

> As implemented, the `LoginController::credentials()` override was dropped (ruling R5): login already refuses disabled and deleted accounts through `SoftDeletes` and the `Disabable` global scope. The e2e login test stays as a regression guard. `SetUserPassword` groups its username/email lookup so the scopes guard both branches (ruling R6, one extra test).

**Files:**
- Create: `app/Console/Commands/OffboardVendor.php`, `app/Console/Commands/SetUserPassword.php`
- Modify: `Modules/Cms/Http/Controllers/Auth/LoginController.php` (add `credentials()`)
- Modify: `Modules/Cms/Config/config.php` (`root` and `superadmin` entries)
- Modify: `Modules/Cms/Database/Seeders/UsersTableSeeder.php` (`createRootUser()` guard)
- Test: `tests/Feature/VendorOffboardingTest.php`, `tests/Feature/SetUserPasswordCommandTest.php`, `tests/e2e/specs/security/vendor-access.spec.ts` (login part)

**Interfaces:**
- Consumes: local accounts `parity-admin` / `parity-superadmin` (Task 1); `attemptLogin`, `sql`, `artisan`, `appShell`, `requireLocal` (Task 1).
- Produces:
  - `php artisan aldar:offboard-vendor [--domain=namaa-solutions.com]`, exit 0.
  - `php artisan aldar:set-password {username}`, which prompts `New password (min 12 characters)` and then `Repeat the new password`. Exit 0 on success, 1 on error.
  - Login refuses users whose `disabled_at` or `deleted_at` is set.

- [ ] **Step 1: Write the failing PHPUnit tests**

`tests/Feature/VendorOffboardingTest.php`:

```php
<?php

namespace Tests\Feature;

use App\User;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorOffboardingTest extends TestCase
{
    use DatabaseTransactions;

    /** Puts the vendor account back into its production state (an e2e run may have offboarded it). */
    private function activeVendor(): User
    {
        $vendor = User::where('email', 'root@namaa-solutions.com')->firstOrFail();
        $vendor->forceFill([
            'password'    => Hash::make('vendor-knows-this'),
            'status'      => 'ACTIVE',
            'disabled_at' => null,
            'deleted_at'  => null,
        ])->save();
        Bouncer::assign('ROOT')->to($vendor);

        return $vendor;
    }

    private function otherAccounts(): array
    {
        return DB::table('users')
            ->where('email', 'not like', '%@namaa-solutions.com')
            ->orderBy('id')
            ->get(['id', 'password', 'status', 'disabled_at', 'deleted_at'])
            ->map(function ($row) { return (array) $row; })
            ->all();
    }

    public function test_vendor_accounts_are_disabled_and_stripped_of_roles(): void
    {
        $vendor = $this->activeVendor();
        $this->assertGreaterThan(0, $vendor->roles()->count());

        $this->artisan('aldar:offboard-vendor')->assertExitCode(0);

        $vendor->refresh();
        $this->assertSame('DISABLED', $vendor->status);
        $this->assertNotNull($vendor->disabled_at);
        $this->assertNotNull($vendor->deleted_at);
        $this->assertNull($vendor->remember_token);
        $this->assertFalse(Hash::check('vendor-knows-this', $vendor->password));
        $this->assertSame(0, $vendor->roles()->count());
    }

    public function test_other_accounts_are_untouched(): void
    {
        $this->activeVendor();
        $before = $this->otherAccounts();

        $this->artisan('aldar:offboard-vendor')->assertExitCode(0);

        $this->assertSame($before, $this->otherAccounts());
    }

    public function test_the_root_seeder_does_nothing_without_a_configured_email(): void
    {
        config(['cms.root.email' => null]);
        $count = User::count();

        (new \Modules\Cms\Database\Seeders\UsersTableSeeder())->createRootUser();

        $this->assertSame($count, User::count());
    }
}
```

Also delete `tests/Feature/ExampleTest.php`. It is the framework's placeholder, asserts that `/` returns 200, and fails on the unmodified code because `/` redirects (302) by design. Keep `tests/Unit/ExampleTest.php`, which passes.

```bash
git rm tests/Feature/ExampleTest.php
```

`tests/Feature/SetUserPasswordCommandTest.php`:

```php
<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SetUserPasswordCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sets_the_password_from_hidden_prompts(): void
    {
        $this->artisan('aldar:set-password', ['username' => 'parity-admin'])
            ->expectsQuestion('New password (min 12 characters)', 'correct horse battery')
            ->expectsQuestion('Repeat the new password', 'correct horse battery')
            ->assertExitCode(0);

        $this->assertTrue(Hash::check('correct horse battery', User::where('username', 'parity-admin')->first()->password));
    }

    public function test_rejects_short_and_mismatched_passwords(): void
    {
        $original = User::where('username', 'parity-admin')->first()->password;

        $this->artisan('aldar:set-password', ['username' => 'parity-admin'])
            ->expectsQuestion('New password (min 12 characters)', 'short')
            ->assertExitCode(1);

        $this->artisan('aldar:set-password', ['username' => 'parity-admin'])
            ->expectsQuestion('New password (min 12 characters)', 'correct horse battery')
            ->expectsQuestion('Repeat the new password', 'something different')
            ->assertExitCode(1);

        $this->assertSame($original, User::where('username', 'parity-admin')->first()->password);
    }

    public function test_unknown_account_fails(): void
    {
        $this->artisan('aldar:set-password', ['username' => 'nobody-here'])->assertExitCode(1);
    }
}
```

- [ ] **Step 2: Write the failing e2e login test**

Append to `tests/e2e/specs/security/vendor-access.spec.ts`:

```ts
import { attemptLogin } from '../../support/auth';
import { appShell, artisan, sql } from '../../support/docker';
import { requireLocal } from '../../support/env';

test.describe('former vendor account', () => {
  test.beforeEach(() => {
    requireLocal('changes account state');
    const hash = appShell(`php -r 'echo password_hash("vendor-knows-this", PASSWORD_BCRYPT);'`);
    sql(`UPDATE users SET password = '${hash}', status = 'ACTIVE', disabled_at = NULL, deleted_at = NULL WHERE email = 'root@namaa-solutions.com'`);
    artisan('cache:clear');
  });

  test('a disabled account cannot log in even with the right password', async ({ page }) => {
    sql("UPDATE users SET disabled_at = NOW() WHERE email = 'root@namaa-solutions.com'");
    expect(await attemptLogin(page, 'developer', 'vendor-knows-this')).toBe(401);
  });

  test('after offboarding, the old password no longer works', async ({ page }) => {
    artisan('aldar:offboard-vendor');
    expect(await attemptLogin(page, 'developer', 'vendor-knows-this')).toBe(401);
  });
});
```

Move the three new `import` lines to the top of the file next to the existing `import { test, expect }`.

- [ ] **Step 3: Run the tests to verify they fail**

Run: `docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Feature/VendorOffboardingTest.php tests/Feature/SetUserPasswordCommandTest.php`
Expected: FAIL, `The command "aldar:offboard-vendor" does not exist.`

Run: `(cd tests/e2e && npx playwright test specs/security/vendor-access.spec.ts)`
Expected: the "disabled account" test FAILS with 200 (login succeeds).

- [ ] **Step 4: Refuse disabled accounts at login**

In `Modules/Cms/Http/Controllers/Auth/LoginController.php`, add after `username()`:

```php
    /**
     * Disabled or deleted accounts never match, whatever the password.
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), [
            'disabled_at' => null,
            'deleted_at'  => null,
        ]);
    }
```

- [ ] **Step 5: Create the commands**

`app/Console/Commands/OffboardVendor.php`:

```php
<?php

namespace App\Console\Commands;

use App\User;
use Bouncer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OffboardVendor extends Command
{
    protected $signature = 'aldar:offboard-vendor {--domain=namaa-solutions.com : Email domain of the former vendor}';

    protected $description = 'Disable every account of the former vendor and revoke its roles';

    public function handle(): int
    {
        $domain = ltrim((string) $this->option('domain'), '@');
        $users = User::where('email', 'like', '%@' . $domain)->get();

        if ($users->isEmpty()) {
            $this->info("No accounts found for @{$domain}.");
            return 0;
        }

        foreach ($users as $user) {
            Bouncer::sync($user)->roles([]);

            $user->forceFill([
                'password'       => Hash::make(Str::random(64)),
                'remember_token' => null,
                'status'         => 'DISABLED',
                'disabled_at'    => now(),
                'deleted_at'     => now(),
            ])->save();

            $this->line("Disabled #{$user->id} {$user->username} <{$user->email}>");
        }

        Bouncer::refresh();

        return 0;
    }
}
```

`app/Console/Commands/SetUserPassword.php`:

```php
<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetUserPassword extends Command
{
    protected $signature = 'aldar:set-password {username : Username or email of the account}';

    protected $description = 'Set a new password for an account; the value is prompted and never echoed';

    public function handle(): int
    {
        $identity = (string) $this->argument('username');
        $user = User::where('username', $identity)->orWhere('email', $identity)->first();

        if (! $user) {
            $this->error("No account found for {$identity}.");
            return 1;
        }

        $password = (string) $this->secret('New password (min 12 characters)');
        if (mb_strlen($password) < 12) {
            $this->error('The password must be at least 12 characters.');
            return 1;
        }

        if ($password !== (string) $this->secret('Repeat the new password')) {
            $this->error('The passwords do not match.');
            return 1;
        }

        $user->forceFill([
            'password'       => Hash::make($password),
            'remember_token' => null,
        ])->save();

        $this->info("Password updated for #{$user->id} {$user->username}.");

        return 0;
    }
}
```

`app/Console/Kernel.php` already loads `app/Console/Commands`, so the commands are registered automatically.

- [ ] **Step 6: Remove the vendor defaults from the seeder config**

In `Modules/Cms/Config/config.php`, replace the `'root'` and `'superadmin'` arrays with:

```php
    'root' => [
        'email'             => env('CMS_ROOT_SEED_EMAIL'),
        'username'          => 'developer',
        'type'              => 'ROOT',
        'verification_code' => 'VERIFIED',
        'status'            => 'ACTIVE',
        'password'          => \Hash::make(env('CMS_ROOT_SEED_PASSWORD', '')),
    ],
    'superadmin' => [
        'email'             => env('CMS_SUPERADMIN_SEED_EMAIL'),
        'username'          => 'superadmin',
        'type'              => 'ROOT',
        'verification_code' => 'VERIFIED',
        'status'            => 'ACTIVE',
        'password'          => \Hash::make(env('CMS_SUPERADMIN_SEED_PASSWORD', '')),
    ],
```

In `Modules/Cms/Database/Seeders/UsersTableSeeder.php`, make `createRootUser()` start with:

```php
        if (empty(config('cms.root.email'))) {
            return;
        }
```

- [ ] **Step 7: Run the tests to verify they pass**

Run: `docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Feature/VendorOffboardingTest.php tests/Feature/SetUserPasswordCommandTest.php`
Expected: `OK (6 tests, …)`.

Run: `(cd tests/e2e && npx playwright test specs/security/vendor-access.spec.ts specs/smoke)`
Expected: `4 passed` + `11 passed`. The smoke admin login proves active accounts still log in.

- [ ] **Step 8: Commit**

```bash
git add app/Console/Commands Modules/Cms/Http/Controllers/Auth/LoginController.php Modules/Cms/Config/config.php Modules/Cms/Database/Seeders/UsersTableSeeder.php tests/Feature/VendorOffboardingTest.php tests/Feature/SetUserPasswordCommandTest.php tests/e2e/specs/security/vendor-access.spec.ts
git commit -m "Add vendor offboarding and hidden-prompt password commands

Login now refuses disabled or deleted accounts, and the seeder config
no longer defaults to the former vendor's emails.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 9: Production patching toolkit

> The committed scripts supersede the listings in this task. Later review fixes changed them: the rollback hint on any failure after the backup (c878a9b), and in the final review wave a SHA-pinned deploy, `rollback.sh --reopens-public-seed <stamp>`, 403 probes for the `.htaccess` deny blocks, a random unknown image size, and `ROUTES_FILE` for local runs of `verify-production.sh`.

**Files:**
- Create: `scripts/server/db-dump.php`
- Create: `scripts/hotfix/deploy.sh`, `scripts/hotfix/rollback.sh`, `scripts/hotfix/verify-production.sh`, `scripts/hotfix/probe-client-ip.sh`, `scripts/hotfix/known-baseline-edits.txt`

**Interfaces:**
- Produces:
  - `scripts/hotfix/deploy.sh [--dry-run] <git-ref>`. It prints the file list and a drift report; without `--dry-run` it also prints a backup stamp (`YYYYmmdd-HHMMSS`).
  - `scripts/hotfix/rollback.sh --reopens-public-seed <stamp>` (refuses to run without the flag).
  - `scripts/hotfix/verify-production.sh [base-url]`, which exits non-zero on any failed check.
  - `scripts/hotfix/probe-client-ip.sh`.
  - `php scripts/server/db-dump.php <app-dir> <output.sql.gz>`, which prints `ok tables=<n> file=<path>`.

- [ ] **Step 1: Write `scripts/server/db-dump.php`**

```php
<?php
// Dumps the application database with the credentials Laravel itself resolves,
// so the password never passes through a shell, a terminal or a log.
// Usage (on the server): php db-dump.php <app-dir> <output.sql.gz>

[, $appDir, $output] = $argv + [null, null, null];
if (! $appDir || ! $output) {
    fwrite(STDERR, "usage: php db-dump.php <app-dir> <output.sql.gz>\n");
    exit(2);
}

require $appDir . '/vendor/autoload.php';
$app = require $appDir . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = config('database.connections.' . config('database.default'));

$command = sprintf(
    'set -o pipefail; mysqldump --single-transaction --quick --routines --triggers --events --no-tablespaces --default-character-set=utf8mb4 -h %s -u %s %s | gzip > %s',
    escapeshellarg($db['host']),
    escapeshellarg($db['username']),
    escapeshellarg($db['database']),
    escapeshellarg($output)
);

$process = proc_open(['/bin/bash', '-c', $command], [2 => ['pipe', 'w']], $pipes, null, array_merge(getenv(), ['MYSQL_PWD' => $db['password']]));
$errors = stream_get_contents($pipes[2]);
$code = proc_close($process);

if ($code !== 0) {
    fwrite(STDERR, $errors);
    exit($code);
}

$tables = (int) Illuminate\Support\Facades\DB::selectOne('SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = DATABASE()')->n;
$dumped = (int) trim((string) shell_exec('gunzip -c ' . escapeshellarg($output) . ' | grep -c "^CREATE TABLE"'));

if ($tables !== $dumped) {
    fwrite(STDERR, "table count mismatch: database={$tables} dump={$dumped}\n");
    exit(1);
}

echo "ok tables={$tables} file={$output}\n";
```

- [ ] **Step 2: Test the dump script locally**

The app image has no `mysqldump` (production does). Install the client in the running container only; the image is not changed:

```bash
docker compose exec -T app sh -c 'apt-get -o Acquire::Check-Valid-Until=false update >/dev/null && apt-get install -y --no-install-recommends mariadb-client >/dev/null'
docker compose exec -T app php scripts/server/db-dump.php /var/www/html /tmp/test-dump.sql.gz
```

Expected: `ok tables=56 file=/tmp/test-dump.sql.gz`.

- [ ] **Step 3: Write `scripts/hotfix/known-baseline-edits.txt`**

```
# Files the baseline commit changed on purpose relative to the server copy.
# deploy.sh skips the drift check for these paths.
.gitignore
app/Http/Middleware/RedirectToHttps.php
Modules/Cms/Config/config.php
Modules/Notification/Config/config.php
```

- [ ] **Step 4: Write `scripts/hotfix/deploy.sh`**

```bash
#!/usr/bin/env bash
# Patches the current production public_html in place with the application files
# that changed between the server snapshot (the repository's root commit) and <git-ref>.
# Usage: scripts/hotfix/deploy.sh [--dry-run] <git-ref>
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"

DRY_RUN=0
if [ "${1:-}" = "--dry-run" ]; then DRY_RUN=1; shift; fi
REF="${1:?usage: scripts/hotfix/deploy.sh [--dry-run] <git-ref>}"

REMOTE="${REMOTE:-codecamb}"
APP="domains/aldar-emlak.com/public_html"
PHP="/opt/alt/php74/usr/bin/php"
STAMP="$(date +%Y%m%d-%H%M%S)"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

PATHSPEC=(-- . ':!tests' ':!docs' ':!scripts' ':!docker' ':!docker-compose.yml' ':!.gitignore' ':!phpunit.xml')
ROOT_COMMIT="$(git rev-list --max-parents=0 "$REF")"

if git diff --name-only --diff-filter=DR "$ROOT_COMMIT" "$REF" "${PATHSPEC[@]}" | grep -q .; then
  echo "Deleted or renamed application files are not supported by the hotfix deploy:" >&2
  git diff --name-status --diff-filter=DR "$ROOT_COMMIT" "$REF" "${PATHSPEC[@]}" >&2
  exit 1
fi

git diff --name-only --diff-filter=AM "$ROOT_COMMIT" "$REF" "${PATHSPEC[@]}" > "$WORK/files.txt"
[ -s "$WORK/files.txt" ] || { echo "Nothing to deploy."; exit 0; }
echo "Files to deploy ($(wc -l < "$WORK/files.txt" | tr -d ' ')):"
sed 's/^/  /' "$WORK/files.txt"

echo "Drift check against the server..."
DRIFT=0
: > "$WORK/added.txt"
while IFS= read -r file; do
  if ! git cat-file -e "$ROOT_COMMIT:$file" 2>/dev/null; then
    echo "$file" >> "$WORK/added.txt"
    expected="absent"
    actual="$(ssh -n "$REMOTE" "test -e '$APP/$file' && echo present || echo absent")"
  elif grep -qxF "$file" scripts/hotfix/known-baseline-edits.txt; then
    continue
  else
    expected="$(git show "$ROOT_COMMIT:$file" | tr -d '\r' | shasum -a 256 | cut -d' ' -f1)"
    actual="$(ssh -n "$REMOTE" "tr -d '\r' < '$APP/$file' | sha256sum" | cut -d' ' -f1)"
  fi
  if [ "$expected" != "$actual" ]; then
    echo "  DRIFT: $file" >&2
    DRIFT=1
  fi
done < "$WORK/files.txt"
[ "$DRIFT" -eq 0 ] || { echo "Server files changed since the snapshot. Aborting." >&2; exit 1; }
echo "  no drift"

if [ "$DRY_RUN" -eq 1 ]; then
  echo "Dry run: nothing uploaded."
  exit 0
fi

echo "Backing up (stamp $STAMP)..."
ssh -n "$REMOTE" "mkdir -p ~/aldar-backup && chmod 700 ~/aldar-backup"
scp -q scripts/server/db-dump.php "$REMOTE:aldar-backup/db-dump.php"
ssh "$REMOTE" "cd $APP && tar --ignore-failed-read -czf ~/aldar-backup/hotfix-$STAMP.tar.gz -T - 2>/dev/null" < "$WORK/files.txt"
ssh "$REMOTE" "cat > ~/aldar-backup/hotfix-$STAMP.added" < "$WORK/added.txt"
ssh -n "$REMOTE" "$PHP ~/aldar-backup/db-dump.php ~/$APP ~/aldar-backup/hotfix-$STAMP.sql.gz"

echo "Uploading..."
tr '\n' '\0' < "$WORK/files.txt" | xargs -0 git archive --format=tar "$REF" -- | ssh "$REMOTE" "tar -xif - -C $APP"

echo "Clearing caches..."
ssh -n "$REMOTE" "cd $APP && $PHP artisan view:clear && $PHP artisan route:clear && $PHP artisan config:clear && $PHP artisan cache:clear"

echo "Verifying..."
if ! scripts/hotfix/verify-production.sh "https://aldar-emlak.com"; then
  echo "Verification FAILED. Roll back with: scripts/hotfix/rollback.sh $STAMP" >&2
  exit 1
fi
echo "Deployed $REF. Rollback: scripts/hotfix/rollback.sh $STAMP"
```

- [ ] **Step 5: Write `scripts/hotfix/rollback.sh`**

```bash
#!/usr/bin/env bash
# Restores the files replaced by a hotfix deploy and removes the files it added.
# The database dump from that deploy is kept but not restored.
# Usage: scripts/hotfix/rollback.sh <stamp>
set -euo pipefail
STAMP="${1:?usage: scripts/hotfix/rollback.sh <stamp>}"
REMOTE="${REMOTE:-codecamb}"
APP="domains/aldar-emlak.com/public_html"
PHP="/opt/alt/php74/usr/bin/php"

ssh -n "$REMOTE" "set -e
cd $APP
tar -xzf ~/aldar-backup/hotfix-$STAMP.tar.gz
while IFS= read -r f; do [ -n \"\$f\" ] && rm -f -- \"\$f\"; done < ~/aldar-backup/hotfix-$STAMP.added
$PHP artisan view:clear
$PHP artisan route:clear
$PHP artisan config:clear
$PHP artisan cache:clear"

echo "Rolled back hotfix $STAMP. DB dump kept at ~/aldar-backup/hotfix-$STAMP.sql.gz"
```

- [ ] **Step 6: Write `scripts/hotfix/verify-production.sh`**

```bash
#!/usr/bin/env bash
# Read-only checks that the hotfix is live on production.
# It never requests /seed or /migrate: on unpatched code those URLs run commands.
# Usage: scripts/hotfix/verify-production.sh [base-url]
set -uo pipefail
BASE="${1:-https://aldar-emlak.com}"
REMOTE="${REMOTE:-codecamb}"
APP="domains/aldar-emlak.com/public_html"
PHP="/opt/alt/php74/usr/bin/php"
FAIL=0

pass() { echo "ok    $1"; }
fail() { echo "FAIL  $1"; FAIL=1; }
status() { curl -s -o /dev/null -w '%{http_code}' -L --max-redirs 5 "$1"; }

ROUTES="$(ssh -n "$REMOTE" "cd $APP && $PHP artisan route:list --columns=method,uri,middleware" 2>&1)" || { echo "cannot read route list"; exit 1; }

if echo "$ROUTES" | grep -qE '\| (seed|migrate|update_currency|clear-cache) +\|'; then
  fail "public maintenance routes are still registered"
else
  pass "seed, migrate, update_currency, clear-cache are not public routes"
fi

for uri in admin/attachments/store admin/attachments/delete admin/tinymce/uploader admin/update-currency admin/clear-cache; do
  if echo "$ROUTES" | grep -E "\| $uri +\|" | grep -q staff; then pass "$uri requires staff"; else fail "$uri requires staff"; fi
done

for uri in contact-us/store contact-us/store-inner contact-us/subscribe; do
  if echo "$ROUTES" | grep -E "\| $uri +\|" | grep -q contact.guard; then pass "$uri is guarded"; else fail "$uri is guarded"; fi
done

for path in /en /ar /en/contact-us /ar/contact-us /en/articles; do
  code="$(status "$BASE$path")"; [ "$code" = 200 ] && pass "GET $path 200" || fail "GET $path ($code)"
done

code="$(status "$BASE/img/85x85/defaults/base.png")"; [ "$code" = 200 ] && pass "allowed image size 200" || fail "allowed image size ($code)"
code="$(status "$BASE/img/1234x987/defaults/base.png")"; [ "$code" = 404 ] && pass "unknown image size 404" || fail "unknown image size ($code)"

for worker in /service-worker.js /firebase-messaging-sw.js; do
  body="$(curl -s "$BASE$worker")"
  case "$body" in
    *binaa-prod*) fail "$worker still has the vendor Firebase config (purge the Hostinger CDN cache if the file on disk is new)" ;;
    *"registration.unregister()"*) pass "$worker unregisters itself" ;;
    *) fail "$worker unexpected content" ;;
  esac
done

code="$(curl -s -o /dev/null -w '%{http_code}' "$BASE/graph/.htaccess")"; [ "$code" != 200 ] && pass "graph/.htaccess not readable" || fail "graph/.htaccess is readable"

exit $FAIL
```

- [ ] **Step 7: Write `scripts/hotfix/probe-client-ip.sh`**

```bash
#!/usr/bin/env bash
# Shows which client address and scheme PHP sees on production behind Hostinger's CDN.
# Uploads a throwaway script with a random name, requests it once, then deletes it.
set -euo pipefail
REMOTE="${REMOTE:-codecamb}"
APP="domains/aldar-emlak.com/public_html"
NAME="ip-probe-$(openssl rand -hex 16).php"

ssh "$REMOTE" "cat > $APP/public/$NAME" <<'PHP'
<?php
header('Content-Type: text/plain');
foreach (['REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTPS', 'HTTP_X_FORWARDED_PROTO', 'SERVER_PORT'] as $key) {
    echo $key, '=', $_SERVER[$key] ?? '', "\n";
}
PHP
trap 'ssh -n "$REMOTE" "rm -f $APP/public/$NAME"' EXIT

echo "This machine's public IP: $(curl -s https://api.ipify.org)"
curl -s "https://aldar-emlak.com/$NAME"
```

- [ ] **Step 8: Check the scripts**

Run: `chmod +x scripts/hotfix/*.sh && bash -n scripts/hotfix/deploy.sh scripts/hotfix/rollback.sh scripts/hotfix/verify-production.sh scripts/hotfix/probe-client-ip.sh && echo syntax-ok`
Expected: `syntax-ok`.

Run: `scripts/hotfix/deploy.sh --dry-run hotfix/security`
This is read-only: it reads hashes of server files over SSH and uploads nothing.
Expected: the file list (application files from Tasks 2–8), `no drift`, `Dry run: nothing uploaded.`

- [ ] **Step 9: Commit**

```bash
git add scripts/server scripts/hotfix
git commit -m "Add hotfix deploy, verify, rollback and probe scripts

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 10: Full verification and pull request

**Files:** none new.

- [ ] **Step 1: Run the whole suite twice in a row**

PHPUnit 8.5 takes one path per invocation, so run the two directories separately:

```bash
docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Unit
docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Feature
(cd tests/e2e && npx playwright test) && (cd tests/e2e && npx playwright test)
```

Expected:
- PHPUnit `tests/Unit`: `OK (3 tests, …)` (ImageSizesConfig 1, FirebaseTls 1, ExampleTest 1).
- PHPUnit `tests/Feature`: `OK (7 tests, …)` (VendorOffboarding 3, SetUserPasswordCommand 4). 10 tests in total.
- Playwright: every spec passes on both runs (smoke 11, security 36; 47 in total).

- [ ] **Step 2: Check the diff for anything outside Phase H**

Run: `git diff --stat main...hotfix/security`
Expected: only the files listed in this plan's File Structure, the e2e lockfile and specs.

- [ ] **Step 3: Push and open the PR**

```bash
git push -u origin hotfix/security
gh pr create --base main --head hotfix/security --title "Phase H: urgent security hotfix and vendor offboarding" --body "$(cat <<'EOF'
Implements Phase H of docs/superpowers/specs/2026-09-14-aldar-security-hotfix-and-laravel-13-upgrade-design.md.

**Closes**
- H7: public /seed and /migrate (re-activated the vendor ROOT account)
- H8: unauthenticated attachment upload and delete
- H1: TinyMCE uploader could write executable PHP under public/graph
- H2, H3: public currency refresh and cache flush
- H4: unbounded image variant generation, including path aliases (`%2e` segments, `//`, backslashes, double-encoded escapes, `.cache/…` files, `?path=`) that each minted a new cache entry
- H5: link spam on the contact forms (honeypot + per-IP limit; drops and refusals are logged with route and IP only)
- H6: FCM TLS verification
- H9: unauthenticated mail header-injection scripts under public/modules (PHP there now returns 403)
- N1, N2, N6: vendor offboarding command, vendor seeder credentials out of config, vendor service workers retired
- Task 10a: `aldar:set-password --generate-to` and `scripts/server/rotate-db-password.php` rotate admin and DB passwords server-side (ruling R11), so Claude never reads, prints or types a secret value (N4, N5)
- Task 10b: `TrustProxies` trusts only `X-Forwarded-For` from a proxy, even when `$proxies` is set for Task 11's rollout — the forwarded host, scheme and port stay untrusted (ruling R13)

**Intentional output changes**
- Dashboard "update currency" is a POST form, styled the same.
- Contact forms gain a hidden honeypot field.
- Unknown image sizes return 404 unless already cached.
- Non-canonical image paths return 404.
- Any PHP file under /modules returns 403.

**Tests**
- Playwright: smoke (11) and security (36) specs, green twice in a row locally.
- PHPUnit: 10 command and source-guard tests.

**Rollout** follows Task 11 of docs/superpowers/plans/2026-09-14-phase-h-security-hotfix.md and needs owner approval at each production step.

🤖 Generated with [Claude Code](https://claude.com/claude-code)
EOF
)"
```

---

### Task 11: Production rollout (owner approval required at every step)

**Files:** possibly `app/Http/Middleware/TrustProxies.php` (Step 2). Local only, never committed: the user snapshots from Steps 3 and 9 (they contain emails).

All commands run from the repo root.

- [ ] **Step 1: Get explicit approval to touch production**

Ask the owner in chat: "Ready to roll out the hotfix to aldar-emlak.com now: probe the client IP, snapshot users and roles, back up, deploy the PR head, verify, offboard the vendor straight away, then rotate the admin passwords, the DB password and APP_KEY. Go?" Do not continue without a clear yes.

- [ ] **Step 2: Probe how the client IP reaches PHP**

Run: `scripts/hotfix/probe-client-ip.sh`

The script's "This machine's public IP" line now reads from `api64.ipify.org`, which returns whichever address family curl actually used — the site is reached over IPv6, so an IPv6 address here is normal, not a sign the probe is broken. The script also prints its own `REMOTE_ADDR equals this machine's public IP: yes/no` line; use that instead of eyeballing the two addresses (an `api.ipify.org`-style IPv4-only comparison would wrongly look like a mismatch for an IPv6 client that is in fact the real one).

Decide from the output:
- **`REMOTE_ADDR equals this machine's public IP: yes`:** no change; go to Step 3.
- **`REMOTE_ADDR equals this machine's public IP: no` and `HTTP_X_FORWARDED_FOR` starts with this machine's IP:** PHP sees the CDN. Only the client IP is needed; the forwarded-header restriction (Task 10b) is already committed and proven by `tests/Unit/TrustProxiesTest.php` — even with `$proxies` set, `X-Forwarded-Host`, `-Proto` and `-Port` stay untrusted, so this step only turns proxy trust on for the CDN's IP.
  1. In `app/Http/Middleware/TrustProxies.php`, change:

     ```php
         protected $proxies = '*';
     ```

  2. Re-run `docker compose exec -T app php vendor/phpunit/phpunit/phpunit tests/Unit/TrustProxiesTest.php` and `(cd tests/e2e && npx playwright test)` before committing. All must pass.
  3. Commit with message `Trust only X-Forwarded-For from Hostinger's CDN` (ending with the `Co-Authored-By` trailer), push to the PR branch, and wait for the PR to show the new head.
- **Anything else:** stop and report the output to the owner. Do not deploy H5 with an unknown client address.

- [ ] **Step 3: Snapshot users and role assignments (read-only)**

The output contains emails (PII). It goes to `.superpowers/`, which `.git/info/exclude` keeps out of git; never commit or paste it.

Plain `artisan tinker` fails on Hostinger with "Unable to create PsySH runtime directory … /run/user/<uid>"; pointing `XDG_RUNTIME_DIR` at a directory the account can write to (created with `mkdir -p` and locked down with `chmod 700`) fixes it.

```bash
SNAP=.superpowers/sdd/2026-09-14-phase-h-security-hotfix
cat > "$SNAP/snapshot.php" <<'PHP'
foreach (DB::table('users')->orderBy('id')->get() as $u) { echo "user | $u->id | $u->username | $u->email | $u->status | disabled_at=" . ($u->disabled_at ?: '-') . " | deleted_at=" . ($u->deleted_at ?: '-') . PHP_EOL; }
foreach (DB::table('perms_assigned_roles')->join('perms_roles', 'perms_roles.id', '=', 'perms_assigned_roles.role_id')->orderBy('perms_assigned_roles.id')->get(['perms_assigned_roles.id', 'perms_roles.name', 'entity_type', 'entity_id']) as $a) { echo "role | $a->id | $a->name | $a->entity_type #$a->entity_id" . PHP_EOL; }
$permRows = DB::table('perms_permissions')->orderBy('id')->get();
echo "permissions | " . $permRows->count() . " | " . hash('sha256', $permRows->toJson()) . PHP_EOL;
echo "abilities | " . DB::table('perms_abilities')->count() . PHP_EOL;
echo "roles | " . DB::table('perms_roles')->count() . PHP_EOL;
PHP
ssh codecamb 'mkdir -p $HOME/.psysh-runtime && chmod 700 $HOME/.psysh-runtime && cd domains/aldar-emlak.com/public_html && XDG_RUNTIME_DIR=$HOME/.psysh-runtime /opt/alt/php74/usr/bin/php artisan tinker' < "$SNAP/snapshot.php" | grep -E '^(user|role|permissions|abilities|roles) \|' > "$SNAP/users-before-deploy.txt"
grep -c '^user ' "$SNAP/users-before-deploy.txt"; grep -c '^role ' "$SNAP/users-before-deploy.txt"
grep '^permissions \|^abilities \|^roles ' "$SNAP/users-before-deploy.txt"
```

Expected: two non-zero counts, plus the `permissions`, `abilities` and `roles` lines. `DB::table` bypasses the SoftDeletes and Disabable scopes, so disabled and deleted accounts are listed too. Bouncer's ability grants (`perms_permissions`, joining `perms_abilities` to `perms_roles`/users) are captured by row count and a sha256 of the ordered rows, plus the `perms_abilities` and `perms_roles` counts, so the Step 9 audit catches any new ability grant even when the row count alone would not move.

- [ ] **Step 4: Dry run, then deploy the PR head SHA, then verify**

```bash
git fetch origin
SHA="$(gh pr view hotfix/security --json headRefOid -q .headRefOid)"
test "$SHA" = "$(git rev-parse origin/hotfix/security)" || { echo "SHA mismatch"; exit 1; }
echo "PR head $SHA"
scripts/hotfix/deploy.sh --dry-run "$SHA"
scripts/hotfix/deploy.sh "$SHA"
```

Expected:
- the dry run prints `Deploying <sha> at <sha>`, a file list that includes `public/modules/.htaccess`, and `no drift`;
- the deploy ends with `Deployed <sha> (<sha>). Backup stamp: <stamp>`, and every check prints `ok`, including `PHP under /modules and /graph refused (403, existing-file probe; graph/.htaccess is identical)` and `graph/.htaccess and modules/.htaccess are identical`.

Record the stamp. If a service-worker check reports `FAIL (CDN stale: origin copy is new, purge the Hostinger CDN cache)`, purge the CDN cache in hPanel (Websites → aldar-emlak.com → CDN → Purge/Flush cache), then re-run `scripts/hotfix/verify-production.sh`.

**If verification fails for any other reason,** stop and report to the owner. This is the only point where a rollback is safe, because the vendor has not been offboarded yet; with the owner's explicit decision run `scripts/hotfix/rollback.sh --reopens-public-seed <stamp>`.

- [ ] **Step 5: Offboard the vendor immediately**

Run straight after Step 4 passes, before any smoke test, so the vendor's ROOT account cannot create new admin users in the meantime:

```bash
ssh codecamb 'cd domains/aldar-emlak.com/public_html && /opt/alt/php74/usr/bin/php artisan aldar:offboard-vendor'
```

Expected: `Disabled #1 developer <root@namaa-solutions.com>`.

**From this point, fix forward.** Never run `rollback.sh` without an explicit owner decision: it needs `--reopens-public-seed`, because it restores the public `/seed` route and the vendor's seeder password.

- [ ] **Step 6: Rotate the admin passwords, hands-free (N5, ruling R11)**

Claude generates the new passwords server-side and never reads, prints or types them. For each of `aldar`, `aldar-emlak` and `growth`:

```bash
STAMP=$(date +%Y%m%d-%H%M%S)
ssh codecamb 'cd domains/aldar-emlak.com/public_html && /opt/alt/php74/usr/bin/php artisan aldar:set-password aldar --generate-to=$HOME/aldar-credentials/'"$STAMP"'.txt'
```

(The `--generate-to=` value uses `$HOME` inside the single-quoted remote command, expanded by the *remote* shell once ssh delivers it — Bash does not expand a bare `~` embedded after `--generate-to=`, which would otherwise create a literal `./~` directory instead of the operator's home directory. `$STAMP` is spliced in from the local shell via the adjacent double-quoted segment.)

Replace `aldar` with the account name each time (same `$STAMP`, so all three land in one file); each run prints `Password for #<id> <username> written to <file>.` — never the password itself. The owner reads the file themselves over SSH, e.g. `ssh codecamb "cat ~/aldar-credentials/${STAMP}.txt"`, and confirms they can log in with each new password.

- [ ] **Step 7: Rotate the DB password, hands-free (N4, ruling R11)**

Claude runs `scripts/server/rotate-db-password.php`, which generates the new password, writes it to a credentials file, backs up `.env`, and updates `.env` and the database — never printing, logging or returning the old or new password.

1. Upload the script and run it:

   ```bash
   ssh -n codecamb 'mkdir -p ~/aldar-backup ~/aldar-credentials && chmod 700 ~/aldar-backup ~/aldar-credentials'
   scp -q scripts/server/rotate-db-password.php codecamb:aldar-backup/rotate-db-password.php
   STAMP=$(date +%Y%m%d-%H%M%S)
   ssh codecamb 'cd domains/aldar-emlak.com/public_html && /opt/alt/php74/usr/bin/php ~/aldar-backup/rotate-db-password.php "$(pwd)" $HOME/aldar-credentials/'"$STAMP"'.txt'; echo "exit: $?"
   ```

   (Same `$HOME`-inside-single-quotes reasoning as Step 6: the credentials-file argument must be an absolute path with no literal `~`, and `$HOME` is left for the *remote* shell to expand. `"$(pwd)"` is likewise passed through literally so the remote shell expands it to `public_html`'s real, non-symlinked path — the script refuses to run if the app directory it resolves doesn't match this argument.)

2. Act on the exit code:
   - **0 (success).** One confirmation line was printed naming no secret; `.env` and the database password now match (the script itself re-verifies this — both the live `.env` content and a fresh DB connection — before printing it), and the pre-rotation `.env` is backed up next to the credentials file, under `~/aldar-credentials/env-before-db-rotation-*` (not `~/aldar-backup/`). Go to item 3.
   - **1 (refused, nothing changed).** Either the host refused `SET PASSWORD`, or a safety check failed before anything was touched (wrong working directory, cached config, an environment variable overriding `.env`, or a malformed `.env`); `.env` and the DB password are still the originals. This is not a blocking failure: add "Rotate the DB password (N4)" to the Step 12 hand-over list and continue to Step 8. (A safety check failing this early runs before the script writes its `.env` backup, so — unlike exit codes 0 and 2-4 below — there may be no `env-before-db-rotation-*` file to point the owner at.)
   - **2 (partial rotation — needs a human now).** The DB password changed but `.env` could not be rewritten to match, so the site is broken. Stop immediately and report to the owner; do not continue the rollout.
   - **3 (new password unverifiable).** Stop and report to the owner, pointing at `~/aldar-credentials/env-before-db-rotation-*` and `~/aldar-credentials/${STAMP}.txt` so they can recover manually.
   - **4 (unknown state — needs a human now).** The `SET PASSWORD` reply was lost (e.g. the connection dropped) and neither the old nor the new password currently works. Stop immediately; the new password is the last line of the credentials file and the script deliberately left `.env.rotating` in place next to `.env` as the only remaining record of what `.env` should say. Report both paths to the owner.
3. **Verify.** Run `scripts/hotfix/verify-production.sh`; all checks must print `ok` (the pages only render with a working DB connection).

- [ ] **Step 8: Rotate APP_KEY (N3), wipe sessions, remove stale `.env` copies**

Claude runs each command after approval. Only hashes are printed, never the key.

1. Record the hash of the current key line:

   ```bash
   ssh codecamb 'cd domains/aldar-emlak.com/public_html && grep "^APP_KEY=" .env | sha256sum'
   ```

2. Generate a new key:

   ```bash
   ssh codecamb 'cd domains/aldar-emlak.com/public_html && /opt/alt/php74/usr/bin/php artisan key:generate --force'
   ```

   Expected: `Application key set successfully.`
3. Re-run the command from item 1. The hash **must differ**. `key:generate` prints success even when it could not rewrite the line; if the hash is unchanged, stop and report.
4. Wipe every session (a `*` glob can exceed the argument limit):

   ```bash
   ssh codecamb 'cd domains/aldar-emlak.com/public_html && find storage/framework/sessions -type f ! -name .gitignore -delete && find storage/framework/sessions -type f ! -name .gitignore | wc -l'
   ```

   Expected: `0`.
5. List every stale `.env` copy, then delete exactly that list. Everything except `.env` and `.env.example` goes, including `.env.bak-*` and any `.env.production`:

   ```bash
   ssh codecamb 'cd domains/aldar-emlak.com/public_html && ls -la .env* && find . -maxdepth 1 -name ".env*" ! -name .env ! -name .env.example -print'
   ssh codecamb 'cd domains/aldar-emlak.com/public_html && find . -maxdepth 1 -name ".env*" ! -name .env ! -name .env.example -print -delete && ls -la .env*'
   ```

   Expected: the final listing shows only `.env` and `.env.example`.
6. Clear the config cache:

   ```bash
   ssh codecamb 'cd domains/aldar-emlak.com/public_html && /opt/alt/php74/usr/bin/php artisan config:clear'
   ```

7. Run `scripts/hotfix/verify-production.sh`; all `ok`.

- [ ] **Step 9: Audit users and role assignments against the snapshot**

```bash
SNAP=.superpowers/sdd/2026-09-14-phase-h-security-hotfix
ssh codecamb 'mkdir -p $HOME/.psysh-runtime && chmod 700 $HOME/.psysh-runtime && cd domains/aldar-emlak.com/public_html && XDG_RUNTIME_DIR=$HOME/.psysh-runtime /opt/alt/php74/usr/bin/php artisan tinker' < "$SNAP/snapshot.php" | grep -E '^(user|role|permissions|abilities|roles) \|' > "$SNAP/users-after-rotation.txt"
diff "$SNAP/users-before-deploy.txt" "$SNAP/users-after-rotation.txt"
```

Expected: the only differences are the `@namaa-solutions.com` rows (now `DISABLED` with `disabled_at` and `deleted_at` set) and the removal of their role assignments. The `permissions`, `abilities` and `roles` lines must match exactly: any change means a new Bouncer ability grant. Any new `user` id, new `role` row, or changed `permissions`/`abilities`/`roles` line is a finding: stop and report it to the owner before the smoke test.

- [ ] **Step 10: Check the contact-form logs for false positives**

Claude runs this automatically; it needs no admin login. Contact-form drops and limiter refusals are logged as `Contact form submission dropped by the honeypot` / `refused by the rate limiter` in `storage/logs/laravel-<date>.log`:

```bash
ssh codecamb 'cd domains/aldar-emlak.com/public_html && grep -h "Contact form submission" storage/logs/laravel-*.log | tail -n 20'
```

Skim the output for a plausible false positive (a real visitor dropped by the honeypot or the limiter). Report any to the owner; this does not block Step 11.

- [ ] **Step 11: Merge the PR**

After Step 9's audit shows only the expected differences and Step 10's log check raises no concern:

```bash
gh pr merge hotfix/security --merge --delete-branch
```

The owner smoke test that needs an admin login (Step 12) is not a precondition for this merge.

- [ ] **Step 12: Hand over the owner-only items**

Report these to the owner as still open:
- **Owner smoke test on production.** Not blocking; run at the owner's convenience after the merge:
  1. log in at https://aldar-emlak.com/en/admin with the new password;
  2. upload an image in a TinyMCE editor;
  3. upload an image in a project's media dropzone;
  4. upload an attachment on a content type that has one;
  5. click the dashboard "update currency" button;
  6. submit one contact form on the site (`contact-us/store`);
  7. subscribe with the footer form (`contact-us/subscribe`).

  If anything fails, report it — Claude can fix forward, but cannot itself perform these logged-in checks.
- **N8: git history rewrite.** Run it only now, after the deploy (command in the spec's Open items). `deploy.sh` drift-checks the server against the root commit, so it must not be rewritten before the hotfix is live.
- **N7:** confirm the two near-duplicate SSH keys and enable hPanel 2FA.
- **Other access paths the spec does not cover:**
  - Hostinger FTP accounts (`.ftpquota` sits in the web root);
  - hPanel account sharing and collaborators;
  - the hPanel cron job list (also confirm `schedule:run` is there, so scheduled currency updates keep working);
  - Remote MySQL allowed hosts;
  - the domain registrar and DNS;
  - control of the Gmail mailboxes behind admin accounts 8, 27 and 29, since password-reset mails go there.
- **Backup retention.** The server keeps `~/aldar-backup/hotfix-<stamp>.{tar.gz,sql.gz,added}` and, whenever `scripts/server/rotate-db-password.php` reached its `.env`-backup step before exiting — exit 0, exit 1 *after* the backup step (the `SET PASSWORD` failure case, as opposed to an earlier exit 1 that ran before any backup was written), 2, 3, 4, or 255 (an uncaught error) — `~/aldar-credentials/env-before-db-rotation-*` (next to the credentials file, not under `~/aldar-backup/`). Check with `ls ~/aldar-credentials/`. The `.sql.gz` dumps contain PII and the `env-before-db-rotation-*` copies contain the old secrets: delete both once the rollback window closes. Also delete `~/aldar-credentials/*.txt` once every new password has been read and confirmed.
- If Step 7 hit exit 1, **rotate the DB password (N4)** manually: in hPanel → Databases, change the password of user `u859703690_claaal` (letters, digits and `-_.!@%^*` only, no quotes, `$`, `#`, `\` or spaces), then set `DB_PASSWORD=` in `public_html/.env` to match via hPanel File Manager.
- Delete the local snapshots `.superpowers/sdd/2026-09-14-phase-h-security-hotfix/users-*.txt` after the audit is accepted.

### Rollout record (2026-09-15)

Facts only, no secrets:

- Deploy SHA `73cb9a1`, run by the owner from their own terminal, because Claude Code's auto-mode classifier blocks production deploys.
- Backup stamp `20260915-023119`.
- The vendor was offboarded.
- Admin and DB passwords were generated server-side into `~/aldar-credentials/20260915-*.txt`.
- `APP_KEY` was rotated and sessions were wiped.
- Stale `.env.bak-*` copies were deleted.
- The Step 9 audit showed only the expected vendor diff.
- PR #1 was merged as `c0efd8d`.
- The CDN was purged by the owner.
