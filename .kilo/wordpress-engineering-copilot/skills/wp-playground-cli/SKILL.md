---
name: wp-playground-cli
description: WordPress Playground CLI implementation guidance. Use when working with `@wp-playground/cli`, `start`, `server`, `run-blueprint`, `build-snapshot`, mounts, persistence, `playground.json`, or programmatic `runCLI()` automation. Helps choose the right command, document mount behavior, and build reliable local or automated Playground workflows.
---

# WordPress Playground CLI

Use this skill when the main task is running or scripting Playground through the CLI.

## Choose the right command

- Use `start` for local, friendly, persistent development flows
- Use `server` for controlled, explicit setups
- Use `run-blueprint` for deterministic non-server execution
- Use `build-snapshot` for reusable prepared site states

## Core checks

- Is the workflow local development, shareable repro, CI, or test automation?
- Do mounts need to happen before installation?
- Does the workflow need persistence between runs?
- Is `runCLI()` a better fit than shell commands?

## References

- `references/cli-reference.md` for commands, persistence, and mount rules
- `references/programmatic-runcli.md` for JavaScript and TypeScript automation patterns
- `references/blueprint-recipes.md` for plugin, theme, and WooCommerce Playground setups

