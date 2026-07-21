---
name: never-context-dump-in-permanent-docs
description: Permanent documentation states current truth and durable rationale; it must not include chat history, previous-agent mistakes, stale implementation notes, or debugging stories.
severity: must
---
# Never context-dump in permanent docs

**Rule:** Permanent repository documentation must state current policy, current process, current product
truth, or durable rationale. Do not mention previous agents, chat history, one-off mistakes, temporary
refactors, debugging stories, stale plans, or why a document was added after a past failure. Rewrite
durable rationale as current-state explanation, move long-term decisions to ADRs, move task-specific
history to task files, and delete everything else.

**Why:** Context dumps make future readers solve old conversations instead of current work. They also
train coding agents to preserve stale implementation chatter as doctrine.

**Good / Bad:**
```text
Bad:  "We added this because the previous agent forgot to update CLAUDE.md."
Good: "Update the canonical owner, then update references that point to it."
```

**See also:** [[own-canonical-sources-of-truth]], [[never-write-redundant-content]], ai/keep-agent-context-files-curated.

**Enforced by:** review of permanent documentation and agent context files.
