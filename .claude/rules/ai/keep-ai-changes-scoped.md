---
name: keep-ai-changes-scoped
description: Keep AI-assisted changes small and single-intent; separate mechanical refactors, formatting, and dependency bumps from behaviour, never edit unrelated files or infra to pass, and list what was changed and reused.
severity: should
---
# Keep AI-assisted changes scoped

**Rule:** Keep AI-assisted changes small and single-intent. Separate mechanical refactors, formatting, and dependency bumps from behaviour changes; never opportunistically edit unrelated files, CI, or infrastructure to make generated code pass; squash agent output into meaningful human commits. Every touched file must directly support the stated change, and the PR lists the files intentionally changed and the existing paths checked for reuse.

**Why:** Agents modify files opportunistically and mix cleanup with features, which hides the real risk under churn. A scoped, single-intent change is reviewable, revertible, and forces the reuse search to be explicit rather than assumed.

**Good / Bad:**
```text
# Bad: one PR mixing a feature with a repo-wide reformat and a dependency bump.
feat: add book PDF export (+ reformat 240 files, bump 11 deps, tweak CI)

# Good: one intent per PR; mechanical changes split out.
feat: add book PDF export          # behaviour only, lists files + reuse checked
chore: php-cs-fixer formatting     # separate, mechanical
```

**See also:** process/follow-contributing-for-commits, [[own-every-ai-assisted-line]].

**Enforced by:** review (scope + churn check), CONTRIBUTING commit/PR conventions.
