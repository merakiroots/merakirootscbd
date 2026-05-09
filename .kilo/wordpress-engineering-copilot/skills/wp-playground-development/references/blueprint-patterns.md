# Blueprint Patterns

Use this reference for Blueprint review and authoring.

## Good default shape

```json
{
  "$schema": "https://playground.wordpress.net/blueprint-schema.json",
  "landingPage": "/wp-admin/",
  "preferredVersions": {
    "php": "8.3",
    "wp": "latest"
  },
  "steps": [
    {
      "step": "login",
      "username": "admin",
      "password": "password"
    }
  ]
}
```

## Review rules

- Make version-sensitive environments explicit
- Use the smallest possible step list
- Seed only the content needed for the demo or repro
- Prefer `installPlugin`, `installTheme`, `setSiteOptions`, `wp-cli`, and `writeFile` only when they map directly to the need
- Set a meaningful `landingPage`

## Warning signs

- Manual setup after load
- Heavy file writes for a simple plugin or theme repro
- Local-only assumptions presented like shareable demos
- Missing login for admin pages

