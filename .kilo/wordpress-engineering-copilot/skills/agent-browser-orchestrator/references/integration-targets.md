# Integration Targets

## ChatGPT and Codex

Use this pack as a reusable browser-automation knowledge layer. For coding tasks, insert the `integrations/chatgpt-codex/AGENTS.md` content into the target repository's `AGENTS.md` or project instructions.

## OpenAI Agents SDK

Use `integrations/openai-agents-sdk/agent_browser_agents.py` as a starter definition for routing between browser automation, QA, remote browser, and eval agents.

## VS Code

Use `integrations/vscode/.vscode/tasks.json` for local smoke checks and doctor commands.

## JetBrains

Use `integrations/jetbrains/agent-browser-agent-profile.md` as the local-agent profile/instructions page.

## Local/open-source model hosts

Use the model-agnostic subagent specs under `subagents/model-agnostic/`. Each spec describes role, tools, inputs, outputs, stop conditions, and verification requirements without depending on a specific model vendor.
