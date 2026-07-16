# 1. Book version stamp and content-addressed PDF cache

## Status

Accepted.

## Context

Catalyst renders a book to PDF on demand. Every request re-rendered the book from
scratch through Gotenberg, and the displayed version was a hand-typed literal
(`v0.1`) baked into a cover field, with no way to tell whether a given PDF matched
the current content or the current presentation code.

Two questions had no answer a reader could trust:

1. Was this PDF produced by the current CSS and JS, or by an older build?
2. Does this PDF reflect the current book content, including the skill trees and
   images it renders, or is it stale?

## Decision

Compute two identifiers before rendering, and derive both the printed version and
the stored filename from them so neither can drift or go stale.

- **Webpack hash**: a digest of the bytes of every CSS and JS file the `print`
  Encore entrypoint loads. It moves whenever the presentation code changes. It is
  global per build (the same for every book).
- **Book hash**: a digest of the book's canonical JSON plus the bytes of every
  file its pages link to (skill-tree JSON, cover and QR images, paper textures,
  node icons). It moves on any content change, including editing the inside of a
  linked file or replacing one with different bytes under the same name. Each page
  type declares its linked files through `PageTypeInterface::referencedContentPaths()`,
  so new page types are covered without touching the fingerprinter.

Version, previously a free-text literal, becomes book-level metadata: a `bookType`
enum and a `{major, minor}` version, both stored in the book JSON. The full stamp
is `vMAJOR.MINOR.WEBPACK.BOOK`. The front cover prints `bookType` and `vMAJOR.MINOR`;
the back cover prints the brand, the title, and the full stamp with both hashes.
Segments are joined by a hexagon icon.

PDFs are cached by content address at `var/books-pdf/{bookName}_{webpackHash}_{bookHash}.pdf`.
The PDF action serves the file if it already exists and asks Gotenberg to build it
otherwise, then prunes older PDFs for the same book. The book id (its slug) is the
`bookName` in the filename; the human title is what the back cover shows.

## Consequences

- A version bump, a content edit, a linked-asset change, or an asset rebuild all
  yield a new filename, so the cache is self-invalidating and a stale PDF is never
  served.
- Repeat downloads of an unchanged book skip Gotenberg entirely.
- The webpack build output must exist before a PDF is generated; when it is
  missing the fingerprinter fails fast with a clear error rather than guessing.
- `var/books-pdf/` is disposable local state, git-ignored like the rest of `var/`.
- Hashes are truncated to eight hex characters for readable filenames and stamps,
  matching the existing asset-fingerprint convention. Collisions are possible in
  theory but irrelevant for a single-user local authoring tool.
