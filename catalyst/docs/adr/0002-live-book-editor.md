# 2. Live book editor

## Status

Accepted.

## Context

The book editor was a set of server-rendered forms: adding, reordering, or
customizing a page posted and redirected back to the editor, and the only way to
see the result was to generate the PDF. There was no on-page preview, so authoring
meant a round trip through Gotenberg for every check.

We want the editor to show the book as it will print, updating as pages are added,
reordered, and customized, with PDF generation reduced to the final export step.
That needs two things the old editor lacked: a faithful preview of the printed
output, and controls that update it without a full-page reload.

The app had no reactive-frontend tooling. It runs on Webpack Encore plus Stimulus,
with hand-written CSS tokens; there was no Turbo and no Symfony UX Live Component.

## Decision

**Preview by embedding the existing print route in an iframe.** The `app_book_print`
route already renders the exact HTML Gotenberg turns into the PDF, and `print.css`
already styles it as A4 sheets on a grey canvas under `@media screen`. The editor
embeds that route in an `<iframe>`, so the preview is the printed output by
construction, with no second renderer to keep in sync. The iframe also isolates the
print stylesheet (fixed A4 leaves, page-break rules, its own web fonts, the ability
pagination script) from the editor chrome. A `?chrome=0` flag drops the print
toolbar in the embed. Each printed leaf carries a `data-page-id` so the editor can
scroll the preview to the selected page; this changes no rendered output, so the
content-addressed PDF cache (ADR 0001) is unaffected.

**Reactive controls with Symfony UX Live Components, with Turbo Drive off.** A
single `LiveBookEditor` component owns the editor state (selected page, a preview
nonce) and delegates every mutation to the existing `BookEditor` service. A Stimulus
controller owns the iframe: when a mutation bumps the nonce, it reloads the iframe
(debounced) so the preview follows edits; selecting a page scrolls without a reload.
Turbo is not installed. Live Components re-render by morphing their own DOM subtree
and do not require it, so the whole-page navigation model that makes Turbo Drive
re-execute page scripts never applies here.

**Progressive enhancement is preserved.** The component renders as server-side HTML,
and every control is a real form or link posting to the existing BookController
routes. With JavaScript off, adding, moving, deleting, and customizing pages still
work through full-page requests, page selection falls back to a `?page` query
parameter, and the preview is a plain iframe.

## Consequences

- The preview cannot drift from the PDF: both come from one route and one stylesheet.
- One new runtime dependency, `symfony/ux-live-component` (which pulls
  `symfony/ux-twig-component`). Turbo is not added; confirm it stays out of
  `composer.lock` and `package-lock.json` on future updates.
- This is the first Live Component in the app. It establishes the reactive-frontend
  pattern the frontend rules already point to.
- Each preview reload re-fetches web fonts and reruns ability pagination inside the
  iframe, so reloads are debounced and driven only by content changes, not by
  selection or field edits.
- Book mutations stay synchronous through `BookEditor`, which persists before the
  action returns, so the component and the reloaded iframe always read the same
  saved state. Keeping persistence synchronous is load-bearing for that guarantee.
