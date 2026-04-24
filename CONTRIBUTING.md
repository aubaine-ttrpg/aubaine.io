# Contributing to Aubaine

Aubaine is a fiction-first tabletop RPG; this repository hosts its Symfony 8 web companion.

See the [README](README.md) for setup — install, run, and test.

## Rules

Every file under [`rules/`](rules/) is mandatory. The YAML frontmatter on each rule names when it applies; skim the folder before starting a task and open any rule whose subject your change plausibly touches. Proposing a new rule follows [RULES_SYSTEM](rules/RULES_SYSTEM.md).

## Commits

One gitmoji + imperative sentence-case subject, no trailing period. Domain emoji overrides apply: 📏 for rules, 🤖 for AI-in-dev tooling, 🐳 for Docker. Details in [COMMIT_CONVENTION](rules/COMMIT_CONVENTION.md).

## AI agents

Agents working in this repo — Claude Code and similar — read [CLAUDE.md](CLAUDE.md) at session start and follow the same rules. The project-level [`.claude/settings.json`](.claude/settings.json) enforces plan mode by default and carries the allow / ask / deny permission sets. Changes to `CLAUDE.md`, `.claude/settings.json`, or files under `.claude/skills/` affect every contributor using the tool, so they go through the same rule-and-plan flow as code changes.

## Game content

Game-design pages (mechanics, math, reference tables, lore) live under [`wiki/`](wiki/) — see its README for conventions.
