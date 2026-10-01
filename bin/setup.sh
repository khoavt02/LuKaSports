#!/usr/bin/env bash
#
# One-command setup: starts the stack and, on a fresh database, installs
# WordPress + WooCommerce, activates the DALETIC theme/plugin, applies
# the Vietnamese store settings and seeds products, reviews, blog posts
# and pages. On an already-installed site it only starts the containers
# and re-applies the dynamic site URL, so it's safe to run on every boot
# (the Codespaces devcontainer runs it as its postStartCommand).
#
#   bash bin/setup.sh
#
# Admin login: WP_ADMIN_USER / WP_ADMIN_PASSWORD from .env. When .env
# doesn't exist yet it's created from .env.example with a random admin
# password, since a Codespaces port can be public.

set -euo pipefail
cd "$(dirname "$0")/.."

log() { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }

if [ ! -f .env ]; then
	log "Creating .env from .env.example"
	cp .env.example .env
	password=$(LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c 20 || true)
	sed -i.bak "s/^WP_ADMIN_PASSWORD=.*/WP_ADMIN_PASSWORD=${password}/" .env
	if [ -n "${CODESPACES:-}" ]; then
		# Public demo: don't print PHP notices to visitors.
		sed -i.bak 's/^WP_DEBUG=.*/WP_DEBUG=/' .env
	fi
	rm -f .env.bak
fi

set -a
# shellcheck disable=SC1091
. ./.env
set +a

WP_PORT="${WP_PORT:-8090}"
if [ -n "${CODESPACE_NAME:-}" ]; then
	SITE_URL="https://${CODESPACE_NAME}-${WP_PORT}.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN:-app.github.dev}"
else
	SITE_URL="http://localhost:${WP_PORT}"
fi

wp() { docker compose run --rm -T wpcli "$@"; }

log "Starting containers"
docker compose up -d db wordpress

log "Waiting for MySQL"
for _ in $(seq 1 60); do
	if docker compose exec -T db mysqladmin ping -h 127.0.0.1 -uroot -p"${MYSQL_ROOT_PASSWORD}" --silent >/dev/null 2>&1; then
		break
	fi
	sleep 3
done

log "Waiting for WordPress files"
for _ in $(seq 1 60); do
	docker compose exec -T wordpress test -f /var/www/html/wp-config.php && break
	sleep 2
done

# Build every URL from the request's host, so the same database works on
# localhost, a Codespaces URL or a tunnel without a search-replace.
# (Host behind a proxy is fixed up by docker/php/forwarded-host.php.)
url_expr="( !empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] !== 'off' ? 'https' : 'http' ) . '://' . ( \$_SERVER['HTTP_HOST'] ?? 'localhost:${WP_PORT}' )"

if wp core is-installed >/dev/null 2>&1; then
	log "WordPress already installed — skipping install and seed"
	wp config set WP_HOME "$url_expr" --raw --quiet
	wp config set WP_SITEURL "$url_expr" --raw --quiet
else
	log "Installing WordPress"
	wp core install \
		--url="$SITE_URL" \
		--title="${WP_SITE_TITLE:-DALETIC}" \
		--admin_user="${WP_ADMIN_USER:-admin}" \
		--admin_password="${WP_ADMIN_PASSWORD}" \
		--admin_email="${WP_ADMIN_EMAIL:-dev@example.com}" \
		--skip-email
	wp config set WP_HOME "$url_expr" --raw --quiet
	wp config set WP_SITEURL "$url_expr" --raw --quiet

	log "Installing WooCommerce and activating DALETIC"
	wp plugin install woocommerce --activate
	wp theme activate lukasports
	wp plugin activate lukasports-core

	log "Vietnamese language + store settings"
	wp language core install vi
	wp site switch-language vi
	wp language plugin install woocommerce vi
	wp option update woocommerce_currency VND
	wp option update woocommerce_currency_pos right
	wp option update woocommerce_price_thousand_sep "."
	wp option update woocommerce_price_decimal_sep ","
	wp option update woocommerce_price_num_decimals 0
	wp option update woocommerce_default_country VN
	wp wc tool run install_pages --user=1
	wp rewrite structure '/%postname%/' --hard

	log "Seeding products, reviews, blog posts and pages"
	wp eval-file lukasports-bin/seed-products.php
	wp eval-file lukasports-bin/seed-reviews-blog.php
	wp eval-file lukasports-bin/seed-pages.php
fi

if [ -n "${CODESPACE_NAME:-}" ] && command -v gh >/dev/null 2>&1; then
	log "Making port ${WP_PORT} public"
	gh codespace ports visibility "${WP_PORT}:public" -c "$CODESPACE_NAME" >/dev/null 2>&1 \
		|| echo "Could not change port visibility automatically — set it to Public in the Ports tab."
fi

log "Done"
echo "Site:  ${SITE_URL}"
echo "Admin: ${SITE_URL}/wp-admin  (user: ${WP_ADMIN_USER:-admin}, password: WP_ADMIN_PASSWORD in .env)"
