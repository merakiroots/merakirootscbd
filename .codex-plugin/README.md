# .codex-plugin — Meraki Codex Ops

This directory contains the Codex plugin manifest for the Meraki Roots CBD
workspace.

## Structure

```
.codex-plugin/
└── plugin.json   — Codex plugin manifest
.codex/
├── config.toml   — project-local Codex config and agent registry
└── agents/       — read-only role definitions
.mcp.json         — MCP server configuration at plugin root
```

## What This Provides

- Pinned project-local MCP defaults: GitHub, Context7, Exa, Memory,
  Playwright, Sequential Thinking, and local WordPress MCP adapter
- Repo-specific role guidance for explorer, reviewer, and docs researcher
- A truthful local plugin manifest that does not claim to bundle the full ECC
  skill library

## Installation

Codex plugin support is preview-era and may vary by CLI build. For local
development, run from the repository root so `./.mcp.json` resolves correctly.

```bash
codex plugin install ./
```

## MCP Servers Included

| Server | Purpose |
|---|---|
| `github` | GitHub API access |
| `context7` | Live documentation lookup |
| `exa` | Neural web search |
| `memory` | Persistent memory across sessions |
| `playwright` | Browser automation & E2E testing |
| `sequential-thinking` | Step-by-step reasoning |
| `wordpress-local` | Local WP-CLI stdio MCP adapter for this checkout |

## Notes

- MCP server credentials are inherited from the launching environment or
  user-level Codex config. Do not commit tokens here.
- This manifest does not override `~/.codex/config.toml`; it provides
  repo-local defaults and plugin metadata.
- Validate the setup with:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/validate-codex-assets.ps1
```
