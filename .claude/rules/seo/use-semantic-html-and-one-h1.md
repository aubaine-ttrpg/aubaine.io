---
name: use-semantic-html-and-one-h1
description: Every Astro page uses landmark elements, exactly one h1, and heading levels that never skip.
paths: ["almanach/src/**/*.astro"]
severity: should
---
# Use semantic HTML and one h1

**Rule:** Structure each page with landmark elements: `<header>`, `<nav>`, `<main>`, and `<footer>`. Give the page exactly one `<h1>`, naming the page's subject. Nest headings in order (`h1`, then `h2`, then `h3`) and never skip a level to reach a size. Reach for the element that carries the meaning (`<article>`, `<section>`, `<time>`, `<ul>`), not a `<div>` with a class.

**Why:** WCAG 1.3.1 Info and Relationships requires structure to live in the markup, not only in the styling. WCAG 2.4.6 Headings and Labels and 2.4.10 Section Headings rely on an ordered, single-`h1` outline so screen-reader and search-engine users can scan the page and search engines can read its shape. Landmarks give assistive tech its skip targets. Style headings with the sigil type tokens so the heading level tracks meaning, not size (frontend/always-use-design-tokens-not-magic-values).

**Good / Bad:**
```astro
<!-- Bad: two h1s, div landmarks, and h2 skipped to h4 just for a smaller size. -->
<div class="top">...</div>
<h1>Aubaine</h1>
<h1>Combat</h1>
<div class="body"><h4>Initiative</h4></div>
```
```astro
<!-- Good: real landmarks, one h1, headings in order. -->
<header>...</header>
<nav aria-label="Breadcrumb">...</nav>
<main>
  <h1>Combat</h1>
  <section>
    <h2>Initiative</h2>
  </section>
</main>
<footer>...</footer>
```

**See also:** [[emit-complete-page-metadata]], accessibility/always-use-correct-aria-roles, accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** axe + review.
