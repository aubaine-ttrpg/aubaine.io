---
name: CONFIG_LIST_FILES
description: Formatting for plain-text config files organized as lists of entries with `#` comments — `.gitignore`, `.dockerignore`, `.env`, `.env.example`, and similar. Applies when creating or editing any such file.
---

# Config List Files

Plain-text config files that consist of a list of entries with `#` comments — `.gitignore`, `.dockerignore`, `.env`, `.env.example`, `.prettierignore`, and similar — are organized into thematic sections separated by visual dividers.

## Section shape

A section is three comment lines plus its entries:

```
# ────────────────────────────────────────────────────────────────────────────────
# 📦 Build & Dependencies
# ────────────────────────────────────────────────────────────────────────────────
node_modules/
vendor/
public/build/
```

One blank line between sections.

## Dividers and line width

- Dividers are `# ` followed by 80 `─` (U+2500 BOX DRAWINGS LIGHT HORIZONTAL) characters — 82 chars total.
- Same divider above and below every heading.
- Non-divider content lines inside a section stay ≤ 80 characters total (including the `# ` prefix on prose lines). Long URLs go on their own line within the cap, or use a descriptive pointer when no shorter form exists.

## Section heading

- Format: `# <emoji> <Title Case Heading>`.
- One thematic emoji per heading.
- Title case with `&` for conjunctions ("Build & Dependencies").
- Parenthetical qualifiers earn their keep only when they add information the entries cannot: `# 🐳 Docker (local overrides)` distinguishes from committed Docker files, `# ☁️ AWS Task Definitions (decrypted, never commit)` is a security note. Parentheticals that restate what the entries already convey are dropped.

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
| Readme-style preamble | 📖 |

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
- No inline comments after entries; if an entry needs context, the section uses a prose wrap (see below).

## Sections with prose content

A section that carries prose — a preamble, context, or comments annotating entries — wraps the heading and prose in three divider lines (top, heading-bottom, closing). Entries, if any, live outside the wrap, directly after the closing divider:

```
# ────────────────────────────────────────────────────────────────────────────────
# 📖 Preamble (prose only, no entries)
# ────────────────────────────────────────────────────────────────────────────────
# Prose line 1.
# Prose line 2.
# ────────────────────────────────────────────────────────────────────────────────
```

```
# ────────────────────────────────────────────────────────────────────────────────
# 🧭 Section With Context
# ────────────────────────────────────────────────────────────────────────────────
# Context line explaining the entry below.
# ────────────────────────────────────────────────────────────────────────────────
SOME_VAR=value
```

A thematic emoji goes on the heading (📖 for readme-style preambles is a natural default). Preamble sections go first in the file. Entry-only sections (no prose inside) skip the closing divider — entries follow directly after the opening block.

## Tooling-managed blocks

Tools like Symfony Flex use `###> bundle-name ###` … `###< bundle-name ###` markers to auto-add and auto-remove entries when bundles are installed or removed. These blocks are replaced with the section format like any other group.

When `composer require` or a Flex recipe adds a new `###> … ###` block to a file, the block is promoted in the same commit that introduces the dependency: the markers are removed, the entries gain a divider + emoji heading, and related entries are regrouped thematically (Flex's per-bundle split is not preserved). Auto-management by the recipe ends there — entries in a promoted section are maintained manually from that point. The trade-off is deliberate: visual consistency across the file outweighs recipe auto-management.

## Env-file specifics

When the list-config file is a `.env` variant, Symfony's configuration semantics apply in addition to the formatting rules above. Authoritative source: <https://symfony.com/doc/current/configuration.html>.

- **Loading order**, earliest → latest (later wins):
  1. `.env` — committed, defaults.
  2. `.env.$APP_ENV` — committed, per-environment.
  3. `.env.local` — gitignored, machine-specific.
  4. `.env.$APP_ENV.local` — gitignored, machine + env.
  5. `.env.local.php` — composer dump-env prod output.
  Real system environment variables win over every `.env` file.
- **Production secrets** go through [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html), not any committed `.env` file. Committed `.env` files hold only defaults safe in dev.
- **Production deploys** run `composer dump-env prod` (or `APP_ENV=prod APP_DEBUG=0 php bin/console dotenv:dump`) to compile `.env.local.php`. That file short-circuits per-request `.env` parsing.
- **Interpolation:** `${VAR}` substitutes; `${VAR:-default}` falls back. Single quotes hold literal values (no `$` expansion); double quotes allow `${}` interpolation. A reference resolves against earlier lines in the same file or earlier-loaded files.
- **Security:** env-var values leak through `phpinfo()`, `debug:dotenv`, or the Symfony profiler. Committed `.env` files hold no secrets regardless; the profiler never runs in production.
