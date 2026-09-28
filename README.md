# Linea

Custom WordPress + WooCommerce premium activewear storefront. Custom theme (`linea`) + custom plugin (`linea-core`), no page builder, no Elementor/Divi/WPBakery.

A sibling project to SportKit, same underlying architecture (custom theme + custom plugin, consultation-led conversion flow, no page builder), rebuilt with a minimalist/premium visual direction and a general-athleisure catalog instead of team/football jerseys — warm off-white + near-black + a muted brass accent, sentence-case copy, sharp-to-barely-rounded corners, no pill-shaped buttons.

### Where things live

- **Design tokens**: `--sk-*` custom properties in `assets/css/core.css` — ink/paper/surface/line/muted/accent colors, spacing scale, radius (kept small/sharp, not pill), shadow (faint). One system sans stack for both display and body text — no self-hosted webfont.
- **Leads**: custom table `wp_linea_leads` — `wp-content/plugins/linea-core/includes/leads.php` (schema + CRUD), `includes/ajax.php` (public submit endpoint), `includes/admin/` (list table + detail screen).
- **Contact/CTA settings**: `includes/settings.php` (Settings API, option `linea_settings`) — edit at Linea → Settings in wp-admin. Theme reads it via `linea_get_contact_info()`.
- **Consultation modal**: `wp-content/themes/linea/template-parts/forms/consultation-modal.php`, rendered once in `footer.php`. Opens on any `data-linea-cta="consult"` click (see `assets/js/main.js`).
- **UTM/attribution**: captured client-side into a first-touch, 30-day cookie (`assets/js/main.js`), read server-side by the AJAX handler when a lead is created.
- **Events**: `window.lineaTrack(name, data)` in `assets/js/main.js` — fires `view_product`, `click_consultation`, `click_messenger`, `click_zalo`, `click_phone`, `submit_consultation`, `click_design_team`.
- **Collections**: `product_collection` taxonomy (`includes/taxonomies.php`), rewrite base `bo-suu-tap` — tag-style, assignable per product, independent of Product Category.

## Requirements

- Docker + Docker Compose

Nothing else needs to be installed locally — WordPress, MySQL, and WP-CLI all run in containers. Only the theme (`wp-content/themes/linea`) and plugin (`wp-content/plugins/linea-core`) source live in this repo; WordPress core and default wp-content live in a Docker-managed volume so the repo only tracks custom code.

This project runs alongside SportKit (`AoBongDa/`) on different ports — nothing here touches that stack.

## Quick start (one command)

```bash
bash bin/setup.sh
```

Creates `.env` (with a random admin password) if missing, starts the containers and — on a fresh database — installs WordPress + WooCommerce, activates the theme/plugin, applies the Vietnamese store settings and runs all seed scripts. On an existing install it just starts the stack, so it's safe to re-run. The manual steps below are what it automates.

### GitHub Codespaces (temporary public demo)

The repo ships a `.devcontainer/` that runs `bin/setup.sh` on every Codespace start. Create a Codespace (Code → Codespaces → Create), wait for the first setup (~5–6 min), then in the **Ports** tab set port 8090 to **Public** (the script tries to do this itself) and share its `*.app.github.dev` URL. The admin password is in the Codespace's `.env`.

Codespaces stop after the idle timeout (30 min by default, max 4 h — visitors browsing the site don't count as activity) and use the account's free monthly quota; the database lives only in that Codespace. Good for a scheduled demo, not for hosting.

## How to run

```bash
cp .env.example .env
docker compose up -d db wordpress
```

Site: http://localhost:8090 (or `${WP_PORT}` from `.env`). First run installs WordPress; if you're starting from scratch, run through setup with WP-CLI:

```bash
docker compose run --rm wpcli core install \
  --url="http://localhost:8090" \
  --title="Linea" \
  --admin_user="admin" \
  --admin_password="<choose one>" \
  --admin_email="you@example.com"

docker compose run --rm wpcli plugin install woocommerce --activate
docker compose run --rm wpcli theme activate lukasports
docker compose run --rm wpcli plugin activate lukasports-core
docker compose run --rm wpcli wc tool run install_pages --user=1
docker compose run --rm wpcli rewrite structure '/%postname%/' --hard
```

> **Windows/Git Bash note:** if you run wp-cli commands from Git Bash, prefix any command whose arguments contain a leading `/` (like the `rewrite structure` command above, or paths passed to `wp eval`) with `MSYS_NO_PATHCONV=1` — otherwise MSYS silently mangles the argument into a Windows path (e.g. `/%postname%/` becomes `/C:/Program Files/Git/%postname%/`), which corrupts `permalink_structure` in the database while still *looking* fine for most pages (categories/products use other rewrite mechanisms) until you hit a post/blog URL or a custom taxonomy archive.

Then set up (once, from scratch):

```bash
docker compose run --rm wpcli language core install vi
docker compose run --rm wpcli site switch-language vi
docker compose run --rm wpcli language plugin install woocommerce vi

docker compose run --rm wpcli option update woocommerce_currency VND
docker compose run --rm wpcli option update woocommerce_currency_pos right
docker compose run --rm wpcli option update woocommerce_price_thousand_sep "."
docker compose run --rm wpcli option update woocommerce_price_decimal_sep ","
docker compose run --rm wpcli option update woocommerce_price_num_decimals 0
docker compose run --rm wpcli option update woocommerce_default_country VN

# Classic cart/checkout instead of the WooCommerce Blocks default — this
# theme's CSS targets the classic shortcode markup, not block markup.
docker compose run --rm wpcli post update <cart_page_id> --post_content='[woocommerce_cart]'
docker compose run --rm wpcli post update <checkout_page_id> --post_content='[woocommerce_checkout]'
```

Optional dev tools:

```bash
docker compose --profile tools up -d phpmyadmin   # http://localhost:8091
```

## How to add products

Standard WooCommerce: wp-admin → Products → Add New. Two Linea-specific things live in the "Linea — Thông tin bổ sung" metabox on the product editor:

- **Lợi ích sản phẩm** — one benefit per line, shown as a checklist next to the price.
- **Hỗ trợ thêu tên theo yêu cầu** — checkbox; adds a benefit line automatically.

Global attributes (Size/Màu sắc/Loại hình vận động/Chất liệu/Năm ra mắt/Giới tính) already exist under Products → Attributes.

For bulk dev/test data, `bin/seed-products.php` creates 8 categories and 25 products (mix of simple/variable, on-sale/regular) across Áo Tank, Áo Thun Thể Thao, Quần Legging, Quần Shorts, Áo Khoác Gió, Áo Hoodie, Đồ Tập Yoga and Phụ Kiện. It's idempotent — re-running deletes anything it created before (tagged `_linea_seed`) and recreates it:

```bash
docker compose run --rm wpcli eval-file lukasports-bin/seed-products.php
docker compose run --rm wpcli eval-file lukasports-bin/seed-reviews-blog.php
docker compose run --rm wpcli eval-file lukasports-bin/seed-pages.php
```

`seed-pages.php` creates every Page the header/footer link to (Sản phẩm, Về LukaSports, Liên hệ, Bộ sưu tập, Blog, the three policy pages), moves the WooCommerce pages to Vietnamese slugs (`/san-pham/`, `/gio-hang/`, `/thanh-toan/`, `/tai-khoan/`), sets Settings → Reading to a static front page with `/blog/` as the posts page, turns off WooCommerce's "coming soon" mode, and creates three product collections. Existing pages are matched by slug and keep their content, so it's safe to re-run.

### Category URLs

Every top-level product category is served at a clean URL (`/ao-bong-da/`) automatically — `inc/hooks.php` routes the request to the native category archive, which renders with the shop template (category chips, sorting, pagination). The native `/product-category/{slug}/` URL 301-redirects to the clean one. No Page is needed per category; if you *want* a custom landing page, create a Page whose slug matches the category's slug with template **"LukaSports — Category Landing"** and it takes precedence.

### Page templates

- **LukaSports — Giới thiệu** (`template-about.php`): brand story — the Page content is the story text; stats/values/process/CTA are layout.
- **LukaSports — Liên hệ** (`template-contact.php`): contact channels from LukaSports → Settings plus an inline consultation form (leads land in the same table, `source = contact_page`).
- **LukaSports — Bộ sưu tập** (`template-collections.php`): index of `product_collection` terms; each links to `/bo-suu-tap/{slug}/`.

### Blog / policy pages

Created by `seed-pages.php`. If you set things up by hand instead: the theme's `home.php` (posts index) only takes effect once **Settings → Reading** is set to *"A static page"* with a **Posts page** assigned, and the footer expects Pages at `/chinh-sach-doi-tra/`, `/chinh-sach-giao-hang/`, `/chinh-sach-bao-mat/` and `/lien-he/`.

## How to change homepage content

`front-page.php` lists the sections in order (hero → trust bar → categories → best sellers → team CTA → social proof → blog). Same section files as SportKit under `template-parts/`, same "Xem tất cả →" style patterns. Contact links (hotline/Messenger/Zalo/Facebook/address) come from `linea_get_contact_info()` in `inc/helpers.php`.

Design tokens (colors, type scale, spacing, radius, shadows, breakpoints) are CSS variables in `assets/css/core.css`, mirrored in `theme.json` for the block editor.

## Known issues / limitations

Same shape as SportKit's — no payment/shipping integration, no customer accounts/CRM, social proof section is empty until real testimonials are added via `linea_social_proof_items`, no automated visual/Lighthouse testing in this environment.

## Backup

Two things to back up: the `db_data` and `wp_data` Docker volumes, and this repo (theme + plugin + `bin/` + compose files).
