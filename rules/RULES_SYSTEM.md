---
name: RULES_SYSTEM
description: How rules under rules/ are defined, named, structured, and retired — UPPER_SNAKE_CASE filename slugs, the YAML frontmatter schema (name, description), description authoring (keyword-rich and activation-oriented), per-rule commit policy (significant revisions in their own commit, gitmoji 📏), `git mv` for renames, retirement without tombstones. Applies when adding, renaming, splitting, or retiring a rule, or when editing the frontmatter schema or description guidance.
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
- `description` — activation-oriented prose enumerating concrete trigger keywords: file globs the rule governs, framework, library, attribute, or tool names it touches, and action verbs that surface its scope. One or two sentences sufficient. The description alone carries enough context for an agent to decide whether the rule applies; the body is read only once the description matches. The SessionStart hook injects every rule's description as the rules index, so descriptions carry the full discovery surface.

The body is free-form: format definitions, workflows, examples, tables. A short rule can be two paragraphs.

## Updating

- Rule changes follow the [commit convention](COMMIT_CONVENTION.md) like any other commit.
- Renames use `git mv` so history tracks the file.
- A retired rule is deleted from the folder — no tombstone, no placeholder.
- Each significant rule revision lands in its own commit, separate from unrelated code changes.
