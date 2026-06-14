# WebBreda — local development commands
# Requires: docker, docker compose, just

set dotenv-load := true

# Show available commands
_default:
    @just --list


# ─────────────────────────────── Dev ───────────────────────────────

# Build, init db (first run), start db + web (+ ngrok if NGROK_AUTHTOKEN set), then show info
[group('dev')]
dev: _envcheck
    docker compose build
    ./scripts/db-init.sh
    docker compose up -d
    @if [ -n "${NGROK_AUTHTOKEN:-}" ]; then echo "Starting ngrok tunnel..."; docker compose --profile ngrok up -d ngrok; fi
    @just info

# Restore db/muonline.bak and install WebEngine tables (idempotent)
[group('dev')]
init: _envcheck
    docker compose build web
    ./scripts/db-init.sh

# Show local environment info (URLs, database connection, ngrok public URL, etc.)
[group('dev')]
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
    if docker compose ps --status running --services 2>/dev/null | grep -qx ngrok; then
        url=""
        for _ in 1 2 3 4 5 6 7 8; do
            url=$(curl -s "http://localhost:${NGROK_WEB_PORT:-4040}/api/tunnels" 2>/dev/null \
                  | grep -oE '"public_url":"https://[^"]+"' | head -1 | sed 's/.*"public_url":"//; s/"$//')
            [ -n "$url" ] && break
            sleep 1
        done
        if [ -n "$url" ]; then
            echo "  ngrok    : $url"
            echo "             dashboard: http://localhost:${NGROK_WEB_PORT:-4040}"
        else
            echo "  ngrok    : starting… check dashboard http://localhost:${NGROK_WEB_PORT:-4040}"
        fi
    else
        echo "  ngrok    : just ngrok   (set NGROK_AUTHTOKEN in .env to enable)"
    fi
    echo "  Logs     : just logs     Stop: just stop"
    echo ""

# Force a re-link of ./src (rarely needed — the web container auto-syncs on file add/remove)
[group('dev')]
sync:
    docker compose exec web /usr/local/bin/overlay.sh

# Start an ngrok tunnel to the web container (for payment webhook testing), then show the URL
[group('dev')]
ngrok: _envcheck
    @test -n "${NGROK_AUTHTOKEN:-}" || (echo "Set NGROK_AUTHTOKEN in .env first (https://dashboard.ngrok.com)." && exit 1)
    docker compose --profile ngrok up -d ngrok
    @just info


# ────────────────────────────── Build ──────────────────────────────

# Image/video assets excluded from the patch build (already present on the server)
media_excludes := "-x '*.png' -x '*.jpg' -x '*.jpeg' -x '*.gif' -x '*.bmp' -x '*.webp' -x '*.ico' -x '*.svg' -x '*.webm' -x '*.mp4'"

# Full deploy ZIP — all of ./src, including image/video assets
[group('build')]
build-full: (_zip "webbreda-overlay-full.zip" "")

# Lighter deploy ZIP — code/config only, skips media already uploaded to the server
[group('build')]
build-patch: (_zip "webbreda-overlay-patch.zip" media_excludes)

# Zip ./src into ./dist/{{name}} inside the web image (no host tooling needed beyond docker/just)
_zip name excludes:
    mkdir -p dist
    docker compose run --rm --no-deps --user "$(id -u):$(id -g)" --entrypoint sh -v "$(pwd)/dist:/dist" web \
        -c "cd /src && rm -f '/dist/{{name}}' && zip -rq '/dist/{{name}}' . -x './.git*' {{excludes}}"
    @echo "Created dist/{{name}}"


# ───────────────────────────── Database ────────────────────────────

# Open a SQL shell on the database
[group('database')]
db:
    docker compose exec db /opt/mssql-tools18/bin/sqlcmd -S localhost -U "${DB_USER}" -P "${DB_PASS}" -C -d "${DB_NAME}"


# ──────────────────────────── Containers ───────────────────────────

# Tail logs
[group('containers')]
logs:
    docker compose logs -f

# Stop containers without removing them (quick resume with `just dev`)
[group('containers')]
stop:
    docker compose --profile ngrok stop

# Remove containers and network (data is kept in volumes)
[group('containers')]
down:
    docker compose --profile ngrok down

# Stop and DELETE all data (database + webroot volumes)
[group('containers')]
reset:
    docker compose --profile ngrok down -v


# Fail early if .env is missing
_envcheck:
    @test -f .env || (echo "Missing .env — copy it from .env.example first: cp .env.example .env" && exit 1)
