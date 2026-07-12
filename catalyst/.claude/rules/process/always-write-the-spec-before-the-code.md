---
name: always-write-the-spec-before-the-code
description: Write or refine the ticket spec first (Example Mapping, Given/When/Then, cross-cutting checklist, TDD plan), then implement from it.
severity: must
---
# Always write the spec before the code
**Rule:** No feature or fix starts without a spec. Author or refine the ticket in tasks/ATHLETIS-XXX via the `.claude/skills/athletis-spec` skill first: Example Mapping, then Given/When/Then, then the cross-cutting checklist (`.claude/skills/athletis-spec/references/spec-checklist.md`), then a TDD plan. Tests come from the spec. A bug fix begins with a failing regression test.

**Why:** Spec-first and TDD make behaviour executable instead of hoped-for, and the ticket's Given/When/Then is what the tests assert. Coding from a one-liner skips the cross-cutting concerns (RGPD, security, cost) that the checklist forces you to answer.

**Good / Bad:**
```
Bad:  "build the lead form" then straight into a controller.
Good: tasks/ATHLETIS-123 with Example-Mapped rules, Given/When/Then scenarios, checklist answered, TDD plan.
```

See `testing/always-example-map-before-coding` for the mapping step and `testing/always-write-given-when-then-scenarios` for the scenarios.

**Enforced by:** review (no implementation PR without a spec'd ticket behind it).
