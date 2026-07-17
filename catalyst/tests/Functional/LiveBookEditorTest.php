<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Book\BookEditor;
use App\Book\BookRepository;
use App\Book\BookType;
use App\Book\Model\Book;
use App\Book\Version;
use App\Twig\Components\LiveBookEditor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

final class LiveBookEditorTest extends WebTestCase
{
    use InteractsWithLiveComponents;

    public function testAddPageThroughTheComponentPersistsAndSelectsIt(): void
    {
        $book = $this->createBook();

        try {
            $component = $this->createLiveComponent('LiveBookEditor', ['bookId' => $book->id()]);
            $component->call('addPage', ['type' => 'cover-front']);

            $state = $component->component();
            self::assertInstanceOf(LiveBookEditor::class, $state);
            self::assertSame(1, $state->previewNonce, 'adding a page must bump the preview nonce');

            $saved = $this->repository()->find($book->id());
            self::assertCount(1, $saved->pages());
            self::assertSame('cover-front', $saved->pages()[0]->type());
            self::assertSame($saved->pages()[0]->id(), $state->selectedPageId, 'the new page becomes selected');
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testSelectPageDoesNotReloadThePreview(): void
    {
        $book = $this->createBook();
        $editor = $this->editor();
        $editor->addPage($book, 'cover-front');
        $second = $editor->addPage($book, 'cover-back');

        try {
            $component = $this->createLiveComponent('LiveBookEditor', ['bookId' => $book->id()]);
            $component->call('selectPage', ['pageId' => $second->id()]);

            $state = $component->component();
            self::assertInstanceOf(LiveBookEditor::class, $state);
            self::assertSame($second->id(), $state->selectedPageId);
            self::assertSame(0, $state->previewNonce, 'selection must not trigger a preview reload');
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testMovePageReordersThroughTheComponent(): void
    {
        $book = $this->createBook();
        $editor = $this->editor();
        $first = $editor->addPage($book, 'cover-front');
        $second = $editor->addPage($book, 'cover-back');

        try {
            $component = $this->createLiveComponent('LiveBookEditor', ['bookId' => $book->id()]);
            $component->call('movePage', ['pageId' => $second->id(), 'direction' => 'up']);

            $saved = $this->repository()->find($book->id());
            self::assertSame($second->id(), $saved->pages()[0]->id());
            self::assertSame($first->id(), $saved->pages()[1]->id());
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testDeletePageRemovesItAndReselectsARemainingPage(): void
    {
        $book = $this->createBook();
        $editor = $this->editor();
        $first = $editor->addPage($book, 'cover-front');
        $second = $editor->addPage($book, 'cover-back');

        try {
            $component = $this->createLiveComponent('LiveBookEditor', ['bookId' => $book->id(), 'selectedPageId' => $first->id()]);
            $component->call('deletePage', ['pageId' => $first->id()]);

            $state = $component->component();
            self::assertInstanceOf(LiveBookEditor::class, $state);
            self::assertSame($second->id(), $state->selectedPageId, 'deleting the selected page reselects a remaining one');

            $saved = $this->repository()->find($book->id());
            self::assertCount(1, $saved->pages());
            self::assertSame($second->id(), $saved->pages()[0]->id());
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testSavePersistsCustomizedPageData(): void
    {
        $book = $this->createBook();
        $page = $this->editor()->addPage($book, 'cover-front');

        try {
            $component = $this->createLiveComponent('LiveBookEditor', ['bookId' => $book->id(), 'selectedPageId' => $page->id()]);
            $component->submitForm(['cover_front' => ['title' => 'Renamed Cover']], 'save');

            $saved = $this->repository()->find($book->id());
            self::assertSame('Renamed Cover', $saved->findPage($page->id())->data()['title'] ?? null);
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testSaveWithBlankTitleIsRejected(): void
    {
        $book = $this->createBook();
        $page = $this->editor()->addPage($book, 'cover-front');

        try {
            $component = $this->createLiveComponent('LiveBookEditor', ['bookId' => $book->id(), 'selectedPageId' => $page->id()]);

            $this->expectException(UnprocessableEntityHttpException::class);
            $component->submitForm(['cover_front' => ['title' => '']], 'save');
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testNoJsFallbackRendersTheSelectedPageForm(): void
    {
        $client = static::createClient();
        $editor = static::getContainer()->get(BookEditor::class);
        self::assertInstanceOf(BookEditor::class, $editor);

        $book = $editor->create('Fallback Book', null, BookType::Archetype, new Version(0, 1));
        $editor->addPage($book, 'cover-front');
        $second = $editor->addPage($book, 'cover-back');

        try {
            $crawler = $client->request('GET', '/books/'.$book->id().'?page='.$second->id());

            self::assertResponseIsSuccessful();
            // The properties panel is server-rendered with the selected page's form,
            // posting to that page's edit route (the no-JS save path).
            self::assertGreaterThan(0, $crawler->filter('form[action$="/pages/'.$second->id().'"]')->count());
        } finally {
            $repository = static::getContainer()->get(BookRepository::class);
            self::assertInstanceOf(BookRepository::class, $repository);
            $repository->delete($book->id());
        }
    }

    private function editor(): BookEditor
    {
        $editor = static::getContainer()->get(BookEditor::class);
        self::assertInstanceOf(BookEditor::class, $editor);

        return $editor;
    }

    private function repository(): BookRepository
    {
        $repository = static::getContainer()->get(BookRepository::class);
        self::assertInstanceOf(BookRepository::class, $repository);

        return $repository;
    }

    private function createBook(): Book
    {
        return $this->editor()->create('Live Editor Test', null, BookType::Archetype, new Version(0, 1));
    }

    private function deleteBook(string $id): void
    {
        $this->repository()->delete($id);
    }
}
