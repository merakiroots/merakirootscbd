# Browser Recipes

## Plugin UI verification

- Launch Playground with the plugin active
- Land on the plugin page or relevant admin screen
- Verify notices, settings forms, and save flows

## Theme preview verification

- Land on the front page and Site Editor or Appearance screen
- Use seeded content that exercises the theme, not empty defaults

## WooCommerce checkout verification

- Verify the target surface first:
  - Checkout Block
  - classic checkout
  - cart
  - mini-cart or cart drawer if relevant
- Seed at least one product and a known cart-ready state
- Land on the exact screen under test

### Cart checks

- Confirm line items, quantity controls, fees, notices, and transition to checkout
- Verify any pickup or eligibility messaging appears before checkout if that is part of the architecture

### Checkout checks

- Confirm required fields, extension UI, validation messages, and submission blocking behavior
- Verify guest and logged-in behavior separately when account state matters

### Shipping checks

- Confirm the intended methods appear for the seeded destination
- Verify changes in destination or cart contents update eligibility as expected
- For pickup-point flows, confirm location UI appears only when the related method is active

### Tax checks

- Confirm item totals, shipping totals, and tax lines match the intended setup
- Use simple seeded prices so visual verification is reliable

### Payment checks

- Confirm only the intended gateways are visible
- Verify payment UI updates when shipping method, totals, or pickup selection changes
- Keep happy-path and gateway-unavailable verification separate

### Submission checks

- Verify the final submit path still enforces server-side validation
- If the extension re-validates pickup or shipping eligibility at submission, confirm the visible failure state is understandable
