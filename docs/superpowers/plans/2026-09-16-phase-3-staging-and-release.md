# Phase 3: Staging and Release-Based Deployment Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Put the Laravel 13 / PHP 8.4 application on a staging site and on repeatable, reversible releases on Hostinger, then cut production over from the in-place `public_html` to a release symlink, with a read-only parity probe proving each step.

**Architecture:**
- Nine tasks. Tasks 1–4 are code and scripts the agent writes and proves locally in Docker. Tasks 5–9 are server work whose commands the owner runs, with the agent preparing every script, checklist and verification.
- Deployment becomes `releases/<id>` directories plus a `current` symlink, with `shared/.env` and `shared/storage` linked in. `rollback.sh` repoints `current`.
- The parity suite doubles as the release probe: a read-only subset runs against staging and, after the cutover, against production.
- Nothing in this plan contacts aldar-emlak.com from an agent session. Every production or staging command is marked **owner runs**.

**Tech Stack:** Laravel 13.31 / PHP 8.4 (`/opt/alt/php84` on Hostinger), MariaDB, Hostinger shared hosting with LiteSpeed and a CDN, bash deploy scripts over SSH, Playwright for the remote probe.

**Spec:** `docs/superpowers/specs/2026-09-14-aldar-security-hotfix-and-laravel-13-upgrade-design.md`. Its "Phase 3 — Staging and release-based deployment" section binds this plan, including the "Found in Phase 1 (final review, P1-R51)" preflight block. "Constraints", "Non-goals" and "Open items" apply too.

**Branch:** `deploy/releases`, cut from `security/hardening` at 802ca0d once PRs #4 and #5 have merged (ruling P3-R1).

**Research:** `.superpowers/research/phase-3/A-ops-inventory.md` (git-ignored).

**Controller rulings that bind this plan:** P3-R1..R3 in `.superpowers/sdd/2026-09-16-phase-3-staging-and-release/progress.md`.

## Global Constraints

These apply to every task. An implementer sees them together with its own task.

**Safety**
- **No agent session contacts aldar-emlak.com, staging.aldar-emlak.com, or the server over SSH.** Every command that touches a server is written into a runbook or script for the owner to run, and is labelled **owner runs**. An agent that finds itself about to open an SSH or HTTPS connection to either host must stop and report.
- **Production changes are never irreversible without a backup first.** Every deploy step that writes takes a DB dump and a file backup before it runs, and `rollback.sh` must be proven on staging before the cutover.
- **Never print or commit secrets.** No `.env` contents, passwords, API keys, tokens, or IP address values in code, scripts, tests, commit messages, reports or runbooks. Say "the client IP", "the DB password". `grep -q '^KEY=' .env` is allowed; printing its value is not.
- **No PII in git.** DB dumps, lead data and logs stay out of the repository.

**Parity and behaviour**
- **Parity is 1:1.** Implementers never run `UPDATE_PARITY=1` and never edit files under `tests/e2e/snapshots/` or `tests/upgrade/*.json`. A difference is fixed in code, or reported with its diff for a controller ruling.
- **No visual or UX change.** Compiled CSS/JS in `public/` stays byte-identical: `git diff --stat main...HEAD -- public/css public/js` prints nothing, and `git diff --name-status main...HEAD -- public/modules` lists only the two deletions inherited from Phase 2's S16.
- **Keep every feature**, including dormant ones, and keep the Laravel 7-style application structure.
- **The local gate still applies to every task that changes app code:** `npm test` (parity + chromium) from a global-setup DB reset, PHPUnit, `scripts/upgrade/check-structure.sh`, phpstan `[OK]`, and an empty `storage/logs/deprecations.log` with `LOG_DEPRECATIONS_CHANNEL=deprecations` set. The suite totals at the start of this phase are `npm test` 396 (321 parity + 75 chromium) and PHPUnit 47.

**Delivery**
- One commit per task unless the task says otherwise; stage explicit paths.
- **Commit trailer:** every commit message ends with `Co-Authored-By: Claude <model> <noreply@anthropic.com>` naming the model that wrote it.
- Runbooks live in `docs/deploy/`, scripts in `scripts/deploy/`. The Phase H scripts in `scripts/hotfix/` stay untouched: they document how the pre-release server was patched.

**Environment**
- Local work happens in `/Users/mohammedelkasim/Desktop/aldar-php`, the checkout the Docker stack bind-mounts (app on http://localhost:8080, MariaDB on 3307, Mailpit on 8025).
- Commands inside the container: `docker compose exec -T app <cmd>`.
- A second local PHP version is not available; anything that must be proven on PHP 8.4 is proven in the app container, which runs 8.4.25.

---

## File structure

| Path | Task | Responsibility |
|---|---|---|
| `tests/e2e/support/global-setup.ts` | 1 | Narrow the live-site refusal so staging can be probed with an explicit opt-in |
| `tests/e2e/support/env.ts` | 1 | `IS_LOCAL` stays local-only; add `IS_REMOTE_PROBE` |
| `scripts/deploy/probe-release.sh` (new) | 1 | Run the read-only parity subset against a base URL |
| `tests/e2e/PARITY.md` | 1, 9 | Document the remote probe and the final counts |
| `config/app.php`, `config/services.php` (or a new `config/debug.php`) | 2 | Config keys that replace `env()` calls outside config |
| ~32 controllers, `Modules/Permissions/Providers/BouncerServiceProvider.php`, `Modules/Cms/Resources/views/layouts/master.blade.php` | 2 | `env()` → `config()` so `config:cache` is safe |
| `tests/Unit/ConfigCacheSafetyTest.php` (new) | 2 | Fails if `env()` appears outside `config/` |
| `scripts/deploy/preflight.sh` (new) | 3 | PHP version, extensions, writable paths, platform check on the target host |
| `tests/Unit/PreflightScriptTest.php` (new) | 3 | Drives the preflight script against crafted inputs |
| `scripts/deploy/deploy.sh` (new) | 5 | Build a release, link shared state, migrate, probe, switch |
| `scripts/deploy/rollback.sh` (new) | 5 | Repoint `current` to the previous release |
| `scripts/deploy/verify-release.sh` (new) | 6 | Read-only post-switch checks against a base URL |
| `docs/deploy/staging.md` (new) | 4 | Owner runbook: create staging, copy the DB, protect it |
| `docs/deploy/releases.md` (new) | 5 | Owner runbook: layout, first release, rollback drill |
| `docs/deploy/cutover.md` (new) | 8 | Owner runbook: the production cutover and its rollback |
| `docs/deploy/preflight-answers.md` (new) | 4, 7 | The owner's answers to the server questions, filled in as they are learned |

---

## Task 1: Let the parity suite probe a remote host, read-only

**Files:**
- Modify: `tests/e2e/support/global-setup.ts`
- Modify: `tests/e2e/support/env.ts`
- Create: `scripts/deploy/probe-release.sh`
- Modify: `tests/e2e/PARITY.md`
- Test: `tests/e2e/specs/security/remote-probe-guard.spec.ts` (new)

**Interfaces:**
- **Produces:** `scripts/deploy/probe-release.sh <base-url>`, used by Tasks 5, 6 and 8; `PARITY_ALLOWED_HOST`, the only way a non-local host is ever allowed.

**Context.** `global-setup.ts:15-17` refuses any host that is or ends with `aldar-emlak.com` *before* it checks the `PARITY_ALLOWED_HOST` allowlist at `:23-26`. Staging will be `staging.aldar-emlak.com`, so the suite cannot reach it today. The refusal must keep production unreachable while letting staging through on an explicit opt-in.

- [ ] **Step 1: Write the failing test**

Create `tests/e2e/specs/security/remote-probe-guard.spec.ts`:

```ts
import { test, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import path from 'node:path';

// The guard is the only thing standing between a parity run and the live site, so it is tested
// directly: each case runs playwright's own list mode in a child process with a BASE_URL.
const E2E_DIR = path.resolve(__dirname, '../..');

function listWith(env: Record<string, string>): { code: number; output: string } {
  try {
    const output = execFileSync('npx', ['playwright', 'test', '--list', '--project', 'parity', 'specs/parity/behaviour.spec.ts'], {
      cwd: E2E_DIR,
      env: { ...process.env, SKIP_DB_RESET: '1', ...env },
      encoding: 'utf8',
      stdio: ['ignore', 'pipe', 'pipe'],
    });
    return { code: 0, output };
  } catch (error) {
    const e = error as { status: number; stdout: string; stderr: string };
    return { code: e.status, output: `${e.stdout}${e.stderr}` };
  }
}

test('the live site is refused even with an opt-in', () => {
  for (const host of ['https://aldar-emlak.com', 'https://www.aldar-emlak.com']) {
    const run = listWith({ BASE_URL: host, PARITY_ALLOWED_HOST: new URL(host).hostname });
    expect(run.code, `${host} must not run`).not.toBe(0);
    expect(run.output).toContain('Refusing to run against the live site');
  }
});

test('a non-local host without the opt-in is refused', () => {
  const run = listWith({ BASE_URL: 'https://staging.aldar-emlak.com' });
  expect(run.code).not.toBe(0);
  expect(run.output).toContain('set PARITY_ALLOWED_HOST');
});

test('staging runs only with the matching opt-in', () => {
  const run = listWith({ BASE_URL: 'https://staging.aldar-emlak.com', PARITY_ALLOWED_HOST: 'staging.aldar-emlak.com' });
  expect(run.code, run.output).toBe(0);
});
```

- [ ] **Step 2: Run it and watch it fail**

Run: `(cd tests/e2e && npx playwright test specs/security/remote-probe-guard.spec.ts --reporter=line)`
Expected: the third test fails, because `staging.aldar-emlak.com` hits the live-site refusal.

- [ ] **Step 3: Narrow the refusal**

In `tests/e2e/support/global-setup.ts`, replace the live-site block with:

```ts
  // Phase 0 never talks to the live site, not even read-only, and no opt-in can override that.
  // Phase 3 probes staging read-only, so only the production hostnames are refused outright.
  // Specific message kept because the existing test expectations grep for "Refusing".
  const PRODUCTION_HOSTS = ['aldar-emlak.com', 'www.aldar-emlak.com'];
  if (PRODUCTION_HOSTS.includes(host)) {
    throw new Error(`Refusing to run against the live site (BASE_URL=${BASE_URL})`);
  }
```

Leave the allowlist below it unchanged: a non-production, non-local host still needs `PARITY_ALLOWED_HOST` to match exactly.

- [ ] **Step 4: Add the remote-probe flag**

In `tests/e2e/support/env.ts`, after `IS_LOCAL`:

```ts
/** True when the suite targets a non-local host that was explicitly allowed (Phase 3 staging probe). */
export const IS_REMOTE_PROBE = !IS_LOCAL && process.env.PARITY_ALLOWED_HOST !== undefined;
```

`requireLocal()` is unchanged: every spec that writes data still refuses to run remotely.

- [ ] **Step 5: Write the probe script**

Create `scripts/deploy/probe-release.sh`:

```bash
#!/bin/bash
# Read-only parity probe against a deployed release.
#   scripts/deploy/probe-release.sh https://staging.aldar-emlak.com
# Runs only specs that never write: no fixtures, no DB reset, no admin writes.
# The production hostnames are refused by tests/e2e/support/global-setup.ts and cannot be probed.
set -euo pipefail

BASE_URL="${1:?usage: probe-release.sh <base-url>}"
HOST="$(printf '%s' "$BASE_URL" | sed -E 's#^[a-z]+://##; s#/.*$##; s#:.*$##')"

READ_ONLY_SPECS=(
  specs/parity/golden-master.spec.ts
  specs/security/headers.spec.ts
  specs/security/module-scripts.spec.ts
  specs/security/images.spec.ts
)

cd "$(dirname "$0")/../../tests/e2e"
BASE_URL="$BASE_URL" PARITY_ALLOWED_HOST="$HOST" SKIP_DB_RESET=1 \
  npx playwright test --reporter=line "${READ_ONLY_SPECS[@]}"
```

`chmod +x` it.

- [ ] **Step 6: Run the new spec green, plus the local gate**

Run: `(cd tests/e2e && npx playwright test specs/security/remote-probe-guard.spec.ts --reporter=line)` — 3 passed.
Run the probe script against the local stack as a smoke test of the script itself: `scripts/deploy/probe-release.sh http://localhost:8080` — every listed spec passes.
Then the local gate (see Global Constraints). `npm test` becomes 399 (321 + 78).

- [ ] **Step 7: Document it**

In `tests/e2e/PARITY.md`, add a "Remote probe" section: the script, the three specs, the rule that production hostnames are never probed, and that every writing spec refuses to run remotely through `requireLocal`.

- [ ] **Step 8: Commit**

```bash
git add tests/e2e/support/global-setup.ts tests/e2e/support/env.ts tests/e2e/specs/security/remote-probe-guard.spec.ts scripts/deploy/probe-release.sh tests/e2e/PARITY.md
git commit -m "Allow a read-only parity probe against staging, never against production"
```

---

## Task 2: Make `config:cache` safe

**Files:**
- Create: `config/debug.php`
- Modify: `Modules/Permissions/Providers/BouncerServiceProvider.php`, `Modules/Cms/Resources/views/layouts/master.blade.php`, and every controller that calls `env()`
- Test: `tests/Unit/ConfigCacheSafetyTest.php` (new)

**Interfaces:**
- **Consumes:** nothing.
- **Produces:** `config('debug.enabled')` and `config('app.env')` as the replacements for `env('APP_DEBUG')` and `env('APP_ENV')`; `config('permissions.superpowers')` for `ENABLE_SUPERPOWERS`.

**Context.** Phase 1's final review found `env()` calls outside `config/`: they return `null` once `config:cache` runs, which Task 5's deploy does. The research counted 32 `env('APP_DEBUG')` sites across 15 controllers, one `env('APP_ENV')` in `master.blade.php`, and `env('ENABLE_SUPERPOWERS', true)` in `BouncerServiceProvider`. The last one is the dangerous one: cached config would turn it `null`, changing who holds ROOT superpowers.

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/ConfigCacheSafetyTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/** Phase 3: config:cache makes env() outside config/ return null, so no app code may call it. */
class ConfigCacheSafetyTest extends TestCase
{
    private const ROOTS = ['app', 'Modules', 'routes', 'resources'];

    public function test_no_application_code_calls_env(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        foreach (self::ROOTS as $dir) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$root/$dir"));
            foreach ($files as $file) {
                if (! $file->isFile() || ! preg_match('/\.(php|blade\.php)$/', $file->getFilename())) {
                    continue;
                }
                $relative = str_replace("$root/", '', $file->getPathname());
                // Compiled assets and vendored libraries are not application code.
                if (str_contains($relative, '/Resources/assets/') || str_contains($relative, '/Includes/')) {
                    continue;
                }
                if (preg_match('/\benv\s*\(/', (string) file_get_contents($file->getPathname()))) {
                    $offenders[] = $relative;
                }
            }
        }

        sort($offenders);
        $this->assertSame([], array_values(array_unique($offenders)), "env() outside config/ breaks config:cache in:\n" . implode("\n", $offenders));
    }
}
```

- [ ] **Step 2: Run it and read the list**

Run: `docker compose exec -T app php vendor/bin/phpunit tests/Unit/ConfigCacheSafetyTest.php`
Expected: FAIL, listing every offending file. That list is the work for the next step; record it in the report.

- [ ] **Step 3: Add the config keys**

Create `config/debug.php`:

```php
<?php

return [
    /*
     | Debug output inside application code. Read through config() so config:cache keeps it,
     | unlike env(), which returns null once the config is cached (Phase 3).
     */
    'enabled' => env('APP_DEBUG', false),
];
```

Add to `config/permissions.php` if it exists, otherwise create it with a single key:

```php
    /* ENABLE_SUPERPOWERS=false disables Bouncer's ROOT superpowers. Read through config() so
       config:cache cannot silently turn it null (Phase 3). */
    'superpowers' => env('ENABLE_SUPERPOWERS', true),
```

- [ ] **Step 4: Replace every call site**

- Controllers: `env('APP_DEBUG')` → `config('debug.enabled')`. Do not change the surrounding condition or what it guards.
- `Modules/Cms/Resources/views/layouts/master.blade.php`: `env('APP_ENV')` → `config('app.env')`.
- `Modules/Permissions/Providers/BouncerServiceProvider.php`: `env('ENABLE_SUPERPOWERS', true)` → `config('permissions.superpowers')`.

Keep each file's line endings, and change nothing else on those lines.

- [ ] **Step 5: Prove it under a cached config**

```bash
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan tinker --execute="dump(config('debug.enabled'), config('app.env'), config('permissions.superpowers'));"
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080/en
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080/ar
docker compose exec -T app php artisan config:clear
```

Expected: the three values are the `.env` values, not `null`; both pages answer 200. Record the output.

- [ ] **Step 6: Run the test green, then the local gate**

Run: `docker compose exec -T app php vendor/bin/phpunit` — the new test passes, total 48.
Then the full local gate. The parity suite must be unchanged: this task alters no output.

- [ ] **Step 7: Commit**

```bash
git add config Modules app tests/Unit/ConfigCacheSafetyTest.php
git commit -m "Read debug, env and superpowers flags through config so config:cache is safe"
```

---

## Task 3: The host preflight script

**Files:**
- Create: `scripts/deploy/preflight.sh`
- Test: `tests/Unit/PreflightScriptTest.php` (new)
- Modify: `docs/deploy/preflight-answers.md` (created in Task 4; if Task 4 has not run, create it here with the answers section empty)

**Interfaces:**
- **Produces:** `scripts/deploy/preflight.sh <php-binary> <app-root>`, called by `deploy.sh` (Task 5) before any release is built, and run standalone by the owner on staging and production.

**Context.** Composer's platform check only enforces the PHP version: `composer.json` declares no extension requirements, so a missing `gd`, `intl`, `exif`, `zip`, `bcmath` or `pdo_mysql` on `/opt/alt/php84` would surface as a runtime fatal, not as an install failure. Phase 1's review also requires PHP ≥ 8.4.1 and a writable `bootstrap/cache`, where nwidart writes `modules.php`.

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/PreflightScriptTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/** The preflight script must pass on this container's PHP 8.4 and fail loudly on a bad binary. */
class PreflightScriptTest extends TestCase
{
    private function run(string $args): array
    {
        $script = dirname(__DIR__, 2) . '/scripts/deploy/preflight.sh';
        exec("bash " . escapeshellarg($script) . " $args 2>&1", $output, $code);

        return [$code, implode("\n", $output)];
    }

    public function test_it_passes_on_this_php(): void
    {
        [$code, $output] = $this->run(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(dirname(__DIR__, 2)));
        $this->assertSame(0, $code, $output);
        $this->assertStringContainsString('preflight OK', $output);
    }

    public function test_it_fails_on_a_missing_binary(): void
    {
        [$code, $output] = $this->run('/nonexistent/php ' . escapeshellarg(dirname(__DIR__, 2)));
        $this->assertNotSame(0, $code);
        $this->assertStringContainsString('not executable', $output);
    }
}
```

- [ ] **Step 2: Run it and watch it fail**

Run: `docker compose exec -T app php vendor/bin/phpunit tests/Unit/PreflightScriptTest.php`
Expected: FAIL, the script does not exist.

- [ ] **Step 3: Write the script**

Create `scripts/deploy/preflight.sh`:

```bash
#!/bin/bash
# Preflight for a deploy target. Read-only: prints findings and exits non-zero on any failure.
#   scripts/deploy/preflight.sh /opt/alt/php84/usr/bin/php ~/domains/aldar-emlak.com
set -uo pipefail

PHP_BIN="${1:?usage: preflight.sh <php-binary> <app-root>}"
APP_ROOT="${2:?usage: preflight.sh <php-binary> <app-root>}"
FAIL=0
note() { printf '  %-14s %s\n' "$1" "$2"; }
fail() { printf '  %-14s %s\n' "FAIL" "$1"; FAIL=1; }

if [ ! -x "$PHP_BIN" ]; then
  echo "FAIL: $PHP_BIN is not executable"
  exit 1
fi

VERSION="$("$PHP_BIN" -r 'echo PHP_VERSION;')"
note "php" "$VERSION ($PHP_BIN)"
"$PHP_BIN" -r 'exit(version_compare(PHP_VERSION, "8.4.1", ">=") ? 0 : 1);' || fail "PHP 8.4.1 or newer is required (vendor/composer/platform_check.php)"

for EXT in fileinfo gd intl mbstring exif zip bcmath pdo_mysql openssl json; do
  if "$PHP_BIN" -r "exit(extension_loaded('$EXT') ? 0 : 1);"; then note "ext" "$EXT"; else fail "missing extension: $EXT"; fi
done

# Intervention 3 needs JPEG and WebP; the image route re-encodes uploads as JPEG and Phase H accepts WebP.
"$PHP_BIN" -r 'exit((function(){ $i = gd_info(); return !empty($i["JPEG Support"]) && !empty($i["WebP Support"]) && !empty($i["FreeType Support"]); })() ? 0 : 1);' \
  || fail "gd lacks JPEG, WebP or FreeType support"

MEMORY="$("$PHP_BIN" -r 'echo ini_get("memory_limit");')"
note "memory_limit" "$MEMORY"

for DIR in "$APP_ROOT/bootstrap/cache" "$APP_ROOT/storage"; do
  if [ -d "$DIR" ] && [ -w "$DIR" ]; then note "writable" "$DIR"; else fail "not writable: $DIR"; fi
done

if [ "$FAIL" -eq 0 ]; then echo "preflight OK"; else echo "preflight FAILED"; fi
exit "$FAIL"
```

`chmod +x` it.

- [ ] **Step 4: Run the test green**

Run: `docker compose exec -T app php vendor/bin/phpunit tests/Unit/PreflightScriptTest.php` — 2 passed.
Also run it by hand and paste the output into the report: `docker compose exec -T app bash scripts/deploy/preflight.sh "$(which php)" /var/www/html`.

- [ ] **Step 5: Run the local gate and commit**

```bash
git add scripts/deploy/preflight.sh tests/Unit/PreflightScriptTest.php
git commit -m "Add a deploy-target preflight for PHP version, extensions and writable paths"
```

---

## Task 4: The staging runbook

**Files:**
- Create: `docs/deploy/staging.md`
- Create: `docs/deploy/preflight-answers.md`

**Interfaces:**
- **Consumes:** `scripts/deploy/preflight.sh` (Task 3), `scripts/deploy/probe-release.sh` (Task 1).
- **Produces:** the owner's answers file that Tasks 5, 7 and 8 read.

**Context.** The spec asks for `staging.aldar-emlak.com` on the same account, with its own DB copy, HTTP Basic Auth and `X-Robots-Tag: noindex`, on PHP 8.4. Ten server facts are unknown (research section 6), and the plan must not guess them.

- [ ] **Step 1: Write `docs/deploy/preflight-answers.md`**

A table the owner fills in, one row per question, each with "how to find out" as a command they can paste. The questions:

1. Can `public_html` be replaced by a symlink to `current/public`, and does LiteSpeed serve through it? (Today it is a real directory.)
2. Does `/opt/alt/php84/usr/bin/php` exist, and does `scripts/deploy/preflight.sh` pass with it?
3. Is there an hPanel cron running `schedule:run`, and with which PHP binary?
4. How does the client IP reach PHP behind the CDN (`scripts/hotfix/probe-client-ip.sh`)? This gates `TrustProxies` and Phase 2's S6 rate limiter.
5. Can the PHP version be selected per directory with an `.htaccess` handler, so a release can switch 7.4 → 8.4 atomically?
6. Is there a CDN purge control in hPanel, and can it be triggered from the command line?
7. What is the production database name, and can a second database be created for staging?
8. How much free disk is there, given uploads are 3.1 GB and five releases are kept?
9. Which SSH keys exist, and is hPanel 2FA on? (Phase H open item N7.)
10. Has the N8 git history rewrite run? (Phase H open item; it must happen before Phase 3's first release, because the release id is a commit SHA.)

- [ ] **Step 2: Write `docs/deploy/staging.md`**

Sections, every command marked **owner runs**:
- Create the subdomain and its document root; note the path the owner reports back.
- Create the staging database and import a production dump, with the `mysqldump`/import commands and a reminder that the dump never enters git.
- Copy `.env` to `shared/.env` for staging and change: `APP_ENV=staging`, `APP_DEBUG=false`, `APP_URL=https://staging.aldar-emlak.com`, the DB name, `SESSION_SECURE_COOKIE=true`, `LOG_DEPRECATIONS_CHANNEL=deprecations`, and mail settings that cannot reach real customers.
- Protect the site: `.htpasswd` Basic Auth plus `Header always set X-Robots-Tag "noindex, nofollow"`, with the exact `.htaccess` block, and the note that Basic Auth must allow the Playwright probe (the probe sends credentials from the environment; document the variable names, never the values).
- Run `scripts/deploy/preflight.sh` and paste the output into `preflight-answers.md`.
- The staging smoke list: home ar/en, a property page, a contact form submission, admin sign-in, an article created and deleted, currency change.
- The SMTP check: send one password-reset mail from staging and confirm it arrives, because Laravel 13 derives the SMTP scheme from the port and ignores `mail.encryption`.

- [ ] **Step 3: Verify the runbook is executable as written**

Re-read both files and check that every command is complete, has no placeholder like `<fill in>` in a position that would silently do the wrong thing, and never prints a secret. Any value the owner must supply appears as a shell variable they set at the top of the section.

- [ ] **Step 4: Commit**

```bash
git add docs/deploy/staging.md docs/deploy/preflight-answers.md
git commit -m "Add the staging setup runbook and the server preflight questions"
```

---

## Task 5: `deploy.sh` and `rollback.sh`

**Files:**
- Create: `scripts/deploy/deploy.sh`, `scripts/deploy/rollback.sh`
- Create: `docs/deploy/releases.md`
- Test: `tests/Unit/DeployScriptTest.php` (new)

**Interfaces:**
- **Consumes:** `preflight.sh` (Task 3), `probe-release.sh` (Task 1), the answers file (Task 4).
- **Produces:** `deploy.sh <staging|production> <git-ref> [--dry-run]` and `rollback.sh <staging|production>`, used by Tasks 7 and 8.

**Context.** The spec's server layout is `releases/<YYYYmmddHHMMSS>-<sha>/`, `shared/.env`, `shared/storage`, `current -> releases/<…>`, `public_html -> current/public`, keeping the last 5 releases. The scripts run from the developer machine over SSH, the way `scripts/hotfix/deploy.sh` already does; reuse its SSH-alias handling and its `--dry-run` discipline, not its in-place patching or its drift check against the root commit.

- [ ] **Step 1: Write the failing test**

`tests/Unit/DeployScriptTest.php` drives both scripts in a fake environment: a temporary directory standing in for the server root, with `SSH_CMD=bash -c` style injection so no network is used. Assert:
- `deploy.sh production <ref> --dry-run` prints the planned release id and exits 0 without creating anything;
- a deploy into the temp root creates `releases/<id>`, links `shared/.env` and `shared/storage`, and leaves `current` pointing at the new release;
- a second deploy keeps `current` pointing at the newest, and prunes to five;
- `rollback.sh` moves `current` back to the previous release and refuses when there is only one;
- both scripts exit non-zero when `preflight.sh` fails, and neither switches `current` when the probe step fails.

- [ ] **Step 2: Run it and watch it fail** — the scripts do not exist.

- [ ] **Step 3: Write `deploy.sh`**

Order of operations, each step aborting the deploy on failure, and `current` switching only at the end:

1. Resolve `<git-ref>` to one SHA; refuse a dirty tree and a ref that is not pushed.
2. Read the target's SSH alias and paths from a small config block at the top of the script (staging vs production), never hard-coded secrets.
3. Run `preflight.sh` on the target with that target's PHP binary.
4. Build the release directory from `git archive <sha>` under `releases/<timestamp>-<sha7>`.
5. Link shared state: `.env`, `storage`, and any path the answers file names.
6. `composer install --no-dev --optimize-autoloader` with the target PHP binary. Fail on any "Ambiguous class resolution" line in the output (Phase 2's P2-R30 guards the repo; this guards the server).
7. `config:cache` and `view:cache`. `route:cache` runs only if the answers file records that `route:trans:cache` works, otherwise it is skipped with a printed reason (the localized routes 404 under a plain `route:cache`).
8. Back up the database with `scripts/server/db-dump.php`, then `php artisan migrate --force`. The migration set is expected to be a no-op on production.
9. Run `probe-release.sh` against the release's URL. On staging that is the staging host. On production, the probe runs against the *staged* release URL if one exists, otherwise this step is skipped with a printed reason and the post-switch verification in Task 6 carries the weight.
10. Switch `current` atomically (`ln -sfn` into a temp name plus `mv -T`), then clear opcache by touching the front controller or restarting PHP-FPM if the host exposes it.
11. Run `verify-release.sh` (Task 6). On failure, print the exact `rollback.sh` command and exit non-zero.
12. Prune to the last 5 releases.

`--dry-run` stops after step 3 and prints the plan.

- [ ] **Step 4: Write `rollback.sh`**

Repoint `current` to the previous release, clear the caches, run `verify-release.sh`, and print what it did. Refuse when there is no previous release. It never touches the database: a migration that must be undone is a forward fix, and the DB backup from step 8 is the recovery path.

- [ ] **Step 5: Run the tests green, then the local gate**

- [ ] **Step 6: Write `docs/deploy/releases.md`** — the layout diagram, the first-release procedure, how to read the script output, and the rollback drill Task 7 runs.

- [ ] **Step 7: Commit**

```bash
git add scripts/deploy/deploy.sh scripts/deploy/rollback.sh docs/deploy/releases.md tests/Unit/DeployScriptTest.php
git commit -m "Add release-based deploy and rollback scripts with a dry run and a preflight gate"
```

---

## Task 6: `verify-release.sh`

**Files:**
- Create: `scripts/deploy/verify-release.sh`
- Test: `tests/Unit/VerifyReleaseScriptTest.php` (new)

**Interfaces:**
- **Consumes:** nothing.
- **Produces:** `verify-release.sh <base-url>`, called by `deploy.sh` step 11, `rollback.sh`, and by the owner after the cutover.

**Context.** Phase H's `verify-production.sh` is the model: read-only, randomised where it can be, and specific about what it proves. This is its Phase 3 successor, and it must also prove the Phase 1 and Phase 2 hardening survived the deploy.

- [ ] **Step 1: Write the failing test** — the script is driven against the local stack and must pass there, and must fail when given a URL that answers 404 for the home page.

- [ ] **Step 2: Write the script.** Checks, all read-only:
- `/en` and `/ar` answer 200 and carry the four security headers plus the Report-Only CSP (Phase 2 S3);
- a property page, an article page and the contact page answer 200;
- an image URL answers 200 with `image/jpeg`, and a randomised unknown size answers 404 (Phase H H4);
- a random `.php` name under `/graph/uploads/original/tinymce/` and under `/modules/` answers 403 or 404, never 200 (Phase H H1/H9, Phase 2 S16);
- `/en/admin/notification/config` redirects to login for an anonymous caller (Phase 2 S9);
- `GET /en/admin/clear-cache` is not routed (Phase 2 S7 made it POST);
- the deployed commit matches the release id, read from a path the release writes at build time;
- no page carries `X-Powered-By` with a 7.x version, proving the PHP version switch.

- [ ] **Step 3: Run it green against the local stack, then the local gate.**

- [ ] **Step 4: Commit**

```bash
git add scripts/deploy/verify-release.sh tests/Unit/VerifyReleaseScriptTest.php
git commit -m "Add the post-switch release verification script"
```

---

## Task 7: Prove the release layout on staging (**owner runs**, agent prepares and reads back)

**Files:**
- Modify: `docs/deploy/preflight-answers.md`, `docs/deploy/releases.md`

**Context.** The one fact that decides the layout — whether `public_html` may be a symlink — is unknown, and today it is a real directory. The spec's fallback is a `public_html` holding only the front controller and assets, with the application living outside it.

- [ ] **Step 1: The owner runs the staging setup** from `docs/deploy/staging.md`, and pastes the preflight output into the answers file.
- [ ] **Step 2: The owner runs `deploy.sh staging <sha> --dry-run`, then for real**, and pastes the output.
- [ ] **Step 3: The agent reads the output and records the outcome** in `releases.md`: symlink worked, or the fallback layout is required. If the fallback is required, the agent writes the fallback into `deploy.sh` as a second layout mode and re-runs Task 5's tests. That change is its own commit.
- [ ] **Step 4: The owner runs the rollback drill**: deploy a second release, roll back, verify, roll forward. The agent records the timings and any surprise.
- [ ] **Step 5: The owner runs the read-only probe** (`scripts/deploy/probe-release.sh https://staging.aldar-emlak.com`) and the staging smoke list, and reports the results. Snapshot mismatches caused by staging data are expected; the agent classifies each one and lists them in `releases.md` rather than editing any baseline.
- [ ] **Step 6: Commit** the updated runbooks and any layout-mode change.

---

## Task 8: The cutover runbook

**Files:**
- Create: `docs/deploy/cutover.md`

**Context.** Spec: final DB backup, maintenance mode for a few minutes, switch, read-only parity subset against production, watch the logs for 24 hours, roll back on any regression. Phases 1 and 2 add: everyone is logged out once (sessions moved to JSON and `APP_KEY` stays), the cookie-consent and area cookies reset while `default-currency` survives, `perms_roles.level` and every `perms_*.scope` must be NULL, and the production `.env` needs `APP_DEBUG=false` and `SESSION_SECURE_COOKIE=true`.

- [ ] **Step 1: Write the runbook**, every command marked **owner runs**, in this order:
1. Confirm the answers file is complete and the staging drill passed.
2. Confirm PRs #4 and #5 are merged and the release SHA is on `main`.
3. Confirm `perms_roles.level` and every `perms_*.scope` are NULL in production (the exact SQL, read-only).
4. Final DB dump and a file backup of the current `public_html`.
5. Set the production `.env` values: `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `LOG_DEPRECATIONS_CHANNEL=deprecations`, the currconv key (rotated, Phase 2 S14), and the mail settings staging proved.
6. Maintenance mode on.
7. `deploy.sh production <sha>`.
8. Switch the PHP version for the release, either by the per-directory handler or in hPanel, whichever the answers file recorded.
9. Maintenance mode off, then `verify-release.sh https://aldar-emlak.com` (**owner runs**; no agent session may call it).
10. Purge the CDN cache, then re-check one page and one asset for staleness.
11. The owner's smoke list, the same six flows as staging.
12. Watch `storage/logs` for 24 hours: `laravel-*.log` for exceptions and `deprecations.log`, which must stay empty.
13. The rollback decision rule: any failed check in step 9, or any 500 in a smoke flow, means `rollback.sh production` immediately, then diagnose from the backups.

- [ ] **Step 2: Add the post-cutover cleanup list**: purge the old `public_html` backup after a week, delete the staging DB copy if it holds production leads, and re-check that `~/aldar-credentials/*` files from Phase H are gone.
- [ ] **Step 3: Commit.**

---

## Task 9: Documentation, counts and the PR

**Files:**
- Modify: `tests/e2e/PARITY.md`, `docs/deploy/releases.md`
- Create: the PR body in the workspace, not in git

- [ ] **Step 1: Update `PARITY.md`** with the final suite totals and the remote-probe section.
- [ ] **Step 2: Exit evidence:** `docker compose restart app`, then the full local gate twice in a row, plus PHPUnit, structure, phpstan, the deprecation check and the two `public/` diff checks. Both `npm test` runs must show the same total with zero failures.
- [ ] **Step 3: Write the PR body** covering: what changed in code (Tasks 1–3), the scripts and runbooks, what the owner ran on staging and what it proved, every question from the answers file with its answer, the accepted differences (if any) from the staging probe, and the cutover checklist with its rollback rule.
- [ ] **Step 4: Commit the docs, then the controller opens the PR** with base `main` (or the previous phase branch if it has not merged yet).

---

## Self-review

**1. Spec coverage (Phase 3 section).**

| Spec item | Task |
|---|---|
| `staging.aldar-emlak.com` with its own DB copy, Basic Auth, `X-Robots-Tag: noindex`, PHP 8.4 | 4, 7 |
| Server layout: `releases/`, `shared/.env`, `shared/storage`, `current`, `public_html` | 5, 7 |
| `deploy.sh` steps 1-8 (archive, link, composer, caches, DB backup, migrate, probe, switch, keep 5) | 5 |
| `rollback.sh` points `current` back | 5 |
| Validate the symlinked `public_html`, with the front-controller fallback | 7 |
| Per-release PHP version switching, or hPanel at cutover | 4 (question 5), 8 (step 8) |
| Production cutover: backup, maintenance mode, switch, read-only parity, 24-hour watch, rollback | 8 |
| Found in Phase 1: `route:cache` vs mcamara | 5 (step 7 gate), 4 (question) |
| Found in Phase 1: `env()` outside config under `config:cache` | 2 |
| Found in Phase 1: Hostinger preflight (PHP ≥ 8.4.1, extensions, `bootstrap/cache`) | 3 |
| Found in Phase 1: SMTP scheme from the port | 4 (staging mail check) |
| Phase 2 S6: the IP-keyed limiter needs the H5 TrustProxies probe | 4 (question 4) |
| Phase 2 P2-R11: static-file security headers | 6 (verify), 7 (decide on staging) |
| `migrate --force` is a no-op on production | 5 (step 8), 8 (step 7) |
| Open item N8: history rewrite before release ids are SHAs | 4 (question 10) |

**2. Placeholder scan.** Tasks 1-3 carry complete code. Tasks 5 and 6 specify each script's steps, order, abort conditions and test assertions rather than full source, because both scripts' exact commands depend on answers the owner supplies in Task 4; each step names what it must do and what proves it. No step says "handle errors" without saying which error and what the script does about it.

**3. Name and type consistency.** `PARITY_ALLOWED_HOST` (Task 1) is the same variable the existing `global-setup.ts` allowlist reads. `IS_REMOTE_PROBE` is new and used only by later specs. `config('debug.enabled')`, `config('app.env')` and `config('permissions.superpowers')` (Task 2) are the only new config keys, and each appears in its own `config/*.php` file. `preflight.sh <php-binary> <app-root>`, `probe-release.sh <base-url>`, `verify-release.sh <base-url>`, `deploy.sh <target> <git-ref> [--dry-run]` and `rollback.sh <target>` keep those signatures everywhere they are called.

**4. Task pairs sharing files or interfaces.**

| Tasks | Shared thing | Finding |
|---|---|---|
| 1 → 5, 6, 7, 8 | `probe-release.sh` | Task 1 creates it; later tasks only call it |
| 3 → 5, 7 | `preflight.sh` | created once, called by `deploy.sh` and by the owner |
| 4 → 5, 7, 8 | `preflight-answers.md` | Task 4 creates the questions; 7 fills them; 5 and 8 read them |
| 5 → 6 | `deploy.sh` calls `verify-release.sh` | Task 5 references it before Task 6 writes it: Task 5's step 11 must tolerate the script being absent during its own tests, and Task 6 makes it real. Consistent, and called out here so the implementer expects it. |
| 2 → 5 | `config:cache` in the deploy | Task 2 must land before any release runs `config:cache` |
| 1, 9 | `tests/e2e/PARITY.md` | different sections |

**5. Known unknowns the plan does not resolve.** The symlink capability, the CDN's client-IP behaviour, per-release PHP switching, the cron entry, and the production DB name are all owner answers. Tasks 5 and 8 read them rather than assuming; Task 7 is the gate that turns them from questions into recorded facts before production is touched.
