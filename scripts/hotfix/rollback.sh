#!/usr/bin/env bash
# Restores the files replaced by a hotfix deploy and removes the files it added.
# The database dump from that deploy is kept but not restored.
# It also restores the public /seed route and the vendor's seeder password, so it refuses to
# run without --reopens-public-seed. After aldar:offboard-vendor has run, fix forward instead.
# Usage: scripts/hotfix/rollback.sh --reopens-public-seed <stamp>
set -euo pipefail
USAGE="usage: scripts/hotfix/rollback.sh --reopens-public-seed <stamp>"
REOPENS_SEED=0
STAMP=""
for arg in "$@"; do
  case "$arg" in
    --reopens-public-seed) REOPENS_SEED=1 ;;
    -*) echo "Unknown option: $arg" >&2; echo "$USAGE" >&2; exit 1 ;;
    *) STAMP="$arg" ;;
  esac
done
if [ "$REOPENS_SEED" -ne 1 ]; then
  echo "Rolling back restores the public /seed route and the vendor's seeder password. Only use this before aldar:offboard-vendor has run, or fix forward instead." >&2
  echo "$USAGE" >&2
  exit 1
fi
[ -n "$STAMP" ] || { echo "$USAGE" >&2; exit 1; }
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
