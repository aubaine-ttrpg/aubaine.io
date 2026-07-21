<?php

declare(strict_types=1);

namespace App\Book\View;

use App\Book\Model\Book;
use App\Page\Type\CoverFrontPageType;
use App\Support\RelativeTimeFormatter;
use DateTimeInterface;

/**
 * Builds the preview-card view models for the books grid: derives each book's
 * cover image, monogram fallback, type label, and last-updated labels.
 */
final class BookCardViewFactory
{
    public function __construct(private readonly RelativeTimeFormatter $relativeTime)
    {
    }

    /**
     * @param list<Book> $books
     *
     * @return list<BookCardView>
     */
    public function forList(array $books): array
    {
        return array_map(fn (Book $book): BookCardView => $this->forBook($book), $books);
    }

    public function forBook(Book $book): BookCardView
    {
        $updatedAt = $book->updatedAt();

        return new BookCardView(
            id: $book->id(),
            title: $book->title(),
            titleSizeClass: $this->titleSizeClass($book->title()),
            typeLabelKey: $book->bookType()->labelKey(),
            coverImage: $this->coverImage($book),
            monogram: $this->monogram($book->title()),
            pageCount: $book->pageCount(),
            updatedRelative: $this->relativeTime->ago($updatedAt),
            updatedIso: $updatedAt->format(DateTimeInterface::ATOM),
            updatedTooltip: $this->relativeTime->absolute($updatedAt),
        );
    }

    /**
     * The image of the first front-cover page, if any carries one.
     */
    private function coverImage(Book $book): ?string
    {
        foreach ($book->pages() as $page) {
            if (CoverFrontPageType::KEY !== $page->type()) {
                continue;
            }

            $image = $page->data()['image'] ?? null;
            if (\is_string($image) && '' !== $image) {
                return $image;
            }
        }

        return null;
    }

    private function monogram(string $title): string
    {
        $first = mb_substr(trim($title), 0, 1);

        return '' === $first ? '?' : mb_strtoupper($first);
    }

    /**
     * Step the overlaid title size down for longer names so they stay inside the
     * cover (print-safe, no JS fit-to-width; mirrors CoverFrontPageType).
     */
    private function titleSizeClass(string $title): string
    {
        $length = mb_strlen(trim($title));

        return match (true) {
            $length >= 24 => 'cover-card__title--xxlong',
            $length >= 20 => 'cover-card__title--xlong',
            $length >= 16 => 'cover-card__title--long',
            default => '',
        };
    }
}
