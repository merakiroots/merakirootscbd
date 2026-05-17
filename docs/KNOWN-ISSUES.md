# Known Issues

### Active stack quirks

- Docker, Node, and Git may not be on `PATH` in this shell even when installed in standard Windows locations.
- Docker Compose runtime validation works when PowerShell prepends `C:\Program Files\Docker\Docker\resources\bin` to `PATH`.
- DDEV is the current launch runtime at `https://my-existing-site.ddev.site`; `.ddev/config.yaml` intentionally disables Mutagen with `performance_mode: "none"` after a stale sync exposed only `wp-content` in the web container.
- Local PHP CLI is available as `D:\php.exe` (PHP 8.3.31), so static PHP linting is available outside Docker.
- Node is available as `C:\Program Files\nodejs\node.exe`; the root `npm` scripts still describe a generic `wp-env` setup and are not the launch verification path.
- Product image imports in DDEV may print nonfatal `chmod(): Operation not permitted` warnings from WordPress image generation on the Windows bind mount. Re-run the launch audit instead of treating those warnings as an import failure.
- Live payment processing is not configured yet. Until the approved provider gateway is enabled, the storefront uses an intentional payment-provider review mode: browsing/cart/checkout layout remain visible, but order submission is disabled.

### Legacy theme/plugin dependencies still present

- The launch docs mention OceanWP/child-theme bridge context, but this workspace contains the custom `meraki-block-theme` and `meraki-commerce-core` target surfaces. Runtime theme truth still must be checked once WP-CLI/browser access works.

### Pages that are legally or commercially sensitive

- Privacy Policy, Refund Policy, Shipping Policy, Terms of Service, product warnings, and FDA/compliance copy need human owner review before launch.
- Contact and Partner pages must stay usable even if a form plugin is not connected.

### Imports that have already partially run

- The launch catalog import has been run locally through `scripts/wp-cli/meraki-import-catalog.php`.
- Current local state is expected to contain 30 published products, 30 published COA posts, mapped category thumbnails, and one draft merch T-shirt without a primary image.
- Re-run `scripts/wp-cli/meraki-audit-launch.php` after any import or data repair.

### Data that must not be overwritten casually

- WooCommerce product records, product images, `_mr_current_coa_id`, `_mr_coa_file`, COA post meta, category media, redirect mappings, checkout/payment/tax/shipping settings.

### Product categories with known oddities

- Expected category slugs include `tinctures`, `capsules`, `vape-cartridges`, `terpsolate-diamonds`, `topicals`, `body-lotion`, and `merch`. Verify live terms before importing or remapping media.

### Redirect behavior already configured elsewhere

- Unknown. The Redirection import CSVs exist, but the active redirect plugin/config must be checked before importing.

### Host-specific limitations

- Hostinger production paths and logs differ from local Docker paths. Verify actual Hostinger runtime paths before applying production commands.

## Escalation Rules

Codex should pause and call out the risk clearly if it discovers:

- production data corruption risk
- ambiguous product-to-COA remapping
- payment/shipping changes outside launch scope
- destructive import behavior
- runtime failures that suggest a deeper environment mismatch
