<?php

declare(strict_types=1);

namespace App\Page;

use App\Book\Model\Book;

/** Resolves each page of a book to its print template and view model. */
final class PageViewFactory
{
    public function __construct(private readonly PageTypeRegistry $registry)
    {
    }

    /**
     * @return list<PageView>
     */
    public function forBook(Book $book): array
    {
        $views = [];
        foreach ($book->pages() as $page) {
            $type = $this->registry->get($page->type());
            $views[] = new PageView($page->id(), $type, $type->template(), $type->buildViewModel($page->data()));
        }

        return $views;
    }
}
