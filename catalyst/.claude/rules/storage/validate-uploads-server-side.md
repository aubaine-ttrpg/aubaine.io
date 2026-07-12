---
name: validate-uploads-server-side
description: Validate every upload server-side (size cap, content-sniffed MIME, extension allow-list, dimensions where relevant) and never serve user files with an attacker-controlled content type.
paths: ["src/**/*.php", "config/**"]
severity: must
---
# Validate uploads server-side

**Rule:** Validate every uploaded file on the server: enforce a size cap, detect the MIME type by sniffing the content (not by trusting the request header or filename), check the extension against an allow-list, and validate dimensions where relevant. When serving a user-provided file back, set a safe, server-decided `Content-Type` and never echo the one the uploader supplied.

**Why:** The client-supplied content type and filename are attacker-controlled, so a file named `avatar.png` can be an HTML or SVG payload; sniffing the real bytes is what prevents disguised malware and stored XSS, and a size cap blocks oversized-upload abuse (REVIEW_DOCTRINE §10.10 "uploads validate size, MIME type, extension, and content expectations server-side", "user-controlled files are never served with unsafe content types"). Client-side checks are convenience only, never security (REVIEW_DOCTRINE §10.2 "relying on client-side validation for security or data integrity").

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
public ?UploadedFile $avatar = null;
```

**See also:** security/always-sanitize-user-html, security/always-validate-input-server-side, [[keep-buckets-private-and-presign-short-lived]].

**Enforced by:** Symfony Validator + review + test (a disguised or oversized file is rejected).
