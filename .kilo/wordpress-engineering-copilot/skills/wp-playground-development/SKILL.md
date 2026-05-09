---
name: wp-playground-development
description: WordPress Playground routing, review, and implementation guidance. Use when reviewing Blueprints, Playground demos, playground.json setup flows, `@wp-playground/cli` usage, embedded Playground demos, reproducible bug repros, or when user mentions "WordPress Playground", "Blueprint", "playground.json", "run-blueprint", "build-snapshot", "embed Playground", "zero-setup repro", or "Playground preview". Helps choose between browser, CLI, and debugging workflows while keeping demos deterministic and developer-friendly.
---

# WordPress Playground Development

Use this as the routing and review layer for WordPress Playground work.

## Primary jobs

- Review a Blueprint, embed, or Playground workflow for reproducibility
- Choose the right specialist follow-up skill
- Draft Playground-based demo or repro plans for plugins, themes, or WooCommerce work

## Route to the right specialist

- Use `wordpress-playground-browser` when the work is about browser behavior, iframe inspection, dev servers, embedded demos, or UI debugging.
- Use `wp-playground-cli` when the work is about `start`, `server`, `run-blueprint`, `build-snapshot`, mounts, persistence, or programmatic `runCLI()`.
- Use `wp-playground-debugging` when the work is about Xdebug, DevTools, IDE integration, or failures that require instrumentation.

## Review workflow

1. Identify the Playground entrypoint.
   - Blueprint JSON
   - CLI command
   - Embedded iframe or `blueprint-url`
   - Programmatic `runCLI()` script
2. Check reproducibility first.
   - Explicit WordPress and PHP versions when compatibility matters
   - Ordered, self-contained setup
   - No hidden manual steps
3. Check the landing target.
   - The user should arrive at the relevant admin screen, front-end page, or checkout state
4. Check setup weight.
   - Prefer the smallest Blueprint or mount setup that demonstrates the issue
5. Suggest a specialist skill if the task moves from review into execution

## Findings rubric

- `CRITICAL`: Non-reproducible setup, hidden steps, broken ordering, or an unusable landing state
- `WARNING`: Implicit versions, brittle mounts, weak persistence assumptions, or an overbuilt repro
- `INFO`: Could simplify steps, improve landing page, or separate demo from test data

## References

- `references/blueprint-patterns.md` for reproducible Blueprint structure
- `references/embedding-and-repros.md` for demos, `blueprint-url`, and embedded Playground use
- `references/playground-recipes.md` for plugin, theme, and WooCommerce-oriented setup recipes
- `references/sample-playground-blueprint.json` for a minimal reusable fixture

## Workflows

- `workflows/wp-playground.md` for a quick scan
- `workflows/wp-playground-review.md` for a deeper review pass

