---
name: wordpress-playground-browser
description: Browser workbench for WordPress Playground. Use when debugging the Playground website, embedded Playground demos, iframe behavior, UI issues inside Playground, browser console or network failures, or when a task needs a WordPress instance inspected interactively in the browser. Helps with Playwright-style debugging, nested iframe navigation, and Playground dev-server workflows.
---

# WordPress Playground Browser

Use this skill when the job is to inspect or debug WordPress Playground in a real browser workflow.

## Use when

- A Playground site or embed behaves incorrectly in the browser
- You need to inspect the WordPress UI running inside Playground
- You need console, network, or screenshot verification
- You need to validate a plugin, theme, or WooCommerce flow inside a Playground instance

## Core browser model

Playground often has nested layers:

1. Parent app or chrome
2. Outer Playground iframe or remote shell
3. Inner WordPress iframe

Assume iframe boundaries matter for clicks, selectors, screenshots, and debugging.

## Workflow

1. Start or identify the right Playground URL
2. Confirm whether the target is:
   - the main Playground website
   - an embedded demo
   - a local CLI-backed instance
3. Inspect the visible page structure and identify iframe boundaries
4. Check browser console errors and failed network requests
5. Navigate to the exact WordPress page under test
6. Capture screenshots or snapshots for verification

## Browser checks

- Is the correct iframe loaded?
- Does the landing page match the intended workflow?
- Are there failed network requests or hanging requests?
- Are admin menus, modals, and checkout flows actually interactable?
- Is the problem in the parent app, the Playground shell, or the inner WordPress app?

## References

- `references/browser-debugging.md` for dev-server and iframe debugging guidance
- `references/javascript-api-and-embeds.md` for embedding and browser API usage
- `references/browser-recipes.md` for plugin, theme, and WooCommerce browser validation patterns

