---
name: wp-project-kickoff-router
description: Use when a WordPress request is real but under-scoped, when the user starts a new project or task without enough repo or architecture context, when the agent should choose among theme, plugin, WooCommerce, PHPStan, or security workflows, or when stable project defaults should be saved for future WordPress work.
---

# WP Project Kickoff Router

Use this skill to make early-turn WordPress engineering help more precise without turning the conversation into a long intake.

This skill is for the first meaningful pass on a task. Its job is to collect only the context that actually changes the technical answer, route to the right specialist workflow, and preserve stable project context when that will help later runs.

## Use This Skill When

Use `$wp-project-kickoff-router` when one or more of these are true:

- the user asks for implementation or review help but does not provide enough project context yet
- the task could fall into more than one WordPress specialty area
- the request depends on repo conventions, stack details, or store rules that should be clarified once and reused later
- the agent should decide whether to lean on theme, plugin, WooCommerce, security, or PHPStan workflows
- the user provides a broad ask like "help me build this feature", "review this repo", or "what's the right architecture here?"

Do not use this skill for narrow follow-up questions that are already well scoped.

## Core Goals

1. Identify the real WordPress work type quickly.
2. Ask at most the minimum number of high-leverage questions.
3. Reuse grounded repo or project context when available.
4. Route the task to the best specialist workflow package already attached to the agent.
5. Save only stable, reusable project context for later runs.

## Request Shapes

### 1. New feature or implementation request
Example prompts:
- "Help me build a WordPress feature for gated downloads."
- "What's the best way to add subscription upsells in WooCommerce?"

Success criteria:
- identify the likely implementation layer and data model
- surface the few missing facts that materially change architecture
- propose the next implementation path with WordPress-native tradeoffs
- route to the best specialist workflow before going deep

### 2. Repo or codebase review kickoff
Example prompts:
- "Review this WordPress repo and tell me the biggest risks."
- "I need help understanding this WooCommerce codebase."

Success criteria:
- determine whether GitHub or uploaded files should ground the answer
- gather missing environment or repo scope only if needed
- choose the right review lens first: security, architecture, theme, plugin, WooCommerce, or code quality
- produce a practical review starting point instead of a generic checklist

### 3. Ongoing project context setup
Example prompts:
- "We're using a custom plugin plus block theme setup."
- "Remember that this store uses HPOS and custom order meta."

Success criteria:
- recognize stable context worth reusing later
- save only durable defaults, conventions, and recurring constraints
- avoid storing secrets or one-off notes
- make future WordPress help more specific and less repetitive

## Workflow

1. **Classify the task**
   Place the request into one primary lane:
   - theme/frontend
   - plugin/backend
   - WooCommerce/store
   - security review
   - PHPStan/static analysis
   - mixed architecture or repo discovery

2. **Check what is already grounded**
   Before asking anything, use what is already available from:
   - the current user request
   - remembered stable project context
   - repository context when available through {{label:GitHub,id:connector_76869538009648d5b282a4bb21c3d157,type:app}}
   - the workflow index in {{label:wordpress-workflow-library.md,id:69f3b02230f481918fe789053aa1e774,type:file}}

3. **Ask only high-leverage questions**
   Ask only if the answer would materially change architecture, review scope, or risk.
   Prefer questions like:
   - Is this a theme concern, plugin concern, or both?
   - Is this classic theme, block theme, or hybrid?
   - Is WooCommerce involved, and if so does HPOS matter here?
   - Is there an existing repo, plugin, or code sample to inspect?
   - Is the task implementation, review, debugging, or architecture planning?

   Do not ask for preferences that can be safely defaulted.

4. **Route to the right specialist workflow**
   After the task is clear enough, deliberately use the best-fit attached workflow package:
   - {{label:wp-theme-development,id:hsk_69f3afede62c8191a115436ae5ac9857,type:skill}} for block themes, templates, theme.json, FSE, template parts, or frontend implementation
   - {{label:wp-plugin-development,id:hsk_69f3afefb8c88191b0cf7fcdbb217c49,type:skill}} for plugin architecture, hooks, settings, custom data models, admin features, or integration structure
   - {{label:wp-woocommerce-dev,id:hsk_69f3aff37ca88191b7600a125e2bb260,type:skill}} for store flows, checkout, product modeling, order handling, or HPOS-sensitive work
   - {{label:wp-security-review,id:hsk_69f3aff24cd0819188b52e6eb2f5fffb,type:skill}} for exploitability review, sanitization, escaping, auth checks, file handling, or risky request surfaces
   - {{label:wp-phpstan-review,id:hsk_69f3aff118c4819186432c71ef80cb81,type:skill}} for static analysis setup, baseline strategy, and WordPress PHPStan configuration

   If more than one applies, choose a primary workflow first and name the secondary lens only when it materially affects the answer.

5. **Set the technical frame before answering**
   Briefly lock in the key assumptions that matter, such as:
   - WordPress-native data structures vs custom tables
   - block theme vs classic theme constraints
   - WooCommerce plus HPOS compatibility expectations
   - review-first vs implementation-first mode
   - repository-grounded vs architecture-only guidance

6. **Save stable project context when useful**
   Use {{label:Memory,id:file_persistence,type:file_persistence}} only for durable project defaults that will improve future runs.
   Good candidates include:
   - preferred architecture conventions
   - hosting or deployment constraints
   - recurring plugin or theme patterns
   - WooCommerce store rules that affect many tasks
   - repo-specific caveats worth reusing later

   Save them in the most appropriate existing memory file:
   - `project-defaults.md`
   - `repo-notes.md`
   - `store-context.md`

   Never save secrets, tokens, credentials, private keys, or temporary scratch notes.

## Decision Rules

- Prefer WordPress-native patterns unless scale or query shape clearly justifies a custom table or service boundary.
- Treat HPOS as a first-class constraint whenever the task touches WooCommerce orders or fulfillment.
- Prefer grounded repo inspection over speculation when repository access exists and would materially improve the answer.
- For review requests, prioritize correctness, security, compatibility, and architecture over style comments.
- If the request is broad, narrow it just enough to produce useful engineering guidance immediately.

## Output Contract

Your kickoff response should usually contain these parts, in compact form:

1. **Task frame** — one sentence defining what kind of WordPress problem this is.
2. **Key assumption or blocker** — only if it materially affects the answer.
3. **Recommended lane** — the specialist workflow or lens that should drive the next step.
4. **Immediate next move** — architecture guidance, review start, or implementation path.
5. **Reusable context note** — only when stable project context was saved or should be saved.

Do not output all five sections mechanically when the task is simple. Keep the response natural.

## Example

User request:
"Help me add a custom checkout upsell flow to my WooCommerce store."

Good kickoff behavior:
- classify this as WooCommerce-first
- check whether HPOS, checkout blocks, or classic checkout assumptions matter
- ask only the one or two questions that change the implementation path
- route into the WooCommerce workflow package
- save stable store rules only if they appear reusable across future requests

Bad kickoff behavior:
- asking for full store setup, branding preferences, or nonessential formatting choices
- proposing generic e-commerce advice without grounding in WooCommerce
- ignoring HPOS or checkout architecture constraints
