#!/usr/bin/env bash
#
# Container entrypoint for the WebEngine web service.
#   1. Seed the WebEngine core into the webroot (first run only).
#   2. Overlay our ./src customizations on top (symlinks -> hot reload).
#   3. Generate includes/config/webengine.json from environment (if missing).
#   4. Ensure cache/logs are writable.
#   5. Watch ./src and re-link automatically when files are added/removed.
set -euo pipefail

CORE=/opt/webengine-core
WEBROOT=/var/www/html
CONFIG="$WEBROOT/includes/config/webengine.json"

# 1. Seed core (the webroot is a named volume, so this runs once).
if [ ! -f "$WEBROOT/index.php" ]; then
    echo "[entrypoint] Seeding WebEngine ${WEBENGINE_VERSION:-core} into webroot..."
    cp -a "$CORE/." "$WEBROOT/"
fi

# 2. Overlay our customizations.
SRC_DIR=/src WEB_ROOT="$WEBROOT" /usr/local/bin/overlay.sh

# 3. Generate the WebEngine config from env (kept out of the repo; runtime only).
if [ ! -s "$CONFIG" ]; then
    echo "[entrypoint] Generating webengine.json from environment..."
    envsubst < /opt/webengine/webengine.json.tpl > "$CONFIG"
fi

# 4. Writable runtime paths.
mkdir -p \
    "$WEBROOT/cache/news/translations" \
    "$WEBROOT/cache/profiles/guilds" \
    "$WEBROOT/cache/profiles/players" \
    "$WEBROOT/logs"
chown -R www-data:www-data "$WEBROOT/cache" "$WEBROOT/logs" "$WEBROOT/includes/config" 2>/dev/null || true

# 5. Auto-sync: re-link whenever files are added/removed under /src. Editing
#    existing files is already live (the symlinks point at the host files), so
#    this only fires for create/delete/move — no manual `just sync` needed.
if [ -d /src ] && command -v inotifywait >/dev/null 2>&1; then
    (
        while inotifywait -r -q -e create -e delete -e move /src >/dev/null 2>&1; do
            sleep 0.3  # coalesce bursts (e.g. git checkout) into one re-link
            SRC_DIR=/src WEB_ROOT="$WEBROOT" /usr/local/bin/overlay.sh >/dev/null 2>&1 || true
        done
    ) &
    echo "[entrypoint] Watching /src for added/removed files (auto-sync enabled)."
fi

exec "$@"
