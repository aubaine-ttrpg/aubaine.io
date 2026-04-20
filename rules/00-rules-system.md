---
name: 00-rules-system
description: How rules are created, named, and updated. Applies when adding a new rule, renaming a rule file, changing rule numbering, or editing the frontmatter schema.
---

# Rule 00 — Rules System

## What a rule is

A **standing, mandatory directive** binding for everyone on the project, in every interaction, for every task. A rule:

- Overrides transient preferences, convenience shortcuts, and "just this once" exceptions.
- Covers a *class* of actions (all commits, all docs, all tests…), not one instance.
- Remains in force until explicitly superseded by another rule update.

## Where rules live

- All rules live as `.md` files in [`/rules/`](.) at repo root.
- [`/CLAUDE.md`](../CLAUDE.md) declares this folder mandatory.

## Naming and numbering

- Filename format: **`NN-kebab-case-slug.md`**, where `NN` is a zero-padded two-digit number.
- **`00-*.md` is reserved for meta-rules** — rules about the rules system itself (this file).
- Application rules start at **`01`**. Numbers are monotonic and never reused, even after deletion.
- The slug names *what the rule is*, not its motivation. `01-commit-convention.md` ✓. `01-because-i-hate-messy-commits.md` ✗.

## Required structure

Every rule file starts with a YAML frontmatter block followed by a level-1 heading:

```md
---
name: NN-kebab-case-slug
description: One-line activation hint. States the domain of the rule and typical trigger keywords so an agent (or human) can decide when to load the full file.
---

# Rule NN — Title
```

Schema:

- **`name`** — matches the filename slug (`01-commit-convention` etc.).
- **`description`** — a single sentence, activation-oriented. This is what tells the agent (and anyone skimming the folder) when the rule applies without having to read the whole file.

Beyond the title, each rule uses whatever structure best serves its subject — format definitions, workflows, examples, tables. A short rule can be two paragraphs.

## Updating rules

- Rule updates are changes like any other — they go through the commit convention defined in [Rule 01](01-commit-convention.md).
- **Rename** with `git mv` so history follows the file.
- **Never reuse a number.** If a rule is retired, replace its file content with a tombstone: a single paragraph stating the rule is deprecated, when, and which rule (if any) supersedes it. Keep the original number.
- Significant rule revisions deserve their own commit — do not bundle a rule change with unrelated code changes.
