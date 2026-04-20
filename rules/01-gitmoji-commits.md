# Rule 01 — Commit messages use Gitmoji

**Status:** Mandatory. No exceptions.

Every git commit in this repository **must** start its subject line with a Gitmoji (the emoji character, not the `:code:` form). Reference: <https://gitmoji.dev/>.

## Format

```
<emoji> <Imperative sentence-case subject>

<optional body — bullet list of what changed and why>

Co-Authored-By: ...
```

- Exactly **one** emoji at the start, followed by a single space.
- Subject in **sentence case**, imperative voice ("Add…", "Fix…", "Refactor…"), no trailing period.
- Keep the subject under ~72 characters. Use the body for detail.
- Match the repo's existing style (see `git log`).

## Picking the emoji

Choose the one that best matches the **primary intent** of the commit. Common ones:

| Emoji | Code | When to use |
|---|---|---|
| ✨ | `:sparkles:` | Introduce a new feature |
| 🐛 | `:bug:` | Fix a bug |
| ♻️ | `:recycle:` | Refactor code without changing behavior |
| 🔥 | `:fire:` | Remove code or files |
| 📝 | `:memo:` | Add or update documentation |
| 🎨 | `:art:` | Improve code structure or formatting |
| ⚡️ | `:zap:` | Improve performance |
| 🚚 | `:truck:` | Move or rename files/resources |
| 🗃️ | `:card_file_box:` | Perform database-related changes (schema, migrations) |
| 🔧 | `:wrench:` | Add or update configuration files |
| 🔒 | `:lock:` | Fix security or handle secrets |
| 🚀 | `:rocket:` | Deploy stuff |
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

For anything not covered above, consult <https://gitmoji.dev/> and pick the best match.

## Splitting commits

If a change touches multiple distinct intents (e.g., a refactor *and* a bug fix), split it into separate commits with their own emoji. A single commit should have a single primary intent.

## What this rule does not do

- It does not change the Git Safety Protocol from the main Claude Code instructions (never amend, never force-push to main, etc.).
- It does not dictate when to commit — only how commits are formatted when they happen.
