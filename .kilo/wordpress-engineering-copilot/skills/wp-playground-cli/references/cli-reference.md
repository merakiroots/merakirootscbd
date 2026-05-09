# CLI Reference

Use this reference when selecting Playground CLI commands and flags.

## Command selection

- `start`: easiest local default, auto-login, opens browser, persists the site
- `server`: explicit control for advanced local work, CI, or custom mounts
- `run-blueprint`: execute a Blueprint without serving a site
- `build-snapshot`: turn a Blueprint-defined site into a reusable ZIP snapshot

## Versions

- Use `--wp=<version>` and `--php=<version>` when compatibility matters
- Treat defaults as convenient, not sufficient for version-sensitive debugging

## Mount rules

- Use `--auto-mount` in common plugin or theme roots
- Use `--mount` for explicit mapping
- Use `--mount-before-install` when the mounted files must affect installation or boot

## Persistence rules

- `start` is the persistent local workflow
- `server` is more explicit and may use temp-backed state depending on mounts
- If the database location matters, document it
- If repeatability matters more than persistence, prefer deterministic Blueprint execution

## Good examples

```bash
npx @wp-playground/cli@latest start
npx @wp-playground/cli@latest server --blueprint=./playground.json --login
npx @wp-playground/cli@latest run-blueprint ./playground.json
npx @wp-playground/cli@latest build-snapshot --blueprint=./playground.json --outfile=site.zip
```

