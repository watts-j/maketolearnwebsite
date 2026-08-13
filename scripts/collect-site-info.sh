#!/usr/bin/env bash
# Collect a WordPress environment inventory using WP-CLI (pre-installed on SiteGround).
#
# Normally run for you by scripts/pull-site.sh. To run it by hand instead, SSH in and:
#   cd ~/www/maketolearn.org/public_html
#   bash collect-site-info.sh ~/site-inventory
#
# Every command is best-effort: a failure writes a .err file and moves on, so one
# missing WP-CLI feature never sinks the whole inventory.
set -uo pipefail

OUT="${1:-site-inventory}"
mkdir -p "$OUT"

run() { # run <outfile> <command...>
  local f="$OUT/$1"; shift
  if "$@" >"$f" 2>"$f.err"; then rm -f "$f.err"; else echo "warn: '$*' failed (see $(basename "$f").err)"; fi
}

run php-version.txt      php -v
run wp-version.txt       wp core version --extra
run wp-core-updates.csv  wp core check-update --format=csv
run themes.csv           wp theme list --format=csv
run plugins.csv          wp plugin list --format=csv
run post-types.csv       wp post-type list --format=csv
run taxonomies.csv       wp taxonomy list --format=csv
run menus.csv            wp menu list --format=csv
run cron-events.csv      wp cron event list --format=csv
run site-options.txt     sh -c 'for o in siteurl home blogname template stylesheet; do printf "%s: " "$o"; wp option get "$o"; done'
run content-counts.txt   sh -c 'for t in $(wp post-type list --field=name); do printf "%s: " "$t"; wp post list --post_type="$t" --format=count; done'

echo "Inventory written to $OUT"
