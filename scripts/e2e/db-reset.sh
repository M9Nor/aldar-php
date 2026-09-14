#!/usr/bin/env bash
# Rebuilds the local Docker database from the production dump and adds the
# e2e test accounts. Local only: it talks to the docker compose services.
set -euo pipefail
cd "$(dirname "$0")/../.."

DUMP="${DUMP:-_db-backup/aldar-db-20260914-1704.sql.gz}"
E2E_PASSWORD="${E2E_PASSWORD:-e2e-local-password}"

[ -f "$DUMP" ] || { echo "Dump not found: $DUMP" >&2; exit 1; }

docker compose exec -T db mariadb -uroot -proot -e \
  "DROP DATABASE IF EXISTS aldar; CREATE DATABASE aldar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON aldar.* TO 'aldar'@'%';"
gunzip -c "$DUMP" | docker compose exec -T db mariadb -uroot -proot aldar

HASH="$(docker compose exec -T app php -r 'echo password_hash($argv[1], PASSWORD_BCRYPT);' "$E2E_PASSWORD")"

docker compose exec -T db mariadb -ualdar -paldar aldar <<SQL
INSERT INTO users (username, email, status, verification_code, password, email_verified_at, created_at, updated_at) VALUES
  ('parity-superadmin', 'parity-superadmin@aldar.test', 'ACTIVE', 'VERIFIED', '${HASH}', NOW(), NOW(), NOW()),
  ('parity-admin',      'parity-admin@aldar.test',      'ACTIVE', 'VERIFIED', '${HASH}', NOW(), NOW(), NOW());
INSERT INTO perms_assigned_roles (role_id, entity_id, entity_type)
SELECT r.id, u.id, CONCAT('App', CHAR(92), 'User')
FROM users u
JOIN perms_roles r ON r.name = IF(u.username = 'parity-superadmin', 'SUPERADMIN', 'ADMIN')
WHERE u.username IN ('parity-superadmin', 'parity-admin');
SQL

docker compose exec -T app php artisan cache:clear >/dev/null
echo "Local DB reset; test accounts: parity-superadmin, parity-admin"
