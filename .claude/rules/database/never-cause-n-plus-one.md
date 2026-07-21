---
name: never-cause-n-plus-one
description: Load an association with a single join or batch query, never one query per row inside a loop.
paths: ["catalyst/**"]
severity: must
---
# Never cause an N+1

**Rule:** When a view, export, or serializer touches an association across many rows, load it in one query: a fetch-join in the DQL/QueryBuilder, or a single aggregate/`WHERE ... IN` batch. Never walk a collection that lazy-loads one element at a time. A book list that shows each book's page count must get the counts in the list query; do not fetch the books, then read `$book->getPages()` per book. The query count of a list must not grow with the number of rows.

**Why:** One query per row makes the request cost scale with the table. It looks fine on the handful of books in dev and stalls on a full library. A fetch-join or one grouped `COUNT` keeps the cost flat regardless of row count. This is the loading behaviour Doctrine calls fetch mode: the default is lazy, so the join or batch has to be explicit. The regression is caught by the query-count test in testing/always-assert-query-count-does-not-scale, which proves the count this rule keeps flat cannot creep back.

**Good / Bad:**
```php
// Bad: lazy collection walked per book -> 1 query for the list + 1 per book.
$books = $this->bookRepository->findAll();
foreach ($books as $book) {
    $counts[$book->getId()] = count($book->getPages()); // fires a query each pass
}

// Good: one grouped query returns every count at once.
$counts = $this->bookRepository->createQueryBuilder('b')
    ->select('b.id AS book_id', 'COUNT(p.id) AS page_count')
    ->leftJoin('b.pages', 'p')
    ->groupBy('b.id')
    ->getQuery()
    ->getArrayResult();
```

**See also:** testing/always-assert-query-count-does-not-scale, [[never-flush-in-request-subscribers]], architecture/always-thin-controllers.

**Enforced by:** PHPUnit query-count assertion (testing/always-assert-query-count-does-not-scale) + review.
