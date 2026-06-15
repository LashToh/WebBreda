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

# 4. Writable runtime paths. WebEngine resolves the entries in
#    includes/config/writable.paths.json relative to includes/, so the real
#    cache and logs live under includes/cache and includes/logs (NOT the webroot
#    root). Apache runs as www-data, so these must be owned by www-data.
mkdir -p \
    "$WEBROOT/includes/cache/news/translations" \
    "$WEBROOT/includes/cache/profiles/guilds" \
    "$WEBROOT/includes/cache/profiles/players" \
    "$WEBROOT/includes/logs"
chown -R www-data:www-data "$WEBROOT/includes/cache" "$WEBROOT/includes/logs"
# Config files WebEngine rewrites at runtime (webengine.json, module XMLs, …).
# Skip overlay symlinks with -not -type l so we don't chown host files via /src.
find "$WEBROOT/includes/config" -not -type l -exec chown www-data:www-data {} + 2>/dev/null || true

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
