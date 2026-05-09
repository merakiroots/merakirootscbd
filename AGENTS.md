# AGENTS.md

> Project context for AI coding agents working in this repository.
> This is a **private/internal** file — do not share externally.

---

## Project Overview

This is a **WordPress/WooCommerce development environment** for the **Meraki Roots CBD** storefront, running entirely inside Docker Compose. The core custom code lives in a single WordPress plugin: **Meraki Commerce Core** (`wp-content/plugins/meraki-commerce-core/`).

The repo is **not** a standalone PHP project — it is a WordPress application with Docker infrastructure, WP-CLI tooling, and a plugin that follows WordPress conventions (not framework conventions).

---

## Architecture

### Plugin: `meraki-commerce-core`

- **Namespace:** `MerakiCommerceCore` (PSR-4, mapped to `src/`)
- **Entry point:** `meraki-commerce-core.php` (plugin header → `Bootstrap::boot()`)
- **DI container:** `src/Support/Container.php` — simple factory-based container, not Laravel/Symfony
- **Autoloading:** Plugin registers its own `spl_autoload_register` — do NOT rely on Composer autoload for plugin classes

### Domain boundaries (all under `src/Domain/`):

| Directory | Purpose |
|---|---|
| `COA/` | Certificate of Analysis — custom post type (`mr_coa`), meta boxes, migration CLI command, product ↔ COA relationships |
| `ProductMeta/` | Product meta schema and registration (custom fields on WooCommerce products) |
| `Frontend/` | Lab results shortcode `[meraki_lab_results]`, block rendering, COA presentation on single product pages |
| `Compliance/` | Compliance text generation and claim-risk helper logic |
| `Rest/` | Custom REST API fields for COA data |

### Key wiring points (`Bootstrap.php` lines 52–82):

- `init` → registers COA post type, product meta, COA meta, lab results block, block assets
- `rest_api_init` → REST fields
- `woocommerce_single_product_summary` → COA presenter (priority 28)
- `woocommerce_product_options_general_product_data` → COA panel
- `woocommerce_process_product_meta` → COA panel save
- `save_post_mr_coa` → meta box save
- `plugins_loaded` → textdomain, version option
- WP-CLI: `CoaMigrationCommand` registered when `WP_CLI` is defined

### Docker services (`docker-compose.yaml`):

| Service | Image | Purpose |
|---|---|---|
| `db` | `mariadb:11.4` | MySQL database (port 3306) |
| `wordpress` | Custom `docker/Dockerfile` | Apache + PHP 8.3 + Xdebug (port 8080) |
| `wpcli` | `wordpress:cli` | WP-CLI container (no persistent process) |
| `composer` | Custom `docker/Dockerfile` | Composer-only container for plugin deps |
| `phpmyadmin` | `phpmyadmin:latest` | DB GUI (port 8081) |
| `mailhog` | `mailhog/mailhog:latest` | Email catcher (port 8025) |

Named volumes: `db_data`, `wp_core`, `composer_cache`

---

## Environment

**Credentials** (`.env`, never commit real values):

```
DB:    mysql | wordpress | wordpress | root
WP:    admin / admin123
URL:   http://localhost:8080
Admin: http://localhost:8080/wp-admin
```

**Debug settings** (`docker-compose.yaml` `WORDPRESS_CONFIG_EXTRA`):

- `WP_DEBUG` = true, `WP_DEBUG_LOG` = true, `WP_DEBUG_DISPLAY` = false
- `SCRIPT_DEBUG` = true, `SAVEQUERIES` = true, `WP_ENVIRONMENT_TYPE` = local

**Xdebug:** Enabled in `docker/php.ini` — client host `host.docker.internal`, port `9003`.

**PHP config** (`docker/php.ini`): `memory_limit=512M`, `upload_max_filesize=128M`, `opcache.enable=0`.

---

## Developer Commands

### Docker

```bash
docker compose up -d              # start all services
docker compose down               # stop and remove containers
docker compose restart            # restart containers
docker compose logs -f            # follow logs
docker compose logs -f wordpress  # WordPress logs only
docker compose exec -T wordpress <cmd>  # run non-interactive (CI/scripting)
```

### WP-CLI (inside WordPress container)

```bash
# Generic
docker compose exec wordpress wp <command> --allow-root

# Plugin management
docker compose exec wordpress wp plugin list --status=active
docker compose exec wordpress wp plugin install <slug> --activate --allow-root

# WooCommerce
docker compose exec wordpress wp wc product list --format=table
docker compose exec wordpress wp wc product list --sku=<SKU> --format=table

# Permalinks
docker compose exec wordpress wp rewrite structure '/%postname%/' --allow-root
docker compose exec wordpress wp rewrite flush --allow-root

# Cache
docker compose exec wordpress wp cache flush --allow-root || true
docker compose exec wordpress wp litespeed-purge all --allow-root || true

# WordPress core & WooCommerce updates
docker compose exec wordpress wp core update --allow-root
docker compose exec wordpress wp plugin update woocommerce --allow-root
```

### Composer (inside Composer container)

```bash
docker compose exec composer composer install --working-dir=/var/www/html/wp-content/plugins/meraki-commerce-core
```

### Testing & Linting (run from plugin directory inside container)

```bash
# PHPStan (level 5)
docker compose exec composer phpstan analyse src --level=5 --working-dir=/var/www/html/wp-content/plugins/meraki-commerce-core

# PHP CodeSniffer (WPCS)
docker compose exec composer phpcs . --standard=WordPress --working-dir=/var/www/html/wp-content/plugins/meraki-commerce-core

# PHP Code Beautifier (auto-fix)
docker compose exec composer phpcbf . --standard=WordPress --working-dir=/var/www/html/wp-content/plugins/meraki-commerce-core

# PHPUnit (if tests exist)
docker compose exec composer phpunit --working-dir=/var/www/html/wp-content/plugins/meraki-commerce-core
```

### Data Import

CSV import files are in `wp-content/plugins/meraki-commerce-core/uploads/imports/`:

- `meraki_woocommerce_products_full_import_v3.csv` — full product import
- `meraki_woocommerce_products_update_by_sku_v3.csv` — SKU-based product updates
- `meraki_redirects_redirection_import.csv` — Redirection plugin redirect import
- `meraki_product_image_map.csv` — product image mapping
- `meraki_category_media_map.csv` — category media mapping

Run imports via WP-CLI or the plugin's import UI after `docker compose up -d` finishes.

### Initial Setup Script

`scripts/01-install-baseline.sh` — run **inside the WordPress container** after first `docker compose up -d` + DB initialization (~30–60s wait):

```bash
# Do NOT run this before WordPress is fully initialized.
# Wait for: docker compose logs wordpress | grep "ready"
docker compose exec wordpress bash /scripts/01-install-baseline.sh
```

This installs OceanWP theme + required plugins, activates the child theme, sets permalink structure, and runs the bootstrap PHP file.

---

## Coding Standards

- **PHP Standard:** [WordPress Coding Standards (WPCS)](https://github.com/WordPress/WordPress-Coding-Standards) — enforced via `phpcs`
- **Static Analysis:** PHPStan level 5 with [`szepeviktor/phpstan-wordpress`](https://github.com/szepeviktor/phpstan-wordpress)
- **Formatter:** `phpcbf` with WordPress standard
- **Indentation:** 4 spaces (`.editorconfig`)
- **Line endings:** LF
- **Composer platform:** PHP `8.3.1`
- **Dependencies:** `composer/ca-bundle`, `wp-cli/wp-cli-bundle`

Plugin follows WordPress's [prefixing convention](https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything) — all public functions, hooks, and meta keys use the `mr_` or `meraki_` prefix.

---

## Plugin Source Map

```
wp-content/plugins/meraki-commerce-core/
├── meraki-commerce-core.php    # Plugin entry (header + autoloader + Bootstrap::boot)
├── uninstall.php               # Cleanup on uninstall
├── composer.json               # Plugin deps + dev deps + scripts
├── src/
│   ├── Bootstrap.php           # Plugin wiring — all hooks, actions, filters
│   ├── Support/
│   │   ├── Container.php       # Simple DI container (set/get/factory)
│   │   └── Assets.php          # Enqueue admin JS, register block assets
│   └── Domain/
│       ├── COA/                # Certificate of Analysis custom post type
│       │   ├── CoaPostType.php      # Custom post type registration
│       │   ├── CoaMetaRegistrar.php # Meta fields for COA posts
│       │   ├── CoaAdminMetaBox.php   # Admin UI meta box
│       │   ├── CoaRepository.php     # DB queries: all, get_current_for_product, upsert_for_product, get_public_data
│       │   ├── CoaNormalizer.php     # Normalize product ID arrays
│       │   ├── ProductCoaPanel.php   # WooCommerce product edit panel
│       │   └── CoaMigrationCommand.php # WP-CLI migration command
│       ├── ProductMeta/
│       │   ├── ProductMetaSchema.php    # Meta field definitions
│       │   └── ProductMetaRegistrar.php # Register meta on products
│       ├── Frontend/
│       │   ├── LabResultsQuery.php      # Query COAs for lab results display
│       │   ├── LabResultsShortcode.php  # [meraki_lab_results] shortcode
│       │   ├── LabResultsBlock.php      # Block editor support
│       │   └── ProductCoaPresenter.php  # Render COA callout on single product
│       ├── Compliance/
│       │   ├── ComplianceText.php    # Compliance disclaimer text generation
│       │   └── ClaimRiskHelper.php   # Claim risk assessment helpers
│       └── Rest/
│           └── RestFields.php        # Custom REST API endpoint fields
├── uploads/imports/            # CSV import files (never commit real data)
└── vendor/                     # Composer dependencies (WPCS, PHPStan, PHPUnit)
```

---

## Common Pitfalls

1. **Don't edit `wp-config.php` directly** — it's generated/managed by Docker env vars via `WORDPRESS_CONFIG_EXTRA` in `docker-compose.yaml`. Custom constants go there.

2. **WP-CLI needs `--allow-root`** — the WordPress container runs as root, and WP-CLI refuses to run as root without this flag.

3. **Plugin classes use the `MerakiCommerceCore\` namespace** — the plugin has its own autoloader registered in `meraki-commerce-core.php`, not Composer's. Don't assume Composer autoloading for plugin code.

4. **Wait for DB initialization** — MySQL takes ~10–20s to first start. If `docker compose exec` commands fail with connection refused, wait and retry.

5. **`/scripts/01-install-baseline.sh` is destructive** — it installs/activates themes and plugins and overwrites permalink structure. Only run on fresh or disposable environments.

6. **CSV data files contain real product data** — do not commit `uploads/imports/*.csv` files with real SKU/pricing data to public repos.

7. **WPCS `PrefixAllGlobals` sniff** — the plugin uses `mr_` prefix. If adding new functions/classes/meta keys, ensure they are prefixed or whitelisted.

8. **PHPStan requires WordPress stubs** — run `phpstan analyse` from the Composer container where `szepeviktor/phpstan-wordpress` is installed. Running from the repo root without the plugin's `vendor/` will miss WordPress type definitions.

9. **Xdebug port is 9003** — the php.ini config maps `xdebug.client_port` to `9003` (not the default 9000). Configure your IDE accordingly.

10. **Do not install plugins/themes via WP Admin** in this environment — they are ephemeral Docker volumes. Use `wp plugin install` or mount them via Docker volumes (already configured).