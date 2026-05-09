# Debugging Playbooks

## Plugin debugging

- Mount the plugin
- Enable Xdebug
- Land on the plugin page that triggers the code path

## Theme debugging

- Mount or activate the theme
- Seed the content needed to hit the target template or block rendering path

## WooCommerce debugging

- Build a minimal store state
- Seed only the cart, checkout, shipping, tax, payment, or pickup state needed for the target rule
- Debug only the path under test instead of a whole catalog setup

### Cart-path debugging

- Break on cart validation, fee calculation, and custom cart item data paths
- Keep the cart small so hook order and notices stay readable

### Checkout-path debugging

- Break on checkout field hydration, validation, order creation, and extension data persistence
- Reproduce the same rule on Checkout Block and classic only after the minimal path works

### Shipping-path debugging

- Break where shipping packages, eligibility rules, or pickup providers are resolved
- Use one destination per repro so rate selection is deterministic

### Tax-path debugging

- Break where taxable address and totals are finalized
- Keep prices simple enough that a wrong branch is obvious

### Payment-path debugging

- Break where gateway availability is filtered and again where final submission is validated
- Separate “gateway hidden” from “gateway shown but rejected” scenarios

### Pickup-point extension debugging

- Break where the selected point is stored in the checkout request
- Break again where eligibility is re-validated before order creation
- Confirm only the final order-facing snapshot is persisted
