<?php

declare(strict_types=1);

namespace App\Tests\Book\View;

use App\Book\BookType;
use App\Book\Model\Book;
use App\Book\Model\Page;
use App\Book\Version;
use App\Book\View\BookCardViewFactory;
use App\Support\RelativeTimeFormatter;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BookCardViewFactoryTest extends TestCase
{
    public function testUsesFirstCoverFrontImage(): void
    {
        $book = $this->book('druide', 'Druide', BookType::Domaine, [
            new Page('p1', 'skill-tree', []),
            new Page('p2', 'cover-front', ['image' => 'druide.png']),
            new Page('p3', 'cover-front', ['image' => 'ignored.png']),
        ]);

        $view = $this->factory()->forBook($book);

        self::assertSame('druide.png', $view->coverImage);
        self::assertSame('book.type.domaine', $view->typeLabelKey);
        self::assertSame(3, $view->pageCount);
    }

    public function testSkipsCoverFrontWithoutImage(): void
    {
        $book = $this->book('x', 'X', BookType::Archetype, [
            new Page('p1', 'cover-front', ['image' => '']),
            new Page('p2', 'cover-front', ['image' => 'real.png']),
        ]);

        self::assertSame('real.png', $this->factory()->forBook($book)->coverImage);
    }

    public function testNoCoverFallsBackToMonogram(): void
    {
        $book = $this->book('berserker', 'berserker', BookType::Archetype, [
            new Page('p1', 'skill-tree', []),
        ]);

        $view = $this->factory()->forBook($book);

        self::assertNull($view->coverImage);
        self::assertSame('B', $view->monogram);
    }

    public function testEmptyTitleMonogramIsPlaceholder(): void
    {
        $book = $this->book('untitled', '', BookType::Archetype, []);

        self::assertSame('?', $this->factory()->forBook($book)->monogram);
    }

    public function testTitleSizeClassStepsDownWithLength(): void
    {
        $factory = $this->factory();

        // Common names (Technomancien = 13) stay at the big default size.
        self::assertSame('', $factory->forBook($this->book('feu', 'Feu', BookType::Archetype, []))->titleSizeClass);
        self::assertSame('', $factory->forBook($this->book('techno', 'Technomancien', BookType::Archetype, []))->titleSizeClass);
        self::assertSame(
            'cover-card__title--long',
            $factory->forBook($this->book('presti', 'Prestidigitateur', BookType::Archetype, []))->titleSizeClass,
        );
        self::assertSame(
            'cover-card__title--xlong',
            $factory->forBook($this->book('illus', 'Maître des Illusions', BookType::Archetype, []))->titleSizeClass,
        );
        self::assertSame(
            'cover-card__title--xxlong',
            $factory->forBook($this->book('necro', 'Grand Nécromancien Éternel', BookType::Archetype, []))->titleSizeClass,
        );
    }

    /**
     * @param list<Page> $pages
     */
    private function book(string $id, string $title, BookType $type, array $pages): Book
    {
        $at = new DateTimeImmutable('2026-07-01T00:00:00+00:00');

        return new Book($id, $title, null, $type, new Version(0, 1), $at, $at, $pages);
    }

    private function factory(): BookCardViewFactory
    {
        $translator = new class implements TranslatorInterface, LocaleAwareInterface {
            /** @param array<array-key, mixed> $parameters */
            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return $id;
            }

            public function getLocale(): string
            {
                return 'fr';
            }

            public function setLocale(string $locale): void
            {
            }
        };

        $clock = new MockClock(new DateTimeImmutable('2026-07-21T12:00:00+00:00'));

        return new BookCardViewFactory(new RelativeTimeFormatter($clock, $translator, $translator));
    }
}
