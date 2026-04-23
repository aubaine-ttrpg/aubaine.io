---
name: RULES_SYSTEM
description: How rules are created, named, and updated. Applies when adding a new rule, renaming a rule file, or editing the frontmatter schema.
---

# Rules System

## Definition

A rule is a standing project standard. Each rule:

- Covers a class of actions (all commits, all templates, all tests), not a single instance.
- Takes precedence over transient preferences and one-off shortcuts.
- Stays in force until a later rule change supersedes it.

## Location

Rules live as `.md` files in [`rules/`](.) at the repo root. [`CLAUDE.md`](../CLAUDE.md) declares this folder mandatory.

## Filename convention

- Filename format: `UPPER_SNAKE_CASE.md` — uppercase words joined by underscores. Same family as `README.md`, `CONTRIBUTING.md`, `LICENSE`.
- The slug names what the rule covers, not its motivation. `COMMIT_CONVENTION.md` ✓. `CLEAN_HISTORY_MATTERS.md` ✗.

## File structure

Every rule file opens with a YAML frontmatter block followed by a level-1 heading:

```md
---
name: UPPER_SNAKE_CASE
description: One activation-oriented sentence stating the domain and typical trigger keywords.
---

# Title
```

Frontmatter keys:

- `name` — matches the filename slug.
- `description` — one activation-oriented sentence. The description alone carries enough context to decide whether the rule applies to a given task.

The body is free-form: format definitions, workflows, examples, tables. A short rule can be two paragraphs.

## Updating

- Rule changes follow the [commit convention](COMMIT_CONVENTION.md) like any other commit.
- Renames use `git mv` so history tracks the file.
- A retired rule is deleted from the folder — no tombstone, no placeholder.
- Each significant rule revision lands in its own commit, separate from unrelated code changes.
