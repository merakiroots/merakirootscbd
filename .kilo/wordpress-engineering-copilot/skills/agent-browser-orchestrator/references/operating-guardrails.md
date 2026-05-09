# Agent Browser Operating Guardrails

Use these rules for any browser-automation or webapp-testing task.

## Tool preference

Prefer the most direct available browser control path for the environment:

1. Use an existing ChatGPT/Codex browser or DevTools/MCP capability when the session already exposes one.
2. Use `agent-browser` locally when shell access exists and Chrome/Chromium can be controlled through CDP.
3. Use Vercel Sandbox when the workflow needs an ephemeral remote microVM, reproducibility, or deployment-adjacent testing.
4. Use AWS Bedrock AgentCore only when the task explicitly requires AWS-hosted browsers or AWS credential context.

## Interaction loop

1. Open or attach to the target.
2. Snapshot the accessibility tree with interactive refs.
3. Act on the current refs.
4. Re-snapshot after every navigation, submission, modal opening, client-side render, or DOM-changing click.
5. Save evidence: URL, title, screenshot path, console/network signals when available, and a concise action log.

## Safety and data handling

- Never invent element refs. Re-snapshot when refs are stale or missing.
- Avoid destructive production actions unless the user explicitly requested them.
- Do not submit purchases, legal agreements, account deletions, or irreversible changes without explicit confirmation.
- Prefer test/staging accounts and sandbox environments for QA.
- Redact credentials, tokens, session cookies, and personal data from reports.

## Verification standard

A browser task is not complete until the final state is observed. For testing work, include at least one concrete artifact: final screenshot, snapshot excerpt, console/network finding, failed selector, reproduced error message, or command transcript.
