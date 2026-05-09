---
name: wp-project-onboarding
description: Collect missing repo, codebase, or store defaults for WordPress and WooCommerce work only when the current task needs that context, then persist it in Memory and resume the user's request.
---

# WordPress Project Onboarding

## When to use this skill

Use this skill only when the current request needs missing user-specific project defaults listed in `references/onboarding-contract.yaml`.

Examples:

- the user wants code review, architecture help, or implementation guidance tied to a recurring repo, plugin, theme, or codebase, but that default project context is missing
- the user wants WooCommerce implementation, migration, or store review help that depends on stable store conventions or business rules that should persist across future runs
- the current request clearly refers to "my repo", "our plugin", "this store", or a recurring project context that would otherwise need to be re-explained in later runs

Do not use this skill when the current request can be completed from the current message, existing Memory state, attached files, and available repo or store context alone.

## Memory state

Use {{label:Memory,id:file_persistence,type:file_persistence}} as the backing store for the onboarding keys in `references/onboarding-contract.yaml`.

Persist those keys for the current runtime end user in a single compact state file named `wp-project-defaults.yaml`, and read that file before asking the user to restate defaults.

Do not use Memory as a general transcript or scratchpad. Store only durable defaults that the contract says should be reused across future runs for that same runtime end user.

## Supporting Files

- `references/onboarding-contract.yaml` — the trigger conditions, required state, optional state, completion rules, and skip conditions for project onboarding

## Preflight

1. Read `references/onboarding-contract.yaml`.
2. Read `wp-project-defaults.yaml` from Memory if it exists.
3. Compare the current request and available persisted state against the contract's `trigger_when` and `required_state` fields.
4. If no trigger matches, skip this skill and continue the user's original request.
5. If the current request already provides any contract-defined defaults that are missing from Memory, persist those values to `wp-project-defaults.yaml` using the contract's `store_as` keys.
6. Re-check `required_state` after persisting request-supplied values. If all required state is now present, skip user questions and continue the user's original request.
7. If required state is still missing, ask the current runtime end user for only the missing required fields.

## Onboarding workflow

1. Ask one concise question at a time.
2. Treat `project_scope` as the main required field only when the current task depends on recurring project context that is not already available.
3. When the current task is repo-centric, collect the smallest durable identifier that will help future runs, such as the repository name, plugin or theme name, or codebase label the user actually uses.
4. When the current task is store-centric, collect the smallest durable identifier that will help future runs, such as the store name, store role, or business context label the user actually uses.
5. Use safe defaults for optional preferences when the user does not provide them.
6. Persist collected answers to `wp-project-defaults.yaml` using the contract's `store_as` keys.
7. After `completion_requires` is satisfied, stop onboarding and resume the user's original request immediately.

## Output

Do not summarize onboarding for its own sake. Confirm only the defaults that matter for the current task, then continue the original request.
