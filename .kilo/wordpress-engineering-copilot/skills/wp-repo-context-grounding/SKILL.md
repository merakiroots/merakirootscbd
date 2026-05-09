---
name: wp-repo-context-grounding
description: Use when the user wants repo-specific WordPress, WooCommerce, theme, plugin, migration, debugging, or code-review help and the answer will be materially better if the agent first inspects the target codebase, infers architecture, and saves stable project context for future runs.
---

# WP Repo Grounding

Use this skill to ground WordPress engineering help in the actual repository before giving implementation advice, review findings, or migration guidance.

Use it when the request depends on repo facts such as plugin structure, theme architecture, WooCommerce customization points, coding conventions, monorepo layout, or existing implementation patterns.

Do not use it for one-off generic WordPress questions that can be answered well without repository context.

## Request Shapes

Use this skill for requests like:

- "Review this plugin architecture and tell me where this feature should live."
- "Look through the repo and explain how checkout customizations are wired."
- "Before suggesting a fix, inspect the codebase and summarize the relevant WordPress structure."

Success means you leave the agent with enough grounded repo context to give specific, project-aware help instead of generic WordPress advice.

## Workflow

1. Identify the target repo, branch, and scope.
   - First prefer the repo or project already established in {{label:Memory,id:file_persistence,type:file_persistence}}.
   - If the current request clearly names a repo, plugin, theme, or package, use that.
   - If no grounded repo target is available and the task cannot be completed safely without one, ask one narrow question for the repo or package to inspect.

2. Inspect the repo with {{label:GitHub,id:connector_76869538009648d5b282a4bb21c3d157,type:app}}.
   - Read only the files and directories needed to understand the relevant architecture.
   - Prefer top-level manifests, bootstrap files, plugin headers, theme files, build config, package manifests, composer files, CI config, and the exact feature area the user asked about.
   - Do not browse broadly just because the repo is large.

3. Classify the WordPress shape of the codebase.
   - Determine whether the relevant area is a plugin, theme, WooCommerce extension, mu-plugin, monorepo package, shared library, or migration/supporting service.
   - Note whether the project uses classic theme patterns, block-theme patterns, custom tables, REST/AJAX handlers, WP-CLI commands, JavaScript build tooling, or WooCommerce HPOS-sensitive code.

4. Extract durable project context.
   - Capture stable facts that will improve future runs, such as repository purpose, important package boundaries, coding conventions, recurring architectural patterns, key plugin/theme entry points, and known integration surfaces.
   - Save only stable, reusable context to {{label:Memory,id:file_persistence,type:file_persistence}}.
   - Do not save secrets, temporary debugging artifacts, speculative conclusions, or one-off task notes.

5. Turn repo inspection into actionable help.
   - Tie recommendations to the actual code layout and existing patterns you observed.
   - When the repo already has a strong pattern, prefer extending it over inventing a new architecture.
   - When the current structure is risky, say so plainly and explain the smallest safe correction.

## Output Contract

When this skill is used, structure the response around these sections when helpful:

1. **Repo context**
   - The relevant package, plugin, theme, or module you inspected
   - The important entry points, directories, and integration surfaces

2. **What this implies for the task**
   - The existing project pattern that should probably be followed
   - Any architecture, security, WooCommerce, or build constraints that materially affect the answer

3. **Recommended next move**
   - The safest implementation or review path based on what you found

If the user asked for review findings, prioritize concrete findings over a broad repo tour.

If the user asked for implementation help, convert the repo context into a specific implementation path, not just a summary.

## Guardrails

- Do not invent repository facts.
- Distinguish confirmed repo evidence from inference.
- Prefer small, targeted inspection over exhaustive crawling.
- If the repo context is still incomplete, state the exact missing piece instead of pretending the repo supports a conclusion it does not.
- Do not let repo-grounding delay straightforward work once the key architectural facts are known.
