---
name: guard-generated-data-against-drift
description: Every committed generated data file has a regenerate-and-compare test, so committed bytes can never diverge from the formula that made them.
paths: ["codex/**"]
severity: must
---
# Guard generated data against drift

**Rule:** Every committed generated `data/` file gets a test that regenerates it from the lab and asserts the committed bytes match, exactly what `make regen` would write. A committed file that no longer matches its formula fails the suite. Do not hand-edit a generated file; change the formula and regenerate.

**Why:** A generated file that is edited by hand, or left behind when its formula changes, becomes a second source of truth that drifts from the lab. That is the failure ai/never-create-drift forbids: prefer generated output over a hand-maintained copy. A regenerate-and-compare test makes the committed bytes provably a function of the code, so the commit and `make regen` can never disagree. This depends on the lab staying deterministic, per [[keep-the-lab-deterministic]].

**Good / Bad:**
```python
# Bad: the generated file is committed but nothing checks it still matches the formula;
#      the data/ JSON silently rots the moment the derivation changes.

# Good: regenerate and compare, so a stale commit turns the suite red.
def test_committed_data_matches_the_lab():
    on_disk = (DATA / "roll_distribution.json").read_text()
    assert on_disk == generate.roll_distribution_json()   # the bytes `make regen` would write
```

**See also:** [[keep-the-lab-deterministic]], [[assert-exact-golden-values]], [[always-test-critical-paths]], ai/never-create-drift.

**Enforced by:** pytest (regenerate-and-compare, tied to `make regen`) + review.
