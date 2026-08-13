#!/usr/bin/env bash
# Pull the maketolearn.org WordPress code from SiteGround into this repo.
#
# Run from your own machine, from anywhere inside a clone of this repo:
#   bash scripts/pull-site.sh
#
# Prerequisites: SSH access enabled in Site Tools → Devs → SSH Keys Manager
# (see README.md, Step 1), and rsync installed locally (macOS/Linux have it;
# on Windows use WSL or Git Bash with rsync).
set -euo pipefail

# ---- Fill these in from Site Tools → Devs → SSH Keys Manager ----------------
SG_USER="CHANGE_ME"                                  # e.g. u1234-ab12cd34
SG_HOST="CHANGE_ME"                                  # hostname shown in Site Tools
SG_PORT="18765"                                      # SiteGround's SSH port
REMOTE_WP_ROOT="www/maketolearn.org/public_html"     # relative to SSH home (absolute paths also fine)
SSH_KEY=""                                           # optional, e.g. ~/.ssh/siteground_maketolearn
# -----------------------------------------------------------------------------

if [[ "$SG_USER" == "CHANGE_ME" || "$SG_HOST" == "CHANGE_ME" ]]; then
  echo "Edit scripts/pull-site.sh first: set SG_USER and SG_HOST (Site Tools → Devs → SSH Keys Manager)." >&2
  exit 1
fi

SSH_OPTS=(-p "$SG_PORT")
SCP_OPTS=(-P "$SG_PORT")
if [[ -n "$SSH_KEY" ]]; then
  SSH_OPTS+=(-i "$SSH_KEY")
  SCP_OPTS+=(-i "$SSH_KEY")
fi
SSH_CMD="ssh ${SSH_OPTS[*]}"

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO_ROOT"
mkdir -p wp-content docs/site-inventory

echo "==> Pulling wp-content (code only — media, caches, and backups excluded)..."
rsync -avz -e "$SSH_CMD" \
  --exclude 'uploads/' \
  --exclude 'upgrade/' \
  --exclude 'upgrade-temp-backup/' \
  --exclude 'languages/' \
  --exclude 'cache/' \
  --exclude 'et-cache/' \
  --exclude 'litespeed/' \
  --exclude 'w3tc-config/' \
  --exclude 'wflogs/' \
  --exclude 'backup*/' \
  --exclude 'backups*/' \
  --exclude 'ai1wm-backups/' \
  --exclude 'updraft/' \
  --exclude 'node_modules/' \
  --exclude '*.zip' --exclude '*.tar.gz' --exclude '*.sql' --exclude '*.sql.gz' \
  "$SG_USER@$SG_HOST:$REMOTE_WP_ROOT/wp-content/" wp-content/

echo "==> Copying production .htaccess for reference (review before committing)..."
scp "${SCP_OPTS[@]}" "$SG_USER@$SG_HOST:$REMOTE_WP_ROOT/.htaccess" docs/site-inventory/htaccess.txt \
  || echo "    (no .htaccess found — skipping)"

echo "==> Running the WP-CLI inventory on the server..."
ssh "${SSH_OPTS[@]}" "$SG_USER@$SG_HOST" "cd $REMOTE_WP_ROOT && bash -s \$HOME/site-inventory" \
  < "$REPO_ROOT/scripts/collect-site-info.sh" \
  || echo "    (inventory had warnings — check output above; partial results still download)"

echo "==> Downloading inventory results to docs/site-inventory/ ..."
rsync -avz -e "$SSH_CMD" "$SG_USER@$SG_HOST:site-inventory/" docs/site-inventory/ \
  || echo "    (nothing to download)"

echo
echo "Done. Next: review with 'git status' (expect wp-content/ and docs/site-inventory/ only —"
echo "no wp-config.php, no *.sql, no uploads/), then commit and push. See README.md Steps 3-5."
