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
