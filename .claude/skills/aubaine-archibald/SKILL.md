---
name: aubaine-archibald
description: Authors and revises project documentation in Aubaine — rule files under rules/, engineering docs under docs/, game-design pages under wiki/, and project-level README files — in the internal engineering-standards voice the repo uses. TRIGGER when — creating a new file under rules/, docs/, or wiki/; revising any existing markdown in those folders; updating README.md, docs/README.md, or wiki/README.md; voice-polishing any markdown prose in the repo; restructuring rules or docs layouts. SKIP when — writing code, commit messages, PR descriptions, or anything outside the documentation folders above.
paths:
  - rules/*.md
  - docs/*.md
  - docs/**/*.md
  - wiki/*.md
  - wiki/**/*.md
  - README.md
---

# Archibald

Project documentation is written as **internal engineering standards**. The same prose serves two audiences at once — developers on the team and coding agents that load these files as context.

Three distinct registers sit under this shared voice:

| Folder | Register | Aim |
|---|---|---|
| [`rules/`](../../../rules) | **Project standards** | Enforceable, opinion-forward directives. Clarity, enforceability, quick decisions. |
| [`docs/`](../../../docs) | **Subsystem reference** | Teach the shape of the code: mental model, file layout, flow, gotchas. Per [Rule 08](../../../rules/08-technical-documentation.md). |
| [`wiki/`](../../../wiki) | **Game-design reference** | Mechanics, math, reference tables for Aubaine the TTRPG. |

The voice is shared across all three. The content each register carries is different.

## Voice

### Principles (shared)

- **Declarative, not imperative.** State what the standard or mechanic *is*, not what a reader should do. The document records how things are; it does not instruct anyone.
- **No direct reader address.** Prose never says "you", "your", or "should you". Third-person reference to the audience ("another developer", "a newcomer", "the GM", "the player") is fine when it clarifies the explanation.
- **No meta-instructions.** No "your task is…", "you should generate…", "this document explains how to…". The document says what is.
- **No AI-specific framing.** Phrases like "for an agent", "when an assistant", "AI-readable" do not appear. Coding agents are a consumer, not an addressee. [`CLAUDE.md`](../../../CLAUDE.md) is referenced by filename when relevant.
- **Positive form.** Every `never` or `don't` anchors to a concrete positive statement in the same breath. Concepts that were never on the table are not negated pre-emptively.
- **No speculation.** Prose describes the current state. Hypothetical future features and "can be added later…" are omitted.
- **Rationale, not narration.** Reasons are stated briefly inline. Process narration ("we decided to…", "after evaluating…") is stripped.
- **Examples over prose.** Tables, bullet lists, code fences, and worked examples communicate faster than paragraphs.

### Register: `rules/` — project standards

Rule files are **standards, not documentation**. They are opinion-forward, short, and enforceable. The upstream stack (PHP, Symfony, Doctrine, Twig, Tailwind, PHPUnit, and any later addition) is treated as prerequisite knowledge; a rule does not teach or summarize it.

Shape a rule optimizes for: clarity, enforceability, quick decision-making, consistency across developers and agents.

Prefer:

- "Entities use single-value primary keys."
- "Services are private by default."
- "Cascade is reserved for compositions."
- "Choose attributes over XML and YAML for mapping."
- "Controllers extend `AbstractController`."

Avoid:

- Long introductions.
- Tutorials or walkthroughs.
- Feature overviews ("Symfony provides…", "Twig lets you…").
- Generic framework explanation.
- Verbatim restating of official documentation.

Upstream conventions are **linked** rather than restated. Restating a convention is justified only when it cements a project-specific choice (e.g. "attributes over XML/YAML, project-wide") or when enforcement requires the exact spelling (e.g. whitespace inside Twig delimiters).

### Register: `docs/` — subsystem reference

Engineering docs teach one topic end-to-end: the mental model, where the code lives, how a typical operation flows, and the constraints that would otherwise trip up a newcomer. Structure follows the subject per [Rule 08](../../../rules/08-technical-documentation.md); no rigid template applies.

The ingredients to draw from: purpose, mental model, file layout, end-to-end flow, extending it, gotchas. A page uses the ones that clarify its subject and skips the rest.

Rationale and tradeoffs are woven inline where they clarify the *how*. No separate "decisions with rejected alternatives" catalog.

### Register: `wiki/` — game-design reference

Wiki pages describe game mechanics as a fixed system. The GM and players are named by role, not addressed.

Subject-driven structure. Useful shapes: overview in one or two sentences, reference tables (damage bands, distance bands, status durations), definition lists for terms, worked dice-resolution examples.

## Voice by example

Rules (register-specific):

| ✗ Avoid | ✓ Prefer |
|---|---|
| "Symfony lets you autowire dependencies by type-hinting." | "Services are private by default; dependencies are type-hinted and injected." |
| "Twig has filters, tests, and functions." | "Filter calls have no space before the parenthesis: `name\|default('Fabien')`." |
| "Doctrine supports composite primary keys, but we avoid them." | "Single-value primary keys only. Composite natural uniqueness is expressed as a unique constraint." |
| "This section explains how forms work in Symfony." | (no section preamble — heading + content.) |

Engineering standards (shared — applies everywhere):

| ✗ Avoid | ✓ Prefer |
|---|---|
| "You must run `make test` before committing." | "The pre-commit hook runs `make test` on PHP-adjacent commits." |
| "An agent should read the frontmatter first." | "The frontmatter states each rule's domain." |
| "This page explains how testing works." | (no preamble — heading + content.) |
| "We recommend using attributes." | "Entity mapping uses PHP attributes." |

Game-design documentation:

| ✗ Avoid | ✓ Prefer |
|---|---|
| "You roll a d20 and add your Ability." | "Resolution rolls a d20 and adds the acting Ability plus the relevant Aptitude." |
| "The GM should decide the DC." | "The DC is set by the GM against the opposition's threat band." |
| "Unleashed skills cost Energy, which you spend." | "Unleashed skills cost 1 Energy per use; Specific skills cost no Energy and are gated by memorization." |

## Governing rules

Documents authored by this skill comply with:

- [Rule 00 — Rules System](../../../rules/00-rules-system.md) — rule file format, naming, numbering.
- [Rule 01 — Commit Convention](../../../rules/01-commit-convention.md) — gitmoji + imperative sentence-case subject. Doc-page edits use `📝`; new rule files use `✨`; retired files use `🔥`.
- [Rule 08 — Technical Documentation](../../../rules/08-technical-documentation.md) — `docs/` filename convention, page structure, maintenance.

## Workflows

### New rule file — `rules/NN-<slug>.md`

1. The next number is one past the highest existing rule. Numbers are monotonic and never reused.
2. The slug names what the rule covers, not its motivation. `01-commit-convention.md` ✓. `01-clean-history-matters.md` ✗.
3. Frontmatter:
   ```yaml
   ---
   name: NN-kebab-case-slug
   description: One activation-oriented sentence stating the domain and typical trigger keywords.
   ---
   ```
4. Opening heading: `# Rule NN — Title`.
5. Body in the `rules/` register: concise project standards. Framework concepts are assumed. Upstream references are linked, not restated, except where restating cements a project-specific choice.
6. Phrasing leans on the "Prefer" list above: "Use X when…", "Do not do Y…", "Choose A over B when…", "X in this project must…".
7. Commit subject: `✨ Add Rule NN — <Title>`.

### New engineering doc — `docs/<TOPIC>.md`

1. Filename: `UPPER_SNAKE_CASE.md`. The slug names the **topic** (a noun): `STACK.md`, `FRONTEND.md`, `DATABASE.md` ✓. `APRIL_WORK.md`, `MY_IDEAS.md` ✗.
2. The page teaches one topic end-to-end. [Rule 08](../../../rules/08-technical-documentation.md) lists the ingredients to draw from — purpose, mental model, file layout, end-to-end flow, extension points, gotchas. Structure follows the subject; no template is mandated.
3. Rationale surfaces inline where it clarifies a mechanism.
4. Out of scope: file-by-file change lists, commit hashes, step-by-step how-tos, per-developer environment notes.
5. [`docs/README.md`](../../../docs/README.md) is updated to index the new page.
6. Commit subject: `📝 Add docs/<TOPIC>.md — <one-line summary>`.

### New game-design page — `wiki/<TOPIC>.md`

1. Filename: `UPPER_SNAKE_CASE.md`, matching the `docs/` convention. The slug is the game-design topic: `DICE_RESOLUTION.md`, `SKILLS.md`, `CHARACTER_PROGRESSION.md`, `STATUS_EFFECTS.md`, `TAGS.md`.
2. Subject-driven structure: one or two sentences of overview, then reference-style content — tables for bands, definition lists for terms, worked examples for resolution.
3. The game is treated as a fixed system. Mechanics are stated in third person; the GM and players are named by role, not addressed.
4. [`wiki/README.md`](../../../wiki/README.md) is updated to index the new page when the wiki grows past a single topic.
5. Commit subject: `📝 Add wiki/<TOPIC>.md — <one-line summary>`.

### Revising an existing file

1. The file is edited in place. Old and new variants do not coexist.
2. When a prior decision is reversed, a dated note explains what changed and why, and the surrounding text is revised. The original reasoning remains visible so the history of thinking stays intact.
3. Commit subject: `📝 <Imperative sentence-case>` describing the change.

### README files

Repo-root [`README.md`](../../../README.md) is a setup-and-orientation document. Imperative setup commands (`composer install`, `make dev`) are the appropriate register for that genre and stay as-is. Non-setup prose — project overview, docs index, license — follows the shared voice principles.

`docs/README.md` and `wiki/README.md` are index + style-guide pages for their folders. They list the pages present and point to the governing rule or convention.

## Pre-flight checks

Before any commit, the author verifies:

- Frontmatter is present where required (rule files) and matches the schema.
- Every `never` / `don't` is anchored to a positive statement.
- No sentence directly addresses the reader or describes the document itself.
- Rule files do not explain framework concepts or restate upstream docs without a project-specific reason.
- Every markdown link resolves to an existing path.
- The commit plan (gitmoji + subject + files) is presented for approval before `gitk add` / `gitk commit`, per the `CLAUDE.md` commit protocol.
