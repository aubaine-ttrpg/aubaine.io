---
name: 08-technical-documentation
description: How technical documentation is written and maintained in the `docs/` folder. Applies when authoring or updating a dev doc, or when looking up the rationale behind a prior architectural choice.
---

# Rule 08 — Technical Documentation

[`docs/`](../docs) holds the repo's technical documentation for developers. Each page is written for another developer joining the project, or for the same developer six months later, who needs to understand *what the codebase looks like* and *why it was built that way*.

## Filename convention

- Format: `UPPER_SNAKE_CASE.md` — uppercase words joined by underscores. Same family as `README.md`, `CONTRIBUTING.md`, `LICENSE`.
- The slug names the **topic** (a noun), not a process or adjective: `FOUNDATION.md`, `STACK.md`, `DATABASE.md`, `AUTH.md` ✓. `COOL_SETUP.md`, `APRIL_WORK.md`, `MY_IDEAS.md` ✗.
- Every `docs/` directory (the root `docs/` and any future subdirectory) carries a `README.md` summarizing its contents.

## When a page exists

A doc page is warranted when:

- A multi-commit project has shaped the architecture, tooling, conventions, or rule set.
- A stack-level choice was made (picking an ORM, an asset pipeline, a test runner).
- A non-obvious constraint shapes the code in ways a newcomer would trip over.

Small single-intent changes do not get a doc page — the commit message covers them.

## Structure

Pages are written for a reader landing cold, and favor decisions and rationale over mechanics.

1. **Overview / Context** — the subject of the page, the problem that shaped it, and the constraints that applied.
2. **Decisions** — the technical choices that define the topic. Each decision states the choice, the reason, and the alternatives rejected with why they lost.
3. **Verification** *(when applicable)* — how the setup is confirmed to work (a command, an observation, a test).
4. **Follow-ups** *(optional)* — open questions, deferred work, lessons for next time.

Out of scope for `docs/`: file-by-file change lists (the diff covers that), commit hashes (the git log covers that), step-by-step how-tos (those belong in `README.md` or code comments), and anything personal to one developer's environment (aliases, editor config, shell setup — that is not the project).

## Maintenance

Doc pages age. When a prior decision is reversed or superseded, the page is edited in place: a dated note explains what changed and why, and the decision text is revised. The original reasoning remains visible so the history of thinking stays intact.

Doc-page changes are committed with the `📝` gitmoji.

## Starting a new project

[`docs/`](../docs) is the first reference for a new project. Prior pages show how similar problems were reasoned through, what constraints applied, and what was left open.
