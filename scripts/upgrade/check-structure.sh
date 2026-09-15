#!/usr/bin/env bash
# Compares the running app's route table, config values and Glide cache paths with the
# Laravel 7 baselines in tests/upgrade/. Exit 0 = identical, 1 = differences (printed as a diff).
set -uo pipefail
cd "$(dirname "$0")/../.."

status=0
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
for section in routes config glide; do
  file="tests/upgrade/${section/glide/glide-cache-paths}.json"
  docker compose exec -T app php scripts/upgrade/structure-snapshot.php "$section" > "$tmp/$section.json" || { echo "snapshot $section failed" >&2; exit 2; }
  if ! diff -u "$file" "$tmp/$section.json"; then status=1; fi
done
[ "$status" -eq 0 ] && echo "structure matches tests/upgrade/"
exit "$status"
