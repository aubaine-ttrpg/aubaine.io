# 2. Page transitions and the play-once intro

## Status

Accepted.

## Context

Aubaine has a signature entrance: a gold crest that collapses into a purple black hole
and dissipates to reveal the page ("the light forgives / the void remembers"). It should
greet a visitor once, set the tone, and then get out of the way. Replaying it on every
click would be tiresome; never showing it again would lose the identity.

This is a product-level behaviour every Aubaine site shares, not a per-app choice. It is
recorded here, at the monorepo level, so Catalyst (the first adopter) and Almanach adopt
the same pattern rather than each inventing one. Catalyst ADR 0002 deliberately kept
Turbo Drive off; this ADR names the client-navigation layer the sites do use.

## Decision

**The intro plays once per full page load.** On a cold load (first visit or a hard
refresh) the blackhole overlay plays, then reveals the page. The light crest holds and spins for
as long as the page is actually loading (assets + webfonts), then a quick (~0.3s) collapse into the
void reveals the page, so the wait is the real load and the flourish stays short. It is a
framework-agnostic module in `@aubaine/sigil` (`fx/blackhole.js` + `fx/blackhole.css`), re-ported to
vanilla JS; the original mock's React runtime is not carried over. It honours
`prefers-reduced-motion` by skipping straight to the revealed page.

**Swup drives page-to-page transitions.** Navigation swaps the shell's containers (rail, sidebar,
stage) with a short veil/dissipate cross-fade on the stage (`fx/page-transition.css`), so moving
between pages feels continuous. Because navigation is a container swap rather than a full load,
the JavaScript runtime persists and the intro's controller stays connected, so the intro
does not replay between pages. A hard refresh reloads everything and replays it. No
storage flag is needed; the page-load lifecycle is the trigger.

**The intro and Swup controllers live outside the swap container.** In Catalyst they are
Stimulus controllers on `<html>`; the topbar, theme, and logo persist across swaps. Swup swaps
the shell's rail, sidebar, and stage together, so their active states stay correct from the
server render. Stimulus and Live Components reconnect automatically when Swup replaces the
content. Links opt out of Swup with `data-no-swup` (PDF downloads); external, `download`, and
`target=_blank` links are ignored by default.

**Theme switching reuses the same morph.** The header logo is the theme switch: it plays the same
light -> void morph as the intro (and its reverse, void -> light), faster and with no load hold,
flipping the theme while the screen is covered. One engine (`fx/blackhole.js`, parameterised by
direction and speed) drives both the intro and the switch, so the sites share a single animation;
reduced-motion users get an instant switch.

## Consequences

- One entrance behaviour across the monorepo: Almanach wires the same Sigil module and
  the same Swup container contract; it does not re-decide the pattern.
- Navigation feels like a single-page app without adopting a heavy client framework, and
  without contradicting Catalyst ADR 0002 (Turbo stays off; Swup is the chosen layer).
- Server-rendered HTML and progressive enhancement are preserved: forms submit normally,
  and with JS off every page still loads and works (the intro simply never runs).
- Swup interacts with the Live Components editor: it must be exercised so the iframe
  preview and Live mutations survive a container swap. If a route ever misbehaves under
  Swup, the fallback is to exclude it (`data-no-swup`) and let it full-load, rather than
  to fight the swap.
- Frequent hard reloads of a local tool replay the intro each time; that is the intended,
  requested behaviour, and reduced-motion users skip it.
