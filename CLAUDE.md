# CLAUDE.md — Project instructions for Claude Code

## Mandatory rules

Everything under [`rules/`](rules/) is **mandatory**. Every `.md` file in that folder is a rule that must be followed without exception.

Before starting any task that could plausibly touch a rule's subject, read the relevant rule file and comply.

If a user instruction appears to conflict with a rule, surface the conflict and ask for clarification.

At the start of every session, glob `rules/*.md` and read each file **in full**. The YAML frontmatter (`name`, `description`) at the top of every rule makes the catalog easy to scan. Cite rules by number when invoking them ("per Rule 01…").

## When a user directive could be a new rule

If the user gives you a directive that is **mandatory** ("must", "always", "never", "from now on"), **durable** (applies beyond the current task), and **class-scoped** (covers a category of actions, not one instance), propose adding it to [`rules/`](rules/).

Otherwise the directive belongs in agent memory or stays inline in the conversation. When unsure, ask the user: *"Is this a rule or a preference?"* before creating a file in `rules/`.

## Producing technical documentation

Follow [Rule 08](rules/08-technical-documentation.md). [`docs/`](docs/) is the repo's technical documentation for developers. When a multi-commit project wraps — or any time an architectural decision would be hard to reconstruct from code alone — synthesize the session context (your working plan file, the todo list, the landed diffs, the directives the user gave along the way) into a finished doc page at `docs/UPPER_SNAKE_CASE.md`. The page is the polished reference aimed at the next developer on the project; your working plan and the chat are source material, not the page's format.

## When you commit

Follow [Rule 01](rules/01-commit-convention.md). On top of that rule, because you are an agent making commits on behalf of the user:

1. Before staging or committing anything, present the commit plan as a table with columns `#`, `Commit (gitmoji + subject)`, `Files`.
2. Wait for explicit approval before running any `git add` or `git commit`.
3. If you discover changes mid-plan (a file you hadn't accounted for, a side effect), stop, re-present the plan, re-ask for approval.
4. End each commit body with the `Co-Authored-By:` trailer.

## Your writing style

When you write rules, READMEs, commit messages, or any other doc in this repo, state what *is*, not what *isn't*:

- Don't negate concepts that were never on the table. If no one would assume X, skip "not X".
- Don't speculate about features we haven't committed to ("can be added later…", "when X is introduced…"). Write for today's state of the repo.
- Don't pre-emptively defend against misreadings. Positive form beats anticipated-objection form.
- Every "never" or "don't" must anchor to a concrete positive statement in the same breath — otherwise cut it.

## Project overview

Aubaine is a fiction-first tabletop RPG. The web companion is a **minimal Symfony 8** app (Twig + Doctrine ORM + SQLite in [`db/`](db/)). The v1 codebase is archived under [`_archive/`](_archive/) with READMEs documenting salvageable pieces; the game design canon lives at [`_archive/docs/`](_archive/docs/).

Keep the stack minimal — add bundles or tooling only when a concrete feature requires them.
