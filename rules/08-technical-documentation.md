---
name: 08-technical-documentation
description: How technical documentation is written and maintained in the `docs/` folder. Applies when authoring or updating a dev doc, or when looking up how a subsystem is organized.
---

# Rule 08 — Technical Documentation

[`docs/`](../docs) holds this repo's **technical documentation for developers**. A page teaches the shape of a part of the codebase: the mental model, where the code lives, how a typical operation flows, and the constraints that would otherwise trip up a newcomer.

The outcome each page aims for: another developer, or the same developer six months later, opens the page cold and walks away knowing how to navigate and extend the subject.

## Filename convention

- Format: `UPPER_SNAKE_CASE.md` — uppercase words joined by underscores. Same family as `README.md`, `CONTRIBUTING.md`, `LICENSE`.
- The slug names the **topic** (a noun), not a process or adjective: `STACK.md`, `TESTING.md`, `FRONTEND.md`, `DATABASE.md` ✓. `APRIL_WORK.md`, `MY_IDEAS.md`, `COOL_SETUP.md` ✗.
- Every `docs/` directory (the root `docs/` and any future subdirectory) carries a `README.md` that indexes its pages.

## When a page exists

A page is warranted when a subsystem, workflow, or constraint is non-obvious from the code alone — when reading the source would leave a newcomer with questions the code cannot answer on its own.

Single-intent changes do not get a doc page. The commit message carries the rationale there.

## What a page does

Each page teaches one topic end-to-end. The structure follows the subject; no single template applies to every page.

The ingredients below are useful to draw from. A page uses the ones that clarify its subject and skips the ones that do not.

- **Purpose** — what the topic is and why it exists in this codebase, in one or two direct sentences.
- **Mental model** — the vocabulary and relationships that come before any code. Which concepts interact, and how.
- **File layout** — where the relevant code lives. Directories, key classes, entry points, with links to actual files.
- **End-to-end flow** — how a typical operation moves through the code, referencing concrete files. A request, a test run, a build, a deploy, a lifecycle event.
- **Extending it** — where new code goes, which patterns apply, how to follow the grain of the existing design.
- **Gotchas** — non-obvious constraints, invariants, and surprises that would otherwise cost an hour of investigation.

Rationale and tradeoffs are woven inline where they clarify the *how*. A design choice surfaces in the sentence that explains the mechanism ("mapping uses attributes because declaration and class sit in one file"), not in a separate decision catalog.

## Out of scope for `docs/`

- File-by-file change lists. The diff covers those.
- Commit hashes, dates, and change history. `git log` covers those.
- Step-by-step install or setup instructions. Those belong in [`README.md`](../README.md) or code comments.
- Per-developer environment notes (aliases, editor config, shell tweaks). Those are not the project.

## Maintenance

Pages age. When a description no longer matches the code, the page is edited in place. When a design choice is reversed, a dated note explains what changed and why, and the surrounding text is revised. The original reasoning remains visible so the history of thinking stays intact.

Doc-page changes are committed with the `📝` gitmoji per [Rule 01](01-commit-convention.md).

## Starting a new project

[`docs/`](../docs) is the first reference for a new project in this repo. Prior pages show how adjacent subsystems are modeled, what vocabulary is in use, and where in the code to start reading.
