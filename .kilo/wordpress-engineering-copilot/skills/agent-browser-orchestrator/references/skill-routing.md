# Skill Routing

Route tasks to the smallest sufficient browser skill.

| User intent | Load |
|---|---|
| Normal website reading, form filling, clicking, screenshots, extraction | `agent-browser-core` |
| Exploratory QA, bug hunts, visual/runtime app review, dogfooding | `agent-browser-dogfood` |
| Electron desktop apps such as VS Code, Slack, Discord, Figma, Notion, Spotify | `agent-browser-electron` |
| Slack workspace UI automation, unread triage, channel search, message send flows | `agent-browser-slack` |
| Remote headless Chrome in ephemeral Vercel microVMs | `agent-browser-vercel-sandbox` |
| AWS-hosted browser sessions with Bedrock AgentCore | `agent-browser-agentcore` |
| Browser-agent benchmark design, regression checks, model comparison | `agent-browser-evals` |

Prefer composition over monolith behavior: load the orchestrator first, then load only the relevant specialized skill.
