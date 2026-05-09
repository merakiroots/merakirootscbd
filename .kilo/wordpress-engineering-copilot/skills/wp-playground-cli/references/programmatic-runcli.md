# Programmatic runCLI

Use `runCLI()` when the user needs automation, integration tests, or a reproducible JavaScript-driven Playground workflow.

## Minimal server example

```ts
import { runCLI } from '@wp-playground/cli';

const cli = await runCLI({
  command: 'server',
  php: '8.3',
  wp: 'latest',
  login: true,
});
```

## Blueprint example

```ts
import { runCLI } from '@wp-playground/cli';

const cli = await runCLI({
  command: 'server',
  blueprint: {
    landingPage: '/wp-admin/',
    steps: [
      {
        step: 'setSiteOptions',
        options: {
          blogname: 'Playground Test Site',
        },
      },
    ],
  },
});
```

## Mount example

```ts
import { runCLI } from '@wp-playground/cli';

const cli = await runCLI({
  command: 'server',
  mount: [
    {
      hostPath: './my-plugin',
      vfsPath: '/wordpress/wp-content/plugins/my-plugin',
    },
  ],
});
```

## Testing guidance

- Prefer `runCLI()` for integration and E2E harness setup
- Dispose of the server cleanly after tests
- Be explicit about versions and mounts
- Seed only the required WordPress state

## WooCommerce matrix guidance

Use `runCLI()` when the same checkout rule must be tested across multiple states.

- Keep one shared helper for booting WooCommerce with the plugin mounted
- Vary one dimension per test suite when possible:
  - destination
  - shipping method
  - tax mode
  - guest vs logged-in customer
  - payment gateway availability
  - pickup-point selection state
- Prefer separate Blueprint fixtures over one enormous all-scenarios Blueprint
- Use one suite for Checkout Block behavior and one for classic checkout adapters when both are supported
