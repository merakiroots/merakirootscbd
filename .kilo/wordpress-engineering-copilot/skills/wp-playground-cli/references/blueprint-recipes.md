# Blueprint Recipes

## Plugin recipe

- Mount or install the plugin
- Activate it
- Land on its settings or workflow page

## Theme recipe

- Install or mount the theme
- Activate it
- Seed content and options so the front page is meaningful

## WooCommerce recipe

- Install WooCommerce and confirm the standard store pages exist
- Treat Checkout Block as the default target, then mirror the same rule on classic checkout if the project supports both
- Keep each Blueprint focused on one checkout question

### Minimal cart recipe

- Seed one simple shippable product
- Add the product to cart through a deterministic step or setup script
- Land on `/cart/`
- Use this when testing fees, notices, cart validation, or transition rules

### Minimal checkout recipe

- Start from the minimal cart recipe
- Log in only if the rule depends on account state
- Land on `/checkout/`
- Use this when testing checkout fields, validation, payment availability, or custom extension data

### Shipping recipe

- Set one explicit destination
- Configure only the shipping methods needed for the scenario
- Build separate Blueprints for:
  - standard parcel eligible
  - freight-only eligible
  - local pickup or pickup-point eligible
  - no eligible shipping result

### Tax recipe

- Set tax mode explicitly
- Use one product price simple enough to inspect totals quickly
- Use a destination that predictably changes tax behavior
- Keep tax-only repros separate when taxes are the architectural question

### Payment recipe

- Enable one baseline gateway for the happy path
- Build a separate Blueprint when gateway availability depends on shipping method, destination, pickup choice, or total
- Keep payment success and payment rejection scenarios separate

### Pickup-point or custom checkout extension recipe

- Mount or install the extension
- Seed one eligible provider and one eligible location
- Add a second Blueprint for an invalid or stale selection case
- Reproduce the same domain rule on both blocks and classic if both are supported

### Programmatic `runCLI()` rule

- Prefer `runCLI()` for automated matrix coverage across:
  - cart shape
  - shipping destination
  - shipping method
  - tax mode
  - customer state
  - payment state

## Review rule

When a recipe becomes large, ask whether a snapshot would be clearer than a long setup sequence.
