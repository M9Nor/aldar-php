#!/bin/bash
# Read-only parity probe against a deployed release.
#   scripts/deploy/probe-release.sh https://staging.aldar-emlak.com
# Runs only specs that never write: no fixtures, no DB reset, no admin writes.
# The production hostnames are refused by tests/e2e/support/global-setup.ts and cannot be probed.
set -euo pipefail

BASE_URL="${1:?usage: probe-release.sh <base-url>}"
HOST="$(printf '%s' "$BASE_URL" | sed -E 's#^[a-z]+://##; s#/.*$##; s#:.*$##')"

# A spec that calls requireLocal or shells into the app container via support/docker must never
# be listed here: it would fail against a real remote host instead of skipping. Image checks live
# in scripts/deploy/verify-release.sh instead (read-only, no local exec).
READ_ONLY_SPECS=(
  specs/parity/golden-master.spec.ts
  specs/security/headers.spec.ts
  specs/security/module-scripts.spec.ts
)

cd "$(dirname "$0")/../../tests/e2e"
BASE_URL="$BASE_URL" PARITY_ALLOWED_HOST="$HOST" SKIP_DB_RESET=1 \
  npx playwright test --reporter=line "${READ_ONLY_SPECS[@]}"
