---
name: sequential-workbench
description: "portable stepwise problem-solving and planning with explicit revision + branching, plus retroactive project study and knowledge weaving into a project knowledge base. use for complex multi-stage tasks or when mid-project context is missing, including coding in codex, system design, debugging, and refactors. outputs are gpt/codex-friendly: concise decision trace, evidence links, and copy-pastable prompts for other llms."
---

# Sequential Workbench

## What this skill does
Use a **structured workbench** to:

1. **Study the project context (retroactively if needed)**
2. **Reason in sequential steps** with **branching** and **revisions**
3. **Weave new knowledge** into a persistent **Project Knowledge Base (PKB)** that grows over time

This skill is designed to be **portable** across LLMs. It avoids vendor-specific tools and instead uses:
- a stable **Trace Schema** (JSON)
- a stable **Prompt Capsule** for Codex/GPT
- optional scripts to generate/merge PKB artifacts when a repo filesystem is available


## Workflow decision tree

### Step 0 — Decide if “project study mode” is needed
Enter **Project Study Mode** if *any* are true:
- the request happens **mid-project** and details are missing (“what’s going on here?”, “continue this implementation”, “fix failing tests”)
- the task depends on **existing code/design/system constraints** not fully present in chat
- the solution space is broad and needs **grounding** before planning

Otherwise, skip to **Step 2**.


## Step 1 — Project Study Mode (retroactive)
Goal: build a concise, evidence-backed snapshot of the project.

### 1A) Gather evidence
Prefer sources in this order:
1. **Local repo files** (if available): run `scripts/project_scan.py` to produce a study pack
2. **Connected repositories / docs** (if available): search via connectors (e.g., GitHub, Drive) for READMEs, architecture docs, ADRs, and recent issues/PRs
3. **Conversation history**: extract known requirements, constraints, and decisions

**If a repo filesystem is available:**
- Run: `python scripts/project_scan.py --root . --out /mnt/data/project_study_pack` (or equivalent path)
- Inspect the generated `project_study_pack.md` and `project_study_pack.json`

### 1B) Produce a Project Study Summary
Output a compact summary:
- **Project goal** (1–3 bullets)
- **Tech stack signals** (language, framework, build system, test runner)
- **Key artifacts** (important files + why)
- **Current state** (what’s implemented / failing / unknown)
- **Constraints** (security, performance, UX, backward compatibility)

Keep it brief and evidence-linked (file paths, URLs, commit SHAs, etc.).


## Step 2 — Run the Sequential Workbench Trace
Goal: progress through the task using a **decision trace**, not a hidden chain-of-thought.

### Output format: Trace Blocks
Maintain a running **TRACE** object (JSON) following `references/trace-schema.md`.

Rules:
- Each step must be **actionable** and **testable**.
- Prefer **short step summaries** (not internal deliberation).
- Use **revisions** when evidence invalidates a prior step.
- Use **branches** when two approaches are viable and differ materially.

### Default step pattern
For each step, output:
- **Goal**
- **Observations** (evidence)
- **Decision** (what you chose)
- **Next action** (what to do next)

Stop when the task is complete or when additional inputs are required.


## Step 3 — Knowledge Weaving (PKB)
Goal: interweave new understanding into a persistent **Project Knowledge Base**.

### PKB files (recommended)
- `docs/pkb/project-kb.json` (machine-mergeable)
- `docs/pkb/project-kb.md` (human-readable)

If a project already has a knowledge base (e.g., `docs/design/design-kb.json`), treat it as an upstream input and link between them.

### What to write to the PKB
Write only durable information:
- decisions (what/why)
- constraints
- conventions (naming, layering, tokens, components)
- interfaces / contracts
- known pitfalls
- references to evidence (paths/urls)

Avoid transient chatter.

### How to merge
If a filesystem is available:
- Create a delta file `pkb_delta.json` using the Trace Schema entries.
- Merge into the PKB using `scripts/kb_merge.py`.


## Portable “Prompt Capsule” (Codex/GPT)
When handing off to another LLM (especially Codex), produce a **Prompt Capsule** using `references/portable-prompts.md`.

Minimum capsule fields:
- context summary
- relevant evidence list
- current trace snapshot
- explicit next actions
- success criteria


## Quality gates
Before finalizing:
- confirm the plan aligns to constraints
- confirm any code plan includes a validation command list (build/test/lint)
- include 1–3 alternatives only if they materially differ


## Resources
- Trace schema: `references/trace-schema.md`
- Portable prompt templates: `references/portable-prompts.md`
- Project scan script: `scripts/project_scan.py`
- PKB merge script: `scripts/kb_merge.py`
