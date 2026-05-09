# Export Notes

This bundle is intentionally **model-agnostic**.

## Why it is not platform-specific
- The instructions, starter prompts, tool summary, and skill inventory can be adapted to Codex Desktop, Claude, Gemini, or other agent-style environments.
- The core logic of the agent is preserved here as plain markdown rather than being tied to one runtime.

## What you will still need to adapt manually
- replace builder-native entity tags with plain names or local equivalents
- reconnect GitHub and Shopify in the destination environment
- decide how each attached skill should become a reusable module, local prompt, or sub-agent in the destination environment
- copy over any attached files you still want available outside this editor

## Suggested migration order
1. Bring over `system-instructions.md`
2. Recreate tool access from `tools-and-runtime.md`
3. Add the supporting files you still need
4. Rebuild the most important skills first, starting with the kickoff, onboarding, review, and repo-grounding workflows
5. Test the new environment with one starter prompt at a time
