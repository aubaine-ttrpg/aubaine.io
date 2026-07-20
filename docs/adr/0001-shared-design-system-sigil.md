# 1. Shared design system (Sigil) and Storybook

## Status

Accepted.

## Context

Aubaine has two web surfaces that must look like one product: Catalyst (the Symfony
authoring tool) now, and Almanach (the Astro public site) later. Until now there was
no shared UI layer. Catalyst carried two independent stylesheets: `print.css` (the
book PDFs) and `app.css` (the editor), each with its own colours and fonts, and there
was no place to see a component in isolation or to prove it works in light and dark.

The printed books are the oldest and most finished surface, so their fonts and gold
are the real brand. Any shared system has to take those as the source of truth and,
critically, must not change how the PDFs render.

This ADR is monorepo-wide: it introduces a new top-level project alongside `codex/`,
`content/`, `catalyst/`, and `almanach/`. Cross-cutting decisions like this one live
in the root `docs/adr/`; Catalyst-internal decisions stay in `catalyst/docs/adr/`.

## Decision

**A shared design-system package, `@aubaine/sigil`, in `sigil/`.** It owns the design
tokens (a curated gold ramp and a void/purple ramp, the font roles, and light/dark
semantic tokens), the base and component CSS (BEM, styled only through tokens), the
logo marks, and the light-to-void intro effect. A single `web.css` entry is what the
websites import.

**The PDF is the token source of truth, and it keeps rendering identically.** The gold,
ink, frame, and font tokens are defined once in Sigil with the exact values the PDF has
always used. Catalyst's `print.css` drops its own `:root` and imports
`@aubaine/sigil/tokens/brand.css` + `tokens/fonts.css`; the computed values are
unchanged, so the printed pages do not move a pixel. (The content-addressed PDF cache,
Catalyst ADR 0001, re-keys because the print bundle's bytes change; that is expected
and self-invalidating.) Game-data colours (domains, characteristics, papers) stay owned
by the PHP enums and are never copied into CSS.

**Delivery by `file:` dependency.** Catalyst and, later, Almanach declare
`"@aubaine/sigil": "file:../sigil"` and `@import '@aubaine/sigil/...'`. This keeps the
per-project install flow (no root workspace restructure) while giving a real package
name for the future.

**Storybook previews Sigil, framework-agnostically.** Stories use the HTML renderer, so
the same catalog documents the CSS both Twig and Astro consume, with a light/dark
toolbar and the accessibility addon.

**PDF-component stories are generated from Catalyst, not hand-written.** A console
command, `app:design:dump`, renders the real print templates for each page type and
dumps the PHP enum colours to `sigil/fixtures/`; the stories load those fixtures. A
story can therefore never drift from the Twig macros or the enums.

## Consequences

- One home for the brand: a token changes in one file and both PDFs (values) and
  websites follow.
- The websites gain a real visual identity, built on the PDF fonts and gold plus the
  aubaine-v1 dark-first aesthetic, in light and dark.
- Catalyst is the first adopter and the test bed; Almanach reuses the same package.
- Sigil adds Storybook tooling (Vite based) to the repo. It is dev-only and previews
  the library; it ships nothing to production.
- Regenerating fixtures needs built Catalyst assets, so `app:design:dump` runs after
  `npm run dev`. The fixtures are reproducible, not authored.
- `print.css` changes are values-identical only; the guard is a rendered-page check,
  not a CSS-text diff.
