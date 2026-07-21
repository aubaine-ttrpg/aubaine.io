---
name: no-ai-tells
description: Committed markdown reads like finished game text, plain and concrete, with no marketing, filler, or em/en-dashes, and all reasoning quarantined in a non-normative Design Notes section.
paths: ["**/*.md"]
severity: must
---
# No AI-tells

**Rule:** Committed markdown reads like finished game text, not generated copy. Apply the
aubaine-content-writer skill's `docs/no-ai-tells.md` (forbidden phrases, replacements, and
sentence-level tests) to every markdown file before you commit it.

- Plain language, short sentences, concrete nouns, active verbs. State the definite fact, not
  "may/might" where a rule is meant.
- No em-dashes or en-dashes anywhere. Use commas, colons, parentheses, or separate sentences.
- The rule body stands alone: understandable with no outside context, and no cross-game
  references such as "like D&D" or "JRPG-style".
- Intent, reasoning, and comparisons go only in a separated `Design Notes` section marked
  non-normative. Keep the firewall: normative text above, reasoning below, never mixed.

**Why:** Generated copy leaks tells (hedging, marketing cadence, dash-joined clauses) that read as
unfinished and untrustworthy in published game text. A player must act on a rule without decoding
intent, so the reasoning is quarantined in Design Notes and the rule states the fact. The
aubaine-content-writer skill owns the full forbidden-phrase list, so this rule points at it rather
than copying it.

**Good / Bad:**
```markdown
Bad:  This powerful, streamlined system might let you push your luck, a real game-changer.

Good: Push your luck: reroll one die. On a 1, the action fails.

      Design Notes (non-normative): push-your-luck keeps the tension high without a
      separate stamina track.
```

**See also:** [[scrutinize-ai-coding-tells]], [[never-create-drift]], process/never-write-redundant-content.

**Enforced by:** the aubaine-content-writer skill (`docs/no-ai-tells.md`) + review.
