# Tool Map

## Read and inspect

| User asks | Use |
| --- | --- |
| “What state is the workspace in?” | `workspace_status` |
| “Find references to X” | `search_workspace` |
| “Show files under directory X” | `list_workspace_files` |
| “Read file X” | `read_workspace_file` |
| “Check the debug log” | `get_wp_debug_log` |
| “Lint this PHP file” | `lint_php_file` |

## WordPress and WooCommerce diagnostics

| User asks | Use |
| --- | --- |
| “List plugins/themes/users/options” | `wp_cli` with read-only args |
| “List WooCommerce products/system status” | `wp_cli` with `wc ...` read-only args |
| “Validate Composer” | `composer_command` with `validate` |
| “Show Composer packages” | `composer_command` with `show` |

## Docker diagnostics

| User asks | Use |
| --- | --- |
| “Are containers running?” | `docker_compose` with `ps` |
| “Show compose config” | `docker_compose` with `config` |
| “Show logs” | `docker_compose` with `logs` and optional service/tail |

## Quality checks

Use `run_quality_check` only when the user asks to test, lint, build, or validate. `plugin-npm-build` writes artifacts and should require explicit mutation intent.

## Blocked or dangerous requests

Do not attempt to bypass blocked reads for `.env`, keys, uploads, `.git`, `.vscode`, `node_modules`, or `vendor`. Explain the boundary and ask for a sanitized excerpt if needed.
