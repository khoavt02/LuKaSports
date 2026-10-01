#!/usr/bin/env bash
#
# One-off rebrand of an existing install from "LukaSports" to "DALETIC".
# Fresh installs don't need this — the seed scripts already use the new
# name. Safe to re-run.
#
#   bash bin/migrate-to-daletic.sh
#
# What it does:
#   1. Site title/tagline → DALETIC.
#   2. Replaces "LukaSports" / "lukasports.vn" in content, settings and
#      reviews (serialization-safe wp search-replace; code identifiers such
#      as option names and CSS classes are lowercase and untouched).
#   3. Moves the About page from /ve-lukasports/ to /ve-daletic/.
#   4. Re-seeds the demo products (now with the DALETIC mark on every
#      image), reviews and blog posts, then re-applies pages/collections.
#      This REPLACES seeded demo products — products you added yourself
#      are not touched.

set -euo pipefail
cd "$(dirname "$0")/.."

wp() { docker compose run --rm -T wpcli "$@"; }

wp option update blogname "DALETIC"
wp option update blogdescription "Áo đấu và đồng phục thể thao"

for pair in "LukaSports:DALETIC" "lukasports.vn:daletic.vn"; do
	wp search-replace "${pair%%:*}" "${pair##*:}" --all-tables-with-prefix --skip-columns=guid --report-changed-only
done

about_id=$(wp post list --post_type=page --name=ve-lukasports --field=ID --format=ids 2>/dev/null || true)
if [ -n "$about_id" ]; then
	wp post update "$about_id" --post_name=ve-daletic
fi

wp eval-file lukasports-bin/seed-products.php
wp eval-file lukasports-bin/seed-reviews-blog.php
wp eval-file lukasports-bin/seed-pages.php
wp cache flush
echo "Rebrand complete."
