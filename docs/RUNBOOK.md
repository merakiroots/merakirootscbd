# Runbook

## Purpose

This file explains exactly how to operate once the real environment is present.

Fill in any project-specific blanks before use.

## Required Runtime Context

- WordPress root path: `D:\merakirootscbd2\`
- Active theme path: `D:\merakirootscbd2\wp-content\themes\meraki-block-theme\`
- Custom plugin path: `D:\merakirootscbd2\wp-content\plugins\meraki-commerce-core\`
- WooCommerce active: `yes`
- PHP version: `8.3.1`
- Node/build tooling used by theme or plugin: `24.11.1`
- WP-CLI available: `yes`
- Composer used: `yes`

## Boot Commands

Use the exact commands Codex should run:

```bash
cd D:/merakirootscbd2
ddev start
ddev wp core version --allow-root
ddev wp plugin list --allow-root
ddev wp theme list --allow-root
```

### Current Launch Runtime

For the 2026-05-09 launch pass, use DDEV as the primary local entrance:

- DDEV resolves WordPress at `https://my-existing-site.ddev.site`.
- `.ddev/config.yaml` has `performance_mode: "none"` because the previous Mutagen sync session exposed only `wp-content` inside `/var/www/html`, which prevented WordPress from loading cleanly.
- The legacy Docker Compose stack has been stopped during this pass so DDEV owns the local web/database runtime.
- WP-CLI verified WordPress `6.9.4`, active theme `meraki-block-theme`, active plugins `meraki-commerce-core` and `woocommerce`, `30` published products, and `30` published COAs.
- Fresh DDEV setup used `admin` / `admin123` for the local administrator account and disabled WooCommerce coming-soon mode.

On this Windows workstation, Docker and Node may not be on `PATH`. Prefer DDEV commands first. If Docker Compose is needed for legacy verification and `docker` is not found from PowerShell, use:

```powershell
$env:PATH = 'C:\Program Files\Docker\Docker\resources\bin;' + $env:PATH
$env:PROJECT_NAME = 'merakirootscbd2'
docker compose up -d
```

## Verification Commands

At minimum, provide working commands for:

```bash
rg --files wp-content/plugins/meraki-commerce-core wp-content/themes/meraki-block-theme scripts -g '*.php' -g '!**/vendor/**' -g '!**/assets/blocks/lab-results/**' | ForEach-Object { php -l $_ }
ddev exec bash -lc 'find wp-content/plugins/meraki-commerce-core/src wp-content/themes/meraki-block-theme scripts/wp-cli -name "*.php" -not -path "*/vendor/*" -print0 | xargs -0 -n1 php -l'
ddev exec bash -lc 'cd wp-content/plugins/meraki-commerce-core && vendor/bin/phpcs'
ddev exec bash -lc 'cd wp-content/plugins/meraki-commerce-core && vendor/bin/phpstan analyse --no-progress'
ddev wp option get permalink_structure --allow-root
ddev wp wc product list --user=1 --allow-root
ddev wp eval-file scripts/wp-cli/meraki-audit-launch.php --allow-root
```

## Data/Import Commands

Document the real commands for:

- product import
- image import or sideload
- category media mapping
- redirect import
- COA migration

Current repeatable local examples:

```bash
ddev wp eval-file scripts/wp-cli/meraki-bootstrap.php --allow-root
ddev wp eval-file scripts/wp-cli/meraki-import-catalog.php --allow-root
ddev wp eval-file scripts/wp-cli/meraki-audit-launch.php --allow-root
ddev wp meraki coa migrate-legacy --dry-run --allow-root
```

These commands are intended for local launch environments. `meraki-bootstrap.php` and `meraki-import-catalog.php` mutate pages, menus, products, categories, media, COA posts, and rewrite rules. Run the audit immediately after any import and do not run the mutating commands against production without a database backup and owner approval.

`scripts/wp-cli/meraki-import-catalog.php` is the launch importer for products, local product images, category thumbnails, and current COA attachments. It prefers real launch photos from `wp-content/uploads/product-photos-current/` and falls back to `wp-content/uploads/product-photos-expanded/` only when the current pack is missing. It also prefers COA PDFs from `wp-content/plugins/meraki-commerce-core/uploads/lab-results/` before falling back to the historical block asset bundle.

`scripts/wp-cli/meraki-audit-launch.php` is the repeatable launch gate. It verifies active theme/plugin registration, WooCommerce page wiring, published product images, product trust fields, current COA posts, readable COA attachments, category thumbnails, and lab-results shortcode output.

## Browser Verification Targets

Check these URLs in the running environment:

- `/`
- `/shop/`
- `/product-category/tinctures/`
- `/product-category/capsules/`
- at least 3 real single-product URLs
- `/lab-results/`
- `/partner/`
- `/contact/`
- `/cart/`

## Logs And Diagnostics

Where to look:

- PHP error log: local PHP CLI errors print to the terminal during lint/static checks.
- WordPress debug log in Docker: `/var/www/html/wp-content/debug-logs/debug.log`, mounted locally at `D:\merakirootscbd2\logs\debug.log`.
- Legacy/local debug log observed: `D:\merakirootscbd2\wp-content\debug_logs\debug.log`.
- Web/server log observed: `D:\merakirootscbd2\logs\server.log`.
- JS build log location: no dedicated JS build log is configured yet.

## Rollback Safety

Before launch-critical edits, you will:

1. inspect the current workspace state
2. avoid deleting unknown changes
3. keep edits scoped
4. leave a short summary of what changed and why
