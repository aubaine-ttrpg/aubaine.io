<?php

declare(strict_types=1);

namespace App\Twig;

use App\Book\BookRepository;
use App\Book\BookType;
use App\Book\Model\Book;
use App\Page\PageTypeInterface;
use App\Page\PageTypeRegistry;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** Thin bridge for things templates can't get from plain objects. */
final class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly PageTypeRegistry $registry,
        private readonly Packages $assets,
        private readonly BookRepository $books,
    ) {
    }

    /** @var list<Book>|null Per-request cache so the sidebar reads books once. */
    private ?array $bookCache = null;

    public function getFunctions(): array
    {
        return [
            new TwigFunction('page_type', $this->pageType(...)),
            new TwigFunction('catalyst_image', $this->image(...)),
            new TwigFunction('nav_books', $this->navBooks(...)),
            new TwigFunction('nav_book_groups', $this->navBookGroups(...)),
        ];
    }

    /**
     * Books for the shell sidebar (newest first).
     *
     * @return list<Book>
     */
    public function navBooks(): array
    {
        return $this->bookCache ??= $this->books->all();
    }

    /**
     * Books grouped by type, in enum order, skipping empty types. Feeds the
     * sidebar's collapsible-per-type list.
     *
     * @return list<array{labelKey: string, count: int, books: list<Book>}>
     */
    public function navBookGroups(): array
    {
        $groups = [];
        foreach (BookType::cases() as $type) {
            $inType = array_values(array_filter(
                $this->navBooks(),
                static fn (Book $book): bool => $book->bookType() === $type,
            ));
            if ([] !== $inType) {
                $groups[] = ['labelKey' => $type->labelKey(), 'count' => \count($inType), 'books' => $inType];
            }
        }

        return $groups;
    }

    public function pageType(string $key): PageTypeInterface
    {
        return $this->registry->get($key);
    }

    /** Resolve a content-image filename to its built asset URL. */
    public function image(string $category, ?string $filename): ?string
    {
        if (null === $filename || '' === $filename) {
            return null;
        }

        return $this->assets->getUrl('build/images/'.$category.'/'.$filename);
    }
}
