---
name: follow-contributing-for-commits
description: Follow CONTRIBUTING.md for commit messages, branch names, and PRs; branch off main and never commit straight to it.
paths: ["**"]
severity: must
---
# Follow CONTRIBUTING for commits
**Rule:** Follow CONTRIBUTING.md for commit messages (Gitmoji), branch names, and PRs; it is the sole source of truth for that policy. The aubaine-commit skill defers to it. Commit and push only when asked, branch off `main`, and never commit straight to `main`.

**Why:** Gitmoji commits make history scannable and releases automatable, and the canonical rules live in CONTRIBUTING.md so they stay in one place, per [[never-duplicate-reference-living-files]]. Committing to `main` skips review and breaks the branch-per-change flow.

**Good / Bad:**
```
Bad:  git commit -m "fix" on main.
Good: on branch feat/book-breadcrumbs, "✨ (catalyst): add breadcrumb trails to the book pages" per CONTRIBUTING.md.
```

**Enforced by:** aubaine-commit + review (both defer to CONTRIBUTING.md and reject off-policy commits).
