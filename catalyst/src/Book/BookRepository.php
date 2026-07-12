<?php

declare(strict_types=1);

namespace App\Book;

use App\Book\Exception\BookNotFoundException;
use App\Book\Model\Book;
use RuntimeException;

/**
 * Stores books as one pretty-printed JSON file each, under a local directory.
 * Writes go through a temp file + atomic rename so a crash never leaves a
 * half-written book. Ids are constrained to a slug shape, which also blocks
 * path traversal.
 */
final class BookRepository
{
    public function __construct(private readonly string $booksDirectory)
    {
    }

    public function exists(string $id): bool
    {
        return is_file($this->path($id));
    }

    public function find(string $id): Book
    {
        $path = $this->path($id);
        if (!is_file($path)) {
            throw BookNotFoundException::forId($id);
        }

        return Book::fromArray($this->decode($path));
    }

    /**
     * @return list<Book> newest first
     */
    public function all(): array
    {
        if (!is_dir($this->booksDirectory)) {
            return [];
        }

        $books = [];
        foreach (glob($this->booksDirectory.'/*.json') ?: [] as $file) {
            $books[] = Book::fromArray($this->decode($file));
        }

        usort($books, static fn (Book $a, Book $b): int => $b->updatedAt() <=> $a->updatedAt());

        return $books;
    }

    public function save(Book $book): void
    {
        $this->ensureDirectory();

        $json = json_encode(
            $book->toArray(),
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR,
        )."\n";

        $target = $this->path($book->id());
        $tmp = $target.'.'.bin2hex(random_bytes(4)).'.tmp';

        if (false === file_put_contents($tmp, $json, \LOCK_EX)) {
            throw new RuntimeException(\sprintf('Unable to write book "%s".', $book->id()));
        }

        if (!rename($tmp, $target)) {
            @unlink($tmp);

            throw new RuntimeException(\sprintf('Unable to persist book "%s".', $book->id()));
        }
    }

    public function delete(string $id): void
    {
        $path = $this->path($id);
        if (is_file($path)) {
            unlink($path);
        }
    }

    /**
     * A unique, filesystem-safe id derived from a base slug, appending -2, -3, …
     * on collision.
     */
    public function nextId(string $baseSlug): string
    {
        $base = $this->sanitizeSlug($baseSlug);
        $candidate = $base;
        $suffix = 2;
        while ($this->exists($candidate)) {
            $candidate = $base.'-'.$suffix;
            ++$suffix;
        }

        return $candidate;
    }

    private function sanitizeSlug(string $slug): string
    {
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return '' !== $slug ? $slug : 'book';
    }

    private function path(string $id): string
    {
        if (1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id)) {
            throw BookNotFoundException::forId($id);
        }

        return $this->booksDirectory.'/'.$id.'.json';
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string $file): array
    {
        $raw = file_get_contents($file);
        if (false === $raw) {
            throw new RuntimeException(\sprintf('Unable to read "%s".', $file));
        }

        $data = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
        if (!\is_array($data)) {
            throw new RuntimeException(\sprintf('Book file "%s" is not a JSON object.', $file));
        }

        /* @var array<string, mixed> $data */
        return $data;
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->booksDirectory) && !mkdir($this->booksDirectory, 0o775, true) && !is_dir($this->booksDirectory)) {
            throw new RuntimeException(\sprintf('Unable to create "%s".', $this->booksDirectory));
        }
    }
}
