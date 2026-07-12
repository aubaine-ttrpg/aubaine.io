---
name: use-browserslist-and-autoprefixer
description: Keep one .browserslistrc as the single source of truth for target browsers; let autoprefixer (CSS) and Babel preset-env (JS) read it, and never hand-write vendor prefixes.
paths: ["assets/**", ".browserslistrc", "package.json", "webpack.config.js"]
severity: must
---
# Use browserslist and autoprefixer

**Rule:** Maintain a single **`.browserslistrc`** at the repo root as the one source of truth for which
browsers we support. **autoprefixer** (PostCSS, via Encore) reads it to add CSS vendor prefixes, and
**Babel `@babel/preset-env`** (via Encore) reads the same list to transpile/polyfill JS, so CSS support
and JS support always match. Target a **modern baseline** (no IE). **Never hand-write vendor prefixes**
(`-webkit-`, `-moz-`); autoprefixer owns them, and manual prefixes drift and double up.

**Why:** Browsers are not just our Chrome. A shared target list gives cross-browser CSS/JS without
manual prefixing and is the one place to widen or narrow support. A modern baseline keeps us free to use
grid, custom properties, `:has`, and `prefers-color-scheme` (needed for [[always-support-light-and-dark-mode]]).
Hand-prefixing is the classic "works on my machine" bug source.

**Good / Bad:**
```css
/* Bad: hand-prefixed, will drift from the real target list. */
.card { -webkit-border-radius: 8px; border-radius: 8px; }

/* Good: write the standard property; autoprefixer adds prefixes per .browserslistrc. */
.card { border-radius: 8px; }
```
```
# .browserslistrc (modern baseline)
> 0.5%
last 2 versions
Firefox ESR
not dead
not op_mini all
```

**See also:** [[route-all-css-and-js-through-encore]], [[always-support-light-and-dark-mode]].

**Enforced by:** Encore (postcss-loader + autoprefixer, babel preset-env), review.
