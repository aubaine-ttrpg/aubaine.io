---
name: follow-contributing-for-commits
description: Follow CONTRIBUTING.md for commit messages, branch names, and PRs; branch off main and never commit straight to it.
paths: ["**"]
severity: must
---
# Follow CONTRIBUTING for commits
**Rule:** Follow CONTRIBUTING.md for commit messages (gitmoji), branch names, and PRs. The `.claude/skills/athletis-commit` skill points there, and the .githooks integrity check runs on commit. Commit and push only when asked, branch off main, and never commit straight to main.

**Why:** Conventional and gitmoji commits make history scannable and releases automatable, and the canonical rules live in CONTRIBUTING.md so they stay in one place, per [[never-duplicate-reference-living-files]]. Committing to main skips review and breaks the branch-per-change flow.

**Good / Bad:**
```
Bad:  git commit -m "fix" on main.
Good: on branch feat/athletis-123-invoice-pdf, "✨ Add Factur-X export to invoices" per CONTRIBUTING.md.
```

**Enforced by:** .githooks + review (the integrity hook and the reviewer reject off-spec commits).
