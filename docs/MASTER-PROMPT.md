# Master Prompt

Use the repo and the markdown files in this folder as source of truth.

Your job is to get `merakirootscbd.com` relaunch-ready by end of day today.

Important context:

- This is a real launch push, not a speculative architecture exercise.
- Customers and clients are waiting.
- The site needs to be stable, credible, and usable today.
- Prefer the fastest production-safe path that preserves the right long-term direction.

Execution rules:

1. Inspect the real codebase and runtime before changing anything.
2. Work in sequential passes.
3. After each pass, review your work skeptically as if you expect mistakes or incompleteness.
4. If a pass is green, move on.
5. If a pass is non-green, fix the non-green items.
6. If a non-green item is not doable in the available time or environment, log it clearly and continue with the next highest-value pass.
7. Do not pretend a runtime validation happened if it did not.
8. Do not invent architecture that conflicts with the provided docs and mappings.
9. Keep changes scoped and production-minded.
10. Do not overwrite unrelated existing work.

Priority order:

1. Runtime stability and no fatal errors
2. Product page quality and trust presentation
3. Lab-results functionality and credibility
4. Navigation and key public pages
5. Mobile correctness
6. Visual polish on non-critical surfaces

Use these documents in this order:

- `PROJECT-TRUTH.md`
- `BUILD-BRIEF.md`
- `RUNBOOK.md`
- `SITE-MAP.md`
- `DATA-CONTRACTS.md`
- `DESIGN-NOTES.md`
- `KNOWN-ISSUES.md`
- `QA-CHECKLIST.md`

Specific expectations:

- If a current COA exists for a product, make it reachable in a clean way.
- Keep trust data plugin-owned where possible.
- Keep layout and presentation theme-owned where possible.
- Do not leave key public pages looking unfinished.
- Do not leave missing helper calls or stale template references in launch code.
- Use real CSVs and mappings instead of guessing.
- Run the shortest practical validations after each major pass.

Working style:

- Start by identifying the real repo root and active theme/plugin paths.
- Read the relevant files before editing.
- State assumptions briefly.
- Make the highest-value change first.
- Re-check after every pass.

Expected end-of-day output:

- a working launch candidate
- a short list of remaining non-blockers
- a short list of any blockers that could not be resolved
- exact file changes made
- exact verification performed

Final rule:

Bias toward shipping a clean, trustworthy site today, not toward endless analysis.
