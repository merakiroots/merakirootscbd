---
name: woocommerce-code-review
description: Review WooCommerce code changes for coding standards compliance. Use when reviewing code locally, performing automated PR reviews, or checking code quality.
---

# WooCommerce Code Review

Review code changes against WooCommerce coding standards and conventions.

## Critical Violations to Flag

### Backend PHP Code

Consult the `woocommerce-backend-dev` skill for detailed standards. Using these standards as guidance, flag these violations and other similar ones:

**Architecture & Structure:**

- Standalone functions - Must use class methods
- Using `new` for DI-managed classes - Classes in `src/` must use `$container->get()`
- Classes outside `src/Internal/` - Default location unless explicitly public
- camelCase naming - Must use snake_case for methods, variables, and hooks
- Yoda condition violations - Must follow WordPress Coding Standards
- Missing `@since` annotations for public or protected methods and hooks
- Missing docblocks for hooks and methods
- Missing validation before deletion or modification
- Using `$instance` in tests - Must use `$sut`
- Missing `@testdox` in unit tests

### UI Text & Copy

Consult the `woocommerce-copy-guidelines` skill. Flag title case in UI where sentence case should be used.

## Review Approach

1. Scan for critical violations
2. Cite relevant WooCommerce standards
3. Provide correct examples
4. Group related issues for clarity
5. Be constructive and explain why a standard matters when useful
