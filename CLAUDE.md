# CLAUDE.md — Project instructions for Claude Code

## Mandatory rules

Everything under [`rules/`](rules/) is **mandatory**. Every `.md` file in that folder is a rule that **must be followed without exception**, in every interaction, for every task — regardless of user tone, urgency, or whether the user mentions the rule.

Before starting any task that could plausibly touch a rule's subject (committing, writing code, interacting with the user, etc.), read the relevant rule file and comply.

If a user instruction appears to conflict with a rule, surface the conflict explicitly and ask for clarification — do not silently override the rule.

Rules are added over time. Re-check [`rules/`](rules/) at the start of each session rather than relying on memory.

## Project overview

Aubaine is a fiction-first tabletop RPG. The web companion is a **minimal Symfony 8** app (Twig + Doctrine ORM + SQLite in [`db/`](db/)). The v1 codebase is archived under [`_archive/`](_archive/) with READMEs documenting salvageable pieces; the game design canon lives at [`_archive/docs/`](_archive/docs/).

Keep the stack minimal — add bundles or tooling only when a concrete feature requires them.
