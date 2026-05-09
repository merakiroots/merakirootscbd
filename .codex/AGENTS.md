# Meraki Roots Codex Layer

This supplements the root `AGENTS.md` with Codex-specific configuration for
the Meraki Roots CBD WordPress/WooCommerce workspace.

## Authority

The root `AGENTS.md` remains the project authority for architecture, coding
standards, Docker/WP-CLI commands, launch boundaries, and verification. This
file only describes durable Codex helpers: project-local MCP defaults, the
read-only role agents, and the local Codex plugin manifest.

## Project-Local Agents

Agents are configured in `.codex/config.toml` and implemented in
`.codex/agents/`.

- `explorer` traces real repo behavior in read-only mode. Start with
  `AGENTS.md`, `docs/`, `wp-content/plugins/meraki-commerce-core/`,
  `wp-content/themes/meraki-block-theme/`, and `scripts/` before touching
  WordPress core, `vendor/`, or `node_modules/`.
- `reviewer` reviews changes for correctness, WordPress security, launch
  regressions, and missing tests. Findings should cite concrete files and
  verification gaps.
- `docs_researcher` verifies framework/API claims against primary docs or
  local source files before implementation decisions depend on them.

## MCP Servers

Treat `.codex/config.toml` as the project baseline. It keeps a small pinned
MCP set: GitHub, Context7, Exa, Memory, Playwright, Sequential Thinking, and
the local WordPress MCP adapter.
Credentials must come from the launching environment or user-level config, not
from this repository.

The root `.mcp.json` mirrors the same MCP set for the local Codex plugin.
`wordpress-local` uses `wp --path=D:/merakirootscbd2 mcp-adapter serve ...`;
the adapter must be installed in the WordPress environment before that server
can start.

## Local Plugin

`.codex-plugin/plugin.json` defines a project-local plugin named
`meraki-codex-ops`. It intentionally describes only what this checkout now
ships: durable MCP defaults and Meraki-specific workflow guidance. It does not
claim to bundle the full ECC skill library.

Validate these assets with:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/validate-codex-assets.ps1
```

## External Action Boundaries

Treat networked tools as read-only by default. Search, inspect, and draft
freely within the requested scope, but require explicit user approval before
posting, publishing, pushing, merging, opening paid jobs, dispatching remote
agents, changing third-party resources, or modifying credentials.

When approval is ambiguous, produce a local plan or draft artifact instead of
taking the external action. Preserve user config and private state unless the
user specifically asks for a scoped change.

## WordPress Scope Notes

Prefer launch-owned surfaces before broad scans: the custom plugin, custom
theme, bootstrap scripts, launch docs, and import/media assets. Avoid editing
WordPress core files unless the user explicitly asks or a runtime trace proves
the issue is in core.
