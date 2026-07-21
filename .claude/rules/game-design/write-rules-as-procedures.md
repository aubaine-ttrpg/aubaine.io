---
name: write-rules-as-procedures
description: Write a codex rule body so it runs in order (purpose, trigger, steps, outcomes, limits) then a short play example, so a new GM runs it without inferring a step.
paths: ["codex/**/*.md"]
severity: should
---
# Write rules as procedures

**Rule:** Order the body of a codex rule so a GM can run it from the top:

1. **Purpose:** the one uncertain thing this rule settles.
2. **Trigger:** the moment at the table that starts it.
3. **Steps:** the actions in sequence, one instruction per step.
4. **Outcomes:** what each result does to the fiction (success, failure, and any partial band the system uses).
5. **Limits:** what this rule does not cover and which rule takes over there.

Close with a short play example that runs the steps once. A reader who has never seen the rule should know when to invoke it, what to roll or spend, and what changes, without guessing.

**Why:** A rule is usable only when the reader knows when and how to use it. The aubaine-content-writer skill states this order (`docs/best-practices.md`, "Make Rules Text Procedural") and its Mechanics Gate checks that the trigger, procedure, roll or resource, and each outcome are all present (`checklists/quality-gates.md`). A step left implicit forces the GM to invent it mid-session, and two tables invent it differently.

**Good / Bad:**
```markdown
Bad:
Forcing a door is a Might check. Hard doors are harder. The GM decides what happens.

Good:
## How it works
Trigger: a character tries to force a stuck or barred door under pressure.
1. The GM sets the target from how the door is held.
2. Roll the action. Meet or beat the target to open it this attempt.
Outcomes: on a success the door gives. On a failure the door holds and the
noise draws whatever is listening.
Limits: a locked mechanism is picked or broken, not forced; see the relevant rule.
```

**See also:** [[keep-the-rule-prose-shape]], [[quarantine-the-why-in-design-notes]], [[cut-mechanics-that-only-restate-common-sense]].

**Enforced by:** the aubaine-content-writer skill (`checklists/quality-gates.md`, Mechanics Gate) + review.
