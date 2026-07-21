---
name: keep-the-rule-prose-shape
description: Every codex rule carries the standard YAML front matter and reads what it is, how it works, its outcomes, and a short at-the-table example, in that order.
paths: ["codex/**/*.md"]
severity: should
---
# Keep the rule prose shape

**Rule:** Give every codex rule the same skeleton so any file is scannable mid-session.

- Open with YAML front matter carrying `title`, `slug`, `version`, `updated`, `tags`, `related`, and `summary`. The `slug` equals the filename without `.md`. The `version` is semantic. The `summary` states what the rule does in one sentence.
- Then the body, in order: what it is, how it works, its outcomes, and a short at-the-table example. Match the headings the existing dice-and-modifiers rules use.
- Reasoning goes last, in a separated Design Notes section, and is not part of the rule.

**Why:** `codex/README.md` fixes this shape: a rule reads on its own as what it is, how it works, its outcomes, and a short play example, with the why kept apart. One shape lets a GM jump to Outcomes or the example without rereading the whole file. Semantic versioning (semver.org) in `version` lets `related` rules and generated indexes track a change. The aubaine-content-writer skill's Accessibility Gate checks that headings are clear and the material scans under time pressure (`checklists/quality-gates.md`).

**Good / Bad:**
```markdown
Bad: a rule with no front matter, one wall of prose, and the reasoning mixed into the steps.

Good:
---
title: Grapple
slug: grapple
version: 0.1.0
updated: 2026-07-21
tags: [combat, control]
related: [turn-structure]
summary: Pin an enemy so they cannot move until they break free.
---
# Grapple
## What it is
## How it works
## Outcomes
## At the table
## Design Notes
```

**See also:** [[write-rules-as-procedures]], [[quarantine-the-why-in-design-notes]], ai/no-ai-tells.

**Enforced by:** the aubaine-content-writer skill (`checklists/quality-gates.md`, Accessibility Gate) + review.
