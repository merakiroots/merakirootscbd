# Browser Debugging

Use this reference when Playground needs interactive browser inspection.

## Local dev server defaults

- Main local Playground website: `http://127.0.0.1:5400/website-server/`
- Expect a parent shell and nested WordPress iframe(s)

## Debugging workflow

1. Open the target page
2. Confirm the iframe structure
3. Inspect console errors
4. Inspect non-static network requests
5. Navigate within WordPress to the page under test
6. Capture a screenshot once the relevant state is visible

## Practical checks

- Pending requests with no status usually mean the request is still hanging
- Failed requests often indicate a Playground-side timeout or runtime issue
- Admin submenu interactions may require hovering the parent menu first

## Good targets to verify

- `/wp-admin/`
- `/wp-admin/plugins.php`
- `/wp-admin/themes.php`
- `/cart/`
- `/checkout/`

