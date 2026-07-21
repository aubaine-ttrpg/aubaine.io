---
name: size-clocks-by-outcome
description: Name a progress clock after the outcome it tracks, not the method used to fill it, and size its segments to how big that outcome is.
paths: ["codex/**/*.md"]
severity: should
---
# Size clocks by outcome

**Rule:** When a rule uses a progress clock to track pressure or a task over time:

- Name the clock after the outcome it delivers when it fills, not the action that fills it. "Guards alerted" reads at the table; "sneak past guards" does not.
- Size the segment count to how large the outcome is: fewer segments for a simple task, more for involved pressure, most for a major or long threat. Match the bands the aubaine-content-writer skill gives (`docs/best-practices.md`, "Use Clocks for Complex Pressure"); do not restate the numbers here.
- State in the rule what filling the clock does to the fiction.

**Why:** A clock named after the method hides what is at stake and does not tell the table what changes when it fills; the aubaine-content-writer skill's check is to name clocks after obstacles or outcomes, not methods (`docs/best-practices.md`). An outcome name doubles as the stakes the players read while they decide whether to keep pushing.

**Good / Bad:**
```markdown
Bad:
Sneak Past Guards [....]
Fill a segment each time the party moves through a patrolled hall.

Good:
Guards Alerted [....]
Fill a segment on each failed stealth roll or loud noise. When it fills, the
watch converges on the party's last known position.
```

**See also:** [[telegraph-danger-and-fail-forward]], [[make-choices-meaningful]].

**Enforced by:** the aubaine-content-writer skill (`docs/best-practices.md`, "Use Clocks for Complex Pressure") + review.
