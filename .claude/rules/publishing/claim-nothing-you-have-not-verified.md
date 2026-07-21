---
name: claim-nothing-you-have-not-verified
description: Never claim balance, playtesting, print compliance, accessibility conformance, or legal compatibility without evidence; write the factual state and the next step instead.
severity: must
---
# Claim nothing you have not verified

**Rule:** Do not claim balance, fairness, extensive playtesting, print compliance, accessibility conformance, or legal or system compatibility unless the artifact carries the evidence for it. Until it does, write the factual state and name the next step, not the aspiration:

- "Prototype rule; not yet table-tested."
- "Contrast checked by script; manual audit pending."
- "Exported with a PDF/X-4 preset; printer approval pending."

This binds every place a claim can appear: codex prose, front matter, back-cover and marketing copy, commit messages, and PR text.

**Why:** The aubaine-visual-supports-designs skill forbids these claims without evidence and gives the factual-language patterns to use instead (`standards/ttrpg_craft.md`, "Playtest and balance claims"). Its production scripts are triage tools only: PDF/X, PDF/UA (ISO 14289), and accessibility conformance can be asserted only after the right validators and a manual review, and only the printer or platform can approve final print acceptance (`standards/output_production_pdf.md`). "Balanced" is a claim the codex lab supports only through the exact-math evidence it generates, never by assertion. An unbacked "fully playtested" or "WCAG AA compliant" is both a false claim and an AI-tell (ai/no-ai-tells).

**Good / Bad:**
```markdown
Bad: claims nobody verified.
> Fully playtested and perfectly balanced. WCAG AA compliant. Print-ready for any press.

Good: the true state, with the next step named.
> Prototype rules; not yet table-tested. Contrast checked by script; manual audit pending.
> Exported with a PDF/X-4 preset; printer approval pending.
```

**See also:** [[respect-the-dual-license-and-credits]], [[keep-pdf-and-export-clean]], game-design/write-rules-as-procedures, ai/no-ai-tells.

**Enforced by:** review + the aubaine-visual-supports-designs skill (`standards/ttrpg_craft.md`, playtest and balance claims).
