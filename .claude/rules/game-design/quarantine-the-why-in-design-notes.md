---
name: quarantine-the-why-in-design-notes
description: Reasoning, comparisons, and rejected alternatives live only in a trailing non-normative Design Notes section; the rule body states the definite fact.
paths: ["codex/**/*.md"]
severity: must
---
# Quarantine the why in Design Notes

**Rule:** Split every codex rule into two zones and never mix them.

- The **rule body** states what happens. It gives triggers, steps, outcomes, costs, and limits as definite facts. State the rule, not the intent behind it.
- A trailing **Design Notes** section holds the reasoning: why the numbers are what they are, comparisons, tradeoffs weighed, and alternatives rejected. Label it non-normative. Nothing here binds play.

A GM must be able to run the whole rule without reading Design Notes. A designer must be able to change the reasoning without touching the rule.

**Why:** `codex/README.md` sets this firewall: what it is, how it works, and outcomes are the rule; anything about why it works is separated and is not part of the rule. A player acts on the fact and cannot pause to decode intent, so intent lives below the line. The aubaine-content-writer skill's no-ai-tell guide flags explaining the design in place of using it and hedging ("may", "might") where a definite fact belongs (`docs/no-ai-tells.md`). Reasoning phrased as a rule reads as optional and splits tables.

**Good / Bad:**
```markdown
Bad:
## How it works
We wanted fights to stay tense, so a downed character probably dies after a
round or two, which felt more dramatic than a long timer.

Good:
## Outcomes
A downed character dies at the start of their next turn unless someone stabilises them first.

## Design Notes
Non-normative. A one-turn window keeps a downed ally an emergency, not a
background timer, and rejects the longer death saves other systems use.
```

**See also:** [[keep-the-rule-prose-shape]], [[write-rules-as-procedures]], ai/no-ai-tells.

**Enforced by:** the aubaine-content-writer skill (`docs/no-ai-tells.md`) + review.
