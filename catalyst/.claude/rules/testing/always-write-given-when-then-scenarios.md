---
name: always-write-given-when-then-scenarios
description: Acceptance criteria are Given/When/Then scenarios derived from green Example-Mapping cards, and tests implement them.
paths: ["tests/**", "src/**"]
severity: must
---
# Always write Given/When/Then scenarios

**Rule:** Write acceptance criteria as Given/When/Then scenarios derived from the green examples on the Example-Mapping board (see the athletis-spec skill), then implement each scenario as a test. Map every green card before coding per [[always-example-map-before-coding]]. No vague "test the form".

**Why:** Green examples become Given/When/Then become tests, the BDD chain in `tests/TEST_DESIGN.md`. A concrete scenario names the precondition, the action and the observable outcome, so the test is unambiguous and the ticket and the test say the same thing.

**Good / Bad:**
```gherkin
# Bad: "test the form", no precondition, action or outcome.

# Good: one green example as a scenario.
Given a coach signed in to BF Paris
When she opens client Sophie of BF Paris
Then she sees Sophie's profile
And opening a Lyon client id returns 404
```

**Enforced by:** review (every acceptance criterion traces to a Given/When/Then scenario and a test).
