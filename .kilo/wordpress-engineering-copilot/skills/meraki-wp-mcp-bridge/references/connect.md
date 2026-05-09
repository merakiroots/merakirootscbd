# Connection Guide

## Local stdio agents

Use stdio for agents running on the same machine as the checkout.

```bash
npm install
npm run mcp:stdio
```

Codex config shape:

```toml
[mcp_servers.merakiRootsWpDev]
command = "npm"
args = ["run", "mcp:stdio", "--silent"]
cwd = "D:\\wp-dev"
startup_timeout_sec = 20
tool_timeout_sec = 120
```

VS Code Agent mode shape:

```json
{
  "servers": {
    "merakiRootsWpDev": {
      "type": "stdio",
      "command": "npm",
      "args": ["run", "mcp:stdio", "--silent"],
      "cwd": "${workspaceFolder}"
    }
  }
}
```

Cursor shape:

```json
{
  "mcpServers": {
    "merakiRootsWpDev": {
      "command": "npm",
      "args": ["run", "mcp:stdio", "--silent"],
      "cwd": "."
    }
  }
}
```

## Browser-hosted agents and ChatGPT Projects

Browser-hosted agents cannot reach `127.0.0.1` on the user's machine. Start HTTP locally and expose it through a trusted HTTPS tunnel.

```bash
npm install
npm run mcp:http
curl http://127.0.0.1:8787/
```

The ChatGPT custom app / MCP URL must end in `/mcp`:

```text
https://your-trusted-tunnel.example/mcp
```

For private development, the server advertises `noauth` tool metadata. For durable/shared use, require OAuth or an authenticated gateway before exposing private workspace data.

## Important environment variables

| Variable | Purpose |
| --- | --- |
| `MCP_HOST` | HTTP bind address. Use `127.0.0.1` unless a tunnel/container needs ingress. |
| `MCP_PORT` | HTTP port, default `8787`. |
| `MCP_ALLOWED_ORIGINS` | Comma-separated CORS allowlist. |
| `MCP_HTTP_BEARER_TOKEN` | Optional bearer token for clients that can send Authorization headers. |
| `MCP_ALLOW_MUTATIONS` | Enables guarded write-capable actions. Keep false unless requested. |
| `WP_DEV_COMPOSE_ROOT` | Override Compose root, usually `wp-docker-template`. |
| `WP_DEV_PLUGIN_PATH` | Override plugin path, usually `wp-docker-template/wp-content/plugins/my-plugin`. |

## Validation sequence

1. Health: `curl http://127.0.0.1:8787/`
2. Client list: use the MCP client's “list tools” command or tools picker.
3. Prompt: `Use the Meraki Roots MCP to run workspace_status and summarize the result.`
4. Safety check: confirm `mutationsEnabled` is false unless the user explicitly needs writes.
