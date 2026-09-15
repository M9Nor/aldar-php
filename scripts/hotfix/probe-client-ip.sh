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

# A raw string compare is unreliable for IP addresses: the same IPv6 address can
# be written with different zero-compression, leading zeros or case, and a
# dual-stack listener may report an IPv4 client as ::ffff:a.b.c.d. Normalise
# both sides with PHP's inet_pton/inet_ntop (which also strips an
# IPv4-in-IPv6 prefix) before comparing; fall back to a lowercase string
# compare if PHP isn't available, and say so.
if command -v php >/dev/null 2>&1; then
  HAVE_PHP=1
else
  HAVE_PHP=0
  echo "note  php not found on PATH; falling back to a lowercase string compare for the REMOTE_ADDR/public-IP equality check"
fi

normalize_ip() {
  value="$1"
  if [ "$HAVE_PHP" = "1" ]; then
    php -r '$a=@inet_pton($argv[1]); if($a===false){echo $argv[1];exit;} $s=inet_ntop($a); if(stripos($s,"::ffff:")===0 && strpos($s,".")!==false){$s=substr($s,7);} echo strtolower($s);' "$value"
  else
    printf '%s' "$value" | tr '[:upper:]' '[:lower:]'
  fi
}

LOCAL_IP_NORM="$(normalize_ip "$LOCAL_IP")"
REMOTE_ADDR_NORM="$(normalize_ip "$REMOTE_ADDR_VALUE")"
if [ "$REMOTE_ADDR_NORM" = "$LOCAL_IP_NORM" ]; then
  echo "REMOTE_ADDR equals this machine's public IP: yes"
else
  echo "REMOTE_ADDR equals this machine's public IP: no"
fi
