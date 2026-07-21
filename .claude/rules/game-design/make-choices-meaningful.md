---
name: make-choices-meaningful
description: A rule offers a choice only when the options carry real, telegraphed tradeoffs; cut or merge options that resolve to the same result.
paths: ["codex/**/*.md"]
severity: should
---
# Make choices meaningful

**Rule:** When a rule presents options, each option must lead somewhere different, and the difference must be visible before the player picks.

- Give each option its own cost, risk, reward, or consequence. State the tradeoff in the rule.
- Cut or merge any option that reaches the same result as another. A choice with one correct answer is not a choice.
- Name the tradeoff in player-facing terms so the decision happens with open eyes, not after the fact.

**Why:** A choice matters when players can compare risk, cost, reward, and consequence; if every path leads to the same result, the aubaine-content-writer skill says to cut or change it (`docs/best-practices.md`, "Make Choices Meaningful"), and its Playability Gate checks that choices have consequences (`checklists/quality-gates.md`). A no-op option adds reading and a decision beat while changing nothing at the table.

**Good / Bad:**
```markdown
Bad:
On your turn you may attack cautiously or attack boldly. Either way you roll
your attack and deal your weapon's damage.

Good:
On your turn choose one:
- Press: take the shot at your normal target, and the enemy gets a free swing back.
- Hold: give up the shot to move without drawing a free swing.
```

**See also:** [[telegraph-danger-and-fail-forward]], [[size-clocks-by-outcome]], [[cut-mechanics-that-only-restate-common-sense]].

**Enforced by:** the aubaine-content-writer skill (`checklists/quality-gates.md`, Playability Gate) + review.
