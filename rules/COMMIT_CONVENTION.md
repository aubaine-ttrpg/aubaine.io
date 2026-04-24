---
name: COMMIT_CONVENTION
description: How commits are named, organized, and authored. Applies when creating, staging, or committing changes, when grouping work into commits, or when picking a gitmoji.
---

# Commit Convention

## Subject format

Every commit subject follows:

```
<emoji> <Imperative sentence-case subject>
```

- Exactly one Gitmoji character (not the `:code:` form), followed by a single space.
- Subject in sentence case, imperative voice (`Add…`, `Fix…`, `Refactor…`), no trailing period.
- Subject length ≤ 72 characters. Details belong in the body.
- Optional body: blank line after the subject, wrapped at ≤ 72 columns. The body explains *why*; the diff already shows *what*.

Reference: <https://gitmoji.dev/>.

## Gitmoji cheatsheet

The emoji reflects the commit's primary intent:

| Emoji | Code | When to use |
|---|---|---|
| ✨ | `:sparkles:` | Introduce a new feature |
| 🐛 | `:bug:` | Fix a bug |
| ♻️ | `:recycle:` | Refactor without behavior change |
| 🔥 | `:fire:` | Remove code or files |
| 📝 | `:memo:` | Add or update documentation |
| 🎨 | `:art:` | Improve code structure or formatting |
| ⚡️ | `:zap:` | Improve performance |
| 🚚 | `:truck:` | Move or rename files/resources |
| 🗃️ | `:card_file_box:` | Database schema or migration changes |
| 🔧 | `:wrench:` | Config file changes |
| 🔒 | `:lock:` | Security or secrets |
| 🙈 | `:see_no_evil:` | Add or update `.gitignore` |
| 🌱 | `:seedling:` | Add or update seed/fixture data |
| 🎉 | `:tada:` | Begin a project or major milestone |
| 🚧 | `:construction:` | Work in progress |
| ➕ | `:heavy_plus_sign:` | Add a dependency |
| ➖ | `:heavy_minus_sign:` | Remove a dependency |
| ⬆️ | `:arrow_up:` | Upgrade dependencies |
| ⬇️ | `:arrow_down:` | Downgrade dependencies |
| 💚 | `:green_heart:` | Fix CI build |
| ✅ | `:white_check_mark:` | Add, update, or pass tests |

For intents not listed here, <https://gitmoji.dev/> is the source of truth; the closest match wins.

## Project emoji overrides

When a commit's primary files sit in one of these domains, the domain emoji takes precedence over the default intent emoji from the cheatsheet:

| Domain | Emoji | Primary files |
|---|---|---|
| Docker | 🐳 | `Dockerfile`, `docker-compose*.yml`, `.dockerignore`, and related container config |
| AI in development | 🤖 | `CLAUDE.md`, `.claude/` (settings, skills, hooks), and other agent-assistance tooling. Not application code that calls AI APIs — that is regular code. |
| Rules | 📏 | Files under `rules/` — new, updated, renamed, or retired rule |

Overlapping domains resolve by the **location** of the primary changed file: a rule file about Docker is 📏 (lives in `rules/`), a skill file about git is 🤖 (lives in `.claude/`). The primary changed file decides.

Other intents follow the standard cheatsheet above.

## Commit craft

- Commits are grouped by primary intent. Each group is one commit.
- Each commit is isolated, single-responsibility, and independently revertible.
- Unrelated changes stay in separate commits — tests apart from features, refactors apart from bug fixes, `.gitignore` edits apart from code changes.
- Files are staged by explicit path. `git status` and `git diff --name-only` confirm the actual change set before staging.

## Splitting coupled changes

A change set spanning multiple intents is split into one commit per intent, each staged by explicit path.

A change set that resists splitting because it is genuinely coupled is committed as a single unit, with the coupling named in the commit body rather than hidden behind a single emoji.
