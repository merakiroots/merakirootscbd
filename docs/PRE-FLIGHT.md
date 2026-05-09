# Pre-Flight

## What Must Be Configured Before Codex Starts

Codex can move much faster and with much better judgment if these are already true:

## 1. Real Working Repo Is Mounted

Codex should have the actual working project, not only exported zip files.

Minimum:

- WordPress root
- active theme
- custom plugins
- mu-plugins if they matter
- any asset build files
- any scripts Codex is expected to run

## 2. Runtime Is Actually Usable

These should already work:

- PHP
- WordPress
- WooCommerce
- WP-CLI
- database connection
- uploads path
- logs

If Codex cannot boot the site or inspect the running app, quality drops fast.

## 3. Environment Variables And Secrets Are Already Available

Examples:

- DB connection
- host-specific config
- API keys used by plugins
- mail or form settings if needed for verification

Do not paste secrets into markdown files. Put them in the environment only.

## 4. Codex Knows Which Site It Is Allowed To Change

State clearly:

- local only
- staging only
- production allowed

If production is allowed, say so explicitly and define the guardrails.

## 5. Launch Inputs Are In The Workspace

At minimum:

- product imports
- media maps
- category maps
- redirect files
- lab-result files
- any screenshots or design references

## 6. A Browser Verification Path Exists

Codex should be able to inspect:

- homepage
- shop
- category pages
- product pages
- lab results
- cart
- partner/contact

Without browser checks, Codex can still help, but the output will not hit the same standard.

## Same-Day High-Standard Output You Can Reasonably Expect

If the environment is real and usable, a strong Codex run today can usually get you:

- a repaired and internally consistent launch candidate
- plugin/theme alignment instead of obvious drift
- fixed broken template references
- fixed missing helper/function mismatches
- improved product-page trust presentation
- working lab-results path
- corrected or completed key public templates
- mapped or partially mapped critical imagery
- launch-focused QA notes with blockers and non-blockers separated

## What A Strong Codex Run Probably Will Not Magically Solve Today

Unless the environment and source data are unusually clean:

- every edge-case content problem across the whole site
- perfect typography and polish on every page
- full legal/compliance review by a human expert
- all import/data-quality anomalies
- every third-party plugin conflict
- hidden production-only infrastructure issues outside the repo

## Expected Deliverables From Codex Today

Set a high bar and ask Codex to return:

1. A working launch candidate in the actual repo
2. A concise launch summary
3. A blocker list
4. A non-blocker list
5. Exact files changed
6. Exact checks run
7. Exact assumptions made
8. Recommended final human spot-checks before relaunch

## Best Instruction To Add Right Before Codex Begins

Use this:

“Treat this as a same-day relaunch push. Optimize for a credible, stable, customer-ready site by EOD. Prefer working, production-safe completion over elegant but unfinished architecture. Work in skeptical sequential passes and keep going until the highest-value launch blockers are gone.”
