---
name: write-an-adr-for-significant-decisions
description: Record any significant or hard-to-reverse technical decision as an ADR in docs/adr/, then link it instead of restating the rationale.
paths: ["**"]
severity: should
---
# Write an ADR for significant decisions
**Rule:** Any significant or hard-to-reverse technical decision gets an ADR in docs/adr/, numbered 0001 and up, using docs/adr/template.md. Write it via the `.claude/skills/athletis-adr` skill. Link the ADR from the rules that need it, instead of restating the rationale, per [[never-duplicate-reference-living-files]].

**Why:** ADR practice (Michael Nygard) keeps the status, context, decision, and consequences in one durable place, so the "why" survives the people who were in the room. A chat thread does not, and a decision with no record gets relitigated.

**Good / Bad:**
```
Bad:  "we picked Mollie, it was discussed on Slack" and nothing written down.
Good: docs/adr/0004-payments-and-pricing.md with Status, Context, Decision, Consequences.
```

**Enforced by:** review (a significant choice without an ADR does not merge).
