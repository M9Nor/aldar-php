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
