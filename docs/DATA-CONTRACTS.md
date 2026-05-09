# Data Contracts

## Principle

Codex should not guess field meaning when real mapping files already exist.

Use the real CSVs and source files as the source of truth.

## Product Meta Contract

Expected trusted fields:

- `_mr_current_coa_id`
- `_mr_product_form`
- `_mr_ingredients`
- `_mr_suggested_use`
- `_mr_warning`

Optional future fields may exist, but launch should not depend on undocumented fields unless clearly present in the runtime.

## COA Contract

COA records should support at least:

- attachment ID
- legacy URL when relevant
- batch ID
- test date
- lab name
- related product IDs
- status

## Media Contract

Use these source files when present:

- `meraki_product_image_map.csv`
- `meraki_category_media_map.csv`
- frontend asset upload pack

Codex should preserve stable filenames and map media deliberately, not ad hoc.

## Redirect Contract

Use:

- `meraki_redirects_redirection_import.csv`
- `meraki_redirects_redirection_import_no_header.csv`

Codex should not improvise rewrite rules if a real redirect import contract already exists.

## Product Import Contract

Use:

- `meraki_woocommerce_products_full_import_v3.csv`
- `meraki_woocommerce_products_update_by_sku_v3.csv`

Codex should inspect these files before deciding whether to modify products in place, update by SKU, or re-import.

## Lab Result Sources

Use the renamed lab-result archive and any current COA files as the source for:

- attachment import
- legacy URL reconciliation
- COA linking

## Data Safety Rules

- no bulk destructive cleanup without clear need
- dry-run migration commands first when possible
- log skipped records explicitly
- preserve product-to-COA relationships carefully
