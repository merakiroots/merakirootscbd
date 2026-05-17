# QA Checklist

## Launch Blockers

These must be green before relaunch:

- homepage loads with no fatal/template failure
- shop archive loads
- category pages load
- single-product pages load
- cart loads
- checkout shows either a live approved payment gateway or the intentional payment-provider review notice with order submission disabled
- mobile menu works
- key navigation links resolve
- lab-results page loads and shows usable output
- no obvious broken images on primary surfaces
- no hardcoded calls to missing helper functions

## Product Trust Checks

- product pages show ingredients when product data exists
- product pages show suggested use when product data exists
- product pages show warnings when product data exists
- product pages expose a current COA link when a current COA exists
- COA link opens the expected file

## Content Checks

- homepage copy does not look like filler
- partner page is usable
- contact page is usable
- legal/trust links in footer are present

## Responsive Checks

- homepage works on mobile
- product page works on mobile
- navigation works on mobile
- buttons are tappable
- no obvious text overlap

## Data Checks

- representative products have the right primary images
- category pages have expected visual identity where mapped
- redirects import or resolve correctly for key old URLs

## Technical Checks

- plugin activates without fatal errors
- theme loads without missing-template issues
- `scripts/wp-cli/meraki-audit-launch.php` passes
- no critical PHP errors in logs
- no obvious JS errors on launch-critical pages

## Final Decision Rule

If an issue affects trust, purchase flow, or basic rendering, it is a blocker.

For provider-review-only publication, a disabled payment gateway is acceptable only when the payment-provider review notice is visible and order submission is disabled. For a customer launch, the approved payment gateway must be enabled and tested.

If an issue is cosmetic and does not degrade trust or navigation, log it and move on if time is tight.
