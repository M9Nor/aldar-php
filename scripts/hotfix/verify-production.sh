#!/usr/bin/env bash
# Read-only checks that the hotfix is live on production.
# It never requests /seed or /migrate: on unpatched code those URLs run commands.
# Usage: scripts/hotfix/verify-production.sh [base-url]
# ROUTES_FILE=<file> reads the route list (artisan route:list --columns=method,uri,middleware)
# from a file instead of the server, so the HTTP checks can run against a local stack.
set -uo pipefail
BASE="${1:-https://aldar-emlak.com}"
REMOTE="${REMOTE:-codecamb}"
APP="domains/aldar-emlak.com/public_html"
PHP="/opt/alt/php74/usr/bin/php"
FAIL=0

pass() { echo "ok    $1"; }
fail() { echo "FAIL  $1"; FAIL=1; }
status() { curl -s -o /dev/null -w '%{http_code}' -L --max-redirs 5 "$1"; }

if [ -n "${ROUTES_FILE:-}" ]; then
  echo "note  route list read from $ROUTES_FILE, not from the server"
  ROUTES="$(cat "$ROUTES_FILE")" || { echo "cannot read route list"; exit 1; }
else
  ROUTES="$(ssh -n "$REMOTE" "cd $APP && $PHP artisan route:list --columns=method,uri,middleware" 2>&1)" || { echo "cannot read route list"; exit 1; }
fi

if echo "$ROUTES" | grep -qE '\| (seed|migrate|update_currency|clear-cache) +\|'; then
  fail "public maintenance routes are still registered"
else
  pass "seed, migrate, update_currency, clear-cache are not public routes"
fi

require_middleware() {
  uri="$1"; mw="$2"; label="$3"
  rows="$(echo "$ROUTES" | grep -E "\| $uri +\|")"
  if [ -z "$rows" ]; then
    fail "$label"
  elif echo "$rows" | grep -qvF -- "$mw"; then
    fail "$label"
  else
    pass "$label"
  fi
}

for uri in admin/attachments/store admin/attachments/delete admin/tinymce/uploader admin/update-currency admin/clear-cache; do
  require_middleware "$uri" staff "$uri requires staff"
done

for uri in contact-us/store contact-us/store-inner contact-us/subscribe; do
  require_middleware "$uri" contact.guard "$uri is guarded"
done

for path in /en /ar /en/contact-us /ar/contact-us /en/articles; do
  code="$(status "$BASE$path")"; [ "$code" = 200 ] && pass "GET $path 200" || fail "GET $path ($code)"
done

code="$(status "$BASE/img/85x85/defaults/base.png")"; [ "$code" = 200 ] && pass "allowed image size 200" || fail "allowed image size ($code)"
# A fresh unknown size per run: a fixed one would fail forever once anything cached it.
UNKNOWN_SIZE="$((1100 + RANDOM % 800))x$((1100 + RANDOM % 800))"
code="$(status "$BASE/img/$UNKNOWN_SIZE/defaults/base.png")"; [ "$code" = 404 ] && pass "unknown image size $UNKNOWN_SIZE 404" || fail "unknown image size $UNKNOWN_SIZE ($code)"

for worker in /service-worker.js /firebase-messaging-sw.js; do
  body="$(curl -s "$BASE$worker")"
  case "$body" in
    *binaa-prod*)
      # The CDN may be caching a stale copy of the file LiteSpeed serves from
      # disk; a cache-buster query string bypasses it and hits the origin.
      body2="$(curl -s "$BASE$worker?v=$RANDOM")"
      case "$body2" in
        *"registration.unregister()"*)
          fail "$worker (CDN stale: origin copy is new, purge the Hostinger CDN cache)" ;;
        *binaa-prod*)
          fail "$worker still has the vendor Firebase config (CDN may be serving a stale copy; the cache-busted request is stale too, so the origin file itself is still old)" ;;
        *)
          fail "$worker still has the vendor Firebase config (CDN may be serving a stale copy; cache-busted request returned unexpected content)" ;;
      esac
      ;;
    *"registration.unregister()"*) pass "$worker unregisters itself" ;;
    *) fail "$worker unexpected content" ;;
  esac
done

# The .htaccess deny blocks can only be proven live against an EXISTING PHP file: a
# missing name falls through to Laravel's front controller on LiteSpeed (404 or a
# redirect), even when the deny block works. process-contact.php is harmless to
# request because its $to is empty. graph/.htaccess is byte-identical to
# modules/.htaccess, so this single probe stands in for both directories.
code="$(curl -s -o /dev/null -w '%{http_code}' "$BASE/modules/frontend/form/process-contact.php")"
LABEL="PHP under /modules and /graph refused (403, existing-file probe; graph/.htaccess is identical)"
[ "$code" = 403 ] && pass "$LABEL" || fail "$LABEL ($code)"

if [ -n "${ROUTES_FILE:-}" ]; then
  echo "skip  graph/.htaccess and modules/.htaccess are identical (ROUTES_FILE set, no ssh)"
else
  if ssh -n "$REMOTE" "cd $APP/public && cmp -s graph/.htaccess modules/.htaccess"; then
    pass "graph/.htaccess and modules/.htaccess are identical"
  else
    fail "graph/.htaccess and modules/.htaccess differ (or the ssh check failed)"
  fi
fi

exit $FAIL
