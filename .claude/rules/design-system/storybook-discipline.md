---
name: storybook-discipline
description: Keep a story per component and meaningful state with uniform autodocs tags, the a11y addon scoped to the story root, a theme toolbar that mirrors production, and print stories that read generated fixtures.
paths: ["sigil/stories/**", "sigil/.storybook/**"]
severity: should
---
# Storybook discipline

**Rule:**

- Give each component a story, with a case for every meaningful state (variants, disabled, hover-driven, empty). A state that has distinct CSS has a case that shows it.
- Tag component stories `tags: ['autodocs']`, spelled the same everywhere, so each gets a generated docs page and the catalog stays uniform.
- Keep the a11y addon on and scoped to the story root (`a11y: { context: '#storybook-root' }`), so axe checks the story, not the Storybook chrome.
- The toolbar theme swap stamps `data-theme` and `color-scheme` on `<html>` exactly as production does, so a story exercises the real light/dark token swap and not a Storybook-only shim.
- Print and game-data stories read the generated, git-ignored `fixtures/` (produced by `app:design:dump`); they never hand-author print markup or enum colours.

**Why:** a story per component and state is what makes Storybook a catalog Twig and Astro authors can trust, and a uniform autodocs tag keeps that catalog consistent. Scoping a11y to the story root keeps its findings about the component. Mirroring the production theme stamp means the swap under test is the one that ships (see [[theme-via-tokens]]). Reading fixtures from `app:design:dump` is what keeps print stories from drifting from the real Twig macros and the PHP enums; a hand-copied swatch or nameplate is a second source that goes stale (docs/adr/0001-shared-design-system-sigil.md; [[the-pdf-is-the-token-source-of-truth]]).

**Good / Bad:**
```js
// Bad: no autodocs tag, and the print swatch is hand-copied from the enum.
export default { title: 'Print/Domains' };
export const Domains = () => `<span style="background:#7a3b8f">Arcana</span>`;

// Good: uniform autodocs on a component story; game-data reads the generated fixture.
export default { title: 'Components/Buttons', tags: ['autodocs'] };
export const GameData = () =>
    fetchJson('/design-data.json').then((d) => renderSwatches(d.domains));
```

**See also:** [[framework-agnostic-accessible-components]], [[theme-via-tokens]], [[the-pdf-is-the-token-source-of-truth]].

**Enforced by:** Storybook a11y addon (axe) plus review.

## Design Notes

Non-normative. The a11y addon reports violations but does not yet fail a run; prefer adding an axe assertion that fails on a violation, and a visual-regression check on the catalog. Neither exists today.
