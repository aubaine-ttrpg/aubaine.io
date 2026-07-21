---
name: add-structured-data-and-sitemap
description: Every page emits valid JSON-LD, the build generates a sitemap through the Astro sitemap integration, and a committed robots.txt points crawlers at it.
paths: ["almanach/src/**", "almanach/astro.config.*"]
severity: should
---
# Add structured data and a sitemap

**Rule:** Emit valid JSON-LD in a `<script type="application/ld+json">`: a `TechArticle` on each docs page (built from that page's frontmatter), plus `WebSite` and `BreadcrumbList` from the shared shell so they appear sitewide. Add the `@astrojs/sitemap` integration in `astro.config` (it reads `site`) so the build writes the sitemap. Commit `public/robots.txt` that allows crawling and names the sitemap URL. Generate the JSON-LD from the same frontmatter the page renders, never a hand-kept copy.

**Why:** the schema.org `TechArticle`, `WebSite`, and `BreadcrumbList` vocabularies let a search engine model the docs and the site structure for rich results. robots.txt and its `Sitemap:` line are the Robots Exclusion Protocol (RFC 9309), and the sitemap follows the sitemaps.org protocol. Building the JSON-LD from frontmatter keeps it from drifting away from the visible page, which is the point of ai/never-create-drift: one fact, one source.

**Good / Bad:**
```astro
{/* Bad: JSON-LD typed by hand, its text copied from the page and already stale. */}
<script type="application/ld+json">
  {"@type":"Article","headline":"Combat","description":"outdated summary"}
</script>
```
```astro
---
// Good: TechArticle generated from the page's own frontmatter, so it cannot drift.
const { title, summary, updated } = Astro.props.frontmatter;
const ld = {
  '@context': 'https://schema.org',
  '@type': 'TechArticle',
  headline: title,
  description: summary,
  dateModified: updated,
};
---
<script type="application/ld+json" set:html={JSON.stringify(ld)} />
```
```js
// astro.config.mjs: sitemap reads `site` to emit absolute URLs.
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

export default defineConfig({
  site: 'https://almanach.aubaine.io',
  integrations: [sitemap()],
});
```
```text
# public/robots.txt: committed, points crawlers at the generated sitemap.
User-agent: *
Allow: /
Sitemap: https://almanach.aubaine.io/sitemap-index.xml
```

**See also:** [[emit-complete-page-metadata]], [[use-semantic-html-and-one-h1]], ai/never-create-drift.

**Enforced by:** Lighthouse (structured data) + @astrojs/sitemap build output + review.
