<?php

declare(strict_types=1);

namespace App\Book;

use App\Book\Model\Book;
use App\Book\Model\Page;
use App\Page\PageTypeRegistry;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Clock\ClockInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * All book mutations funnel through here: create, rename, add/update/move/remove
 * pages. Every change bumps the updated-at stamp (UTC) and persists atomically,
 * so controllers stay thin and the model holds no clock or storage.
 */
final class BookEditor
{
    public function __construct(
        private readonly BookRepository $repository,
        private readonly PageTypeRegistry $pageTypes,
        private readonly SluggerInterface $slugger,
        private readonly ClockInterface $clock,
    ) {
    }

    public function create(string $title, ?string $subtitle, BookType $bookType, Version $version): Book
    {
        $now = $this->now();
        $id = $this->repository->nextId($this->slugger->slug($title)->lower()->toString());
        $book = new Book($id, $title, $subtitle, $bookType, $version, $now, $now);
        $this->repository->save($book);

        return $book;
    }

    public function updateMeta(Book $book, string $title, ?string $subtitle, BookType $bookType, Version $version): void
    {
        $book->updateMeta($title, $subtitle, $bookType, $version);
        $this->persist($book);
    }

    public function addPage(Book $book, string $pageTypeKey): Page
    {
        $pageType = $this->pageTypes->get($pageTypeKey);
        $page = new Page(Uuid::v4()->toRfc4122(), $pageType->key(), $pageType->defaultData());
        $book->addPage($page);
        $this->persist($book);

        return $page;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updatePageData(Book $book, string $pageId, array $data): void
    {
        $book->findPage($pageId)->setData($data);
        $this->persist($book);
    }

    public function movePage(Book $book, string $pageId, int $toIndex): void
    {
        $book->movePage($pageId, $toIndex);
        $this->persist($book);
    }

    public function removePage(Book $book, string $pageId): void
    {
        $book->removePage($pageId);
        $this->persist($book);
    }

    public function delete(Book $book): void
    {
        $this->repository->delete($book->id());
    }

    private function persist(Book $book): void
    {
        $book->touch($this->now());
        $this->repository->save($book);
    }

    private function now(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($this->clock->now())
            ->setTimezone(new DateTimeZone('UTC'));
    }
}
