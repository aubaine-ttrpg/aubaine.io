---
name: static-first-and-island-discipline
description: Almanach outputs a static site with zero client JavaScript by default; add no SSR adapter, no runtime fetch, and hydrate only the island that needs it.
paths: ["almanach/**"]
severity: should
---
# Static-first and island discipline

**Rule:**
- Keep `output: 'static'` (the default). Add no SSR adapter, no server endpoints that run at request time, and no runtime data fetching.
- Ship zero client JavaScript by default. Add a client directive only on the island that needs interactivity.
- Prefer `client:visible`, `client:idle`, or `client:media` over `client:load`. Never blanket-hydrate a page or a layout.
- Build every parameterised route from `getStaticPaths()`.

**Why:** Almanach is a no-backend static site that CI builds from the committed `codex/` and `content/` (README.md). An SSR adapter or a runtime fetch needs a server the site never runs and breaks the CI-only, no-database pipeline (keep-the-build-deterministic-and-read-only). Astro ships no client JavaScript unless a directive asks for it, so eager `client:load` puts a bundle in front of every visitor while `client:visible` and `client:idle` defer it to when it is needed. Static pages stay cacheable and printable ([[print-friendly-pages]]).

**Good / Bad:**
```astro
---
// Bad: opts into a server Almanach never runs, and hydrates everything eagerly.
export const prerender = false;
---
<SearchBox client:load />
```
```astro
---
// Good: a static route from getStaticPaths, with one deferred island.
export async function getStaticPaths() {
  const pages = await getCollection('resolution');
  return pages.map((page) => ({ params: { slug: page.data.slug }, props: { page } }));
}
---
<SearchBox client:visible />
```

**See also:** [[content-collections-and-schemas]], [[keep-the-build-deterministic-and-read-only]], [[print-friendly-pages]].

**Enforced by:** astro check + astro build + review.
