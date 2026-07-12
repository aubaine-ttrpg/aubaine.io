---
name: never-write-redundant-content
description: Zero tolerance for redundancy. Every sentence, comment, table row, and line must add something the reader does not already have; never restate the obvious or what is said elsewhere, and never pad.
severity: must
---
# Never write redundant content

**Rule:** Zero tolerance for redundancy. Every sentence, comment, table row, and line of code must add
something the reader does not already have. Never restate what is already stated (in this file or
another), never state the obvious, never pad. If a line can be deleted without losing information,
delete it.

**Why:** Redundant content is pure cost: more to read, more to keep in sync, more that drifts, and it
buries the signal. This is the DRY discipline of [[never-duplicate-reference-living-files]] (which
governs duplication ACROSS files) applied WITHIN a single file or change.

**Good / Bad:**
```
Bad:  // increment i by one
      $i++;
Bad:  a table row "🚑 ambulance | critical fix" when 🚑 already means that in standard gitmoji.
Good: comment only the non-obvious why; list only what is genuinely project-specific.
```

**See also:** [[never-add-unrequested-or-meta-context]], [[never-duplicate-reference-living-files]], ai/keep-agent-context-files-curated, ai/scrutinize-ai-coding-tells.

**Enforced by:** review (delete anything that adds nothing); the `athletis-ai-tell-remover` skill.
