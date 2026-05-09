# Embedding and Repros

Use this reference for docs pages, issue repros, share links, or embedded demos.

## Good repro pattern

1. State what is broken
2. Provide the Blueprint or `blueprint-url`
3. Land on the exact screen that matters
4. Seed only the required content and settings

## Embedded demo checks

- The iframe or link opens the relevant state, not a generic site
- The repro does not rely on side instructions like "click around until you see it"
- Local files are clearly marked as local-only
- Experimental features are called out

## Lightweight launch formats

- `blueprint-url` for portable demos
- Blueprint JSON for repo-backed examples
- Local CLI plus mounts for development-only repros

