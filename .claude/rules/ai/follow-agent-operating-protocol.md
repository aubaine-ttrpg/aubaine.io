---
name: follow-agent-operating-protocol
description: AI agents discover applicable rules dynamically, update canonical owners, check for contradictions, and keep durable edits small.
severity: must
---
# Follow the agent operating protocol

**Rule:** AI coding agents must discover applicable rules dynamically from `rules/` frontmatter, must
not hardcode dynamic lists, must not create context dumps, and must update canonical sources instead of
duplicating rules. Before adding policy, check for an existing owner and contradictions. Prefer small,
durable edits over verbose generated explanations.

**Why:** Agents amplify foundation defects: a copied list, stale explanation, or duplicate policy is
then loaded into future sessions and repeated. The repository foundation has to be safe for future
humans and future agents.

**Good / Bad:**
```text
Bad:  Add a new "agent rules" document that repeats existing process rules.
Good: Update the existing rule or canonical README, then link it from the procedure that needs it.
```

**See also:** process/own-canonical-sources-of-truth, process/never-hardcode-dynamic-lists, process/never-context-dump-in-permanent-docs, [[never-create-drift]], [[keep-agent-context-files-curated]].

**Enforced by:** review of AI-authored code, docs, skills, agent instructions, and task changes.
