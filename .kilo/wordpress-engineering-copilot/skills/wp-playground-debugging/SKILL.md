---
name: wp-playground-debugging
description: WordPress Playground debugging guidance. Use when enabling Xdebug, connecting an IDE, using Playground DevTools workflows, debugging plugins or themes inside Playground, or diagnosing runtime failures that need instrumentation rather than ordinary Blueprint review. Helps with Xdebug startup, browser-assisted debugging, and practical troubleshooting handoffs.
---

# WordPress Playground Debugging

Use this skill when ordinary review is not enough and the task needs real debugging setup.

## Use when

- The user wants Xdebug in Playground
- The task needs IDE integration
- The task needs browser DevTools-assisted debugging
- A plugin, theme, or WooCommerce flow must be stepped through

## Workflow

1. Decide whether the target is:
   - standalone PHP debugging
   - full WordPress debugging
   - browser plus PHP debugging
2. Prefer `@wp-playground/cli` for WordPress-specific debugging
3. Enable Xdebug deliberately
4. Choose IDE or DevTools integration
5. Confirm the code under test is mounted and reachable in the running instance

## References

- `references/xdebug-setup.md` for CLI, IDE, and DevTools patterns
- `references/debugging-playbooks.md` for plugin, theme, and WooCommerce debugging recipes

