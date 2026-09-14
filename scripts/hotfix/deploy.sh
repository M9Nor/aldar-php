#!/usr/bin/env bash
# Patches the current production public_html in place with the application files
# that changed between the server snapshot (the repository's root commit) and <git-ref>.
# Usage: scripts/hotfix/deploy.sh [--dry-run] <git-ref>
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"

DRY_RUN=0
if [ "${1:-}" = "--dry-run" ]; then DRY_RUN=1; shift; fi
REF="${1:?usage: scripts/hotfix/deploy.sh [--dry-run] <git-ref>}"

REMOTE="${REMOTE:-codecamb}"
APP="domains/aldar-emlak.com/public_html"
PHP="/opt/alt/php74/usr/bin/php"
STAMP="$(date +%Y%m%d-%H%M%S)"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

PATHSPEC=(-- . ':!tests' ':!docs' ':!scripts' ':!docker' ':!docker-compose.yml' ':!.gitignore' ':!phpunit.xml')
ROOT_COMMIT="$(git rev-list --max-parents=0 "$REF")"

if git diff --name-only --diff-filter=DR "$ROOT_COMMIT" "$REF" "${PATHSPEC[@]}" | grep -q .; then
  echo "Deleted or renamed application files are not supported by the hotfix deploy:" >&2
  git diff --name-status --diff-filter=DR "$ROOT_COMMIT" "$REF" "${PATHSPEC[@]}" >&2
  exit 1
fi

git diff --name-only --diff-filter=AM "$ROOT_COMMIT" "$REF" "${PATHSPEC[@]}" > "$WORK/files.txt"
[ -s "$WORK/files.txt" ] || { echo "Nothing to deploy."; exit 0; }
echo "Files to deploy ($(wc -l < "$WORK/files.txt" | tr -d ' ')):"
sed 's/^/  /' "$WORK/files.txt"

echo "Drift check against the server..."
DRIFT=0
: > "$WORK/added.txt"
while IFS= read -r file; do
  if ! git cat-file -e "$ROOT_COMMIT:$file" 2>/dev/null; then
    echo "$file" >> "$WORK/added.txt"
    expected="absent"
    actual="$(ssh -n "$REMOTE" "test -e '$APP/$file' && echo present || echo absent")"
  elif grep -qxF "$file" scripts/hotfix/known-baseline-edits.txt; then
    continue
  else
    expected="$(git show "$ROOT_COMMIT:$file" | tr -d '\r' | shasum -a 256 | cut -d' ' -f1)"
    actual="$(ssh -n "$REMOTE" "tr -d '\r' < '$APP/$file' | sha256sum" | cut -d' ' -f1)"
  fi
  if [ "$expected" != "$actual" ]; then
    echo "  DRIFT: $file" >&2
    DRIFT=1
  fi
done < "$WORK/files.txt"
[ "$DRIFT" -eq 0 ] || { echo "Server files changed since the snapshot. Aborting." >&2; exit 1; }
echo "  no drift"

if [ "$DRY_RUN" -eq 1 ]; then
  echo "Dry run: nothing uploaded."
  exit 0
fi

echo "Backing up (stamp $STAMP)..."
ssh -n "$REMOTE" "mkdir -p ~/aldar-backup && chmod 700 ~/aldar-backup"
scp -q scripts/server/db-dump.php "$REMOTE:aldar-backup/db-dump.php"
ssh "$REMOTE" "cd $APP && tar --ignore-failed-read -czf ~/aldar-backup/hotfix-$STAMP.tar.gz -T - 2>/dev/null" < "$WORK/files.txt"
ssh "$REMOTE" "cat > ~/aldar-backup/hotfix-$STAMP.added" < "$WORK/added.txt"
ssh -n "$REMOTE" "$PHP ~/aldar-backup/db-dump.php ~/$APP ~/aldar-backup/hotfix-$STAMP.sql.gz"

echo "Uploading..."
tr '\n' '\0' < "$WORK/files.txt" | xargs -0 git archive --format=tar "$REF" -- | ssh "$REMOTE" "tar -xif - -C $APP"

echo "Clearing caches..."
ssh -n "$REMOTE" "cd $APP && $PHP artisan view:clear && $PHP artisan route:clear && $PHP artisan config:clear && $PHP artisan cache:clear"

echo "Verifying..."
if ! scripts/hotfix/verify-production.sh "https://aldar-emlak.com"; then
  echo "Verification FAILED. Roll back with: scripts/hotfix/rollback.sh $STAMP" >&2
  exit 1
fi
echo "Deployed $REF. Rollback: scripts/hotfix/rollback.sh $STAMP"
