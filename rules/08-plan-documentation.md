---
name: 08-plan-documentation
description: How technical documentation (the plans/ wiki) is created and maintained in this repo. Applies when finishing a big project, updating existing plan docs, or looking up the rationale behind a prior decision.
---

# Rule 08 — Plan Documentation

`plans/` holds the repo's **human-oriented technical documentation for developers** — the dev wiki. Each page is written for another developer joining the project, or for the same developer six months from now, who needs to understand *why* the codebase looks the way it does: the decisions, the constraints, the rejected alternatives. Diffs cover what changed; git log covers when; plan docs cover **why**.

The folder is named `plans/` (not `docs/`) because `docs/` is reserved for **game-content documentation** — TTRPG mechanics, math, design concepts — aimed at a different audience (players, GMs, designers).

## Filename convention

- **`UPPER_SNAKE_CASE.md`** — uppercase, underscores between words, same family as `README.md`, `CONTRIBUTING.md`, `LICENSE`.
- No numeric prefix. The plan is identified by its subject.
- The slug names the **topic** (a noun), not a process or adjective:
  - `FOUNDATION.md`, `STACK.md`, `RULES_SYSTEM.md`, `DATABASE_CHOICE.md` ✓
  - `MY_PLAN.md`, `COOL_SETUP.md`, `APRIL_WORK.md` ✗

## When to write one

- Multi-commit projects that change architecture, tooling, conventions, or the rule set.
- Stack moves (adding or removing a bundle, switching a pipeline, picking a runner).
- Any decision where the rationale would be hard to reconstruct from the code alone.

Small single-intent changes do not need a plan doc — the commit message is enough.

## Structure

Write for a reader landing cold on the page.

1. **Context** — what prompted the work, the problem, the constraints at the time.
2. **Decisions** — the choices that shape the result. For each: the decision, the rationale, and the alternatives rejected with why they lost.
3. **Verification** — how success was confirmed (what observation, test, or command).
4. **Follow-ups** *(optional)* — open questions, deferred work, lessons for next time.

Keep out: file-by-file change lists (diff covers that), commit hashes (log covers that), step-by-step how-tos (those belong in READMEs or code comments).

## Maintaining plan docs

Plan docs age. When a prior decision is reversed or superseded, update the relevant plan doc: add a dated note explaining what changed and why. Keep the original entry so the history of reasoning stays visible.

Commit plan-doc changes with a `📝` gitmoji.

## Starting a new project

Browse `plans/` first. Prior plan docs are source material for how similar problems were reasoned through, what was decided, and what was left open.
