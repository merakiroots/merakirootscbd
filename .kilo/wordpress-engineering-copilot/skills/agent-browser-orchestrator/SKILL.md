---
name: agent-browser-orchestrator
description: "use as the top-level agent-browser routing and integration skill for chatgpt, codex, openai agents sdk, vs code, jetbrains, and model-agnostic agents. trigger when a task involves browser automation, website reading or writing, webapp qa, form workflows, auth-state handling, screenshots, interactive testing, slack ui automation, electron apps, vercel sandbox, aws agentcore, or deciding which agent-browser subskill or subagent should run."
---

# Agent Browser Orchestrator

Coordinate browser automation, reading, writing, QA, and runtime testing workflows across ChatGPT, Codex, OpenAI Agents SDK agents, VS Code, JetBrains, and model-agnostic/local agents.

## Start here

1. Read `references/skill-routing.md` and choose the narrowest specialized skill.
2. Read `references/operating-guardrails.md` for safety, verification, and artifact expectations.
3. Use `references/integration-targets.md` when installing this pack into a repository, IDE, or agent runtime.
4. When shell access exists, run `scripts/agent_browser_doctor.sh` before relying on local `agent-browser` commands.
5. For a quick runtime proof, run `scripts/smoke_check.sh https://example.com` and inspect the generated screenshot and snapshot.

## Default execution policy

Prefer ChatGPT/Codex-native and OpenAI-compatible workflows. Keep instructions model-agnostic unless the user explicitly selects a model or runtime. Do not introduce editor ecosystems the user did not request. Local integration should target VS Code and JetBrains only.

## Output contract for browser tasks

Return a concise action log, final observed URL/title, relevant findings, and evidence artifacts. For failures, include the exact command or step that failed, the observed error, and the smallest next diagnostic step.
