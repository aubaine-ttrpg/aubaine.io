<?php

declare(strict_types=1);

namespace App\Page;

use App\Book\Model\Book;
use App\Pdf\BookRelease;

/** Resolves each page of a book to its print template and view model. */
final class PageViewFactory
{
    /** Fixed copyright shown on every cover; not per-book, never edited. */
    private const string COPYRIGHT = '© Aubaine';

    public function __construct(private readonly PageTypeRegistry $registry)
    {
    }

    /**
     * Cover pages also receive the book-level version stamp (computed once, in
     * {@see BookRelease}) plus the fixed copyright, so covers render them without
     * each page storing its own copy.
     *
     * @return list<PageView>
     */
    public function forBook(Book $book, BookRelease $release): array
    {
        $coverContext = [
            'bookTypeLabel' => $book->bookType()->labelKey(),
            'bookTitle' => $book->title(),
            'copyright' => self::COPYRIGHT,
            'versionShort' => $release->short(),
            'versionFull' => $release->full(),
        ];

        $views = [];
        foreach ($book->pages() as $page) {
            $type = $this->registry->get($page->type());
            $view = $type->buildViewModel($page->data());
            if ('covers' === $type->category()) {
                $view = array_merge($view, $coverContext);
            }
            // Anchor each physical leaf so the live editor's preview can scroll to it.
            $view['pageId'] = $page->id();
            $views[] = new PageView($page->id(), $type, $type->template(), $view);
        }

        return $views;
    }
}
