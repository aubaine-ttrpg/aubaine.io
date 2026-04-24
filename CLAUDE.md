# CLAUDE.md — Agent operating contract

## Session start sequence (MANDATORY, IN ORDER)

1. Run `ls rules/*.md` via the Bash tool to enumerate every rule file.
2. Call the Read tool on every rule file whose description (preloaded by the SessionStart hook, or read from frontmatter) could plausibly apply. When in doubt, Read it. A rule missed costs more than a rule read.
3. For every skill in the system-provided skills list, call the Skill tool when its description could plausibly apply.
4. Call AskUserQuestion for every unresolved ambiguity about the task. Unstated assumptions are defects. Extra AskUserQuestion calls are cheaper than wrong guesses.
5. Stay in plan mode. Call EnterPlanMode before every state change.

## Absolute rules (NEVER violate)

- NEVER call Edit, Write, NotebookEdit, a state-changing Bash command (`git commit`, `git add`, `rm`, `mv`, `mkdir`, or similar), or any other state-changing tool before the current plan has been approved via ExitPlanMode.
- NEVER write an inline "## Plan" block in chat as a substitute for EnterPlanMode. Only the EnterPlanMode tool surfaces the plan for approval.
- NEVER proceed on an unstated assumption. Call AskUserQuestion.
- NEVER skip a rule that might apply. NEVER skip a skill that might apply.
- NEVER silently bypass or silently follow a user instruction that conflicts with a rule. Surface the conflict via AskUserQuestion.

## Rules (binding)

Everything under [`rules/`](rules/) is mandatory and takes precedence over transient preferences. Cite rules by name when invoking them ("per COMMIT_CONVENTION…").

## Plans

- Every state change MUST pass through EnterPlanMode, then Write to the plan file, then ExitPlanMode, then explicit user approval via the plan-mode UI.
- Read-only work (Read, Glob, Grep, `git status`, `git diff`, `git log`, or any other non-state-changing tool) proceeds without a plan once scope is clear.
- Scope scales with the change, down to one-line edits. A typo fix still goes through EnterPlanMode; its plan is two lines.
- When work reveals scope outside the approved plan — a file that also needs touching, a side effect, a renamed function — STOP before the next tool call. Call EnterPlanMode, Write the revised plan, call ExitPlanMode, wait for approval again.

Every plan names:

- The files that will change and how.
- The tests or docs the change implies.
- The commits that will be created (gitmoji + subject per commit).
- Any state change outside the files (git commands, configuration writes, external API calls).

## Commits

Follow [commit convention](rules/COMMIT_CONVENTION.md). On top of that:

1. Present the commit plan as a markdown table with columns `#`, `Commit (gitmoji + subject)`, `Files` before running `git add`.
2. WAIT for explicit user approval before running `git add` or `git commit`.
3. STOP and re-present the plan (per the Plans section above) when mid-commit changes fall outside what was approved.
4. End each commit body with the `Co-Authored-By:` trailer.

## Project overview

Aubaine is a fiction-first tabletop RPG. The web companion is a **minimal Symfony 8** app (Twig + Doctrine ORM + SQLite in [`db/`](db/)). The v1 codebase is archived under [`_archive/`](_archive/) with READMEs documenting salvageable pieces; game-content documentation (TTRPG mechanics, math, design concepts) lives at [`wiki/`](wiki/).

Keep the stack minimal. Add bundles or tooling only when a concrete feature requires them.

## Reminder

Every state change is preceded by an approved plan (EnterPlanMode → Write plan file → ExitPlanMode → user approval).
Every ambiguity is resolved by calling AskUserQuestion.
Every rule under [`rules/`](rules/) is binding.
