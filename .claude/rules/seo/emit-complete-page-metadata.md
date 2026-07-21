---
name: emit-complete-page-metadata
description: A shared head component emits the title, meta description, one canonical link, and Open Graph and Twitter card tags for every page.
paths: ["almanach/src/layouts/**", "almanach/src/pages/**", "almanach/src/components/**/*.astro"]
severity: should
---
# Emit complete page metadata

**Rule:** Route every page's `<head>` through one shared component (for example `src/components/Head.astro`). It emits:

- a `<title>`,
- a `<meta name="description">` that defaults to the page's frontmatter `summary`,
- exactly one `<link rel="canonical">` built from the configured `site` plus `Astro.url.pathname`,
- the Open Graph tags (`og:title`, `og:description`, `og:type`, `og:url`, `og:image`) and the Twitter card tags (`twitter:card` and its pair).

Set `site` in `astro.config` so the canonical resolves to an absolute URL. Never hand-write head tags per page.

**Why:** the canonical link relation is defined by RFC 6596, and one self-referential canonical per page stops duplicate URLs from splitting index signals. Open Graph (ogp.me) and Twitter cards decide how a shared link renders in a feed. Driving the whole head from a single component keeps every page complete and consistent instead of each route inventing a partial head, and `site` is what makes both the absolute canonical and the sitemap possible.

**Good / Bad:**
```astro
---
// Bad: head written inline per page, no description, relative canonical, no cards.
---
<title>Skills</title>
<link rel="canonical" href="/skills/" />
```
```astro
---
// Good: src/components/Head.astro, one source for every page's metadata.
const { title, description, ogType = 'website', image } = Astro.props;
const canonical = new URL(Astro.url.pathname, Astro.site);
---
<title>{title}</title>
<meta name="description" content={description} />
<link rel="canonical" href={canonical} />
<meta property="og:title" content={title} />
<meta property="og:description" content={description} />
<meta property="og:type" content={ogType} />
<meta property="og:url" content={canonical} />
{image && <meta property="og:image" content={new URL(image, Astro.site)} />}
<meta name="twitter:card" content="summary_large_image" />
```
```astro
---
// Good: the layout defaults the description to the page frontmatter summary.
import Head from '../components/Head.astro';
const { title, summary } = Astro.props.frontmatter;
---
<Head title={title} description={summary} ogType="article" />
```

**See also:** [[add-structured-data-and-sitemap]], [[use-semantic-html-and-one-h1]], performance/optimize-images-and-fonts.

**Enforced by:** Lighthouse SEO audit + review.
