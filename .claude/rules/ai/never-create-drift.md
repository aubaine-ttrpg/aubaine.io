---
name: never-create-drift
description: Never write an enumeration that mirrors an authoritative source (a folder's files, a table's entries, a set's members, another doc's list). Reference the single source; a second copy drifts the moment the source changes.
severity: must
---
# Never create drift

**Rule:** Before writing an inventory, find the canonical owner and link it. Do not re-list a folder's
files, a table's entries, a set's members, config keys, packages, tasks, ADRs, rules, skills, agents,
scripts, workflows, or another document's list in a second place. This includes prose: state no hardcoded count of a
set's members (a number that breaks the moment the set changes) and no reading-order list. If you
are tempted to mirror a real set, name the source instead of its members.

**Why:** every enumerated copy is a promise to update two places forever, and the promise is always
broken; the source gains an entry, the copy goes stale, and a reader trusts the stale copy. This is the
AI-agent form of `process/never-duplicate-reference-living-files` and `process/never-write-redundant-content`,
and it is the single most repeated mistake an agent makes.

**Good / Bad:**
```
Bad:  .claude/rules/README.md lists every rule; a skill restates the gitmoji set;
      CLAUDE.md lists every skill with its description; "the custom 🤖 and 🐳 emoji" named in two files.
Good: "see CONTRIBUTING.md"; "skills live in .claude/skills/ (the harness lists them)";
      "rules load from .claude/rules/ frontmatter". The set has one home; everything else links to it.

Bad:  prose hardcodes a count ("the six characteristics", "four roll types") or a fixed
      reading order for the books; the number goes stale the moment the set grows or shrinks.
Good: name the concept and point to its one source ("the characteristics live in codex/"); let
      the reader count the members there. No count and no reading order committed to prose.
```

**Test before writing any list:** does this set already exist somewhere authoritative? If yes, link it; do not retype it.

**See also:** process/never-hardcode-dynamic-lists, process/own-canonical-sources-of-truth, process/never-duplicate-reference-living-files, process/never-write-redundant-content, [[keep-agent-context-files-curated]], [[scrutinize-ai-coding-tells]].

**Enforced by:** review; the aubaine-content-writer skill.
