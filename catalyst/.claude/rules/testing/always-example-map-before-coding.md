---
name: always-example-map-before-coding
description: Run Example Mapping before writing code and resolve every red Question with the user before fixing acceptance criteria.
paths: ["tests/**", "src/**"]
severity: should
---
# Always example map before coding

**Rule:** Before writing code, run Example Mapping (🟦 Rule, 🟩 Example, 🟥 Question, 🟨 Story) for the story, and resolve every red Question with the user before acceptance criteria are fixed. An unanswered Question leaves the ticket blocked. The green examples then drive [[always-write-given-when-then-scenarios]].

**Why:** Mapping rules, examples and questions surfaces the ambiguity and the missing cases up front, which is far cheaper than discovering them mid-build. The card types and the rule that every red card is resolved first live in `tests/TEST_DESIGN.md`.

**Good / Bad:**
```text
Bad: code straight from "as a coach I manage clients".

Good: map it first.
🟨 Story: a coach manages her clients
🟦 Rule: a coach sees only her own org's clients
🟩 Example: Paris coach opens Lyon client id → 404
🟥 Question: can an Assistant edit a payment?  → ask the user first
```

**Enforced by:** review (ticket shows mapped cards and no open red Question before build).
