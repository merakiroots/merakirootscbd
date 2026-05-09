# Meraki Roots Handoff Pack

This pack is meant to be dropped into a real Codex workspace before asking it to make launch-critical changes.

## Recommended Use Order

1. Read `PROJECT-TRUTH.md`
2. Read `BUILD-BRIEF.md`
3. Read `RUNBOOK.md`
4. Read `SITE-MAP.md`
5. Read `DATA-CONTRACTS.md`
6. Read `DESIGN-NOTES.md`
7. Read `KNOWN-ISSUES.md`
8. Read `QA-CHECKLIST.md`
9. Paste `CODEX-MASTER-PROMPT.md` into Codex with the real repo and runtime available

## What To Give Agents Alongside This Pack

- The actual working WordPress codebase
- The real plugin and theme directories that will ship
- The active uploads or import assets needed for launch
- Any local config stubs that are safe to share
- The exact commands needed to boot, test, and inspect the site
- Any credentials or secrets through the environment only, not in these files

## Today’s Goal

Make `merakirootscbd.com` relaunch-ready by end of day with the highest-confidence path:

- working storefront
- working product pages
- working lab-results experience
- working navigation and key content pages
- acceptable mobile behavior
- no obvious fatal errors
- no broken high-traffic customer flows

## Operating Principle

Today is about a credible, high-quality relaunch, not an ideal future architecture. Codex should prefer the fastest production-safe path that preserves the right long-term direction.
