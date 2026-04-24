# CLAUDE.md — Project instructions for Claude Code

## Session start

At the start of every session:

1. **Rules.** Glob `rules/*.md` and read each file's YAML frontmatter (`name`, `description`). Load the full body of every rule whose description could plausibly apply to the task at hand. Err toward loading too many — a rule read unnecessarily costs a few tokens; a rule missed can ship an incorrect change.

2. **Skills.** Review the available skills list (surfaced by the system). Invoke any skill whose description plausibly applies, via the Skill tool. Same bar: loading a skill that turns out not to fit is cheaper than working without one that would have.

3. **Plan mode.** The harness starts each session in plan mode (see [Plans](#plans)). Plan before every state change.

4. **Ask before assuming.** Surface every ambiguity as a question. The bar is "no unstated assumptions", not "minimum questions to proceed". Extra questions are cheaper than wrong guesses. The user wants you to ask as many question as possible.

## Rules

Everything under [`rules/`](rules/) is mandatory. A rule must be followed without exception. If a user instruction appears to conflict with a rule, surface the conflict and ask for clarification.

Cite rules by name when invoking them ("per COMMIT_CONVENTION…").

## Plans

**Always plan.** Plan mode is mandatory for every state change in this repo. Every task that edits a file, writes a new file, renames, deletes, commits, or touches any state outside the current conversation is preceded by an approved plan. Read-only work (research, exploration, explanation) proceeds without a plan once scope is clear.

**Always via `EnterPlanMode`, never inline.** An inline `## Plan` block in chat is not plan mode. The user must be able to approve or reject the plan through the plan-mode UI — that only happens when the `EnterPlanMode` tool is called explicitly, followed by writing the plan to the plan file, followed by `ExitPlanMode`. Session start begins in plan mode by default (per `.claude/settings.json`); after any `ExitPlanMode` approval, the agent re-enters via `EnterPlanMode` for the next state change.

**Always plan the smallest change.** One-line edits are rarely trivial — a one-line bug fix implies a regression test pinning it, a renamed variable ripples through call sites, a changed constant reshapes how a page renders. Scope scales with the work; a typo fix gets a two-line plan, but it still goes through plan mode.

**Always re-plan on surprise.** When the work reveals something the plan did not cover — a file that also needs touching, a side effect, a renamed function — the agent stops, re-enters plan mode via `EnterPlanMode`, re-presents the revised plan, and waits for approval again.

A plan names:

- The files that will change, and how.
- The tests or docs that the change implies.
- The commits that will be created (gitmoji + subject per commit).
- Any state change outside the files (git operations, configuration, external calls).

The plan waits for explicit user approval through the plan-mode UI before any edit, Write, commit, or other state change.

Special requirements on plans accumulate here as they emerge.

## When you commit

Follow [commit convention](rules/COMMIT_CONVENTION.md). On top of that rule, because you are an agent making commits on behalf of the user:

1. Before staging or committing anything, present the commit plan as a table with columns `#`, `Commit (gitmoji + subject)`, `Files`.
2. Wait for explicit approval before running any `git add` or `git commit`.
3. If you discover changes mid-plan (a file you hadn't accounted for, a side effect), stop, re-present the plan, re-ask for approval.
4. End each commit body with the `Co-Authored-By:` trailer.

## Project overview

Aubaine is a fiction-first tabletop RPG. The web companion is a **minimal Symfony 8** app (Twig + Doctrine ORM + SQLite in [`db/`](db/)). The v1 codebase is archived under [`_archive/`](_archive/) with READMEs documenting salvageable pieces; game-content documentation (TTRPG mechanics, math, design concepts) lives at [`wiki/`](wiki/).

Keep the stack minimal — add bundles or tooling only when a concrete feature requires them.
