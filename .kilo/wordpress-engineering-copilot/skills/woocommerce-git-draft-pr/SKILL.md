---
name: woocommerce-git-draft-pr
description: Create a high-quality draft PR for the current branch. Use when the user says "create a PR", "draft PR", "open a PR", "make a PR", "push and create PR", or "submit PR".
---

# Create Draft PR

Create a concise, reviewer-friendly draft PR from the current branch.

## Core Rules

- Verify the branch is not trunk and has commits ahead of the base branch
- Require a clean working tree before creating the PR
- Use a concise verb-first PR title
- Tailor the PR body to whether the change affects shipped plugin code or only tooling or docs
- Preview the title and body for user approval before pushing and opening the draft PR
