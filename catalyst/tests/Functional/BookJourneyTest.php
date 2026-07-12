<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Book\BookEditor;
use App\Book\BookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookJourneyTest extends WebTestCase
{
    public function testCreateAddPagesAndPrint(): void
    {
        $client = static::createClient();
        $editor = static::getContainer()->get(BookEditor::class);
        self::assertInstanceOf(BookEditor::class, $editor);

        $book = $editor->create('Test Journey', 'Les forces de la nature');
        $editor->addPage($book, 'cover-front');
        $editor->addPage($book, 'skill-tree');
        $editor->addPage($book, 'cover-back');

        try {
            $client->request('GET', '/');
            self::assertResponseIsSuccessful();

            $client->request('GET', '/books/'.$book->id());
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('.editor__catalog');

            $client->request('GET', '/books/'.$book->id().'/print');
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('.cover--recto');
            self::assertSelectorExists('.tree-planche');
            self::assertSelectorExists('.cover--verso');
        } finally {
            $repository = static::getContainer()->get(BookRepository::class);
            self::assertInstanceOf(BookRepository::class, $repository);
            $repository->delete($book->id());
        }
    }

    public function testMissingBookReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/books/does-not-exist');
        self::assertResponseStatusCodeSame(404);
    }
}
