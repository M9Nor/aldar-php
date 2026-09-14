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
