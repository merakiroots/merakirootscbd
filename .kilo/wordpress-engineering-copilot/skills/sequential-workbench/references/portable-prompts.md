# Portable prompt capsules (Codex/GPT)

Use these templates when handing off work to another model.

## Codex capsule (implementation / debugging)

Paste the following into Codex:

```text
You are Codex. Continue this task using the provided evidence and trace. Do not reinvent requirements.

PROJECT SUMMARY
- <1-5 bullets>

EVIDENCE (paths/urls)
- <bullet list>

CURRENT TRACE SNAPSHOT (JSON)
<PASTE TRACE JSON>

WHAT YOU MUST DO NEXT
1) <action>
2) <action>

CONSTRAINTS
- <security/perf/back-compat/a11y>

SUCCESS CRITERIA
- <bullet list>

VALIDATION COMMANDS
- <cmd>
- <cmd>

OUTPUT EXPECTATIONS
- Provide a short plan
- Apply minimal-drift code changes
- Report results of each validation command
```

## GPT capsule (planning / design)

Paste the following into GPT:

```text
Continue this work using the trace below. Keep outputs concise and evidence-linked.

CONTEXT
- <1-5 bullets>

TRACE (JSON)
<PASTE TRACE JSON>

DELIVERABLE
- <what the user wants>

CONSTRAINTS
- <list>

ASKS
- Propose next steps and decision points
- If context is missing, propose a project-study plan
```
