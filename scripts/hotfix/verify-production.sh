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
    *binaa-prod*) fail "$worker still has the vendor Firebase config (purge the Hostinger CDN cache if the file on disk is new)" ;;
    *"registration.unregister()"*) pass "$worker unregisters itself" ;;
    *) fail "$worker unexpected content" ;;
  esac
done

# The .htaccess deny blocks are only proven live by a refused request for a PHP name that
# does not exist: without them the request falls through to Laravel (404 or a redirect).
PROBE="verify-$(openssl rand -hex 8).php"
for dir in graph/uploads/original/tinymce modules; do
  code="$(curl -s -o /dev/null -w '%{http_code}' "$BASE/$dir/$PROBE")"
  [ "$code" = 403 ] && pass "PHP under /$dir refused (403)" || fail "PHP under /$dir not refused ($code)"
done

exit $FAIL
