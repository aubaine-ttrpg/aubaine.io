---
name: always-thin-controllers
description: Controllers only receive the request, call a service or handler, and return a Response; no business logic and no Doctrine queries.
paths: ["catalyst/src/**/Controller/**"]
severity: must
---
# Always keep controllers thin

**Rule:** A controller action does three things: read the request into a DTO, call one service or handler, return a Response. No business rules, no Doctrine queries beyond Symfony's automatic param conversion (the `#[MapEntity]` lookup), no flush.

**Why:** Symfony best practices: thin controllers, then services, then repositories. Logic in controllers cannot be unit-tested without the HTTP layer and tends to be copy-pasted across actions.

**Good / Bad:**
```php
// Bad - query, rules, and persistence in the action
#[Route('/books/{id}/pages', methods: ['POST'])]
public function addPage(Request $request, EntityManagerInterface $em): Response
{
    $book = $em->getRepository(Book::class)->find($request->get('bookId'));
    if ($book->getPages()->isEmpty()) { /* ... */ }
    $em->persist(new Page($book)); $em->flush();
    return $this->redirectToRoute('book_show');
}

// Good - parse, delegate, respond
#[Route('/books/{id}/pages', methods: ['POST'])]
public function addPage(#[MapRequestPayload] AddPage $command, PageCreator $creator): Response
{
    $page = $creator->add($command);
    return $this->redirectToRoute('page_edit', ['id' => $page->getId()]);
}
```

**See also:** [[always-use-dtos-at-boundaries]], [[always-inject-dependencies]].

**Enforced by:** review + PHPStan 9 (no `EntityManagerInterface` in controllers).
