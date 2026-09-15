# Aldar Real Estate — Security Hotfix and Laravel 13 Upgrade — Design

**Date:** 2026-09-14
**Status:** Approved in brainstorming. Written spec pending owner review.
**Repository:** `M9Nor/aldar-php` (private)
**Scope:** Close the confirmed vulnerabilities on the live site first. Then upgrade the technical foundation from Laravel 7.3 / PHP 7.4 to Laravel 13 / PHP 8.4, with no change to features or appearance, and move deployments to GitHub-based releases on Hostinger.

## Context

aldar-emlak.com is a bilingual (ar/en) real-estate site built by Namaa Solutions in 2020. It is a Laravel 7.3 app with five nwidart modules: Backend, Cms, Frontend, Notification and Permissions. It runs on the `codecamb` Hostinger shared account (`~/domains/aldar-emlak.com/public_html`) on PHP 7.4 and MariaDB 11.8.

- About 58k lines of PHP and 47k lines of Blade. There are no real tests: the only two test files are the framework's examples.
- **Business-critical flow:** the contact forms. They have collected 3,700 leads, and leads were still arriving on 2026-09-13.
- **Owner decisions:**
  - Scope is technical foundation and security only. No redesign, SEO, performance or marketing work.
  - Keep every feature, including dormant ones (Firebase push, `finance`, `property_form`, landing pages).
  - Upgrade by a direct jump from 7 to 13, not version by version.
  - Stay on Hostinger. Deploy from GitHub, with a staging site in between.
  - Security fixes are urgent and ship before any upgrade work.
  - The former vendor, Namaa Solutions, must have no access of any kind.
- **Incident clarification:** the owner reported unexplained server edits. Investigation traced them to the owner's own earlier Claude Code sessions:
  - 2026-08-27: fixed the broken `OpportunityController@search` route.
  - 2026-09-14: set `APP_ENV=production` and disabled debug dumps that leaked device tokens.
  - No backdoor, rogue admin account, PHP file in uploads or injected script was found. This is not treated as a breach.
- **Local environment:** Docker (`docker-compose.yml`) with PHP 7.4 Apache, MariaDB 11.8 seeded from `_db-backup/aldar-db-20260914-1704.sql.gz` (not in git), and Mailpit. The site runs at http://localhost:8080.

## Goals

1. Close every confirmed vulnerability on production within the urgent hotfix phase.
2. Remove all access paths for the former vendor.
3. Run on supported software: Laravel 13 (security fixes until ~Q1 2028) and PHP 8.4 (security support until end of 2028).
4. Prove behaviour parity with an automated black-box test suite. The same suite runs against Laravel 7, Laravel 13, staging and production.
5. Make deployments repeatable, auditable and reversible in seconds.

## Non-goals

- Visual or UX changes. Compiled CSS/JS in `public/` stays byte-identical, and there is no move from Mix to Vite.
- Removing dormant features or data.
- Moving to the Laravel 11+ slim application structure.
- Upgrading front-end libraries such as old jQuery. They are reported, not changed.
- SEO, performance or lead-generation work.

## Constraints

- **Parity (1:1).** Any intentional output change must be listed and accepted explicitly in its PR.
- **No PII in git.** DB dumps, lead data, logs and `.env` files stay out. Snapshots of admin pages that show customer data assert structure only.
- **Hostinger shared hosting.** No long-running workers. Cron is configured in hPanel. PHP 7.4 to 8.5 is available under `/opt/alt/phpXX`.
- **Branching.** Every phase lands on `main` through a PR, and `main` is always deployable.

## Phase order

| Phase | Branch | Outcome |
|---|---|---|
| **H — Urgent security hotfix** | `hotfix/security` | Vulnerabilities closed and vendor access removed on the current Laravel 7 production |
| **0 — Parity safety net** | `test/parity-baseline` | Black-box suite green on Laravel 7, baseline snapshots committed |
| **1 — Laravel 13 jump** | `upgrade/laravel-13` | Laravel 13 / PHP 8.4 locally, parity suite green |
| **2 — Remaining hardening** | `security/hardening` | Hardening items that do not need to ship urgently |
| **3 — Staging and release** | `deploy/releases` | Release-based deploys on staging, then production cutover |

---

## Phase H — Urgent security hotfix (Laravel 7, production)

### H.1 Findings and fixes

| ID | Finding (evidence) | Fix |
|---|---|---|
| H1 | **Arbitrary file upload leading to remote code execution.** `Modules/Cms/Http/Controllers/Admin/TinymceController.php` `uploader()` writes base64 content, under a client-supplied filename, to the `graph` disk. That disk is rooted at `public/graph/uploads/original` (`CmsServiceProvider::boot`), so it is web-accessible. A `.php` filename would execute. The permission check is commented out, and the ability it references (`TINYMCE_UPLOADER`) does not exist in the DB. All 133 existing files are images, and the server holds no non-image files there, so there is no sign of exploitation. | Decode the base64 and accept only JPEG, PNG, GIF or WebP, verified by `finfo` MIME detection plus `getimagesizefromstring`. Keep the existing size limit. Generate the filename on the server (random plus an extension derived from the detected MIME) and ignore the client filename. Allow only users passing the `staff` middleware (see H8); do not restore the check against the missing ability. Defence in depth: add `public/graph/.htaccess` denying `.php`, `.phtml` and `.phar`. |
| H2 | **`GET /update_currency` is public** (`routes/web.php:14`) and runs `Artisan::call('update_currency')`, which calls an external currency API. | Move the route into the authenticated admin group as a POST with CSRF. The dashboard button (`Modules/Cms/Resources/views/dashboard.blade.php:23`, currently an `<a target="_blank">` hard-coded to `https://aldar-emlak.com/update_currency`) becomes a small form posting to `route(...)`, with the same classes and label so it looks identical. The schedule in `app/Console/Kernel.php` stays, and a Hostinger cron runs `schedule:run`; check the hPanel cron list first. |
| H3 | **`GET /clear-cache` is public** (`Modules/Frontend/Routes/web.php:20`). It calls `Cache::flush()`. | Move it into the authenticated admin group. |
| H4 | **Unbounded image generation.** `Modules/Cms/Http/Controllers/ImageController.php` `show()` accepts any `{size}`, and `quality`, `extension` and `mark` query parameters, then writes a cached variant per combination (the cache is at 11,788 files, 727 MB). Visitors can fill the disk. | Allow only the 59 size literals that appear in PHP/Blade code (entity `$imageOptions['dimensions']`, `getImage('…')`, `route('image', ['size' => …])`; no JavaScript builds `/img/` URLs), stored in `config/image_sizes.php`, plus `original`. As a 1:1 safety net, a size outside the list is still served when Glide already holds a cached variant for it; otherwise it returns 404. Ignore `quality`, `extension` and `mark`, which no view uses. **Serve only canonical paths.** Glide hashes the path it is given into the cache key, while Flysystem normalises it on read and write, so every alias of a real file would get its own cache entry. Read `size` and `path` from the route parameters (never query input), and return 404 before Glide runs unless the path equals `League\Flysystem\Util::normalizePath($path)`, contains no backslash, no segment starting with `.` (this covers `.cache/…`), and no percent-escape (Glide `rawurldecode()`s the path, so `%252e` and `%252F` are aliases too). |
| H5 | **Spam on the contact forms.** 1,780 of 3,700 leads contain URLs, and 20 contain HTML tags. There is no rate limit on `contact-us/store`, `store-inner` or `subscribe`. | Add a hidden honeypot field to the seven form instances that post to these endpoints. A filled honeypot gets the normal success JSON but nothing is stored. Allow 5 successful submissions per 10 minutes per IP per endpoint. Once over the limit, return HTTP **200** with `{"success": false, "message": <translated text>}`, because `submitFroms()` has an empty `error` callback and would otherwise show nothing. **Before enabling the limit in production,** probe how the client IP reaches PHP behind Hostinger's CDN (`server: hcdn`). If PHP sees CDN addresses, configure `TrustProxies` first; otherwise every visitor would share one quota. Humans see no change. |
| H6 | **TLS verification disabled** for the FCM call (`Modules/Notification/Entities/FirebaseNotification.php`: `CURLOPT_SSL_VERIFYHOST 0`, `CURLOPT_SSL_VERIFYPEER 0`). | Set `CURLOPT_SSL_VERIFYHOST 2` and `CURLOPT_SSL_VERIFYPEER true`. |
| H7 | **Public `GET /seed` and `GET /migrate`** (`Modules/Cms/Routes/web.php:15-35`), confirmed in the production route list. `/seed` runs `db:seed` and `module:seed`, which calls `UsersTableSeeder::createRootUser()`: it re-activates `root@namaa-solutions.com` and resets its password to `config('cms.root.password')`, a literal in the production config. Anyone can re-open the vendor's ROOT account at will, even after N1. | Delete both routes. They must ship in the same deploy as the other fixes and before N1 runs. Test: both return 404. |
| H8 | **Unauthenticated attachment upload and deletion.** `Modules/Cms/Http/Controllers/Admin/AttachmentController.php` has no `auth` middleware (confirmed in the production route list). `store()` takes its validation rules from the request (`validation_rules`), the sub-folder (`sub_folder`) and the client's filename. The file is written before the request fails on `auth()->user()->id`, so anyone can add files or overwrite existing images (for example `sub_folder=../projects`). `destroy()` deletes any attachment row and image by id. | Add the `staff` middleware (authenticated, not disabled or deleted, role SUPERADMIN, ADMIN or Editor) to both actions. Accept `validation_rules` only if it exactly matches one of the five rule strings the admin views send; otherwise use `required|file|max:2048|mimes:jpeg,jpg,png,pdf`. Reduce `sub_folder` to one `[a-z0-9_-]` segment, falling back to `general`. Store under a random server-generated name with the guessed extension. Keep the client filename in the `filename` column only. `dropzoneFront.blade.php` is not included anywhere, so no public page uses these endpoints. |
| H9 | **Unauthenticated mail header-injection scripts under `public/modules`.** `public/modules/frontend/form/process-contact.php`, `form/quote-contact.php`, `landingpage/libs/contact-form-process.php` and `landingpage/libs/quote-form-process.php` are template leftovers that pass `$_REQUEST['email']` straight into `mail()` headers, so a CRLF adds `Bcc:` recipients. Nothing in the app reaches them: the only references are relative AJAX URLs in template JS that resolve under the page URL, from forms no rendered view contains. On a shared account hosting 16 sites, spam abuse risks a Hostinger suspension. | The hotfix deploy cannot delete files, so add `public/modules/.htaccess` with the same PHP deny block as `public/graph/.htaccess`. Test: `GET /modules/frontend/form/process-contact.php` and a random `.php` name under `/modules/` return 403, and a static CSS file under `/modules/` still returns 200. |

### H.2 Removing vendor access

| ID | Access path | Action |
|---|---|---|
| N1 | Active ROOT account `developer` (`root@namaa-solutions.com`, id 1). Its seeder password was a literal in `Modules/Cms/Config/config.php`. Login already refuses disabled or deleted accounts: `App\User` uses `SoftDeletes` plus the `Disabable` global scope, so the user provider never finds such rows. | A new idempotent command, `aldar:offboard-vendor`, handles every user with an `@namaa-solutions.com` email: revoke all roles, set a random 64-character password (never stored or shown), set status `DISABLED`, set `disabled_at` and `deleted_at`, and clear `remember_token`. No `LoginController::credentials()` override is needed (it was dropped as redundant, ruling R5); the command queries users with `withDisabled()->withTrashed()` so reruns and already-disabled vendor rows are handled. A regression test proves the login is rejected even with the old password. Run it only after H7 is live, straight after the deploy is verified. |
| N2 | Literal seeder passwords for `root@`/`superadmin@namaa-solutions.com` in config (a literal `Hash::make` call). | Done in commit `afe3e63` (read from env). The hotfix also removes the vendor emails from the `root`/`superadmin` seeder config (`PermissionsDatabaseSeeder` reads `config('cms.root.*')`). Values come from env, with no vendor defaults. |
| N3 | `APP_KEY` was created by the vendor, and it is also in the leftover `.env.bak-20260914-*` files in `public_html`. | Rotate `APP_KEY` (`php artisan key:generate --force` with `/opt/alt/php74/usr/bin/php`). The DB contains no encrypted data (no `Crypt`/`encrypt`/`encrypted` usage found). Impact: all sessions are logged out once. Delete every stale `.env*` copy except `.env` itself (`.env.bak-*`, any `.env.production`). |
| N4 | The DB password may be known to people who handled the migration. | Rotate it in hPanel and update `.env` in the same step. |
| N5 | Admin accounts 8 (`aldar`), 27 (`aldar-emlak`) and 29 (`growth`) were created during the vendor era, so the vendor may know their passwords. | Owners set new passwords with a new artisan command, `aldar:set-password {username}`, which prompts with hidden input. The owner runs it over SSH; Claude never sees the values. |
| N6 | Service workers in `public/service-worker.js` and `public/firebase-messaging-sw.js` are configured for the vendor's Firebase project `binaa-prod`. 359 browsers subscribed historically. Public pages no longer register them. | Replace both files with self-unregistering workers that call `self.registration.unregister()` and hold no Firebase config. Browsers that check for updates drop the subscription. The Notification module code stays, per the 1:1 constraint. |
| N7 | SSH keys on the Hostinger account. Five keys exist and none belong to the vendor. | **Owner action:** confirm the two keys whose comments are near-identical spellings of the same personal Gmail address. Recommended: enable 2FA on hPanel, because the account hosts 16 sites. |
| N8 | Git history: the baseline commit `0edf5d6` on GitHub still contains the literal seeder passwords. | **Owner action:** rewrite history, **after** the hotfix deploy. The owner approved it, but Claude Code's safety classifier blocks force-pushes. The command is in "Open items" below. N1 makes the passwords useless either way. The deploy's drift check compares the server against the root commit, so rewriting the root first would break it. |

Vendor branding (the footer "Powered by Namaa" link and `twitter:site @namaa_solutions`) is not an access path. It stays untouched in this project.

### H.3 Verification

- **Playwright project** at `tests/e2e/`, TypeScript, with specs in `tests/e2e/specs/{smoke,security}/`. Phase 0 extends the same project. Artisan commands get PHPUnit feature tests in `tests/Feature/`.
- **Security tests.** Each finding gets a test that fails on the current code and passes after the fix:
  - H1: uploading `shell.php` or a PHP payload disguised as `image.jpg` is rejected, and a real JPEG is accepted with a server-generated name.
  - H2, H3: the old public URLs return 404, the new admin routes send anonymous users to login, and the dashboard button still works.
  - H4: an unknown size returns 404, every size on the key pages returns 200, and `quality` does not create new cache files. Non-canonical paths (`%2e` and `%2e%2e` segments, `//`, `%5C`, `%252F`, `.cache/…`, `?path=`) return 404 and leave the cache file count unchanged; the test sends the raw path over a socket so no client normalises it first.
  - H5: a filled honeypot stores no row, and the sixth successful submission within 10 minutes is refused with `success: false`.
  - H7: `/seed` and `/migrate` return 404.
  - H8: anonymous upload and delete requests leave storage and the DB unchanged.
  - H9: PHP under `/modules/` returns 403 while static assets there return 200.
  - N1: the `developer` login is rejected.
- Tests that write data refuse to run unless `BASE_URL` is the local Docker stack.
- **Smoke tests for the flows that must keep working:**
  - home, a property, the articles list and the contact page, in ar and en;
  - one valid contact form submission creates a `contact_us` row;
  - admin login with a local test account;
  - an admin page that uses TinyMCE upload.
- Run locally in Docker. Before deploy, run the whole suite twice.

### H.4 Deploying the hotfix to current production

The release-based structure does not exist yet (Phase 3), so the hotfix patches the current `public_html` in place. `scripts/hotfix/deploy.sh <git-ref>` does the following:

1. Resolve `<git-ref>` to one commit SHA and use that SHA for every later step. Resolve the application files that differ between the root commit (the server snapshot) and that SHA, additions and modifications only, excluding `tests/`, `docs/`, `scripts/` and `docker/`.
2. **Drift check.** For each file, compare the server copy (CR stripped) with the root-commit version. Abort on any mismatch, except for files listed in `scripts/hotfix/known-baseline-edits.txt`, which were deliberately changed in the baseline commit.
3. On the server, back up those paths into `~/aldar-backup/hotfix-<timestamp>.tar.gz`, record newly added files in `hotfix-<timestamp>.added`, and dump the DB with `scripts/server/db-dump.php`, which uses Laravel's own config.
4. Stream the files from `git archive <sha>` into `public_html`.
5. Run `view:clear`, `route:clear`, `config:clear` and `cache:clear` with `/opt/alt/php74/usr/bin/php`.
6. Run `scripts/hotfix/verify-production.sh`, which is read-only:
   - public pages return 200 in ar/en;
   - `/seed`, `/migrate`, `/update_currency` and `/clear-cache` are no longer public routes, and the admin replacements, attachment and TinyMCE routes carry the `staff` middleware (read from the server's route list);
   - the contact endpoints carry `contact.guard`;
   - an unknown image size, randomised on every run, returns 404;
   - `/service-worker.js` and `/firebase-messaging-sw.js` are the unregistering workers;
   - a request for a random `.php` name under `/graph/uploads/original/tinymce/` and under `/modules/` returns 403. This is the only production proof that LiteSpeed honours the `.htaccess` deny blocks: without them the request falls through to Laravel.

`--dry-run` stops after step 2.

`scripts/hotfix/rollback.sh --reopens-public-seed <timestamp>` restores the tarball and clears caches. It refuses to run without that flag, because restoring the old route files brings back the public `/seed` route and the vendor's seeder password. Use it only before `aldar:offboard-vendor` has run; after that, fix forward.

**Rollout order after the deploy is verified** (the exact commands are in Task 11 of the Phase H plan). `AuthenticateSession` is not enabled, so sessions must be wiped only after every credential has changed, and the vendor's ROOT account must not stay active during the owner's smoke test:

1. Offboard the vendor (N1) immediately. From here on, fix forward.
2. Owners set new admin passwords (N5).
3. Back up `.env` on the server, then change the DB password in hPanel and update `.env` straight away (N4). Expect seconds of DB errors.
4. Rotate `APP_KEY` (N3), confirm the key changed, wipe the sessions with `find storage/framework/sessions -type f -delete`, delete every stale `.env*` copy, and clear the config cache.
5. Audit users and role assignments against a snapshot taken before the deploy.
6. Owner smoke test, then re-run the post-deploy check.

---

## Phase 0 — Parity safety net

**Principle.** Black-box tests treat the site as a visitor would. They are independent of the Laravel and PHP versions, so one suite validates Laravel 7, Laravel 13, staging and production.

- **Tooling:** Playwright (TypeScript) in `tests/e2e/`, run with `npm run parity`.
- **Deterministic data:** `scripts/parity/db-reset.sh` re-imports the dump into the Docker DB, then creates `parity-superadmin` and `parity-admin`. Real accounts are never used by tests.

**Layers:**

1. **Golden-master HTML.**
   - A generated URL inventory (150–250 URLs, ar and en) covers: home; property and project pages (deterministic sample by id per type); listing filters and search; articles; services; FAQs; `pages` content; the landing page; 404.
   - Before storing HTML under `tests/e2e/snapshots/`, normalise it: remove CSRF tokens, session-dependent fragments, timestamps and asset version hashes, and collapse whitespace.
   - After the upgrade, any diff is either fixed or accepted in the PR with a reason.
2. **Behaviour.**
   - The three contact endpoints: valid input creates a row, invalid input returns the existing validation JSON.
   - `set_currency`, `/cookies` and the JSON endpoints `listing/regions-by-city-id/{id}` and `installments-by-payments/{id}`.
   - The `img/{size}/{path}` route, the `/` → `/en` redirect and trailing-slash redirects.
3. **Admin.**
   - Login, logout and failed login.
   - Every admin section loads, and its DataTables JSON has the same shape: projects, opportunities, contents by type, cities, areas, categories, tags, countries, configs, users, roles, landing pages.
   - A full content lifecycle: create with image upload, see it on the front end and via `/img`, edit, delete.
4. **Permissions matrix.** Record from Laravel 7 which admin URLs ADMIN can reach versus SUPERADMIN, and assert the identical matrix afterwards. This guards the 326 Bouncer call sites.
5. **Visual.** Eight key pages × ar/en × desktop/mobile, compared with a small pixel-difference tolerance. This catches missing CSS, fonts or assets.

**Privacy.** Public-page snapshots are committed. Admin pages that show lead or user data assert structure only.

**Exit criteria.** The suite is green twice in a row on Laravel 7, and is merged to `main` before any dependency changes.

---

## Phase 1 — Direct jump to Laravel 13 and PHP 8.4

### Dependency matrix

| Package | From | To |
|---|---|---|
| php | ^7.2.5 (runtime 7.4) | ^8.4 |
| laravel/framework | ^7.0 | ^13.0 |
| nwidart/laravel-modules | ^7.0 | ^13.0 |
| silber/bouncer | v1.0.0-rc.8 | ^1.0.4 |
| yajra/laravel-datatables-oracle | ~9.0 | ^13.0 |
| mcamara/laravel-localization | ^1.5 | ^2.4 |
| astrotomic/laravel-translatable | ^11.8 | ^11.17 |
| league/glide | ^1.5 | ^3.0, plus `league/glide-symfony` ^2.1 (supports glide 2–3, not 4) |
| laravel/ui | ^2.0 | ^4.6 |
| laravel/tinker | ^2.0 | ^3.0 |
| guzzlehttp/guzzle | ^6.3 | ^7.0 |
| **Removed** | doctrine/dbal, fideloper/proxy, fruitcake/laravel-cors, caouecs/laravel-lang (translations already published in `resources/lang/{ar,en,tr}`) | — |
| Dev | facade/ignition, fzaninotto/faker, phpunit ^8.5, collision ^4 | fakerphp/faker ^1.24, phpunit ^12, collision ^8, mockery ^1.6, laravel-debugbar ^4.4, **rector/rector + driftingly/rector-laravel ^2.6, larastan/larastan ^3.12** |

### Decisions

- **Keep the existing application structure:** `app/Http/Kernel.php`, `app/Console/Kernel.php`, the providers, and string `'Controller@method'` routes (689 occurrences) with namespace prefixes kept in route service providers.
- **No asset rebuild.** Mix configs stay untouched.
- **Docker image** moves to `php:8.4-apache` on a supported Debian, so the archive-mirror workaround goes away.
- **Config values that must stay the same:**
  - Keep the explicit `session.cookie` and `cache.prefix` (already set in config), so the Laravel 13 default-prefix changes do not apply.
  - Session `serialization` stays `php` in Phase 1. The move to `json` happens in Phase 2.
- **`Paginator::useBootstrapFour()`**, so the default `->links()` markup matches Laravel 7. Five views use the custom `frontend::includes.pagination` view.

### Fix order (one commit per step)

1. **Docker:** PHP 8.4 image.
2. **Dependencies and boot:** new `composer.json`, resolve, and merge only the Laravel 13 config keys that are required. `php artisan about` runs.
3. **Rector pass:** PHP 7.4→8.4 and Laravel 7→13 rule sets, committed alone so the diff can be reviewed.
4. **Routing and middleware:**
   - route namespaces;
   - localization v2 middleware aliases (`localeSessionRedirect`, `localizationRedirect`, `localeViewPath`);
   - base class of `App\Http\Middleware\VerifyCsrfToken` becomes `PreventRequestForgery`;
   - replace TrustProxies and CORS with the built-in middleware;
   - `RedirectToHttps` stays.
5. **Storage and images:**
   - Flysystem 3 changes (`getDriver()` usage in `ImageServiceProvider`);
   - Glide 3 server factory with `SymfonyResponseFactory` in place of the removed `LaravelResponseFactory`;
   - `ImageController`, `ImageManipulator`, the `graph` disk and attachment uploads.
6. **Auth and permissions:**
   - laravel/ui 4 auth traits in `Modules/Cms/Http/Controllers/Auth/*`;
   - Bouncer 1.0.4: compare the schema of the `perms_*` tables with the package migration, and add a migration only if needed.
7. **Modules:** laravel-modules 13 config, `modules_statuses.json` activator, removal of legacy `start.php` loading, and module service providers.
8. **DataTables 13:** admin list endpoints.
9. **Database layer and views:**
   - review the 66 `DB::raw`/`*Raw` call sites for Expression-to-string casts;
   - replace `str_limit` in `Modules/Cms/Resources/views/config/update_config.blade.php` with `Str::limit`;
   - pagination view names;
   - anything the parity suite flags.
10. **Larastan:** a new baseline. No new errors are allowed on touched code.

### Exit criteria

- The parity suite is green, or every diff is accepted and documented in the PR.
- No PHP deprecation entries are logged during a full parity run.
- The owner does a manual smoke test on the local environment.

### Top risks

| Risk | Mitigation |
|---|---|
| Bouncer rc.8 → 1.0.4 schema or behaviour drift | Schema diff before upgrading, plus the permissions-matrix tests |
| Glide response factory removed | glide-symfony factory, plus image route tests including the H4 whitelist |
| Pagination or markup drift | `useBootstrapFour`, plus golden-master HTML |
| The bundled `I18N_Arabic` date library (used by `Modules/Cms/Entities/Traits/Helpers.php`) on PHP 8 | Deprecation-log gate, plus parity pages that render dates. Only its unused `Examples/PDF` files fail PHP 8.4 lint. |
| Localization v2 route or middleware differences | ar/en golden master and redirect tests |

---

## Phase 2 — Remaining hardening

| ID | Item |
|---|---|
| S1 | Cap `items_per_page` from the request (`Modules/Cms/Http/Controllers/CmsController.php:48,102` and similar) at the largest value used by the UI. |
| S2 | Production config: `APP_DEBUG=false` and `SESSION_SECURE_COOKIE=true`, session `serialization` moves to `json` (logs sessions out once), debugbar not installed (`composer install --no-dev`). |
| S3 | Security headers on every response: HSTS, `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`. The CSP is **Report-Only**, allowing the CDNs in use: amcharts, jsdelivr, unpkg, cdnjs, bootstrapcdn, ampproject. |
| S4 | `composer audit` must report zero known vulnerabilities before any deploy. Front-end libraries are listed in a report only. |
| S5 | Review the 165 unescaped `{!! !!}` outputs. Visitor-supplied data must never be printed raw. Admin-authored CMS HTML stays as it is. |
| S6 | `POST admin/notification/postWebToken` is public: anonymous visitors can write `notif_tokens` rows, and a failure returns the exception message and code in the JSON body. Validate and rate-limit the endpoint, and never return exception detail to the client. |
| S7 | CSRF on `GET admin/clear-cache`: a staff member visiting a hostile page flushes the cache. Make it a POST with CSRF, like `admin/update-currency`. Also:
<br>• Align the route's authorization with the menu item, which only `tags.requests` holders see; today any staff member can call the route.
<br>• Check in a browser whether the dashboard "Update currency" button (H2) works. A global jQuery handler cancels submit-button forms, so it may be inert. If it is, fire the form with a native `form.submit()`, as the logout form does. (Phase 2 research.) |
| S8 | `EnsureStaff` answers JSON callers with a plain-text 403. Return a JSON error body when the request expects JSON. |
| S9 | `GET admin/notification/config` answers 200 to anonymous visitors with the Firebase web client config. If no public page's JavaScript reads it, put it behind `staff`; otherwise document that it is public by design. (Found by the Phase 0 permissions matrix.) |
| S10 | State-changing admin GET routes: `admin/projects/update_prices` rewrites prices, `admin/categories/asdwadwadwdaw` creates categories and filter pages, and `admin/users/login_as/{model}` switches the session. Move them to POST with CSRF, as in S7. Remove the `asdwadwadwdaw` seeding route if nothing in the admin UI calls it. |
| S11 | Eight admin GET routes answer 500 for staff: `notification`, `users/show`, `users/identity/validate_`, `tags/list`, `roles/show`, `projects/show`, `opportunity/show`, and `projects/data` without DataTables parameters. Answer 404 or a validation error instead, without changing any working flow. On Laravel 13, `projects/data` already answers 200 (Phase 1, P1-R19), which leaves seven. |
| S12 | The admin lead lists (`admin/{projects,opportunity}/data_requests`) fail with a DataTables "Malformed UTF-8" error on any page that contains a spam submission with invalid bytes, so staff cannot page past it. Encode the response with invalid-UTF-8 substitution so every lead stays reachable. Business-critical: leads. Still true on Laravel 13: 4 of 74 pages per list fail (Phase 1). |
| S13 | Permissions tests for writes. The Phase 0 matrix covers GET only, and nine rows carry no role signal (404 or "302 back" for both roles). Add ADMIN POST probes that change nothing, such as destroy on a nonexistent id, and use ids that answer 200 for SUPERADMIN. |
| S14 | A hard-coded currconv API key in `Modules/Frontend/Http/Controllers/FrontendController.php` is in git history. Read it from config/`.env` (`services.currconv.key`) instead; the owner rotates the key, and the old value joins the N8 history-rewrite scope. (Found in Phase 1, P1-R40.) |
| S15 | `app/Providers/AppServiceProvider.php` turns on `app.debug` and Debugbar for one hard-coded IP address. Remove it: it leaks debug output to whoever holds that address, and it answers 500 for that address once dev packages are absent (`--no-dev`). (Found in Phase 1, P1-R51.) |
| S16 | Delete the dead contact-form scripts `{process,quote}-contact.php` under `Modules/Frontend/Resources/assets/form/` and their `public/modules` copies. They are the H9 mail header-injection scripts: Apache denies PHP under `/modules`, but `module:publish` would copy the lint-fixed, parseable sources back. Deleting PHP files is not a change to compiled CSS/JS. (Found in Phase 1, P1-R49.) |
| S17 | Flysystem 3 returns `false` instead of throwing when a write fails, so uploads such as the TinyMCE image upload report success on a failed write. Make failed writes raise, or check the return value at each call site. (Found in Phase 1, P1-R51.) |
| S18 | Guard the Laravel 7 behaviour restorations (paginator window, DataTables escaping, Glide encoder quality, laravel/ui overrides) so a package update inside the composer constraints fails loudly instead of silently dropping a restoration: `#[\Override]` on class-method overrides, and a PHPUnit reflection test for trait-method overrides, where `#[\Override]` would be a fatal error. (Found in Phase 1, P1-R51.) |
| S19 | Make the `specs/security/images.spec.ts` raw-socket test "an empty segment returns 404 and caches nothing" deterministic. It failed once with a socket hang-up. A flaky security gate erodes trust in the suite. (Found in Phase 1, P1-R51.) |
| S20 | **Plaintext passwords can reach the logs.** `Modules/Cms/Http/Controllers/Admin/UserController.php` `store`, `update` and `updateProfile` call `\Log::debug($request->all())`, and the log channels write at `debug`. Stop logging request bodies there, and never log password fields anywhere. The owner checks the production `storage/logs` for logged request bodies and deletes those lines or files. (Phase 2 research.) |
| S21 | **Admin endpoints that authorize nothing.**<br>• The lead data endpoints (`{projects,opportunity}/data_requests`, `data_properties`, `request_summary`, `properties_summary`, `show_details`) return every lead to any staff account, while the lead list pages require `tags.requests`.<br>• Admin POST routes with no ability check: `contents/delete-attachemnt`, `landing_pages/delete-timeline`, `{projects,opportunity}/delete-{payment,price}`, `tags/save`, and notification `/` and `getList`.<br>Enforce the same ability the corresponding admin page already requires, so SUPERADMIN flows stay unchanged. (Phase 2 research.) |

Each item gets a test added to the suite.

---

## Phase 3 — Staging and release-based deployment

**Staging.**

- `staging.aldar-emlak.com` on the same account, with its own DB copied from production.
- Protected with HTTP Basic Auth and `X-Robots-Tag: noindex`.
- PHP 8.4.

**Server layout.**

```
domains/aldar-emlak.com/
├── releases/<YYYYmmddHHMMSS>-<sha>/
├── shared/.env
├── shared/storage/            # includes uploads (3.1 GB), logs, sessions
├── current -> releases/<…>
└── public_html -> current/public
```

**`scripts/deploy.sh <staging|production> <git-ref>`:**

1. Fetch a `git archive` of the ref from GitHub.
2. Upload it to `releases/<id>` and link `shared/.env` and `shared/storage`.
3. Run `composer install --no-dev --optimize-autoloader` with `/opt/alt/php84/usr/bin/php`.
4. Run `config:cache`, `route:cache` and `view:cache`.
5. Back up the DB, then run `migrate --force`.
6. Run the read-only parity subset against the release.
7. Switch `current` atomically, then run the post-switch smoke checks.
8. Keep the last 5 releases.

`scripts/rollback.sh` points `current` back to the previous release.

**Validate on staging before production:**

- Hostinger serves a symlinked `public_html`. If not, fall back to a `public_html` holding only the front controller and assets.
- The PHP version can be selected per release with an `.htaccess` handler, so PHP 7.4 → 8.4 switches atomically with `current`. If not, switch the PHP version in hPanel at cutover.

**Found in Phase 1 (final review, P1-R51). Resolve on staging before the cutover:**

- **Route caching.** `route:cache` bakes in the CLI locale prefix from `LaravelLocalization::setLocale()`, which is null, so the localized `/en` and `/ar` routes would 404. Use mcamara's `route:trans:cache` with `LoadsTranslatedCachedRoutes`, or skip route caching.
- **Config caching.** Calls to `env()` outside `config/` return null once `config:cache` runs:
  - `ENABLE_SUPERPOWERS` in `Modules/Permissions/Providers/BouncerServiceProvider.php`;
  - 34 `env('APP_DEBUG')` calls in 16 controllers;
  - `env('APP_ENV')` in `Modules/Cms/Resources/views/layouts/master.blade.php`.

  Move them to config first, or leave config uncached.
- **Hostinger preflight for `/opt/alt/php84`.**
  - PHP 8.4.1 or later (`vendor/composer/platform_check.php`).
  - Extensions: `fileinfo`, `gd` with JPEG/WebP/FreeType, `intl`, `mbstring`, `exif`, `zip`, `bcmath`, `pdo_mysql`.
  - A writable `bootstrap/cache`: nwidart 13 writes `modules.php` there.
- **SMTP.** Laravel 13 derives the SMTP scheme from the port and ignores `mail.encryption`. Send a real mail from staging before the cutover.

**Production cutover:**

1. Final DB backup.
2. Maintenance mode for a few minutes.
3. Switch.
4. Run the read-only parity subset against production.
5. Watch the logs for 24 hours. Roll back on any regression.

## Open items

| Item | Owner | Status |
|---|---|---|
| Rewrite git history to drop commit `0edf5d6`'s literal passwords (N8) | Owner | Approved. Blocked by the Claude Code classifier, so the owner runs it (see below). Run it only **after** the hotfix deploy: `deploy.sh` drift-checks the server against the root commit. |
| Confirm both near-duplicate SSH keys (N7) and enable hPanel 2FA | Owner | Pending |
| Check hPanel cron for `schedule:run` (H2) | Implementation | Pending |
| Confirm Hostinger symlink and `.htaccess` PHP-handler behaviour (Phase 3) | Implementation on staging | Pending |

**N8 history rewrite** (run by the owner from the repo root, after the hotfix deploy is verified). It folds `afe3e63` into the root commit and replays every later commit unchanged:

```bash
GIT_SEQUENCE_EDITOR="sed -i '' '2s/^pick/fixup/'" git rebase -i --root && git push --force-with-lease origin main
```

Afterwards, `git log -p --all -- Modules/Cms/Config/config.php | grep -c 'Hash::make("'` must print `0`.

## Observations outside this scope

- `Modules/Cms/Config/config.php` calls `Hash::make()` twice every time config loads. Without a config cache that means every request, which costs roughly 100 ms of bcrypt work. `config:cache` in Phase 3 removes this.
- 48% of leads are spam, so lead counts in any report are inflated. H5 stops new spam; existing rows are untouched.
- Found by the Phase 0 parity suite and recorded as-is:
  - The project and opportunity request lists read the same unfiltered `contact_us` rows.
  - `admin/opportunity/properties` loads its table from the projects endpoint.
  - These are functional bugs, outside the technical-and-security scope, and are reported to the owner.
- Found by Phase 2 research:
  - 17 admin POST routes point to controller methods that do not exist, so they answer 500. Examples: the `areas`, `cities`, `countries` and `tags` `disable`/`enable`/`destroyTranslation` buttons.
  - This is a functional bug, reported to the owner and not fixed.
  - The S13 write probes record these routes as they are.
