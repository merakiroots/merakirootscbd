# Playground Recipes

Use these as starting patterns for WordPress, WooCommerce, plugin, and theme work.

## Plugin preview from a local repo

- Use Playground CLI `server --auto-mount` from the plugin root
- Add a Blueprint `activatePlugin` step if auto-activation is not enough
- Land on `/wp-admin/plugins.php` or the plugin settings page

## Theme preview from a local repo

- Use `server --auto-mount` from the theme root
- Activate the theme explicitly when needed
- Use `setSiteOptions` and seeded content so the front page shows real layout states

## WooCommerce checkout architecture demo

- Treat WooCommerce Checkout Block as the first scenario and classic checkout as a second surface on the same business rules
- Install WooCommerce in the Blueprint and confirm required cart and checkout pages exist
- Seed only the store state needed for the architecture question under review
- Pick one checkout variable at a time when possible:
  - cart contents
  - shipping destination
  - shipping method eligibility
  - tax mode and taxable address
  - guest vs logged-in customer
  - payment gateway availability
  - pickup-point or custom checkout data
- Land on `/cart/`, `/checkout/`, or the exact admin/settings screen that corresponds to the decision being reviewed
- Reproduce the same business rule on both checkout surfaces when the project supports both blocks and classic
- Keep shipping, taxes, coupons, customer state, and payment state intentional rather than accidental

## WooCommerce checkout state matrix

Use these as distinct Playground recipes instead of one oversized demo.

### Cart-state recipe

- Seed one simple product for the minimal path
- Add one mixed cart recipe when eligibility depends on product type, shipping class, vendor, or pickup compatibility
- Decide whether the cart should start empty, single-item, mixed, virtual-only, or shippable
- Land on `/cart/` when the architectural question is about item eligibility, fees, notices, or transition into checkout

### Checkout-state recipe

- Start from a known cart state
- Decide whether the flow is guest checkout, logged-in checkout, or account creation during checkout
- Seed only the fields needed to hit validation or availability logic
- Land on `/checkout/`

### Shipping-state recipe

- Choose one destination country, state, and postcode pattern intentionally
- Configure one eligible shipping case and one ineligible case
- If provider or pickup logic depends on shipping rate selection, predefine which methods should appear
- Separate parcel, freight, and local/pickup scenarios into different repros when the rules differ materially

### Tax-state recipe

- Decide whether tax is enabled, inclusive, or exclusive
- Use a destination that triggers the relevant tax behavior
- Keep the product catalog small so totals are easy to reason about
- Use a dedicated repro when tax behavior changes shipping, payment, or pickup eligibility

### Payment-state recipe

- Choose one baseline gateway that should always be available
- Add one restricted gateway case when gateway availability depends on shipping choice, order total, destination, or pickup state
- Keep success, validation-failure, and gateway-unavailable scenarios separate

### Pickup-point or custom checkout extension recipe

- Seed one provider and one eligible pickup location for the smallest happy path
- Add one failure-path repro where the pickup point becomes invalid before submission
- Re-validate the selection at checkout submission, not only during UI selection
- Persist only the order-facing snapshot needed for support and fulfillment

## PR preview or shareable repro

- Prefer a Blueprint that pulls plugin or theme code from a built ZIP or `git:directory`
- Use a focused landing page
- Keep it small enough that another developer can understand it without local context
