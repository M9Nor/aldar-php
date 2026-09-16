#!/bin/bash
# One-time bootstrap of staging.aldar-emlak.com from an uploaded release (Phase 3, ruling P3-R17).
# The owner runs it from the repository root on their own machine:
#
#   ssh codecamb 'bash -s' < scripts/deploy/bootstrap-staging.sh
#
# Preconditions, all checked below:
#   - a release exists under ~/aldar/staging/releases/ (the newest one is used), with vendor/ installed;
#   - ~/aldar/staging/shared/.env exists and its DB password is set (set-staging-db-password.sh);
#   - ~/aldar/staging/shared/.htpasswd exists (Basic Auth), otherwise staging is not switched on.
#
# It never prints a secret: every credential is read by PHP from Laravel's own config.
# It reads production (a DB dump, hardlinked uploads) and never writes to it.
set -euo pipefail

PROD=$HOME/domains/aldar-emlak.com/public_html
BASE=$HOME/aldar/staging
SHARED=$BASE/shared
DOCROOT_LINK=$PROD/staging
PHP84=/opt/alt/php84/usr/bin/php
PHP74=/opt/alt/php74/usr/bin/php
STAGING_DB=u859703690_staging
STAMP=$(date +%Y%m%d%H%M%S)

say() { printf '\n== %s\n' "$*"; }
die() { printf 'ABORT: %s\n' "$*" >&2; exit 1; }

RELEASE=$(ls -d "$BASE"/releases/*/ 2>/dev/null | sort | tail -1)
RELEASE=${RELEASE%/}
[ -n "$RELEASE" ] || die "no release under $BASE/releases"
[ -f "$RELEASE/vendor/autoload.php" ] || die "vendor/ missing in $RELEASE (run composer install first)"
[ -f "$SHARED/.env" ] || die "$SHARED/.env missing"
grep -q "^DB_PASSWORD=.\+" "$SHARED/.env" || die "staging DB password not set (run set-staging-db-password.sh)"
say "release $(basename "$RELEASE")"

say "1/7 shared storage (hardlinked uploads, P3-R19)"
mkdir -p "$SHARED/storage/app/public" "$SHARED/storage/framework/cache/data" "$SHARED/storage/framework/sessions" \
         "$SHARED/storage/framework/views" "$SHARED/storage/framework/testing" "$SHARED/storage/logs" "$SHARED/storage/debugbar"
[ -e "$SHARED/storage/app/public/uploads" ] || cp -al "$PROD/storage/app/public/uploads" "$SHARED/storage/app/public/uploads"
[ -e "$SHARED/storage/app/modules_statuses.json" ] || cp "$RELEASE/storage/app/modules_statuses.json" "$SHARED/storage/app/" 2>/dev/null \
  || cp "$PROD/storage/app/modules_statuses.json" "$SHARED/storage/app/"
[ -d "$SHARED/storage/app/modules" ] || cp -a "$PROD/storage/app/modules" "$SHARED/storage/app/modules"
[ -e "$SHARED/graph-uploads" ] || cp -al "$PROD/public/graph/uploads" "$SHARED/graph-uploads"
echo "uploads entries: $(find "$SHARED/storage/app/public/uploads" -maxdepth 1 | wc -l), graph entries: $(find "$SHARED/graph-uploads" -maxdepth 1 | wc -l)"

say "2/7 link shared state into the release"
if [ ! -L "$RELEASE/storage" ]; then rm -rf "$RELEASE/storage"; ln -s "$SHARED/storage" "$RELEASE/storage"; fi
if [ ! -L "$RELEASE/public/graph/uploads" ]; then rm -rf "$RELEASE/public/graph/uploads"; ln -s "$SHARED/graph-uploads" "$RELEASE/public/graph/uploads"; fi
ln -sfn "$SHARED/.env" "$RELEASE/.env"
HT=$RELEASE/public/.htaccess
if ! grep -q "Phase 3 staging" "$HT"; then
  cat >> "$HT" <<'EOF'

# --- Phase 3 staging (added by bootstrap-staging.sh) ---
<IfModule mime_module>
  AddHandler application/x-httpd-ea-php84 .php .php7 .phtml
</IfModule>
<IfModule mod_headers.c>
  Header always set X-Robots-Tag "noindex, nofollow"
</IfModule>
EOF
fi
if [ -f "$SHARED/.htpasswd" ] && ! grep -q "AuthUserFile" "$HT"; then
  cat >> "$HT" <<EOF
AuthType Basic
AuthName "Aldar staging"
AuthUserFile $SHARED/.htpasswd
Require valid-user
EOF
fi
echo "links: $(cd "$RELEASE" && ls -la .env storage public/graph/uploads | grep -c ' -> ')/3"

say "3/7 safety check: the staging .env must point at the staging database only"
# The app queries the database while booting, and staging's database is still empty here,
# so both .env files are read with phpdotenv (from this release) instead of booting Laravel.
ENVREAD='require getenv("VENDOR"); $e = Dotenv\Dotenv::createArrayBacked(getenv("ENVDIR"))->load(); echo $e[getenv("KEY")] ?? "";'
TARGET_DB=$(VENDOR="$RELEASE/vendor/autoload.php" ENVDIR="$SHARED" KEY=DB_DATABASE "$PHP84" -r "$ENVREAD" </dev/null)
PROD_DB=$(VENDOR="$RELEASE/vendor/autoload.php" ENVDIR="$PROD" KEY=DB_DATABASE "$PHP84" -r "$ENVREAD" </dev/null)
[ "$TARGET_DB" = "$STAGING_DB" ] || die "staging .env points at an unexpected database"
[ -n "$PROD_DB" ] || die "could not read production's database name"
[ "$TARGET_DB" != "$PROD_DB" ] || die "staging and production resolve to the same database"
echo "target database is the staging database, distinct from production"

say "4/7 copy the production database into staging (read-only on production)"
mkdir -p "$HOME/aldar-backup" && chmod 700 "$HOME/aldar-backup"
SEED=$HOME/aldar-backup/staging-seed-$STAMP.sql.gz
(cd "$PROD" && "$PHP74" "$HOME/aldar-backup/db-dump.php" "$PROD" "$SEED" </dev/null)
IMPORTER=$(mktemp "$HOME/aldar-backup/import-XXXXXX.php")
chmod 600 "$IMPORTER"
cat > "$IMPORTER" <<'EOF'
<?php
// Imports a gzipped dump into the staging database named in <env-dir>/.env, without booting Laravel.
[, $vendor, $envDir, $dump, $expectedDb] = $argv;
require $vendor;
$e = Dotenv\Dotenv::createArrayBacked($envDir)->load();
if (($e['DB_DATABASE'] ?? '') !== $expectedDb) { fwrite(STDERR, "unexpected target database\n"); exit(1); }
$command = sprintf('set -o pipefail; gunzip -c %s | mysql --default-character-set=utf8mb4 -h %s -P %s -u %s %s',
    escapeshellarg($dump), escapeshellarg($e['DB_HOST'] ?? '127.0.0.1'), escapeshellarg($e['DB_PORT'] ?? '3306'),
    escapeshellarg($e['DB_USERNAME']), escapeshellarg($e['DB_DATABASE']));
$process = proc_open(['/bin/bash', '-c', $command], [2 => ['pipe', 'w']], $pipes, null, array_merge(getenv(), ['MYSQL_PWD' => $e['DB_PASSWORD'] ?? '']));
$errors = stream_get_contents($pipes[2]);
$code = proc_close($process);
if ($code !== 0) { fwrite(STDERR, $errors); exit($code); }
$pdo = new PDO('mysql:host='.($e['DB_HOST'] ?? '127.0.0.1').';port='.($e['DB_PORT'] ?? '3306').';dbname='.$e['DB_DATABASE'], $e['DB_USERNAME'], $e['DB_PASSWORD'] ?? '');
$n = (int) $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE'")->fetchColumn();
echo "imported tables={$n}\n";
EOF
"$PHP84" "$IMPORTER" "$RELEASE/vendor/autoload.php" "$SHARED" "$SEED" "$STAGING_DB" </dev/null
rm -f "$IMPORTER"

say "5/7 framework discovery on PHP 8.4"
(cd "$RELEASE" && "$PHP84" artisan package:discover --ansi </dev/null | tail -3)

say "6/7 migrations and caches"
(cd "$RELEASE" && "$PHP84" artisan migrate --force --no-interaction </dev/null | tail -3)
(cd "$RELEASE" && "$PHP84" artisan config:cache </dev/null | tail -1 && "$PHP84" artisan view:cache </dev/null | tail -1)
echo "route:cache skipped (P3-R6: localized routes)"

say "7/7 switch"
ln -sfn "$RELEASE" "$BASE/current"
[ -L "$DOCROOT_LINK" ] || [ ! -e "$DOCROOT_LINK" ] || die "$DOCROOT_LINK is a real directory, refusing to replace it"
if [ -f "$SHARED/.htpasswd" ]; then
  ln -sfn "$BASE/current/public" "$DOCROOT_LINK"
  echo "staging docroot now serves $(basename "$(readlink -f "$BASE/current")") behind Basic Auth"
else
  echo "NOT switched: $SHARED/.htpasswd is missing, so staging keeps its 404 placeholder."
  echo "Create it (set-staging-basic-auth), then run this script again: every step above is idempotent except the DB copy, which simply refreshes staging's data."
fi
echo "seed dump kept at $SEED"
