---
name: always-implement-factories
description: Build complex objects through factories or named constructors so their invariants hold the moment they exist.
paths: ["catalyst/src/**/*.php"]
severity: should
---
# Always implement factories for complex objects

**Rule:** Construct objects with real invariants (Book, Page, SkillTree) through a factory service or a named constructor, not `new` plus a chain of setters. Pass every required value in one call so an object is never half-built or invalid.

**Why:** encapsulation and always-valid objects. A bare constructor with public setters lets callers create a Book with no title or a Page with no parent Book. A named constructor or factory makes the valid creation path the only path.

**Good / Bad:**
```php
// Bad - six setters, any of them skippable, no guarantee of validity
$book = new Book();
$book->setTitle($title);
$book->setSlug($slug);
// ... four more, easy to forget one

// Good - a named constructor that enforces the invariants
final class Book
{
    public static function create(string $title, Slug $slug, Design $design): self
    {
        if (trim($title) === '') {
            throw new \InvalidArgumentException('Book title cannot be empty.');
        }
        $book = new self();
        $book->title = $title;
        $book->slug = $slug;
        $book->design = $design;
        return $book;
    }
}
```

**See also:** [[never-put-business-logic-in-entities]] (the factory sets state; richer rules live in a service).

**Enforced by:** review + PHPStan 9 (private constructor where a named constructor is the entry point).
