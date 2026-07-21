---
name: always-example-map-before-coding
description: Run Example Mapping before writing code and resolve every red Question with the user before acceptance criteria are fixed.
severity: should
---
# Always example map before coding

**Rule:** Before writing code, run Example Mapping (🟦 Rule, 🟩 Example, 🟥 Question, 🟨 Story) for the story, and resolve every red Question with the user before acceptance criteria are fixed. An unanswered Question leaves the ticket blocked. The green examples then drive [[always-write-given-when-then-scenarios]].

**Why:** Mapping rules, examples, and questions surfaces the ambiguity and the missing cases up front, which is far cheaper than discovering them mid-build. Every red card is resolved before the build starts.

**Good / Bad:**
```text
Bad: code straight from "as an author I edit pages".

Good: map it first.
🟨 Story: an author edits a book's pages
🟦 Rule: renaming a page updates the content JSON export
🟩 Example: rename "Combat" to "Combat basics" -> the export shows the new title
🟥 Question: what happens to a page's skill-tree links when it is renamed? -> ask the user first
```

**See also:** [[always-write-given-when-then-scenarios]], [[always-cover-the-five-categories]].

**Enforced by:** review (the ticket shows mapped cards and no open red Question before the build).
