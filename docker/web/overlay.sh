#!/usr/bin/env bash
#
# Overlays our customizations (/src, bind-mounted from ./src on the host) on top
# of the WebEngine core that lives in the webroot. Each file under /src is
# symlinked into the matching path of the webroot, so:
#   - core files keep working (we only shadow the ones we override),
#   - editing a file under ./src on the host is reflected instantly (hot reload).
#
# Re-run this (`just sync`) after ADDING or DELETING files under ./src.
set -euo pipefail

SRC="${SRC_DIR:-/src}"
WEBROOT="${WEB_ROOT:-/var/www/html}"

[ -d "$SRC" ] || { echo "[overlay] no $SRC mounted, skipping"; exit 0; }

# Drop stale overlay symlinks (handles files removed from ./src).
find "$WEBROOT" -type l -lname "$SRC/*" -delete 2>/dev/null || true

# (Re)create a symlink in the webroot for every file under /src.
count=0
while IFS= read -r -d '' file; do
    rel="${file#"$SRC"/}"
    dest="$WEBROOT/$rel"
    mkdir -p "$(dirname "$dest")"
    ln -sfn "$SRC/$rel" "$dest"
    count=$((count + 1))
done < <(find "$SRC" -type f -not -path '*/.git/*' -print0)

echo "[overlay] linked $count file(s) from $SRC into $WEBROOT"
