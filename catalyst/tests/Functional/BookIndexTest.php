<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Book\BookEditor;
use App\Book\BookRepository;
use App\Book\BookType;
use App\Book\Version;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BookIndexTest extends WebTestCase
{
    public function testPageCountPluralizes(): void
    {
        $translator = static::getContainer()->get(TranslatorInterface::class);
        self::assertInstanceOf(TranslatorInterface::class, $translator);

        // "page" for one or fewer, "pages" beyond, in both catalogs.
        self::assertSame('0 page', $translator->trans('book.pages_count', ['%count%' => 0], null, 'fr'));
        self::assertSame('1 page', $translator->trans('book.pages_count', ['%count%' => 1], null, 'fr'));
        self::assertSame('3 pages', $translator->trans('book.pages_count', ['%count%' => 3], null, 'fr'));
        self::assertSame('1 page', $translator->trans('book.pages_count', ['%count%' => 1], null, 'en'));
        self::assertSame('2 pages', $translator->trans('book.pages_count', ['%count%' => 2], null, 'en'));
    }

    public function testGridRendersCoverCardWithArtworkAndActions(): void
    {
        $client = static::createClient();
        $editor = static::getContainer()->get(BookEditor::class);
        self::assertInstanceOf(BookEditor::class, $editor);

        $book = $editor->create('Grid Cover', 'Avec illustration', BookType::Domaine, new Version(0, 1));
        $editor->addPage($book, 'cover-front'); // default data carries an image

        try {
            $crawler = $client->request('GET', '/books');
            self::assertResponseIsSuccessful();

            $card = $crawler->filter('.cover-card')->reduce(
                fn ($node): bool => str_contains($node->text(), 'Grid Cover'),
            );
            self::assertCount(1, $card, 'the book renders as a cover card');

            // Poster artwork, not the monogram fallback.
            self::assertGreaterThan(0, $card->filter('img.cover-card__art')->count());
            // The three actions: settings, PDF download, delete.
            self::assertGreaterThan(0, $card->filter('a[href*="/settings"]')->count());
            self::assertGreaterThan(0, $card->filter('a[href*="/pdf"]')->count());
            self::assertGreaterThan(0, $card->filter('form[action*="/delete"] button')->count());
            // Whole card links into the editor.
            self::assertGreaterThan(0, $card->filter('a.cover-card__link[href$="/books/'.$book->id().'"]')->count());
        } finally {
            $repository = static::getContainer()->get(BookRepository::class);
            self::assertInstanceOf(BookRepository::class, $repository);
            $repository->delete($book->id());
        }
    }

    public function testLongTitleGetsReducedSizeClass(): void
    {
        $client = static::createClient();
        $editor = static::getContainer()->get(BookEditor::class);
        self::assertInstanceOf(BookEditor::class, $editor);

        $book = $editor->create('Prestidigitateur', null, BookType::Archetype, new Version(0, 1));

        try {
            $crawler = $client->request('GET', '/books');
            self::assertResponseIsSuccessful();

            $card = $crawler->filter('.cover-card')->reduce(
                fn ($node): bool => str_contains($node->text(), 'Prestidigitateur'),
            );
            self::assertGreaterThan(0, $card->filter('.cover-card__title.cover-card__title--long')->count());
        } finally {
            $repository = static::getContainer()->get(BookRepository::class);
            self::assertInstanceOf(BookRepository::class, $repository);
            $repository->delete($book->id());
        }
    }

    public function testBookWithoutCoverShowsMonogramFallback(): void
    {
        $client = static::createClient();
        $editor = static::getContainer()->get(BookEditor::class);
        self::assertInstanceOf(BookEditor::class, $editor);

        $book = $editor->create('Nocover', null, BookType::Archetype, new Version(0, 1));

        try {
            $crawler = $client->request('GET', '/books');
            self::assertResponseIsSuccessful();

            $card = $crawler->filter('.cover-card')->reduce(
                fn ($node): bool => str_contains($node->text(), 'Nocover'),
            );
            self::assertCount(1, $card);
            self::assertGreaterThan(0, $card->filter('.cover-card__placeholder .cover-card__monogram')->count());
            self::assertSame('N', trim($card->filter('.cover-card__monogram')->text()));
        } finally {
            $repository = static::getContainer()->get(BookRepository::class);
            self::assertInstanceOf(BookRepository::class, $repository);
            $repository->delete($book->id());
        }
    }
}
