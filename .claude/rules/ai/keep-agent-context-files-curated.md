---
name: keep-agent-context-files-curated
description: Agent context files (CLAUDE.md, .cursor/rules, agent instructions, plans, notes) are part of the codebase; keep them curated, small, and high-signal, and reject duplication, stale plans, logs, or secrets.
severity: should
---
# Keep agent context files curated

**Rule:** Agent context files (CLAUDE.md, `.cursor/rules`, agent instructions, generated implementation plans, architecture summaries, scratch notes, prompt fragments) are part of the codebase: curated, reviewed, small, and high-signal. Reject context that duplicates the doctrine, ADRs, README files, or runbooks; contains stale plans or completed task summaries; pastes logs, chat threads, stack traces, or generated reasoning; narrates obvious code; creates a second source of truth; includes secrets or PII; or grows without ownership or pruning. A context file is not a scrapbook; if it is not useful to a future human reviewer or coding agent, remove it.

**Why:** Context files steer every future session, so bloat and duplication actively misdirect agents and drift from the canonical sources. This is the same DRY discipline the rest of the repository follows (`process/never-duplicate-reference-living-files`): one canonical home, everything else links.

**Good / Bad:**
```markdown
<!-- Bad: CLAUDE.md restates the full accessibility policy and pastes last week's task log. -->
## Accessibility (full text copied from rules/accessibility ...)
## Plan from 2026-06-10: step 1 done, step 2 done ...

<!-- Good: a short pointer to the canonical home, no copy. -->
Accessibility discipline lives in `rules/accessibility/`. Load the rules whose triggers match your change.
```

**See also:** process/never-duplicate-reference-living-files, security/never-leak-internal-context-in-responses.

**Enforced by:** review (context-file diffs are read like code), DRY check against canonical homes.
