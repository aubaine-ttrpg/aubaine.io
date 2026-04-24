---
name: CONFIG_LIST_FILES
description: Formatting for plain-text config files organized as lists of entries with `#` comments — `.gitignore`, `.dockerignore`, `.env`, `.env.example`, and similar. Applies when creating or editing any such file.
---

# Config List Files

Plain-text config files that consist of a list of entries with `#` comments — `.gitignore`, `.dockerignore`, `.env`, `.env.example`, `.prettierignore`, and similar — are organized into thematic sections separated by visual dividers.

## Section shape

A section is three comment lines plus its entries:

```
# ─────────────────────────────────────────────────────────────
# 📦 Build & Dependencies
# ─────────────────────────────────────────────────────────────
node_modules/
vendor/
public/build/
```

One blank line between sections.

## Divider lines

- The top and bottom dividers match exactly: `# ` followed by 60 `─` (U+2500 BOX DRAWINGS LIGHT HORIZONTAL) characters.
- Same length above and below every heading.

## Section heading

- Format: `# <emoji> <Title Case Heading>`.
- One thematic emoji per heading.
- Title case with `&` for conjunctions ("Build & Dependencies").
- Parenthetical qualifiers clarify intent when useful: `# 🐳 Docker (local overrides)`, `# ☁️ AWS Task Definitions (decrypted, never commit)`.

## Emoji guide

Emojis are thematic, not decorative. These defaults keep the visual language stable across files:

| Subject | Emoji |
|---|---|
| Build output, package managers, vendored deps | 📦 |
| Framework runtime (var/, cache, logs produced by the framework) | 🏗️ |
| Uploads, generated assets, media | 📂 |
| Secrets, environment files | 🔒 |
| Docker | 🐳 |
| Cloud / AWS / GCP / Azure | ☁️ |
| Infra-as-code (Terraform, Pulumi) | 🏗️ |
| Dev artifacts (logs, backups, caches) | 🧹 |
| IDE, OS metadata | 🧑‍💻 |
| AI-tool config (Claude Code, Copilot) | 🤖 |
| Testing (reports, cache) | ✅ |
| Reference / example config | ⚙️ |

Subjects not in this table pick a thematic emoji matching their content. Consistency across files matters more than exhaustive emoji coverage.

## Grouping

- Entries are grouped by **purpose**, not alphabetical order.
- Related entries stay together; thematically distinct entries go in separate sections.
- A section needs at least two entries to justify itself; a single-entry section folds into a related one.

## Entry style

- One entry per line.
- Native syntax of the target file:
  - `.gitignore` / `.dockerignore`: glob patterns.
  - `.env` / `.env.example`: `KEY=value` lines.
- No inline comments after entries; if an entry needs context, the section heading carries it.

## Auto-generated sections

Tooling-managed blocks (e.g. Symfony Flex's `###> bundle-name ###` … `###< bundle-name ###` markers in `.gitignore`, or recipe-managed env blocks) are left untouched. The rule applies to hand-authored sections only — Flex needs its markers to add and remove entries when bundles are installed or removed.
