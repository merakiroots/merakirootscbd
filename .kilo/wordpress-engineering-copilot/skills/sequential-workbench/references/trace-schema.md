# Trace schema (portable)

This skill uses a **public, portable trace**. It is *not* a full chain-of-thought.
It is a concise, evidence-backed decision record that another model (Codex/GPT/other) can continue.

## JSON schema (informal)

```json
{
  "meta": {
    "trace_id": "string",
    "created_at": "iso-8601",
    "updated_at": "iso-8601",
    "project": {
      "name": "string?",
      "repo": "string?",
      "root": "string?"
    }
  },
  "study": {
    "enabled": true,
    "sources": ["repo", "github", "drive", "conversation"],
    "signals": {
      "languages": ["string"],
      "frameworks": ["string"],
      "build": ["string"],
      "tests": ["string"],
      "ci": ["string"]
    },
    "evidence": [
      {"type": "file|url|commit|issue", "ref": "string", "note": "string"}
    ],
    "summary": ["string"]
  },
  "steps": [
    {
      "id": "S1",
      "title": "string",
      "goal": "string",
      "observations": [
        {"evidence_ref": "string", "note": "string"}
      ],
      "decision": "string",
      "next_actions": ["string"],
      "status": "open|done|blocked",
      "confidence": "low|medium|high"
    }
  ],
  "revisions": [
    {
      "id": "R1",
      "revises_step_id": "S2",
      "reason": "string",
      "change": "string",
      "evidence": [{"evidence_ref": "string", "note": "string"}]
    }
  ],
  "branches": [
    {
      "branch_id": "B1",
      "from_step_id": "S2",
      "hypothesis": "string",
      "plan": ["string"],
      "tradeoffs": ["string"],
      "status": "open|chosen|discarded"
    }
  ],
  "handoff": {
    "success_criteria": ["string"],
    "validation_commands": ["string"],
    "open_questions": ["string"],
    "next_owner": "gpt|codex|other"
  }
}
```

## Authoring rules
- Keep each `step` short and testable.
- Use `observations` to cite evidence (file paths, URLs, logs).
- Use `revisions` only when something was previously concluded differently.
- Use `branches` only for materially different approaches.
- Always end with `handoff` when you stop early.
