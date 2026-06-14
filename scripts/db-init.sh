#!/usr/bin/env bash
#
# Initialises the local database:
#   1. waits for the MSSQL container to be ready,
#   2. restores db/muonline.bak into the game database (only if missing),
#   3. creates the WEBENGINE_* tables + cron jobs (headless installer),
#   4. applies our plugin SQL (Stripe / MercadoPago transaction tables).
#
# Safe to re-run: every step is idempotent.
set -euo pipefail

cd "$(dirname "$0")/.."
[ -f .env ] && set -a && . ./.env && set +a

DB_NAME="${DB_NAME:-MuOnline}"
DB_USER="${DB_USER:-sa}"
BACKUP_LOGICAL_DATA="muonline"
BACKUP_LOGICAL_LOG="muonline_log"

dc() { docker compose "$@"; }
# Run a query in the db container against the master database.
sql() { dc exec -T db /opt/mssql-tools18/bin/sqlcmd -S localhost -U "$DB_USER" -P "$DB_PASS" -C -b "$@"; }

echo "==> Starting database container..."
dc up -d db

echo "==> Waiting for SQL Server to accept connections..."
for i in $(seq 1 30); do
    if sql -Q "SELECT 1" >/dev/null 2>&1; then
        echo "    ready."
        break
    fi
    [ "$i" = 30 ] && { echo "    timed out waiting for SQL Server." >&2; exit 1; }
    sleep 3
done

echo "==> Checking for existing '$DB_NAME' database..."
HAS_DB=$(sql -h -1 -W -Q "SET NOCOUNT ON; SELECT COUNT(*) FROM sys.databases WHERE name = '$DB_NAME'" | tr -d '[:space:]')

if [ "$HAS_DB" = "0" ]; then
    echo "==> Restoring db/muonline.bak into '$DB_NAME'..."
    sql -Q "RESTORE DATABASE [$DB_NAME] FROM DISK = '/db/muonline.bak' WITH \
        MOVE '$BACKUP_LOGICAL_DATA' TO '/var/opt/mssql/data/${DB_NAME}.mdf', \
        MOVE '$BACKUP_LOGICAL_LOG'  TO '/var/opt/mssql/data/${DB_NAME}_log.ldf', \
        REPLACE"
    echo "    restore complete."
else
    echo "    '$DB_NAME' already exists, skipping restore."
fi

echo "==> Creating WEBENGINE_* tables and cron jobs (headless installer)..."
dc run --rm --no-deps web php /opt/webengine/install-cli.php

echo "==> Applying plugin SQL (Stripe / MercadoPago)..."
for f in db/sql/*_mssql_install.sql; do
    [ -e "$f" ] || continue
    echo "    -> $(basename "$f")"
    # Plugins use {TABLE_PREFIX}; our WE_PREFIX is empty.
    sed 's/{TABLE_PREFIX}//g' "$f" \
        | dc exec -T db /opt/mssql-tools18/bin/sqlcmd -S localhost -U "$DB_USER" -P "$DB_PASS" -C -d "$DB_NAME" \
        || echo "       (skipped: table may already exist)"
done

echo "==> Database initialisation complete."
