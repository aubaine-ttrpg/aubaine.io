# Rule 01 — Commit Convention

## Format

Every commit subject **must** follow:

```
<emoji> <Imperative sentence-case subject>
```

- Exactly **one** Gitmoji character (not the `:code:` form) followed by a single space.
- Subject in **sentence case**, imperative voice ("Add…", "Fix…", "Refactor…"), no trailing period.
- Subject ≤ ~72 characters. Use the body for detail.
- Optional body: blank line after subject, wrapped ≤72 cols. Explain *why*, not *what* — the diff shows what.

Reference: <https://gitmoji.dev/>.

### Gitmoji cheatsheet

Pick the emoji matching the **primary intent** of the commit:

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
| 🗃️ | `:card_file_box:` | Database schema/migration changes |
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

For anything not listed here, consult <https://gitmoji.dev/> and pick the closest match.

## Commit craft

- Before each commit, check `git status` and `git diff --name-only` to see the **actual** changes.
- Group changes by **primary intent**. Each group is one commit.
- Each commit must be **isolated**, **single-responsibility**, and **independently revertible**.
- **Never bundle unrelated changes** — no tests with features, no refactors with bug fixes, no `.gitignore` tweaks with code changes.
- Stage files by explicit path.

### When a commit would touch multiple intents

Split it. Stage only the files for one intent, commit, stage the next, commit. If the files cannot be cleanly separated because they are genuinely coupled, raise it — that coupling is worth discussing before committing.
