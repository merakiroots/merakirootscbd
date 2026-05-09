# Build Brief

## Objective

Get `merakirootscbd.com` relaunch-ready by end of day today.

The site has already waited too long. Clients are waiting and products are sitting. This brief prioritizes a strong, launchable state over long-horizon perfection.

## Primary Goal

Ship a clean, trustworthy WooCommerce storefront for Meraki Roots with:

- stable theme behavior
- product pages that feel intentional and complete
- clear trust/compliance presentation
- accessible current lab-results access
- clean core navigation
- working key pages for customers and partners

## Secondary Goal

Preserve the plugin-owned data direction so the site does not become permanently dependent on brittle theme logic.

## In Scope

- WordPress theme fixes and completion work
- WooCommerce template fixes and launch polish
- plugin wiring needed for product trust and COA behavior
- import or mapping fixes for products, images, category media, and redirects
- page-template and block-theme completion needed for launch
- bug fixes blocking storefront use
- mobile and desktop QA fixes for key surfaces

## Out Of Scope Unless Needed To Unblock Launch

- perfect long-term architecture
- deep refactors with broad blast radius
- non-essential admin UX niceties
- new feature ideation unrelated to launch
- cosmetic polishing on low-traffic pages
- content rewrites unless a page is clearly broken or unsafe

## Non-Negotiables

- no fatal errors
- no white-screen or broken template loading
- no obviously broken cart or product purchase flow
- no hardcoded dependencies on missing helper functions
- no unsafe compliance claims introduced by the build
- no raw placeholder or obviously unfinished content on key public pages

## Launch-Critical Surfaces

- homepage
- shop archive
- top product-category pages
- single-product pages
- lab results page
- partner page
- contact page
- header and footer navigation
- mobile menu

## Done Means

The site is done enough to relaunch today when:

1. The homepage, shop, product pages, and lab-results page all render cleanly.
2. Product pages show ingredients, suggested use, warnings, and current COA access when data exists.
3. The customer can navigate the main site without obvious dead ends.
4. Product/category imagery is mapped correctly enough for a credible storefront.
5. No launch-critical console, PHP, or template failure is left unresolved.
6. Remaining issues, if any, are logged clearly and are not blockers for going live today.

## Priority Order If Time Gets Tight

1. Fatal/runtime stability
2. Purchase-path and product-page trust
3. Lab-results trust flow
4. Navigation and key public pages
5. Mobile correctness
6. Visual polish on supporting pages

## Delivery Style

Codex should work in sequential passes:

1. Diagnose
2. Fix
3. Review skeptically
4. Fix non-green items
5. Repeat

At the end of each pass, Codex should decide whether the result is green enough to move on or whether it still contains launch blockers.
