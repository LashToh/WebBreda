# WebBreda — local development commands
# Requires: docker, docker compose, just

set dotenv-load := true

# Show available commands
_default:
    @just --list

# Build images, initialise the database (first run), then start db + web with hot reload
dev: _envcheck
    docker compose build
    ./scripts/db-init.sh
    docker compose up -d
    @just info

# Show local environment info (URLs, database connection, etc.)
info:
    #!/usr/bin/env bash
    echo ""
    echo "  WebBreda — local environment"
    echo "  ────────────────────────────────────────────"
    echo "  Web      : http://localhost:${WEB_PORT:-8080}"
    echo "  AdminCP  : http://localhost:${WEB_PORT:-8080}/admincp/"
    echo "  Login    : ${WEBENGINE_ADMIN_USER:-mspro} / ${WEBENGINE_ADMIN_USER:-mspro}  (admin web)"
    echo ""
    echo "  Database (SQL Server)"
    echo "    Host   : localhost"
    echo "    Port   : ${DB_PORT_HOST:-1433}"
    echo "    Name   : ${DB_NAME:-MuOnline}"
    echo "    User   : ${DB_USER:-sa}"
    echo "    Pass   : ${DB_PASS:-}"
    echo ""
    echo "  ngrok    : just ngrok   (dashboard: http://localhost:${NGROK_WEB_PORT:-4040})"
    echo "  Logs     : just logs     Stop: just down"
    echo ""

# Restore db/muonline.bak and install WebEngine tables (idempotent)
init: _envcheck
    docker compose build web
    ./scripts/db-init.sh

# Start an ngrok tunnel to the web container (for payment webhook testing)
ngrok: _envcheck
    @test -n "${NGROK_AUTHTOKEN:-}" || (echo "Set NGROK_AUTHTOKEN in .env first (https://dashboard.ngrok.com)." && exit 1)
    docker compose --profile ngrok up ngrok

# Re-link ./src into the running web container (after ADDING or REMOVING files)
sync:
    docker compose exec web /usr/local/bin/overlay.sh

# Image/video assets excluded from the patch build (already present on the server)
media_excludes := "-x '*.png' -x '*.jpg' -x '*.jpeg' -x '*.gif' -x '*.bmp' -x '*.webp' -x '*.ico' -x '*.svg' -x '*.webm' -x '*.mp4'"

# Full deploy ZIP — all of ./src, including image/video assets
build-full: (_zip "webbreda-overlay-full.zip" "")

# Lighter deploy ZIP — code/config only, skips media already uploaded to the server
build-patch: (_zip "webbreda-overlay-patch.zip" media_excludes)

# Zip ./src into ./dist/{{name}} inside the web image (no host tooling needed beyond docker/just)
_zip name excludes:
    mkdir -p dist
    docker compose run --rm --no-deps --user "$(id -u):$(id -g)" --entrypoint sh -v "$(pwd)/dist:/dist" web \
        -c "cd /src && rm -f '/dist/{{name}}' && zip -rq '/dist/{{name}}' . -x './.git*' {{excludes}}"
    @echo "Created dist/{{name}}"

# Open a SQL shell on the database
db:
    docker compose exec db /opt/mssql-tools18/bin/sqlcmd -S localhost -U "${DB_USER}" -P "${DB_PASS}" -C -d "${DB_NAME}"

# Tail logs
logs:
    docker compose logs -f

# Stop all containers
down:
    docker compose --profile ngrok down

# Stop and DELETE all data (database + webroot volumes)
reset:
    docker compose --profile ngrok down -v

# Fail early if .env is missing
_envcheck:
    @test -f .env || (echo "Missing .env — copy it from .env.example first: cp .env.example .env" && exit 1)
