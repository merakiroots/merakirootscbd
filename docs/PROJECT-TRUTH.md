# Project Truth

## Business Truth

The goal is to relaunch today, not to win an architecture award tomorrow.

Customers and clients need a storefront they can trust and use now.

## Technical Truth

The safest direction is:

- plugin-owned data and trust logic
- theme-owned layout and presentation
- launch-ready bridge choices allowed when they reduce risk today

## Source Of Truth Rules

### Design source of truth

- the current best Meraki visual references
- the screenshots and source exports already gathered
- the brand should feel restrained, clean, product-first, and credible

### Data source of truth

- product import CSVs
- image mapping CSVs
- category media maps
- redirect import files
- lab-result source files

### Runtime source of truth

- the actual local or staging WordPress instance Codex is working against is wordpress:version-6.9.4
- the actual active code in the working repo is php:version-8.3.1

### Acceptance source of truth

- `QA-CHECKLIST.md`
- actual browser verification on the running site

## Non-Negotiable Product Trust Rules

- if a current COA exists, it must be reachable from the product or lab-results flow
- ingredients, suggested use, and warnings should not live only in fragile theme markup
- no medical or cure-style claims should be introduced casually during launch fixes

## What Codex May Change Freely

- theme templates
- block templates and template parts
- theme CSS and layout behavior
- plugin implementation details inside the new Meraki-owned plugin surface
- bootstrap or import helper scripts
- mapping files or importer support scripts when needed for launch

## What Codex Must Treat Carefully

- product data
- COA associations
- redirects
- checkout behavior
- legal/compliance-facing copy
- WooCommerce page assignments

## What Codex Should Ask Before Changing If Possible

- destructive data cleanup
- deleting old content in bulk
- swapping away from a known-active production theme without a rollback path
- changing payment, tax, or shipping logic

## Working Standard

When a faster workaround and a production-safe workaround both exist, Codex should prefer the production-safe one if it still fits the end-of-day timeline.
