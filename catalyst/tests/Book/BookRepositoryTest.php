<?php

declare(strict_types=1);

namespace App\Tests\Book;

use App\Book\BookRepository;
use App\Book\Exception\BookNotFoundException;
use App\Book\Model\Book;
use App\Book\Model\Page;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

final class BookRepositoryTest extends TestCase
{
    private string $dir;
    private BookRepository $repository;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/catalyst-books-'.bin2hex(random_bytes(4));
        $this->repository = new BookRepository($this->dir);
    }

    protected function tearDown(): void
    {
        if (!is_dir($this->dir)) {
            return;
        }
        $files = glob($this->dir.'/*');
        foreach (\is_array($files) ? $files : [] as $file) {
            unlink($file);
        }
        rmdir($this->dir);
    }

    public function testSaveThenFindRoundTrips(): void
    {
        $at = new DateTimeImmutable('2026-01-02T03:04:05+00:00');
        $book = new Book('parangon', 'Parangon', 'Les protecteurs', $at, $at, [
            new Page('p1', 'cover-front', ['title' => 'Parangon']),
        ]);
        $this->repository->save($book);

        $found = $this->repository->find('parangon');

        self::assertSame('Parangon', $found->title());
        self::assertSame('Les protecteurs', $found->subtitle());
        self::assertCount(1, $found->pages());
        self::assertSame('cover-front', $found->pages()[0]->type());
        self::assertSame(['title' => 'Parangon'], $found->pages()[0]->data());
        self::assertSame('2026-01-02T03:04:05+00:00', $found->createdAt()->format(DateTimeInterface::ATOM));
    }

    public function testFindMissingBookThrows(): void
    {
        $this->expectException(BookNotFoundException::class);
        $this->repository->find('missing');
    }

    public function testExistsReflectsSaveAndDelete(): void
    {
        $at = new DateTimeImmutable('2026-01-01T00:00:00+00:00');
        self::assertFalse($this->repository->exists('x'));

        $this->repository->save(new Book('x', 'X', null, $at, $at));
        self::assertTrue($this->repository->exists('x'));

        $this->repository->delete('x');
        self::assertFalse($this->repository->exists('x'));
    }

    public function testNextIdAppendsSuffixOnCollision(): void
    {
        $at = new DateTimeImmutable('2026-01-01T00:00:00+00:00');
        self::assertSame('feu', $this->repository->nextId('Feu'));

        $this->repository->save(new Book('feu', 'Feu', null, $at, $at));
        self::assertSame('feu-2', $this->repository->nextId('Feu'));
    }

    public function testAllReturnsNewestFirst(): void
    {
        $old = new DateTimeImmutable('2026-01-01T00:00:00+00:00');
        $new = new DateTimeImmutable('2026-02-01T00:00:00+00:00');
        $this->repository->save(new Book('a', 'A', null, $old, $old));
        $this->repository->save(new Book('b', 'B', null, $new, $new));

        $all = $this->repository->all();
        self::assertCount(2, $all);
        self::assertSame('b', $all[0]->id());
        self::assertSame('a', $all[1]->id());
    }
}
