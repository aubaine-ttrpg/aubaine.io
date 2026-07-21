---
name: prefer-etag-and-conditional-requests
description: Set ETag or Last-Modified on cacheable GETs and answer conditional requests with 304 to save bandwidth.
paths: ["catalyst/src/Controller/**/*.php"]
severity: prefer
---
# Prefer ETag and conditional requests
**Rule:** On cacheable GET responses, set an `ETag` or `Last-Modified` and honor the client's `If-None-Match` or `If-Modified-Since`. When the resource is unchanged, return `304 Not Modified` with no body via `$response->isNotModified($request)`.

**Why:** RFC 9110 conditional requests let a client revalidate with the server and skip the body when nothing changed, which saves bandwidth and render work on repeat views. Symfony computes the `304` for you once the validators are set. The book version stamp is exactly such a validator (see catalyst/docs/adr/0001-book-version-stamp-and-pdf-cache).

**Good / Bad:**
```php
// Bad: always re-send the full body, even when nothing changed.
return $this->json($book);

// Good: validate first, return 304 when fresh.
$response = $this->json($book);
$response->setEtag($fingerprint->bookHash($book));
$response->setLastModified($book->getUpdatedAt());
if ($response->isNotModified($request)) {
    return $response; // 304, empty body
}
return $response;
```

**See also:** [[fingerprint-and-immutable-static-assets]].

**Enforced by:** review + functional test (second request with If-None-Match returns 304).
