#!/usr/bin/env bash
# Shows which client address and scheme PHP sees on production behind Hostinger's CDN.
# Uploads a throwaway script with a random name, requests it once, then deletes it.
set -euo pipefail
REMOTE="${REMOTE:-codecamb}"
APP="domains/aldar-emlak.com/public_html"
NAME="ip-probe-$(openssl rand -hex 16).php"

# Set before the upload, so an interrupted or failed upload still removes the file.
trap 'ssh -n "$REMOTE" "rm -f $APP/public/$NAME"' EXIT

ssh "$REMOTE" "cat > $APP/public/$NAME" <<'PHP'
<?php
header('Content-Type: text/plain');
foreach (['REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTPS', 'HTTP_X_FORWARDED_PROTO', 'SERVER_PORT'] as $key) {
    echo $key, '=', $_SERVER[$key] ?? '', "\n";
}
PHP

LOCAL_IP="$(curl -s https://api64.ipify.org)"
echo "This machine's public IP: $LOCAL_IP"
RESULT="$(curl -s "https://aldar-emlak.com/$NAME")"
echo "$RESULT"
REMOTE_ADDR_LINE="$(echo "$RESULT" | grep '^REMOTE_ADDR=' || true)"
REMOTE_ADDR_VALUE="${REMOTE_ADDR_LINE#REMOTE_ADDR=}"
if [ "$REMOTE_ADDR_VALUE" = "$LOCAL_IP" ]; then
  echo "REMOTE_ADDR equals this machine's public IP: yes"
else
  echo "REMOTE_ADDR equals this machine's public IP: no"
fi
