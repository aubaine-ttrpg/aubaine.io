---
name: validate-uploads-server-side
description: Validate every upload server-side (size cap, content-sniffed MIME, extension allow-list, dimensions where relevant) and never serve user files with an attacker-controlled content type.
paths: ["catalyst/src/**/*.php", "catalyst/config/**"]
severity: must
---
# Validate uploads server-side

**Rule:** Forward-looking: catalyst does not accept file uploads today (cover art is picked from a seeded library, not uploaded), so this rule governs the moment an upload path is added, for cover art or a Design asset. When it is, validate every uploaded file on the server: enforce a size cap, detect the MIME type by sniffing the content (not by trusting the request header or filename), check the extension against an allow-list, and validate dimensions where relevant. When serving a user-provided file back, set a safe, server-decided `Content-Type` and never echo the one the uploader supplied.

**Why:** The client-supplied content type and filename are attacker-controlled, so a file named `cover.png` can be an HTML or SVG payload; sniffing the real bytes is what prevents disguised malware and stored XSS, and a size cap blocks oversized-upload abuse. Client-side checks are convenience only, never security.

**Good / Bad:**
```php
// Bad: trust the browser-supplied type and accept any size.
$mime = $file->getClientMimeType();           // attacker-controlled
$file->move($dir, $file->getClientOriginalName());

// Good: constrain server-side; the type is sniffed by the validator.
#[Assert\Image(
    maxSize: '5M',
    mimeTypes: ['image/png', 'image/jpeg', 'image/webp'],   // checked against real content
    maxWidth: 4000, maxHeight: 4000,
)]
public ?UploadedFile $cover = null;
```

**See also:** security/always-sanitize-user-html, security/always-validate-input-server-side.

**Enforced by:** Symfony Validator + review + test (a disguised or oversized file is rejected).
