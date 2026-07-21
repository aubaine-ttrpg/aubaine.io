---
name: use-kor-format-for-config-files
description: Organize config and dotfiles (.env, .gitignore, and similar) into kor-format sections, each a box-drawing rule line, an emoji + Title header, then the entries.
paths: ["**/.env", "**/.env.*", "**/.gitignore", "**/.dockerignore", "**/Makefile", "**/*.env"]
severity: should
---
# Use kor-format for config files

**Rule:** Group entries in config and dotfiles into labelled **kor-format** sections. A section is a
box-drawing rule line, an emoji + sentence-case Title on the next comment line, the same rule line
again, then the entries:

```
# ─────────────────────────────────────────────────────────────
# 📦 Build & Dependencies
# ─────────────────────────────────────────────────────────────
/vendor/
/node_modules/
```

Use it for `.env`, `.env.test`, `.env.local`, `.gitignore`, `.dockerignore`, and similar sectioned
config. Keep the **same section headers (emoji + label) in the same order across `.env`, `.env.test`,
and `.env.local`** so a value's home is obvious and diffs stay aligned. One concern per section; do not
dump entries above the first header.

**Why:** Config files grow into unscannable walls of keys. kor-format makes a 120-line `.env` navigable
at a glance, keeps related vars together, and makes diffs land in the right section. It is the house
convention already used in `.env` and `.gitignore`. This is a presentation convention, not a place to
restate rules ([[never-duplicate-reference-living-files]]).

**Good / Bad:**
```
# Bad: a flat, unsectioned wall.
APP_ENV=dev
APP_SECRET=...
DATABASE_URL=...
CONTENT_EXPORT_DIR=...

# Good: kor-format sections (see the repo's .env and .gitignore).
# ─────────────────────────────────────────────────────────────
# 🗄️ Database (SQLite)
# ─────────────────────────────────────────────────────────────
DATABASE_URL=sqlite:///%kernel.project_dir%/var/aubaine.db
```

**See also:** security/never-commit-secrets (what may live in committed `.env` vs `.env.local`).

**Enforced by:** review.
