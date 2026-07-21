---
name: telegraph-danger-and-fail-forward
description: A rule signals severe consequences before they land and writes failure so it moves play forward instead of stalling it.
paths: ["codex/**/*.md"]
severity: should
---
# Telegraph danger and fail forward

**Rule:** Two things every rule that can hurt a character must do.

- **Telegraph:** signal a severe consequence before it lands. Give the fiction a sign the players can read: a warning, a smaller version of the threat, a visible timer. After the hit, the players should be able to say they were warned.
- **Fail forward:** write failure so the situation changes. A miss costs time, ground, a resource, or exposure, or it turns the scene. Failure never just leaves the table where it was and asks again.

State both in the rule body, next to the roll and its outcomes.

**Why:** Severe consequences should be foreshadowed, and failure should move play rather than stop it; the aubaine-content-writer skill lists both practices and the check that after a failure something is different (`docs/best-practices.md`, "Telegraph Danger" and "Failure Changes the Situation"). Its Encounter Gate and Playability Gate check that danger is telegraphed and that waiting or failing changes the situation (`checklists/quality-gates.md`). An untelegraphed consequence reads as the GM punishing the player; a failure that only repeats the roll stalls the session.

**Good / Bad:**
```markdown
Bad:
If you fail the climb, you fall and take damage. Roll again to try the climb next turn.

Good:
The rope is already fraying where it crosses the ledge (the sign).
On a failure you do not fall: you slip to the last hold and the fraying rope
gives you one more attempt before it snaps. Climb now or find another way.
```

**See also:** [[make-choices-meaningful]], [[size-clocks-by-outcome]].

**Enforced by:** the aubaine-content-writer skill (`checklists/quality-gates.md`, Encounter Gate and Playability Gate) + review.
