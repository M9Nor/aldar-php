#!/bin/bash
# Creates the Basic Auth file that protects staging.aldar-emlak.com (Phase 3, ruling P3-R16).
# The owner runs it and types the user name and password; nothing is echoed or logged:
#
#   ssh -t codecamb 'bash -s' < scripts/deploy/set-staging-basic-auth.sh
set -euo pipefail
FILE=$HOME/aldar/staging/shared/.htpasswd
command -v openssl >/dev/null || { echo "openssl is not available on this host"; exit 1; }
mkdir -p "$(dirname "$FILE")"
read -rp "Staging user name: " STAGING_USER < /dev/tty
read -rsp "Staging password: " STAGING_PASS < /dev/tty; echo
read -rsp "Repeat the password: " STAGING_PASS2 < /dev/tty; echo
[ -n "$STAGING_USER" ] && [ -n "$STAGING_PASS" ] || { echo "empty user or password, nothing changed"; exit 1; }
[ "$STAGING_PASS" = "$STAGING_PASS2" ] || { echo "the passwords differ, nothing changed"; exit 1; }
HASH=$(printf '%s' "$STAGING_PASS" | openssl passwd -apr1 -stdin)
unset STAGING_PASS STAGING_PASS2
printf '%s:%s\n' "$STAGING_USER" "$HASH" > "$FILE.tmp"
chmod 644 "$FILE.tmp"
mv "$FILE.tmp" "$FILE"
echo "saved $FILE for user $STAGING_USER"
