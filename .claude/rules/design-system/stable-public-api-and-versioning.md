---
name: stable-public-api-and-versioning
description: Treat the package.json exports map as sigil's public API and semver contract, keep component CSS out of it, log every version bump, and let consumers import the narrowest entry.
paths: ["sigil/package.json"]
severity: should
---
# Stable public API and versioning

**Rule:** The `exports` map in `package.json` is sigil's public API, and its version is a semver contract over that map plus the tokens and classes the entries expose:

- Adding a new export is a **minor** bump.
- Removing or renaming an export, or removing or renaming a widely-used token or component class, is a **major** bump.
- An internal fix that keeps the surface stable is a **patch** bump.

Record a changelog entry on every bump, saying what moved. Keep individual `components/*.css` out of the exports map: components ship through `web.css`, so consumers get the composed set, not a pick-and-mix that turns every component into public API. Each consumer imports the narrowest entry it needs, so `print.css` pulls only the token files and never component CSS.

**Why:** an entry point that a consumer imports is a promise, and semver is how a promise is kept or broken on purpose rather than by surprise (semver.org). A changelog turns a version number into a reason. Keeping component files out of `exports` means their internals stay refactorable without a major bump, because only `web.css` is promised. Narrowest-entry imports are what let the PDF stay free of web-only component CSS (see [[the-pdf-is-the-token-source-of-truth]]).

**Good / Bad:**
```json
{
  "Bad: every component becomes public API, so any refactor is a breaking change": {
    "exports": {
      "./web.css": "./src/web.css",
      "./components/button.css": "./src/components/button.css",
      "./components/card.css": "./src/components/card.css"
    }
  }
}
```
```json
{
  "Good: components ship through web.css; tokens split out for print's narrow import": {
    "version": "0.2.0",
    "exports": {
      "./web.css": "./src/web.css",
      "./tokens/brand.css": "./src/tokens/brand.css",
      "./tokens/fonts.css": "./src/tokens/fonts.css"
    }
  }
}
```

**See also:** [[cross-consumer-consistency]], [[the-pdf-is-the-token-source-of-truth]], process/write-an-adr-for-significant-decisions.

**Enforced by:** review.
