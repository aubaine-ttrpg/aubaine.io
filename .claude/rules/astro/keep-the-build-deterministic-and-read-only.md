---
name: keep-the-build-deterministic-and-read-only
description: Almanach builds only from committed codex/ and content/, runs no Catalyst, PHP, database, or network, never writes back to the sources, and fails on schema or link errors.
paths: ["almanach/**"]
severity: must
---
# Keep the build deterministic and read-only

**Rule:**
- Build Almanach only from the committed `codex/` and `content/`.
- The build runs no Catalyst, no PHP, no database, and no network request.
- Almanach never writes back to `codex/`, `content/`, or the local db. It reads them.
- Derive navigation, counts, and cross-links from the collections at build time, never from a hardcoded list.
- Fail the build on a schema violation or a broken internal link.

**Why:** CI builds the static site straight from the committed data, without Catalyst, PHP, or a database in the pipeline, and Almanach is read-only: it does not write back to Catalyst or its db (README.md). A build that reaches for the db, for PHP, or for the network is not reproducible in CI and couples the public site to the local tool. Deriving nav and counts from the collections keeps each fact in its one source (process/never-hardcode-dynamic-lists, ai/never-create-drift): a hardcoded count or a hand-listed menu drifts the moment the content changes. Failing on a schema or link error stops a bad export from shipping ([[content-collections-and-schemas]]).

**Good / Bad:**
```js
// Bad: reaches for the running tool and hardcodes a count that will drift.
const skills = await fetch('http://localhost:8000/api/skills').then((r) => r.json());
---
<p>Browse all 42 skills.</p>
```
```js
// Good: reads the committed collection and derives the count from it.
import { getCollection } from 'astro:content';
const skills = await getCollection('skills');   // committed content/skills/*.json
---
<p>Browse all {skills.length} skills.</p>
```

**See also:** [[content-collections-and-schemas]], [[static-first-and-island-discipline]], process/never-hardcode-dynamic-lists, ai/never-create-drift.

**Enforced by:** astro check + astro build + review (CI builds with no Catalyst, PHP, or db).
