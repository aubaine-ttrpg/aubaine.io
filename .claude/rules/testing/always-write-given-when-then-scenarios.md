---
name: always-write-given-when-then-scenarios
description: Acceptance criteria are Given/When/Then scenarios derived from green Example-Mapping cards, and each scenario becomes a test.
severity: must
---
# Always write Given/When/Then scenarios

**Rule:** Write acceptance criteria as Given/When/Then scenarios derived from the green examples on the Example-Mapping board, then implement each scenario as a test. Map every green card before coding, per [[always-example-map-before-coding]]. No vague "test the editor".

**Why:** Green examples become Given/When/Then scenarios become tests. A concrete scenario names the precondition, the action, and the observable outcome, so the test is unambiguous and the ticket and the test say the same thing.

**Good / Bad:**
```gherkin
# Bad: "test the editor", no precondition, action, or outcome.

# Good: one green example as a scenario.
Given a book with a page titled "Combat"
When the author renames the page to "Combat basics"
Then the page title reads "Combat basics"
And the book's content JSON export records the new title
```
```python
# Good: the same shape in pytest, arrange then act then assert.
def test_meeting_a_mid_target_at_no_modifier_is_a_coin_flip():
    dist = dice.roll_distribution()      # Given a base 3d12 keep-3 roll
    p = dice.p_at_least(dist, 20)        # When we need at least 20
    assert p == Fraction(1, 2)           # Then it is exactly even
```

**See also:** [[always-example-map-before-coding]], [[always-cover-the-five-categories]].

**Enforced by:** review (every acceptance criterion traces to a Given/When/Then scenario and a test).
