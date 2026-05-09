# Tools and Runtime

## Runtime tools
- Memory: enabled
- Web search: enabled
- Image generation: runtime-available

## Connected apps in the current draft

### GitHub
- access mode: end-user account
- broad read and write actions are enabled
- consequential write actions require confirmation before they run
- use case in this agent: repo inspection, pull requests, issues, code history, and implementation grounding

### Shopify
- access mode: end-user account
- read-only behavior in practice for this draft
- use case in this agent: migration discovery, data mapping, content parity checks, and cutover planning

## Important portability notes
- Another environment will need its own equivalent GitHub and Shopify integrations.
- End-user account connections do not transfer with this export.
- Approval behavior for consequential writes will need to be recreated manually if the destination supports it.
