---
name: 00-rules-system
description: How rules are created, named, and updated. Applies when adding a new rule, renaming a rule file, changing rule numbering, or editing the frontmatter schema.
---

# Rule 00 — Rules System

## Definition

A rule is a standing project standard. Each rule:

- Covers a class of actions (all commits, all templates, all tests), not a single instance.
- Takes precedence over transient preferences and one-off shortcuts.
- Stays in force until a later rule change supersedes it.

## Location

Rules live as `.md` files in [`rules/`](.) at the repo root. [`CLAUDE.md`](../CLAUDE.md) declares this folder mandatory.

## Filename and numbering

- Filename format: `NN-kebab-case-slug.md`, with `NN` zero-padded.
- `00-*.md` is reserved for meta-rules — rules about the rules system itself.
- Application rules begin at `01`. Numbers are monotonic and never reused.
- The slug names what the rule covers, not its motivation. `01-commit-convention.md` ✓. `01-clean-history-matters.md` ✗.

## File structure

Every rule file opens with a YAML frontmatter block followed by a level-1 heading:

```md
---
name: NN-kebab-case-slug
description: One activation-oriented sentence stating the domain and typical trigger keywords.
---

# Rule NN — Title
```

Frontmatter keys:

- `name` — matches the filename slug.
- `description` — one activation-oriented sentence. The description alone carries enough context to decide whether the rule applies to a given task.

The body is free-form: format definitions, workflows, examples, tables. A short rule can be two paragraphs.

## Updating

- Rule changes follow [Rule 01](01-commit-convention.md) like any other commit.
- Renames use `git mv` so history tracks the file.
- A retired rule keeps its filename and number. Its body becomes a tombstone: one paragraph stating the rule is deprecated, the date, and the rule (if any) that supersedes it.
- Each significant rule revision lands in its own commit, separate from unrelated code changes.
