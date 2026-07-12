---
name: never-duplicate-reference-living-files
description: Reference canonical living files instead of restating their content in another file.
severity: must
---
# Never duplicate, reference living files
**Rule:** Reference canonical living files instead of restating their content in another file. When you
feel the urge to copy guidance into a second place, link the canonical file instead.

**Reference vs duplication:** a single pointer to a file, folder, or skill ("see `CONTRIBUTING.md`", "tickets live in `tasks/`") is a **reference**, and references are good. **Enumerating a folder's contents** (listing every file of a directory in a README or index) is **duplication**: it goes stale the instant someone adds a file and forgets the list, and readers trust the stale list. Reference the folder and let its files plus their frontmatter be the index; if you need a rendered list, **generate it from frontmatter**, never hand-maintain it.

**Why:** DRY applies to documentation and agent instructions, not only code. Duplicated guidance drifts:
a fix lands in one copy and the others go stale, then readers follow the wrong one. The same principle
for code is architecture/always-factorize.

**Good / Bad:**
```
Bad:  the athletis-commit skill restates the gitmoji table and branch rules in full.
Good: the skill is thin and says "follow CONTRIBUTING.md" with a link, the rules live there once.

Bad:  tasks/README.md (or rules/README.md) lists every file in the folder; a new file is added and the list is forgotten.
Good: the README documents conventions and says "discover them by scanning the folder / frontmatter"; the folder is the index.
```

**See also:** [[own-canonical-sources-of-truth]], [[never-hardcode-dynamic-lists]], [[never-write-redundant-content]].

**Enforced by:** review (catch the second copy and replace it with a link).
