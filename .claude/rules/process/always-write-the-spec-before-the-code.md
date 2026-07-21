---
name: always-write-the-spec-before-the-code
description: Write the spec first (Example Mapping, Given/When/Then scenarios, cross-cutting concerns, test plan), then implement from it.
severity: must
---
# Always write the spec before the code
**Rule:** No feature or fix starts without a spec. Write it first: map the examples, turn them into Given/When/Then scenarios, answer the cross-cutting concerns (security, cost, accessibility), then plan the tests. Tests come from the spec. A bug fix begins with a failing regression test that reproduces it.

**Why:** Spec-first and TDD make behaviour executable instead of hoped-for, and the scenarios' Given/When/Then are what the tests assert. Coding from a one-liner skips the cross-cutting concerns (security, cost, accessibility) that a written spec forces you to answer.

**Good / Bad:**
```
Bad:  "build the page editor" then straight into a controller.
Good: an Example-Mapped spec with Given/When/Then scenarios, cross-cutting concerns answered, and a test plan, then the controller.
```

See `testing/always-example-map-before-coding` for the mapping step and `testing/always-write-given-when-then-scenarios` for the scenarios.

**Enforced by:** review (no implementation without a written spec behind it).
