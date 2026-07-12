---
name: always-thin-controllers
description: Controllers only receive the request, call a service or handler, and return a Response; no business logic and no Doctrine queries.
paths: ["src/**/Controller/**"]
severity: must
---
# Always keep controllers thin

**Rule:** A controller action does three things: read the request into a DTO, call one service or handler, return a Response. No business rules, no Doctrine queries beyond Symfony's automatic param conversion (the `#[MapEntity]` lookup), no flush.

**Why:** Symfony best practices: thin controllers, then services, then repositories. Logic in controllers cannot be unit-tested without the HTTP layer and tends to be copy-pasted across actions.

**Good / Bad:**
```php
// Bad - query, rules, and persistence in the action
#[Route('/invoices', methods: ['POST'])]
public function create(Request $request, EntityManagerInterface $em): Response
{
    $order = $em->getRepository(Order::class)->find($request->get('orderId'));
    if ($order->getTotal() <= 0) { /* ... */ }
    $em->persist(new Invoice($order)); $em->flush();
    return $this->redirectToRoute('invoice_show');
}

// Good - parse, delegate, respond
#[Route('/invoices', methods: ['POST'])]
public function create(#[MapRequestPayload] IssueInvoice $command, InvoiceIssuer $issuer): Response
{
    $invoice = $issuer->issue($command);
    return $this->redirectToRoute('invoice_show', ['id' => $invoice->getId()]);
}
```

**See also:** [[always-use-dtos-at-boundaries]], [[always-inject-dependencies]].

**Enforced by:** review + PHPStan 9 (no `EntityManagerInterface` in controllers).
