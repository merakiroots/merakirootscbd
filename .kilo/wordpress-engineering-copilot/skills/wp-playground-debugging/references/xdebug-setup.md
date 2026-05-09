# Xdebug Setup

Use this reference to enable and use Xdebug in Playground.

## Quick start

```bash
npx @wp-playground/cli@latest server --xdebug
```

## With auto-mount

```bash
npx @wp-playground/cli@latest server --xdebug --auto-mount
```

## IDE-oriented flow

- Use the IDE integration flag when appropriate
- Confirm the project is mounted so path mappings make sense
- Set breakpoints only after verifying the mounted code path

## DevTools-oriented flow

- Use the experimental DevTools option when browser-side debugging is the best fit
- Confirm the DevTools connection URL appears
- Verify that the target WordPress request actually passes through the mounted code

## When to use this skill

- Breakpoint-driven plugin debugging
- Theme template or hook debugging
- WooCommerce checkout flow debugging

