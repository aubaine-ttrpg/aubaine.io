---
name: content-collections-and-schemas
description: Define every codex and content source as an Astro content collection with a zod schema that mirrors the codex frontmatter, so a bad record fails the build.
paths: ["almanach/src/content.config.ts", "almanach/src/pages/**"]
severity: must
---
# Content collections and schemas

**Rule:**
- Declare every source as an Astro 5 collection in `src/content.config.ts` with a content-layer loader. Use `glob()` for the sibling `../codex` Markdown and for the one-JSON-per-entity `../content` subfolders. Use `file()` only when a subfolder is a single file holding an array.
- Create one collection per `content/` subfolder.
- Give each collection a zod `schema` that mirrors the codex frontmatter: `title`, `slug`, `version`, `updated: z.coerce.date()`, `tags`, `related` optional, `summary`.
- Read content only through `getCollection()` and `render()`.
- A frontmatter or JSON record that violates the schema fails the build.

**Why:** The content layer is the typed boundary between the committed data and the pages. Almanach reads `content/` as one collection per subfolder and `codex/` as prose (README.md). The schema mirrors the frontmatter authored in the codex (codex/dice-and-modifiers/resolution.md carries title, slug, version, updated, tags, related, summary): `z.coerce.date()` parses the `updated` string into a Date, `version` follows semver.org, and `related` is optional because a page need not link out. Validating at build turns a malformed record into a failed build instead of a broken page ([[keep-the-build-deterministic-and-read-only]]).

**Good / Bad:**
```js
// Bad: an untyped raw glob, no schema, nothing validated.
const pages = import.meta.glob('../../codex/**/*.md');
```
```js
// Good: src/content.config.ts with loaders and a schema mirroring the codex frontmatter.
import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const codexFrontmatter = z.object({
  title: z.string(),
  slug: z.string(),
  version: z.string(),            // semver.org
  updated: z.coerce.date(),
  tags: z.array(z.string()),
  related: z.array(z.string()).optional(),
  summary: z.string(),
});

export const collections = {
  resolution: defineCollection({
    loader: glob({ pattern: '**/*.md', base: '../codex/dice-and-modifiers' }),
    schema: codexFrontmatter,
  }),
  skills: defineCollection({
    loader: glob({ pattern: '**/*.json', base: '../content/skills' }),
    schema: z.object({ /* mirrors the exported skill record */ }),
  }),
};
```
```js
// Good: query and render only through the collection API.
import { getCollection, render } from 'astro:content';
const pages = await getCollection('resolution');
const { Content } = await render(pages[0]);
```

**See also:** [[keep-the-build-deterministic-and-read-only]], [[match-markdown-rendering-with-catalyst]], [[static-first-and-island-discipline]].

**Enforced by:** astro check + astro build + review (a schema violation stops the build).
