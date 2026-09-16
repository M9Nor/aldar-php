# Phase 1: Laravel 13 / PHP 8.4 Upgrade Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Run aldar-emlak.com on Laravel 13 and PHP 8.4 locally with every feature and every page behaving as it did on Laravel 7.3. The parity suite is the proof.

**Architecture:**
- Jump directly from Laravel 7 to 13 on the existing application structure: Kernel classes, service providers, string `Controller@method` routes, old-style `bootstrap/app.php`.
- First, record structural baselines on Laravel 7 that the black-box suite cannot see: the route table, key config values, Glide cache paths and admin table values.
- Then swap the runtime and dependencies in one step and fix the app area by area. Each area is gated by its slice of the Phase 0 parity suite.

**Tech Stack:** PHP 8.4 (php:8.4-apache on Debian trixie), Laravel 13, nwidart/laravel-modules 13, silber/bouncer 1.0.4, yajra/laravel-datatables-oracle 13, mcamara/laravel-localization 2.4, league/glide 3 + glide-symfony 2.1, PHPUnit 12, Rector 2 + rector-laravel, Larastan 3, Playwright parity suite (`tests/e2e`).

**Spec:** `docs/superpowers/specs/2026-09-14-aldar-security-hotfix-and-laravel-13-upgrade-design.md` (section "Phase 1 — Direct jump to Laravel 13 and PHP 8.4").

**Research used while planning** (git-ignored, under `.superpowers/research/phase-1/`):
- `codebase-inventory.md`
- `package-compat.md`
- `upgrade-guides.md`

## Global Constraints

**Structure and dependencies**
- **Keep the existing application structure.** That means `app/Http/Kernel.php`, `app/Console/Kernel.php`, the providers, and string `'Controller@method'` routes (459 of them) with namespace prefixes kept in the route service providers. `bootstrap/app.php`, `public/index.php` and `artisan` stay in the Laravel 7 style.
- **No asset rebuild.** Compiled CSS/JS in `public/` stays byte-identical. Mix configs and front-end libraries stay untouched.
- **Dependency matrix (spec):**
  - php ^8.4
  - laravel/framework ^13.0
  - nwidart/laravel-modules ^13.0
  - silber/bouncer ^1.0.4
  - yajra/laravel-datatables-oracle ^13.0
  - mcamara/laravel-localization ^2.4
  - astrotomic/laravel-translatable ^11.17
  - league/glide ^3.0 with league/glide-symfony ^2.1 (not glide 4)
  - laravel/ui ^4.6
  - laravel/tinker ^3.0
  - guzzlehttp/guzzle ^7
  - **Removed:** doctrine/dbal, fideloper/proxy, fruitcake/laravel-cors, caouecs/laravel-lang.
  - **Dev:**
    - fakerphp/faker ^1.24
    - phpunit ^12
    - collision ^8
    - mockery ^1.6
    - laravel-debugbar ^4.4
    - rector/rector with driftingly/rector-laravel ^2.6
    - larastan/larastan ^3.12

**Config**
- **Config values stay the same.** Keep the app's own `config/*.php` files, and never re-sync them from the Laravel 13 skeleton. Add only keys that are required.
- **Session `serialization` stays `php`.**
- **`Paginator::useBootstrapFour()`** is called, so `->links()` markup matches Laravel 7.

**Parity and data**
- **Parity is 1:1.** Implementers never run `UPDATE_PARITY=1` and never edit a snapshot.
  - A parity difference is either fixed in application code, or reported with its diff so the controller can rule on it.
  - Every accepted difference is listed with its reason in the PR.
- **No PII in git.** DB dumps, lead data, logs and `.env` files stay out. Admin tables that show customer data are recorded as structure only.
- **Tests that write data run only against the local Docker stack.** Production is never contacted in Phase 1.
- **Frozen test toolchain.** During Phase 1:
  - install `tests/e2e` with `npm ci`, and do not bump `@playwright/test` or its Chromium;
  - keep the `mariadb:11.8` image;
  - run the visual spec on the same macOS machine the `darwin` baselines came from.

  A new browser or database changes serialisation, fonts and tie order for reasons unrelated to the app.
- **The gate is `npm test`.** It runs both Playwright projects: parity plus the Phase H `chromium` specs, which hold the image whitelist, lead storage and login checks. Every gate run starts from the global-setup DB reset. Never gate on a `SKIP_DB_RESET=1` run.

**Delivery**
- **Branch:** `upgrade/laravel-13`, cut from `main` after the Phase 0 PR is merged. It lands on `main` through a PR.
- **Exit criteria:**
  - the parity suite is green, or every diff is accepted and documented in the PR;
  - no PHP deprecation entries are logged during a full parity run;
  - the owner does a manual smoke test on the local environment.
- **Commit trailer:** every commit ends with a `Co-Authored-By: Claude <model> <noreply@anthropic.com>` line naming the model that wrote it.

---

## Working environment

- **Location.** Work happens in place in `/Users/mohammedelkasim/Desktop/aldar-php`, the checkout the Docker stack bind-mounts. The site runs at http://localhost:8080, MariaDB on 3307, and Mailpit on 8025.
- **Runtime switch.** After Task 2 the `app` container runs PHP 8.4 (image `aldar-php84`) and `vendor/` holds Laravel 13.
- **Getting Laravel 7 back** (only when a diff needs a side-by-side look; switch back afterwards the same way):

```bash
git stash --include-untracked   # only if the tree is dirty
git switch main
docker compose build app && docker compose up -d app
docker compose exec -T app composer install --no-interaction
docker compose exec -T app php artisan optimize:clear
```

- **Commands inside the app container:** `docker compose exec -T app <cmd>`.
- **PHPUnit:** `docker compose exec -T app php vendor/bin/phpunit`. If `vendor/bin` lost its exec bit, use `php vendor/phpunit/phpunit/phpunit`.
- **Parity:** `(cd tests/e2e && npm run parity)` runs the full suite in about 8 minutes and resets the DB first. Focused runs use `(cd tests/e2e && npx playwright test specs/parity/<file>.spec.ts)`.
- **Phase H specs:** `(cd tests/e2e && npx playwright test --project=chromium)`, 47 tests.

### Census command

Tasks 2, 4 and 5–8 use this command. It records how many tests in each spec file pass. It is not a gate by itself.

```bash
cd tests/e2e
npx playwright test --project=parity --reporter=json > test-results/parity-census.json 2>/dev/null
node -e '
const r = require("./test-results/parity-census.json"); const c = {};
const walk = s => { (s.suites || []).forEach(walk); (s.specs || []).forEach(sp => {
  const f = sp.file; c[f] = c[f] || { passed: 0, failed: 0, failing: [] };
  for (const t of sp.tests) { const ok = t.results.some(x => x.status === "passed" || x.status === "expected");
    if (ok) c[f].passed++; else { c[f].failed++; if (c[f].failing.length < 15) c[f].failing.push(sp.title); } } }); };
r.suites.forEach(walk); console.log(JSON.stringify(c, null, 2));'
cd ../..
```

### How parity differences are handled (Tasks 5–9)

For every failing parity test, open the diff: `tests/e2e/test-results/**/*-actual.*` next to the snapshot. Then classify it:

1. **Regression.** Laravel 13, PHP 8.4 or a package changed behaviour. Fix it in application code or config, so the output matches Laravel 7 again.
2. **Framework-owned markup or data that no user sees.** For example, an extra key in a DataTables JSON row. Report it as DONE_WITH_CONCERNS, quoting the smallest diff excerpt. Do not update the snapshot; the controller rules.
3. **PHP 8 sort stability.** PHP 8's `sort`/`usort` are stable, and PHP 7.4's were not, so items with equal sort keys can come out in a different order. Report it with evidence showing the keys tie. The controller rules.
4. **A framework-wide mechanical markup change.** For example, `csrf_field()` gained `autocomplete="off"` after Laravel 7, which touches every page with a form. Report it with the vendor source line.
   - If the controller accepts it, the acceptance lands in its own commit: `UPDATE_PARITY=1`, run by the controller or on the controller's explicit instruction.
   - That commit's snapshot diff must contain nothing else. Check with `git diff -U0 tests/e2e/snapshots | grep '^[+-][^+-]' | grep -v '<the accepted change>'`, which must print nothing.
   - Review the remaining diffs only after that commit.

A `Unstable DataTables walk` failure is first treated as test instability: Laravel 13 SQL can shift MariaDB tie order across OFFSET pages. Report it. The controller may rule to add an `order` by the id column to the walk URL in the spec.

Never add a normaliser mask. Never widen a tolerance. Never change a spec's assertions.

---

## File map

| Path | Task | Responsibility |
|---|---|---|
| `scripts/upgrade/structure-snapshot.php` | 1 | Prints the route table, selected config values or Glide cache paths as sorted JSON; runs on Laravel 7 and 13 |
| `scripts/upgrade/check-structure.sh` | 1 | Diffs the live app against `tests/upgrade/*.json` |
| `tests/upgrade/{routes,config,glide-cache-paths}.json` | 1 | Laravel 7 structural baselines |
| `tests/e2e/specs/parity/admin-table-values.spec.ts` + snapshots | 1 | Laravel 7 row values of four public admin tables |
| `docker/php/Dockerfile`, `docker-compose.yml` | 2 | PHP 8.4 image |
| `composer.json`, `composer.lock` | 2 | Laravel 13 dependency set |
| `config/app.php`, `config/session.php`, `config/logging.php` | 2 | Required Laravel 13 keys only |
| `app/Http/Middleware/{TrustProxies,VerifyCsrfToken,CheckForMaintenanceMode}.php` | 2 | Framework base classes |
| `app/Providers/AppServiceProvider.php`, `Modules/Frontend/Providers/FrontendServiceProvider.php` | 2 | Carbon 3 and pagination |
| `Modules/*/Providers/*ServiceProvider.php` (`registerFactories`) | 2 | Legacy factory loader removed in Laravel 8 |
| `phpunit.xml`, `tests/**` | 3 | PHPUnit 12 |
| `rector.php` | 4 | Curated deprecation rules |
| `Modules/Cms/Http/Controllers/ImageController.php`, `Modules/Cms/Providers/ImageServiceProvider.php` | 5 | Glide 3 on Flysystem 3 |
| `Modules/Cms/Http/Controllers/Auth/*`, `Modules/Permissions/**` | 6 | laravel/ui 4, Bouncer 1.0.4 |
| Admin controllers using `DataTables::of` | 7 | yajra 13 |
| Views, `Modules/Frontend/**`, `DB::raw` call sites | 8 | Public pages, localization, database layer |
| `phpstan.neon`, `phpstan-baseline.neon`, `tests/e2e/PARITY.md` | 9 | Larastan, deprecation gate, runbook |

---

### Task 1: Laravel 7 structural baselines

Everything in this task runs on the unchanged Laravel 7 app (PHP 7.4 container). It must be committed before Task 2 touches the runtime.

**Files:**
- Create: `scripts/upgrade/structure-snapshot.php`
- Create: `scripts/upgrade/check-structure.sh`
- Create: `tests/upgrade/routes.json`, `tests/upgrade/config.json`, `tests/upgrade/glide-cache-paths.json` (generated)
- Create: `tests/e2e/specs/parity/admin-table-values.spec.ts`
- Create: `tests/e2e/snapshots/parity/admin-table-values.spec.ts/*.json` (4 generated)

**Interfaces:**
- **Consumes:**
  - `loginAs(page, 'parity-superadmin')` (`support/auth.ts`);
  - `AJAX_HEADERS` (`support/csrf.ts`);
  - `requireLocal` and `BASE_URL` (`support/env.ts`);
  - `maskVolatileValues(html, baseUrl)` (`parity/normalize.ts`).
- **Produces:**
  - `php scripts/upgrade/structure-snapshot.php <routes|config|glide>`, which prints JSON to stdout;
  - `scripts/upgrade/check-structure.sh`, which exits 0 when the live app matches all three baselines and 1 with a unified diff otherwise.

- [ ] **Step 1: Confirm the branch and the Laravel 7 stack**

The controller creates `upgrade/laravel-13` from `main` (Phase 0 merged) and commits this plan on it.

```bash
git branch --show-current
docker compose ps --format '{{.Service}} {{.State}}'
docker compose exec -T app php -v | head -1
```

Expected: `upgrade/laravel-13`; `app running`, `db running`, `mailpit running`; `PHP 7.4.x`.

- [ ] **Step 2: Write the snapshot script**

`scripts/upgrade/structure-snapshot.php`:

```php
<?php
/**
 * Structural baseline for the Laravel 7 -> 13 upgrade. Runs unchanged on both versions.
 *
 *   php scripts/upgrade/structure-snapshot.php routes   every route: methods, uri, name, action, route middleware
 *   php scripts/upgrade/structure-snapshot.php config   selected non-secret config values, paths relative to the app root
 *   php scripts/upgrade/structure-snapshot.php glide    Glide cache paths for fixed inputs (proves cached images stay valid)
 *
 * Output is sorted, pretty-printed JSON on stdout. Never add secrets (keys, passwords, tokens) to the config list.
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$base = rtrim(base_path(), '/');
$relative = function ($value) use (&$relative, $base) {
    if (is_array($value)) {
        return array_map($relative, $value);
    }
    return is_string($value) ? str_replace($base, '{base}', $value) : $value;
};

switch ($argv[1] ?? '') {
    case 'routes':
        $out = [];
        foreach ($app['router']->getRoutes() as $route) {
            $out[] = [
                'methods'    => array_values(array_diff($route->methods(), ['HEAD'])),
                'uri'        => $route->uri(),
                'name'       => $route->getName(),
                'action'     => ltrim($route->getActionName(), '\\'),
                'middleware' => array_values(array_map('strval', $route->middleware())),
            ];
        }
        usort($out, function ($a, $b) {
            return [$a['uri'], implode('|', $a['methods'])] <=> [$b['uri'], implode('|', $b['methods'])];
        });
        break;

    case 'config':
        $keys = [
            'app.locale', 'app.fallback_locale', 'app.timezone', 'app.cipher',
            'auth.defaults.guard', 'auth.providers.users.model',
            'cache.default', 'cache.prefix',
            'database.default',
            'filesystems.default', 'filesystems.disks.public.root', 'filesystems.disks.graph.root',
            'hashing.driver', 'hashing.bcrypt.rounds',
            'laravellocalization.hideDefaultLocaleInURL', 'laravellocalization.useAcceptLanguageHeader',
            'modules.activators.file.statuses-file',
            'session.driver', 'session.cookie', 'session.lifetime', 'session.path', 'session.domain',
            'session.secure', 'session.http_only', 'session.same_site', 'session.serialization',
            'translatable.locales', 'translatable.fallback_locale',
        ];
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = $relative(config($key));
        }
        $out['laravellocalization.supportedLocales(keys)'] = array_keys(config('laravellocalization.supportedLocales'));
        ksort($out);
        break;

    case 'glide':
        $server = $app->make(League\Glide\Server::class);
        $samples = [
            ['articles/parity-sample.png', ['w' => '1000', 'h' => '750']],
            ['articles/parity-sample.png', ['h' => '400']],
            ['projects/parity-sample.jpg', ['w' => '360', 'h' => '240']],
        ];
        $out = [];
        foreach ($samples as [$path, $params]) {
            $out[] = ['path' => $path, 'params' => $params, 'cachePath' => $server->getCachePath($path, $params)];
        }
        break;

    default:
        fwrite(STDERR, "usage: php scripts/upgrade/structure-snapshot.php <routes|config|glide>\n");
        exit(64);
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";
```

- [ ] **Step 3: Write the check script**

`scripts/upgrade/check-structure.sh`:

```bash
#!/usr/bin/env bash
# Compares the running app's route table, config values and Glide cache paths with the
# Laravel 7 baselines in tests/upgrade/. Exit 0 = identical, 1 = differences (printed as a diff).
set -uo pipefail
cd "$(dirname "$0")/../.."

status=0
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
for section in routes config glide; do
  file="tests/upgrade/${section/glide/glide-cache-paths}.json"
  docker compose exec -T app php scripts/upgrade/structure-snapshot.php "$section" > "$tmp/$section.json" || { echo "snapshot $section failed" >&2; exit 2; }
  if ! diff -u "$file" "$tmp/$section.json"; then status=1; fi
done
[ "$status" -eq 0 ] && echo "structure matches tests/upgrade/"
exit "$status"
```

Run: `chmod +x scripts/upgrade/check-structure.sh`

- [ ] **Step 4: Record the baselines and prove they are deterministic**

```bash
mkdir -p tests/upgrade
for s in routes config; do docker compose exec -T app php scripts/upgrade/structure-snapshot.php $s > tests/upgrade/$s.json; done
docker compose exec -T app php scripts/upgrade/structure-snapshot.php glide > tests/upgrade/glide-cache-paths.json
scripts/upgrade/check-structure.sh
node -e 'console.log(require("./tests/upgrade/routes.json").length)'
grep -c '"cachePath"' tests/upgrade/glide-cache-paths.json
```

Expected:
- `structure matches tests/upgrade/`;
- a route count above 459, because the 459 string routes plus closure and `Auth::routes()` routes are included;
- `3`.

Also run `grep -iE 'password|secret|key"|token' tests/upgrade/config.json`. Expected: only `"app.cipher"`. No secret values.

- [ ] **Step 5: Write the admin table values spec (Laravel 7 row values)**

Phase 0 records only the shape of admin tables. This spec also records the row VALUES of four public, non-personal tables, so that a yajra 13 change in escaping or column rendering shows up. The configs table is deliberately left out, because it may hold API keys.

`tests/e2e/specs/parity/admin-table-values.spec.ts`:

```ts
import { test, expect, Page } from '@playwright/test';
import { loginAs } from '../../support/auth';
import { AJAX_HEADERS } from '../../support/csrf';
import { BASE_URL, requireLocal } from '../../support/env';
import { maskVolatileValues } from '../../parity/normalize';

// Row values of public, non-personal admin tables (Phase 1 addition, recorded on Laravel 7).
// Rows are fetched page by page and sorted by id, so query order does not matter.
const TABLES: Record<string, string> = {
  countries: '/en/admin/countries',
  cities: '/en/admin/cities',
  areas: '/en/admin/areas',
  'contents-faqs': '/en/admin/contents/faqs',
};

let page: Page;
test.beforeAll(async ({ browser }) => {
  requireLocal('uses the local parity accounts');
  page = await (await browser.newContext()).newPage();
  await loginAs(page, 'parity-superadmin');
});
test.afterAll(async () => page?.context().close());

for (const [name, url] of Object.entries(TABLES)) {
  test(`admin table values ${name}`, async () => {
    const tableResponse = page.waitForResponse(async response => {
      if (!['xhr', 'fetch'].includes(response.request().resourceType())) return false;
      const body = await response.json().catch(() => null);
      return body !== null && typeof body === 'object' && 'draw' in body;
    }, { timeout: 20_000 });
    expect((await page.goto(url))?.status()).toBe(200);
    const first = await tableResponse;
    const total = ((await first.json()) as { recordsFiltered: number }).recordsFiltered;
    const pageLength = Number(new URL(first.url()).searchParams.get('length')) || 50;

    const rows: Array<Record<string, unknown>> = [];
    for (let start = 0; rows.length < total; start += pageLength) {
      const pageUrl = new URL(first.url());
      pageUrl.searchParams.set('start', String(start));
      pageUrl.searchParams.set('length', String(pageLength));
      const json = await (await page.request.get(pageUrl.toString(), { headers: AJAX_HEADERS })).json();
      if ('error' in json) throw new Error(`DataTables error while walking ${name} at start=${start}`);
      if (json.data.length === 0) break;
      rows.push(...json.data);
    }
    expect(new Set(rows.map(row => String(row.id))).size, `${name}: distinct ids`).toBe(total);
    rows.sort((a, b) => Number(a.id) - Number(b.id));

    const body = maskVolatileValues(JSON.stringify(rows, null, 2), BASE_URL);
    expect(body).toMatchSnapshot(`${name}.json`);
  });
}
```

- [ ] **Step 6: Record and verify the table values on Laravel 7**

```bash
(cd tests/e2e && npx playwright test specs/parity/admin-table-values.spec.ts)
(cd tests/e2e && UPDATE_PARITY=1 npx playwright test specs/parity/admin-table-values.spec.ts)
(cd tests/e2e && npx playwright test specs/parity/admin-table-values.spec.ts)
grep -rhoE '[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}' tests/e2e/snapshots/parity/admin-table-values.spec.ts | sort -u
```

Expected:
1. The first run: `4 failed` (`A snapshot doesn't exist`).
2. The recording run: `4 passed`.
3. The rerun: `4 passed`.
4. The email grep: only `@aldar-emlak.com` addresses, or no output.

If any snapshot holds a secret-looking value (an API key or a token), stop and report NEEDS_CONTEXT without quoting it.

- [ ] **Step 7: Commit**

```bash
git add scripts/upgrade tests/upgrade tests/e2e/specs/parity/admin-table-values.spec.ts tests/e2e/snapshots/parity/admin-table-values.spec.ts
git commit -m "Record Laravel 7 structural baselines for the Laravel 13 upgrade"
```

---

### Task 2: PHP 8.4 image, Laravel 13 dependencies, and a booting app

**Files:**
- Modify: `docker/php/Dockerfile`, `docker-compose.yml`
- Modify: `composer.json`; regenerate `composer.lock`
- Modify:
  - `config/app.php` (providers list)
  - `config/session.php` (`serialization`)
  - `config/logging.php` (deprecations channel)
- Modify:
  - `app/Http/Middleware/TrustProxies.php`
  - `app/Http/Middleware/VerifyCsrfToken.php`
  - `app/Http/Middleware/CheckForMaintenanceMode.php` (only if its base class is gone)
- Modify: `app/Providers/AppServiceProvider.php`, `Modules/Frontend/Providers/FrontendServiceProvider.php`
- Modify: the `registerFactories()` method in `Modules/{Backend,Cms,Frontend,Notification,Permissions}/Providers/*ServiceProvider.php`
- Modify: `tests/upgrade/config.json` (one accepted change, see Step 9)

**Interfaces:**
- **Consumes:** `scripts/upgrade/check-structure.sh` (Task 1).
- **Produces:**
  - a PHP 8.4 `app` container running Laravel 13;
  - `storage/logs/deprecations.log`, written when `.env` has `LOG_DEPRECATIONS_CHANNEL=deprecations`;
  - the first parity census, in the task report.

- [ ] **Step 1: Replace the Dockerfile**

`docker/php/Dockerfile`:

```dockerfile
# Local mirror of the upgraded stack: PHP 8.4 on Apache (Debian trixie) serving Laravel 13.
FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
        libzip-dev libicu-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli gd zip intl bcmath exif opcache \
    && a2enmod rewrite expires headers \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
COPY apache-aldar.conf /etc/apache2/conf-available/aldar.conf
RUN a2enconf aldar

COPY php.ini /usr/local/etc/php/conf.d/zz-aldar.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
```

In `docker-compose.yml`, change `image: aldar-php74` to `image: aldar-php84`.

```bash
docker compose build app && docker compose up -d app
docker compose exec -T app php -v | head -1
docker compose exec -T app php -m | grep -ciE '^(gd|pdo_mysql|intl|zip|bcmath|exif|zend opcache)$'
```

Expected: `PHP 8.4.x (cli)` and `7`.

- [ ] **Step 2: Replace composer.json**

`composer.json`:

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "keywords": [
        "framework",
        "laravel"
    ],
    "license": "MIT",
    "require": {
        "php": "^8.4",
        "astrotomic/laravel-translatable": "^11.17",
        "guzzlehttp/guzzle": "^7.9",
        "laravel/framework": "^13.0",
        "laravel/tinker": "^3.0",
        "laravel/ui": "^4.6",
        "league/glide": "^3.0",
        "league/glide-symfony": "^2.1",
        "mcamara/laravel-localization": "^2.4",
        "nwidart/laravel-modules": "^13.0",
        "silber/bouncer": "^1.0.4",
        "yajra/laravel-datatables-oracle": "^13.0"
    },
    "require-dev": {
        "barryvdh/laravel-debugbar": "^4.4",
        "driftingly/rector-laravel": "^2.6",
        "fakerphp/faker": "^1.24",
        "larastan/larastan": "^3.12",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "phpunit/phpunit": "^12.5",
        "rector/rector": "^2.6"
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "wikimedia/composer-merge-plugin": true
        }
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Modules\\": "Modules/"
        },
        "classmap": [
            "database/seeds",
            "database/factories"
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true,
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ]
    }
}
```

- [ ] **Step 3: Resolve the dependencies without running app scripts**

```bash
rm -f bootstrap/cache/*.php
docker compose exec -T -e COMPOSER_MEMORY_LIMIT=-1 app composer update --no-interaction --no-scripts
docker compose exec -T app composer show laravel/framework nwidart/laravel-modules silber/bouncer yajra/laravel-datatables-oracle mcamara/laravel-localization league/glide | grep -E '^(name|versions)'
```

Expected: the update finishes with no conflict, and the installed versions are 13.x, 13.x, v1.0.4, v13.x, v2.4.x and 3.x.

If Composer reports a conflict, do not loosen a constraint below the spec's matrix. Report BLOCKED with the full conflict text.

- [ ] **Step 4: Apply the known boot fixes**

**a. `app/Http/Middleware/TrustProxies.php`.** fideloper/proxy is removed. Keep the Phase H behaviour: only `X-Forwarded-For` is trusted.

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR;

    /**
     * Only the client address is taken from proxy headers. Forwarded host, scheme and
     * port stay untrusted even when $proxies is set, so clients cannot spoof them past
     * the CDN.
     */
    protected function getTrustedHeaderNames()
    {
        return Request::HEADER_X_FORWARDED_FOR;
    }
}
```

**b. `app/Http/Middleware/VerifyCsrfToken.php`.** Change only the import, so that the class extends the Laravel 13 name:

```php
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;
```

Keep the class body (`$addHttpCookie`, `$except`) as it is. If the file imports the base class under a different alias, keep that alias name and point it at `PreventRequestForgery`.

**c. `app/Http/Middleware/CheckForMaintenanceMode.php`.** Run:
`ls vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ | grep -E 'CheckForMaintenanceMode|PreventRequestsDuringMaintenance'`
If `CheckForMaintenanceMode.php` is missing, change the import to:

```php
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
```

Keep the class name `App\Http\Middleware\CheckForMaintenanceMode`, so `app/Http/Kernel.php` does not change.

**d. Carbon 3 removed `setUTF8`.** In Carbon 2 it only affected `formatLocalized`, which the app never calls.
- Delete the line `Carbon::setUTF8(true);` from `app/Providers/AppServiceProvider.php` and from `Modules/Frontend/Providers/FrontendServiceProvider.php`.
- If `use Carbon\Carbon;` is then unused in FrontendServiceProvider, leave the import; it is harmless.

**e. Pagination markup.** In `app/Providers/AppServiceProvider.php`:
- add `use Illuminate\Pagination\Paginator;`;
- add `Paginator::useBootstrapFour();` as the first line of `boot()`.

Laravel 7's default `->links()` view was `pagination::bootstrap-4`, and the published copy lives in `resources/views/vendor/pagination/bootstrap-4.blade.php`.

**f. `config/app.php` framework providers.** Laravel 13 needs framework providers that did not exist in 7. Replace the whole `'providers' => [ ... ],` array with the framework default list merged with the app's own providers, keeping their order and the commented-out Broadcast line:

```php
    'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),
```

Check the original array first. If it lists any package or app provider not shown above, keep it in the merge list.

**g. `config/session.php`.** After the `'cookie' => env(...)` entry, add:

```php

    /*
    | Laravel 13 option. Kept at "php" so Laravel 7 session files stay readable; Phase 2 moves to "json".
    */
    'serialization' => 'php',
```

**h. `config/logging.php`.**
- After `'default' => env('LOG_CHANNEL', 'stack'),`, add:

```php

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],
```

- Inside `'channels' => [`, add:

```php
        'deprecations' => [
            'driver' => 'single',
            'path' => storage_path('logs/deprecations.log'),
            'level' => 'debug',
        ],
```

- Add `LOG_DEPRECATIONS_CHANNEL=deprecations` to the local `.env`. The file is git-ignored and the line is not a secret.

**i. Legacy factory loader.** Every module provider's `registerFactories()` calls `app(Factory::class)->load(...)` in non-production console runs. Laravel 8 removed `Illuminate\Database\Eloquent\Factory`, so every local `artisan` command would fail.
- In each of the five module service providers, make this the first statement of `registerFactories()`:

```php
        if (! class_exists(Factory::class)) {
            return; // Laravel 8 removed the legacy factory loader; these modules define no class-based factories.
        }
```

- `Factory` is already imported as `Illuminate\Database\Eloquent\Factory` in those files. `class_exists` on the imported name does not autoload-fail.

- [ ] **Step 5: Boot**

First check the middleware alias property the Kernel uses:
`grep -n 'routeMiddleware\|middlewareAliases' vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php`.
If `$routeMiddleware` is no longer read, rename the property in `app/Http/Kernel.php` to `$middlewareAliases`, keeping its contents unchanged.

```bash
docker compose exec -T app composer dump-autoload
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan about --only=environment
docker compose exec -T app php artisan route:list --json | node -e 'let s="";process.stdin.on("data",d=>s+=d).on("end",()=>console.log(JSON.parse(s).length))'
for u in /en /ar /en/authenticate/login /en/articles /en/admin; do curl -s -o /dev/null -w "%{http_code} $u\n" "http://localhost:8080$u"; done
docker compose exec -T app php -r '
require "vendor/autoload.php"; $app = require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (require "vendor/composer/autoload_classmap.php" as $class => $file) {
  if (preg_match("#/(app|Modules)/#", $file) && !str_contains($file, "/Includes/date/") && !str_contains($file, "/Resources/")) {
    class_exists($class) || interface_exists($class) || trait_exists($class);
  }
}
echo "all app classes load\n";'
```

The last command loads every class under `app/` and `Modules/`. That surfaces "Declaration must be compatible" fatals in app classes extending changed package classes, which only fire when a class is first used.

Expected:
- `package:discover` lists the module, localization, bouncer, datatables, translatable and debugbar providers;
- `about` prints Laravel Version 13.x and PHP Version 8.4.x;
- the route count equals `node -e 'console.log(require("./tests/upgrade/routes.json").length)'`;
- `200 /en`, `200 /ar`, `200 /en/authenticate/login`, `200 /en/articles`, and `302 /en/admin`;
- `all app classes load`.

**Any other boot error** (a class not found, an incompatible method signature in an app class that extends a package class, or a missing config key):
- fix it with the smallest change that keeps behaviour, and repeat this step;
- list every such fix in the report with the error message and the reason;
- if a fix would change public output or an admin workflow, report DONE_WITH_CONCERNS instead of guessing.

- [ ] **Step 6: Compare the structure with Laravel 7**

Run: `scripts/upgrade/check-structure.sh`

Expected:
- **routes:** identical. If route middleware names or action strings differ, fix the cause (usually a middleware alias or a route namespace). Only a documented framework rename may remain, and it must be reported.
- **config:** one difference, `"session.serialization": null` → `"php"`.
- **glide:** it may fail at this point, because Glide 3 is wired in Task 5. Record the output in the report.

- [ ] **Step 7: Check the app logs are clean for the smoke URLs**

```bash
: > storage/logs/laravel-$(date +%F).log 2>/dev/null; rm -f storage/logs/deprecations.log
for u in /en /ar /en/authenticate/login; do curl -s -o /dev/null "http://localhost:8080$u"; done
ls storage/logs/; tail -n 20 storage/logs/laravel-$(date +%F).log 2>/dev/null
wc -l storage/logs/deprecations.log 2>/dev/null
```

Expected:
- no `ERROR` entries in the Laravel log for those requests;
- if `deprecations.log` exists, list its distinct messages, with the file:line of the first frame, in the report. Tasks 4–9 remove them.

- [ ] **Step 8: Record the first parity census**

Run the census command from "Working environment", plus `(cd tests/e2e && npx playwright test --project=chromium --reporter=line | tail -3)`. Put both outputs in the report. They are not gates.

- [ ] **Step 9: Accept the one expected config change, then commit**

In `tests/upgrade/config.json`, change `"session.serialization": null` to `"session.serialization": "php"`. Then run:

```bash
scripts/upgrade/check-structure.sh | grep -v '^---\|^+++' | grep -E '^[-+]' | grep -v glide || true
git add docker/php/Dockerfile docker-compose.yml composer.json composer.lock config/app.php config/session.php config/logging.php \
  app/Http/Middleware app/Providers/AppServiceProvider.php Modules/*/Providers tests/upgrade/config.json
git status --short
git commit -m "Move the stack to PHP 8.4 and Laravel 13 dependencies with the minimum boot fixes

Accepted structural change: session.serialization is now explicitly php (Laravel 7 had no key; same format)."
```

Expected: `git status --short` lists only the files you meant to change. If Step 5 needed another fix, add that file to the commit by name.

---

### Task 3: PHPUnit 12 and the existing PHP tests

**Files:**
- Modify: `phpunit.xml`
- Modify: `tests/Feature/SetUserPasswordCommandTest.php` (data provider)
- Modify: any file under `tests/` that PHPUnit 12 rejects

**Interfaces:**
- **Consumes:** the Task 2 stack.
- **Produces:** `docker compose exec -T app php vendor/bin/phpunit` green. Later tasks use it as their PHP test gate.

- [ ] **Step 1: Run the current tests to see the failures**

Run: `docker compose exec -T app php vendor/bin/phpunit 2>&1 | tail -20`

Expected: failures or configuration errors (the old `<filter><whitelist>` schema, `@dataProvider` doc-comments that PHPUnit 12 ignores, or a non-static data provider). Record them.

- [ ] **Step 2: Replace phpunit.xml**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

(`MAIL_DRIVER` became `MAIL_MAILER`, because the app's `config/mail.php` reads `MAIL_MAILER`. Tests must never reach SMTP.)

- [ ] **Step 3: Convert the data provider to a PHPUnit 12 attribute**

In `tests/Feature/SetUserPasswordCommandTest.php`:
- Add `use PHPUnit\Framework\Attributes\DataProvider;` to the imports.
- Replace `/** @dataProvider unsafeCredentialsPathProvider */` with `#[DataProvider('unsafeCredentialsPathProvider')]`.
- Change `public function unsafeCredentialsPathProvider(): array` to `public static function unsafeCredentialsPathProvider(): array`.

If the provider body uses `$this`, move those values into literals.

Run `grep -rn '@test\|@dataProvider\|@depends\|@group\|@runInSeparateProcess' tests/ --include='*.php'`, and convert every hit to its attribute in the same way.

- [ ] **Step 4: Run the suite until it is green**

Run: `docker compose exec -T app php vendor/bin/phpunit`

Expected: all tests pass: 4 Unit files and 2 Feature files, the same test count as on Laravel 7 (`grep -c 'function test' tests/Unit/*.php tests/Feature/*.php` summed, plus the data-provider cases).

If a test fails because of a real app behaviour change (for example `TrustProxiesTest` or `VendorOffboardingTest`), fix the app, not the test. Report any assertion that you believe the upgrade legitimately changes as DONE_WITH_CONCERNS.

- [ ] **Step 5: Commit**

```bash
git add phpunit.xml tests
git commit -m "Run the PHP test suite on PHPUnit 12"
```

---

### Task 4: Deprecation-focused Rector pass and PHP 8.4 lint

**Files:**
- Create: `rector.php`
- Modify: whatever Rector changes (PHP files only, never Blade views)

**Interfaces:**
- **Consumes:** PHPUnit (Task 3); `scripts/upgrade/check-structure.sh`; the census from Task 2.
- **Produces:** a PHP code base that lints on 8.4, and a reviewable Rector commit containing only Rector's own changes.

Rule selection note: the spec asks for the "PHP 7.4→8.4 and Laravel 7→13 rule sets", committed alone. This plan applies the rules from those sets that fix removed or deprecated behaviour. Pure modernisation rules (property promotion, readonly, first-class callables and similar) are left out. They change code without changing behaviour, and they would bury the reviewable diff under 58k lines of churn.

- [ ] **Step 1: Write rector.php**

```php
<?php

use Rector\Config\RectorConfig;
use Rector\Php82\Rector\Encapsed\VariableInStringInterpolationFixerRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/app', __DIR__.'/Modules', __DIR__.'/config', __DIR__.'/database', __DIR__.'/routes', __DIR__.'/tests'])
    ->withSkip([
        '*/Resources/views/*',
        '*.blade.php',
        __DIR__.'/Modules/Cms/Includes/date/I18N/Arabic/Examples',
        __DIR__.'/tests/e2e',
    ])
    ->withRules([
        ExplicitNullableParamTypeRector::class,          // PHP 8.4: implicit nullable parameter types are deprecated
        VariableInStringInterpolationFixerRector::class, // PHP 8.2: "${var}" interpolation is deprecated
    ])
    ->withSets([LaravelLevelSetList::UP_TO_LARAVEL_130]);
```

If `LaravelLevelSetList::UP_TO_LARAVEL_130` is not defined in the installed rector-laravel, check with `grep -n "UP_TO_LARAVEL_1[23]0" vendor/driftingly/rector-laravel/src/Set/LaravelLevelSetList.php`. If it is missing, replace the `withSets` line with `->withComposerBased(laravel: true)`.

- [ ] **Step 2: Dry run and review what would change**

Run: `docker compose exec -T app php vendor/bin/rector process --dry-run --no-progress-bar > /tmp/rector-dry.txt; tail -5 /tmp/rector-dry.txt`

- Read the applied-rules list at the end of the output.
- For every Laravel rule that fires, decide whether it keeps runtime behaviour and output identical:
  - Behaviour-preserving renames are kept, for example a removed helper → its `Str::`/`Arr::` equivalent, or deprecated method names.
  - A rule that changes semantics goes into `->withSkip([RuleClass::class])` with a trailing comment naming the reason. Examples: a changed default argument, a different query, or a different escaping.
- List every fired rule in the report as `kept` or `skipped: reason`.

- [ ] **Step 3: Apply**

```bash
docker compose exec -T app php vendor/bin/rector process --no-progress-bar | tail -5
git diff --stat | tail -3
```

- [ ] **Step 4: Lint every PHP file on 8.4 and compile every Blade view**

```bash
docker compose exec -T app sh -c 'find app Modules config database routes tests -name "*.php" ! -name "*.blade.php" ! -path "*/I18N/Arabic/Examples/*" ! -path "tests/e2e/*" -print0 | xargs -0 -n 50 -P 4 php -l | grep -v "^No syntax errors" || true'
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan view:clear
```

Expected:
- the lint prints nothing;
- `view:cache` reports `Blade templates cached successfully` with no exception. It compiles module view namespaces too.
- A Blade view that fails to compile is a real error: fix it in the view with the smallest change, and list it.

- [ ] **Step 5: Gates**

```bash
docker compose exec -T app php vendor/bin/phpunit
scripts/upgrade/check-structure.sh
```

Then run the census command.

Expected:
- PHPUnit is green.
- The structure check shows the same result as Task 2 (routes and config identical; glide untouched).
- For every spec file, the census passed count is at least Task 2's count. Any spec file with fewer passes points at a Rector rule; skip that rule and repeat Steps 3–5.

- [ ] **Step 6: Commit Rector's changes alone**

```bash
git add rector.php
git commit -m "Add a deprecation-focused Rector configuration"
git add -u app Modules config database routes tests
git commit -m "Apply Rector PHP 8.4 deprecation and Laravel 13 rename rules"
```

If Step 4 needed hand fixes to views or PHP, commit them third: `git commit -m "Fix PHP 8.4 lint and Blade compile errors"`.

---

### Task 5: Images and storage on Glide 3 and Flysystem 3

**Files:**
- Modify: `Modules/Cms/Http/Controllers/ImageController.php`
- Modify: `Modules/Cms/Providers/ImageServiceProvider.php` (only if Step 2 finds `getDriver()` unusable)
- Test, existing:
  - `tests/e2e/specs/security/images.spec.ts`
  - the attachment and TinyMCE security specs under `tests/e2e/specs/security/`
  - `specs/parity/content-lifecycle.spec.ts`
  - `specs/parity/visual.spec.ts`
  - `tests/Unit/ImageSizesConfigTest.php`

**Interfaces:**
- **Consumes:** `tests/upgrade/glide-cache-paths.json` (Task 1).
- **Produces:** `/img/{size}/{path}` behaving as on Laravel 7, including the Phase H canonical-path and size-whitelist rules. Existing `.cache` entries stay valid.

- [ ] **Step 1: See the current failures**

```bash
(cd tests/e2e && npx playwright test --project=chromium specs/security/images.spec.ts --reporter=line | tail -15)
curl -s -o /dev/null -w '%{http_code} %{content_type}\n' "http://localhost:8080/img/1000x750/$(docker compose exec -T app sh -c 'ls storage/app/public/uploads/articles | head -1')"
```

Expected: failures such as `Class "League\Flysystem\Util" not found` in `storage/logs/laravel-*.log`. Record them.

- [ ] **Step 2: Confirm the Glide wiring**

```bash
grep -n "function getDriver" vendor/laravel/framework/src/Illuminate/Filesystem/FilesystemAdapter.php
grep -n "FilesystemOperator" vendor/league/glide/src/ServerFactory.php | head -5
grep -n "function getCachePath\|function sourceFileExists\|function cacheFileExists\|function makeImage\|function deleteCache\|function setDefaults" vendor/league/glide/src/Server.php
```

- **If `getDriver()` exists and returns the Flysystem operator** (Laravel 9+ returns `League\Flysystem\FilesystemOperator`): `ImageServiceProvider` needs no change.
- **If `getDriver()` is missing:** replace both `$filesystem->getDriver()` calls with the `Illuminate\Filesystem\FilesystemAdapter` method that returns the `League\Flysystem\FilesystemOperator`, as found in the vendor source. Record which one.
- All six Server methods must exist.

- [ ] **Step 3: Replace the Flysystem 1 path normaliser in ImageController**

In `Modules/Cms/Http/Controllers/ImageController.php`, change the import `use League\Flysystem\Util;` to:

```php
use League\Flysystem\FilesystemException;
use League\Flysystem\WhitespacePathNormalizer;
```

Replace the `try` block inside `isCanonicalPath()` with the version below. Keep the rest of the method and its docblock unchanged.

```php
        try {
            $canonical = (new WhitespacePathNormalizer())->normalizePath($path) === $path;
        } catch (FilesystemException $e) {
            // Flysystem 3 throws PathTraversalDetected / CorruptedPathDetected where Flysystem 1 threw LogicException.
            $canonical = false;
        }
```

Check that `vendor/league/flysystem/src/PathTraversalDetected.php` and `CorruptedPathDetected.php` both `implements FilesystemException`. If either does not, catch `\RuntimeException` in addition.

- [ ] **Step 4: Prove cached image paths did not move**

Run: `scripts/upgrade/check-structure.sh`

Expected: `structure matches tests/upgrade/`. Routes, config and glide are all identical.

If `glide-cache-paths` differs, Glide 3 changed its cache key, and every image already cached on production would be regenerated. Worse, previously cached non-whitelisted sizes would start answering 404. In that case, configure the Glide server so the key matches Laravel 7:
- find Glide 3's cache-path hook in `vendor/league/glide/src/Server.php`, a cache path callable setter or a factory option;
- set it in `ImageServiceProvider` so it reproduces the 1.x formula exactly: `.cache/` + `$sourcePath` + `/` + `md5($sourcePath.'?'.http_build_query($params))`, where `$params` has the defaults merged, `s`/`p` removed, and is `ksort`ed;
- then rerun this step.

- [ ] **Step 5: Gates**

```bash
docker compose exec -T app php vendor/bin/phpunit --filter ImageSizesConfigTest
(cd tests/e2e && npx playwright test --project=chromium specs/security --reporter=line | tail -5)
(cd tests/e2e && npx playwright test specs/parity/content-lifecycle.spec.ts specs/parity/visual.spec.ts --reporter=line | tail -5)
```

Expected: PHPUnit green; all security specs green; `34 passed` (2 lifecycle + 32 visual).

A visual failure needs one of two outcomes:
- a fix, if the image is wrong (wrong crop, wrong size, or a missing image);
- a report with the diff PNG path, if it is only JPEG re-encoding noise above 1%.

- [ ] **Step 6: Commit**

```bash
git add Modules/Cms/Http/Controllers/ImageController.php Modules/Cms/Providers/ImageServiceProvider.php
git commit -m "Serve images through Glide 3 on Flysystem 3 with the Laravel 7 cache paths"
```

---

### Task 6: Authentication, sessions and permissions

**Files:**
- Modify, only where the checks below find a difference:
  - `Modules/Cms/Http/Controllers/Auth/*.php`
  - `Modules/Permissions/Entities/{Ability,Role}.php`
  - `Modules/Permissions/Providers/BouncerServiceProvider.php`
  - `config/hashing.php`
- Test, existing:
  - `specs/parity/permissions-matrix.spec.ts`
  - `specs/parity/admin-sections.spec.ts` (login-failed and logout)
  - `tests/e2e/specs/security/*` (EnsureStaff, vendor access)
  - `tests/e2e/specs/smoke/*`
  - `tests/Feature/*`

**Interfaces:**
- **Consumes:** the Task 5 stack.
- **Produces:** identical sign-in, sign-out, lockout and role/ability outcomes for anonymous users, ADMIN, SUPERADMIN and disabled users.

- [ ] **Step 1: Run the auth and permission gates to see the failures**

```bash
(cd tests/e2e && npx playwright test specs/parity/permissions-matrix.spec.ts specs/parity/admin-sections.spec.ts --reporter=line | tail -8)
(cd tests/e2e && npx playwright test --project=chromium --reporter=line | tail -8)
docker compose exec -T app php vendor/bin/phpunit tests/Feature
```

- [ ] **Step 2: Diff the laravel/ui auth traits that the app does not override**

```bash
for t in AuthenticatesUsers RedirectsUsers ThrottlesLogins SendsPasswordResetEmails ResetsPasswords ConfirmsPasswords VerifiesEmails RegistersUsers; do
  grep -n "function " vendor/laravel/ui/auth-backend/$t.php | sed "s/^/$t: /"
done
grep -n "function " Modules/Cms/Http/Controllers/Auth/*.php
```

- Fetch laravel/ui v2.0.1's versions of the same traits: https://raw.githubusercontent.com/laravel/ui/v2.0.1/auth-backend/AuthenticatesUsers.php, and likewise for the other files.
- For every trait method the app does NOT override but calls, compare the two bodies.
  - A behaviour difference on a path the login form uses is a regression: override that method in the app controller with the v2.0.1 body.
  - Examples of such differences: `remember` read via `boolean()` instead of `filled()`, a changed redirect, or a changed lockout response.
- List every compared method in the report.

- [ ] **Step 3: Password hashing stays as on Laravel 7**

```bash
grep -n "rounds\|verify\|rehash_on_login" config/hashing.php
docker compose exec -T db mariadb -ualdar -paldar aldar -N -B -e "SELECT LEFT(password, 4) AS prefix, COUNT(*) FROM users GROUP BY prefix"
```

Expected:
- `config/hashing.php` keeps rounds `env('BCRYPT_ROUNDS', 10)` and has no `verify` key, so algorithm verification stays off, as on Laravel 7.
- The prefixes are `$2y$` only.
- If any other prefix appears, add `'rehash_on_login' => false,` to `config/hashing.php`, with a comment saying why, so a successful login never rewrites a legacy hash. Report the prefix counts, which contain no personal data.

- [ ] **Step 4: Bouncer 1.0.4 against the existing perms_* schema**

```bash
docker compose exec -T db mariadb -ualdar -paldar aldar -e "SHOW COLUMNS FROM perms_roles; SHOW COLUMNS FROM perms_abilities; SHOW COLUMNS FROM perms_permissions; SHOW COLUMNS FROM perms_assigned_roles"
grep -rn "'level'\|->level\|'title'\|'scope'\|only_owned\|'options'" vendor/silber/bouncer/src | head -40
grep -n "function " Modules/Permissions/Entities/Ability.php Modules/Permissions/Entities/Role.php
```

- Every column Bouncer 1.0.4 reads or writes must exist in the `perms_*` tables.
- `perms_roles.level` may stay unused, since the package dropped it.
- If a column is missing, add a module migration in `Modules/Permissions/Database/Migrations/` that adds exactly that column, matching Bouncer's own migration stub. Run it with `docker compose exec -T app php artisan migrate --path=Modules/Permissions/Database/Migrations --force`. Report it; the production cutover (Phase 3) must run it.
- For each method overridden in `Ability.php` or `Role.php`, confirm its signature is compatible with the Bouncer 1.0.4 parent. PHP fatals on incompatible declarations.

- [ ] **Step 5: Session and cookie notes for Phase 3**

This step makes no code change. Record in the report:
- Laravel 7.3 cookies lack the `CookieValuePrefix` that later versions validate.
- As a result, the first request after the production cutover drops existing session and currency cookies: everyone is logged out once, and the currency choice resets.

Cite the code path that causes it: `vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php` (the `CookieValuePrefix` validation in `decryptCookie`/`validateValue`).

- [ ] **Step 6: Gates**

```bash
(cd tests/e2e && npx playwright test specs/parity/permissions-matrix.spec.ts specs/parity/admin-sections.spec.ts specs/parity/admin-table-values.spec.ts --reporter=line | tail -5)
(cd tests/e2e && npx playwright test --project=chromium --reporter=line | tail -5)
docker compose exec -T app php vendor/bin/phpunit
```

Expected: the matrix passes (1 passed). `admin-sections` login-failed and logout pass. The chromium project reports 47 passed and PHPUnit is green.

`admin-sections` DataTables snapshots and `admin-table-values` may still fail; they belong to Task 7. List them.

- [ ] **Step 7: Commit**

```bash
git add -A Modules/Cms/Http/Controllers/Auth Modules/Permissions config/hashing.php
git status --short
git commit -m "Keep Laravel 7 sign-in and Bouncer behaviour on laravel/ui 4 and Bouncer 1.0.4"
```

If Steps 2–4 found nothing to change, make no commit, and say so in the report.

---

### Task 7: Admin DataTables on yajra 13

**Files:**
- Modify: the admin controllers that build DataTables — the 19 `DataTables::of` call sites in 14 files. Check them with `grep -rln "DataTables::of" Modules`.
- Modify: `config/datatables.php`, only if a yajra 13 default changes output.
- Test, existing: `specs/parity/admin-sections.spec.ts`, `specs/parity/admin-table-values.spec.ts`, `specs/parity/permissions-matrix.spec.ts`

**Interfaces:**
- **Consumes:** the Task 6 stack.
- **Produces:** admin list endpoints that return the same JSON keys, row keys, actions and (for public tables) values as Laravel 7.

- [ ] **Step 1: Run the gates**

Run: `(cd tests/e2e && npx playwright test specs/parity/admin-sections.spec.ts specs/parity/admin-table-values.spec.ts --reporter=line | tail -20)`

For each failure, open the `-actual.json` file next to the snapshot and compare.

- [ ] **Step 2: Compare yajra 9 and 13 defaults**

```bash
diff <(git show main:config/datatables.php) vendor/yajra/laravel-datatables-oracle/src/config/datatables.php
grep -n "function make\|function render\|DT_RowIndex\|function escapeColumns\|function addIndexColumn\|protected function processResults" -r vendor/yajra/laravel-datatables-oracle/src | head -20
```

The app keeps its own `config/datatables.php`. A key that yajra 13 reads and the app's file lacks falls back to yajra's default, so compare those keys. The usual differences are:
- escaping (`columns.escape`, `columns.raw`);
- the index column name;
- the date format of `created_at`/`updated_at`;
- debug keys (`input`, `queries`), which the parity suite already ignores.

- [ ] **Step 3: Fix differences in the app, one class of difference at a time**

- **A config key that changed default:** add it to `config/datatables.php` with the Laravel 7 value, and cite the yajra 9 default in a comment.
- **A method removed or renamed:** change the controller call to the yajra 13 equivalent that produces the same JSON.
- **Date serialisation** (Laravel 7 and 13 both use `Y-m-d\TH:i:s.u\Z` for `toArray()`): if values differ, find the model `$casts`/`serializeDate` cause.

After each class, rerun Step 1.

- [ ] **Step 4: Gates**

```bash
(cd tests/e2e && npx playwright test specs/parity/admin-sections.spec.ts specs/parity/admin-table-values.spec.ts specs/parity/permissions-matrix.spec.ts --reporter=line | tail -5)
docker compose exec -T app php vendor/bin/phpunit
```

Expected: `51 passed` (46 + 4 + 1). PHPUnit is green.

- [ ] **Step 5: Commit**

```bash
git add -A Modules config/datatables.php
git status --short
git commit -m "Keep the Laravel 7 admin DataTables output on yajra 13"
```

---

### Task 8: Public pages, localization and the database layer

**Files:**
- Modify: `Modules/Cms/Resources/views/config/update_config.blade.php` (`str_limit`)
- Modify, where the parity diffs point:
  - `Modules/Frontend/**`
  - `Modules/Cms/Entities/**`
  - `Modules/Cms/Entities/Traits/Helpers.php`
  - the `DB::raw` call sites
  - views
- Test, existing:
  - `specs/parity/golden-master.spec.ts`
  - `specs/parity/behaviour.spec.ts`
  - `specs/parity/visual.spec.ts`
  - `specs/parity/content-lifecycle.spec.ts`
  - `specs/parity/inventory.spec.ts`
  - `specs/parity/normalize.spec.ts`

**Interfaces:**
- **Consumes:** the Task 7 stack.
- **Produces:** all public pages, JSON endpoints and redirects identical to Laravel 7.

- [ ] **Step 1: Replace the removed `str_limit` helper**

In `Modules/Cms/Resources/views/config/update_config.blade.php`, line 198, replace `str_limit(` with `\Illuminate\Support\Str::limit(`. The arguments stay unchanged.

Then run `grep -rnE '\b(str_limit|str_slug|str_plural|str_singular|str_random|array_get|array_set|array_only|array_except|array_pluck|array_first|array_last|array_wrap|starts_with|ends_with|camel_case|snake_case|studly_case|title_case)\(' app Modules resources routes config`. Expected: no output.

- [ ] **Step 2: Run the public gates**

```bash
(cd tests/e2e && npx playwright test specs/parity/inventory.spec.ts specs/parity/normalize.spec.ts specs/parity/golden-master.spec.ts specs/parity/behaviour.spec.ts --reporter=line | tail -20)
```

Group the failures by root cause, not by URL. Expected classes and where to look:

| Symptom | Likely cause | Where |
|---|---|---|
| Locale redirects or `/` → `/en` differ | mcamara 2.x redirect or session-key behaviour | `vendor/mcamara/laravel-localization/src/Mcamara/LaravelLocalization/Middleware/*`, compared with v1.5.0 on GitHub |
| A date renders differently | Carbon 3 (`translatedFormat`, `diffForHumans`, locale data) or the `I18N_Arabic` date parser on PHP 8.4 | `Modules/Cms/Entities/Traits/Helpers.php` and its callers |
| Items with equal sort keys swap | PHP 8 stable sort | report, per "How parity differences are handled" |
| A list or count differs | a query built with `DB::raw` or a changed Eloquent behaviour | the 25 files with `DB::raw` (inventory §9) |
| Validation JSON wording differs | Laravel 13 fallback messages for keys the app's `resources/lang/*/validation.php` lacks | copy the Laravel 7 message for that key into the app's lang file |
| A 404, 419 or 500 page differs | error views under `resources/views/errors/` are app-owned; check `app/Exceptions/Handler.php` rendering on Laravel 13 | `vendor/laravel/framework/src/Illuminate/Foundation/Exceptions/Handler.php` |
| Pagination markup differs | the `useBootstrapFour()` default, or a changed view name | `resources/views/vendor/pagination/`, `Modules/Frontend/Resources/views/includes/pagination.blade.php` |
| Every form page gains an attribute (e.g. `autocomplete="off"` on the `_token` input) | a framework-wide helper change | class 4 in "How parity differences are handled" |
| Untranslated `module::` keys (e.g. `permissions::roles.datatable.id`) become translated, or the reverse | laravel-modules 13 translation loading or translator fallback | module service providers' `registerTranslations()`, `vendor/nwidart/laravel-modules/src` |

- [ ] **Step 3: Fix each class in application code, rerunning the affected spec after each fix**

- Keep each fix minimal, and give it a one-line comment only when the reason is not obvious from the code.
- When a fix touches a query, also run `specs/parity/admin-sections.spec.ts`.

- [ ] **Step 4: Gates**

`npm test` takes about 11 minutes. Run it with `nohup` into a log file under `.superpowers/`, and poll the log with separate foreground `tail -3` calls.

```bash
(cd tests/e2e && npm test)
docker compose exec -T app php vendor/bin/phpunit
scripts/upgrade/check-structure.sh
```

Expected:
- `npm test`: `368 passed`. That is 321 parity tests (317 from Phase 0 + 4 from Task 1) plus 47 Phase H tests. Report any residual failure with its classification, for a controller ruling.
- PHPUnit green.
- `structure matches tests/upgrade/`.

- [ ] **Step 5: Exploratory zero-tolerance visual run (not a gate)**

The 1% tolerance equals about 10.9k pixels on desktop, which is enough to hide a lost icon font or logo.
1. In `tests/e2e/specs/parity/visual.spec.ts`, temporarily change `maxDiffPixelRatio: 0.01` to `maxDiffPixelRatio: 0`.
2. Run `(cd tests/e2e && npx playwright test specs/parity/visual.spec.ts --reporter=line | tail -40)`.
3. Open the diff PNG of every failing page.
4. Revert the spec: `git diff tests/e2e/specs` must print nothing.

In the report, list each page with any pixel change and what the diff shows. Anything beyond JPEG or anti-aliasing noise is a regression to fix.

- [ ] **Step 6: Commit**

```bash
git add -A Modules app resources config
git status --short
git commit -m "Keep Laravel 7 public pages, redirects and JSON endpoints on Laravel 13"
```

---

### Task 9: Deprecation gate, Larastan baseline, runbook, and exit evidence

**Files:**
- Create: `phpstan.neon`, `phpstan-baseline.neon`
- Modify: application files named by deprecation entries
- Modify: `tests/e2e/PARITY.md`, `docker-compose.yml` (header comment)

**Interfaces:**
- **Consumes:** everything above.
- **Produces:**
  - an empty `storage/logs/deprecations.log` after a full parity run;
  - a Larastan baseline;
  - two consecutive green parity runs;
  - the PR description material in the report.

- [ ] **Step 1: Collect the deprecations from a full run**

```bash
grep -q '^LOG_DEPRECATIONS_CHANNEL=deprecations' .env && echo "channel on"
rm -f storage/logs/deprecations.log
(cd tests/e2e && npm run parity)
(cd tests/e2e && npx playwright test --project=chromium --reporter=line | tail -3)
test -f storage/logs/deprecations.log && sed -E 's/^\[[^]]*\] //' storage/logs/deprecations.log | sort | uniq -c | sort -rn | head -40
```

Expected: `channel on`, then a count of distinct deprecation messages. None is the goal.

- [ ] **Step 2: Fix every deprecation raised from application code**

For each distinct message whose origin is in `app/`, `Modules/`, `config/`, `routes/` or `resources/`:
- **Passing null to a non-nullable internal parameter:** cast at the call site, e.g. `(string) $value`. The result must be what PHP 7.4 produced, which is `''` for null in string functions.
- **Implicit nullable types, or `${}` interpolation left in files Rector skipped:** same fix as Task 4.
- **Dynamic property on an app class:** declare the property on the class.
- **The bundled `I18N_Arabic` library:** fix only files the app loads (`Modules/Cms/Includes/date/I18N/Arabic.php`, `Arabic/Date.php`, `Arabic/CharsetC.php` if loaded), with the same minimal fixes.

For a deprecation raised only from inside `vendor/`:
- note the package and message in the report;
- check whether a newer release in the same major version fixes it (`composer show -a <package>`).

Repeat Step 1 until no application-code deprecations remain.

- [ ] **Step 3: Larastan baseline**

`phpstan.neon`:

```neon
includes:
    - vendor/larastan/larastan/extension.neon
    - phpstan-baseline.neon

parameters:
    level: 2
    paths:
        - app
        - Modules
    excludePaths:
        - Modules/*/Resources/views/*
        - Modules/Cms/Includes/date/I18N/Arabic/Examples/*
    reportUnmatchedIgnoredErrors: false
```

```bash
echo "parameters: []" > phpstan-baseline.neon
docker compose exec -T app php -d memory_limit=2G vendor/bin/phpstan analyse --no-progress --error-format=table > /tmp/phpstan.txt; tail -3 /tmp/phpstan.txt
git diff --name-only main...HEAD -- '*.php' | grep -v '^tests/' > /tmp/touched.txt
```

- For every error in a file listed in `/tmp/touched.txt`, fix it when the fix does not change behaviour. Otherwise keep it, and name it in the report.
- Then generate the baseline for the rest:

```bash
docker compose exec -T app php -d memory_limit=2G vendor/bin/phpstan analyse --no-progress --generate-baseline phpstan-baseline.neon
docker compose exec -T app php -d memory_limit=2G vendor/bin/phpstan analyse --no-progress
```

Expected: `[OK] No errors`.

- [ ] **Step 4: Update the runbook and the compose header**

In `tests/e2e/PARITY.md`:
- change the test counts to 321 parity and 368 for `npm test`, and add the `admin-table-values` row to the Layers table: "Row values of four public admin tables (countries, cities, areas, FAQs), all pages, sorted by id";
- in the first paragraph, add that the suite now runs against Laravel 13 / PHP 8.4, with baselines still from Laravel 7.3;
- add a section "Structural checks": `scripts/upgrade/check-structure.sh` compares routes, key config values and Glide cache paths with `tests/upgrade/*.json`.

In `docker-compose.yml`, add a header comment line: `#   PHP 8.4 / Laravel 13 (image aldar-php84)`.

- [ ] **Step 5: Exit evidence**

```bash
rm -f storage/logs/deprecations.log
(cd tests/e2e && npm test)
(cd tests/e2e && npm test)
docker compose exec -T app php vendor/bin/phpunit
scripts/upgrade/check-structure.sh
test ! -s storage/logs/deprecations.log && echo "no deprecations"
git diff --stat main...HEAD -- public/css public/js public/modules | tail -1
```

Run each `npm test` the same way as in Task 8 Step 4: with `nohup` into a log file, polled with separate foreground `tail -3` calls.

Expected:
- `368 passed` twice in a row;
- PHPUnit green;
- `structure matches tests/upgrade/`;
- `no deprecations`;
- no output from the last command (compiled assets untouched).

- [ ] **Step 6: Commit**

```bash
git add phpstan.neon phpstan-baseline.neon tests/e2e/PARITY.md docker-compose.yml
git add -u app Modules config resources
git status --short
git commit -m "Clear PHP 8.4 deprecations, add a Larastan baseline, and document the upgraded parity run"
```

- [ ] **Step 7: PR material (controller opens the PR)**

In the report, write the PR body. It covers:
- the dependency versions installed (`composer show` for the matrix packages);
- every accepted parity or structure difference, with its ruling;
- vendor-only deprecations;
- the Phase 3 notes: the cookie drop at cutover, any new migration from Task 6, and PHP 8.4 on Hostinger;
- the owner smoke-test checklist:
  1. home ar/en;
  2. a property page;
  3. submit a contact form (in Mailpit);
  4. sign in;
  5. create and delete an article with an image;
  6. change currency.

---

## Self-review

**1. Spec coverage** (Phase 1 section):

| Spec item | Task |
|---|---|
| Dependency matrix (all rows, removals, dev tools) | 2 (composer.json), 4 (Rector), 9 (Larastan) |
| Keep app structure, string routes, route namespaces | Global constraint; 1 and 2 (route table baseline and check) |
| No asset rebuild | Global constraint; 9 Step 5 check |
| Docker `php:8.4-apache` on supported Debian, no archive mirror | 2 Step 1 |
| `session.cookie`/`cache.prefix` unchanged; serialization `php` | 1 (config baseline), 2 Step 4g |
| `Paginator::useBootstrapFour()` | 2 Step 4e |
| Fix order 1 Docker | 2 |
| Fix order 2 dependencies and boot, `php artisan about` | 2 |
| Fix order 3 Rector pass committed alone | 4 |
| Fix order 4 routing and middleware: namespaces, localization aliases, `PreventRequestForgery`, TrustProxies/CORS built-ins, `RedirectToHttps` stays | 2 Step 4a–c (fideloper removal forces it at boot); 1 and 2 route check; 8 localization diffs. CORS was never wired in the Kernel, so removing the package is enough. |
| Fix order 5 storage and images (`getDriver`, Glide 3, `ImageController`, `graph` disk, attachments) | 5 |
| Fix order 6 laravel/ui 4 traits; Bouncer schema compare, migration only if needed | 6 |
| Fix order 7 laravel-modules 13 config, statuses activator, `start.php`, providers | 2 (allow-plugins, boot, factories); research confirmed `start.php` and the statuses path still load |
| Fix order 8 DataTables 13 | 7 |
| Fix order 9 `DB::raw` review, `str_limit`, pagination, parity flags | 8 |
| Fix order 10 Larastan baseline, no new errors on touched code | 9 Step 3 |
| Exit: parity green, no deprecations, owner smoke test | 9 Steps 1–2, 5, 7 |
| Top risks: Bouncer drift; Glide factory; pagination; `I18N_Arabic`; localization v2 | 6 Step 4; 5 Steps 2 and 4; 2 Step 4e and 8; 8 and 9 Step 2; 8 Step 2 |

**2. Placeholder scan.**
- Code steps give full file contents or exact line replacements.
- Steps whose content depends on what the upgraded vendor code does (Tasks 5–8 diff classes) give the exact commands, the decision rule, and where to look.
- No step says "handle edge cases" without a rule.

**3. Type and name consistency.**
- `structure-snapshot.php` sections `routes|config|glide` map to `tests/upgrade/{routes,config,glide-cache-paths}.json` in `check-structure.sh`.
- The parity total is 317 (Phase 0, after its final-review fixes) + 4 = 321, and `npm test` = 321 + 47 = 368, in Tasks 8 and 9.
- `LOG_DEPRECATIONS_CHANNEL=deprecations` (Task 2) is used in Task 9.
- `admin-table-values.spec.ts` uses `maskVolatileValues(html, baseUrl)`, which matches `parity/normalize.ts`.
