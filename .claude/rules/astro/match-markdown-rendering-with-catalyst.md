---
name: match-markdown-rendering-with-catalyst
description: Render codex as plain Markdown with GFM on and SmartyPants off so Almanach's text matches Catalyst's PHP render byte for byte.
paths: ["almanach/astro.config.*", "almanach/src/pages/**"]
severity: should
---
# Match Markdown rendering with Catalyst

**Rule:**
- Render codex as plain Markdown (`.md`). Never author or import MDX.
- In `astro.config`, set `markdown.gfm: true` and `markdown.smartypants: false`.
- Add no remark or rehype plugin that rewrites text content.
- The rendered text must read the same as Catalyst's PHP render of the same file.

**Why:** The codex is authored once and rendered by two engines (README.md): Astro on Almanach, and `league/commonmark` (`^2.7`) through `twig/markdown-extra` (`^3`) in Catalyst. `gfm: true` matches commonmark's GitHub-Flavored extension (tables, strikethrough, autolinks, task lists), so both engines produce the same structure. Astro turns SmartyPants on by default, which rewrites straight quotes to curly and `--` into an en dash. That diverges from the PHP render and breaks the no-dash rule (ai/no-ai-tells, and the aubaine-content-writer `docs/no-ai-tells.md`), so it stays off. A plugin that transforms text makes the two renders drift.

**Good / Bad:**
```js
// Bad: MDX pages, and SmartyPants left on to rewrite quotes and dashes.
export default defineConfig({
  markdown: { /* smartypants defaults to true, mangling the codex text */ },
});
```
```js
// Good: plain Markdown, GFM on for parity, SmartyPants off to keep text verbatim.
import { defineConfig } from 'astro/config';

export default defineConfig({
  markdown: {
    gfm: true,
    smartypants: false,   // keep quotes and dashes as authored, matching the PHP render
  },
});
```

**See also:** [[content-collections-and-schemas]], ai/no-ai-tells.

**Enforced by:** astro build + review (diff the rendered text against Catalyst's render).
