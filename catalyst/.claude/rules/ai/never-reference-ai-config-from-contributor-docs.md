---
name: never-reference-ai-config-from-contributor-docs
description: Contributor-facing docs are the source of truth and never reference anything under .claude/; AI config consumes the human docs, not the reverse. Rules are cited only inside .claude/rules/.
severity: must
---
# Never reference AI config from contributor docs

**Rule:** Keep the docs dependency one-way. Contributor-facing files (the repo root README,
CONTRIBUTING, SECURITY, everything under `docs/`, and `tests/*.md`) are the canonical source of truth
and must not link or point to anything under `.claude/` (`CLAUDE.md`, rules, skills, agents, settings).
AI config does the referencing: `CLAUDE.md`, `.claude/skills/`, and `.claude/agents/` read from the
human docs they consume. A rule is cited only inside `.claude/rules/`.

**Why:** a one-way dependency lets humans evolve the conventions without touching AI plumbing, and stops
the circular drift where a contributor doc points at a skill that points back at the doc. The human
files own the standard; the AI files are procedures around it (DRY / single source of truth). This is
the agent-boundary form of `process/own-canonical-sources-of-truth` and
`process/never-duplicate-reference-living-files`.

**Good / Bad:**
```
Bad:  README links `.claude/rules/`; CONTRIBUTING says "see the athletis-commit skill";
      tests/CONVENTIONS.md defers test-design depth to a file under .claude/.
Good: CONTRIBUTING and tests/CONVENTIONS.md stand alone; the athletis-commit skill reads
      CONTRIBUTING.md and the athletis-testing skill reads tests/CONVENTIONS.md, each saying
      "if the two disagree, the human file wins."
```

**Direction test before adding a link:** does a human doc point into `.claude/`? Remove it. Does an AI
file point at a human doc? That is correct, keep it.

**See also:** process/own-canonical-sources-of-truth, process/never-duplicate-reference-living-files, [[never-create-drift]], [[keep-agent-context-files-curated]].

**Enforced by:** review (a contributor doc that links or points into `.claude/` is caught in review).
