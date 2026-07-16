<?php

declare(strict_types=1);

namespace App\Book\Model;

use App\Book\BookType;
use App\Book\Exception\PageNotFoundException;
use App\Book\Version;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * A book: an ordered list of pages plus a little metadata. The aggregate keeps
 * its own ordering and page-membership invariants; timestamps and page creation
 * are driven from services so this class holds no clocks or id generators.
 */
final class Book
{
    /**
     * @param list<Page> $pages
     */
    public function __construct(
        private readonly string $id,
        private string $title,
        private ?string $subtitle,
        private BookType $bookType,
        private Version $version,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private array $pages = [],
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function subtitle(): ?string
    {
        return $this->subtitle;
    }

    public function bookType(): BookType
    {
        return $this->bookType;
    }

    public function version(): Version
    {
        return $this->version;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return list<Page>
     */
    public function pages(): array
    {
        return $this->pages;
    }

    public function pageCount(): int
    {
        return \count($this->pages);
    }

    public function updateMeta(string $title, ?string $subtitle, BookType $bookType, Version $version): void
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->bookType = $bookType;
        $this->version = $version;
    }

    public function addPage(Page $page): void
    {
        $this->pages[] = $page;
    }

    public function findPage(string $pageId): Page
    {
        foreach ($this->pages as $page) {
            if ($page->id() === $pageId) {
                return $page;
            }
        }

        throw PageNotFoundException::forId($this->id, $pageId);
    }

    public function removePage(string $pageId): void
    {
        $this->findPage($pageId);
        $this->pages = array_values(
            array_filter($this->pages, static fn (Page $page): bool => $page->id() !== $pageId),
        );
    }

    /**
     * Move a page to a new zero-based position, clamped into range.
     */
    public function movePage(string $pageId, int $toIndex): void
    {
        $page = $this->findPage($pageId);
        $remaining = array_values(
            array_filter($this->pages, static fn (Page $candidate): bool => $candidate->id() !== $pageId),
        );

        $toIndex = max(0, min($toIndex, \count($remaining)));
        array_splice($remaining, $toIndex, 0, [$page]);
        $this->pages = $remaining;
    }

    public function touch(DateTimeImmutable $now): void
    {
        $this->updatedAt = $now;
    }

    /**
     * @return array{id: string, title: string, subtitle: ?string, bookType: string, version: array{major: int, minor: int}, createdAt: string, updatedAt: string, pages: list<array{id: string, type: string, data: array<string, mixed>}>}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'bookType' => $this->bookType->value,
            'version' => $this->version->toArray(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'updatedAt' => $this->updatedAt->format(DateTimeInterface::ATOM),
            'pages' => array_map(static fn (Page $page): array => $page->toArray(), $this->pages),
        ];
    }

    /**
     * @param array<mixed, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        $pages = [];
        $rawPages = $raw['pages'] ?? [];
        if (\is_array($rawPages)) {
            foreach ($rawPages as $rawPage) {
                if (\is_array($rawPage)) {
                    $pages[] = Page::fromArray($rawPage);
                }
            }
        }

        $bookType = BookType::tryFrom(\is_string($raw['bookType'] ?? null) ? $raw['bookType'] : '')
            ?? BookType::Archetype;
        $version = Version::fromArray(\is_array($raw['version'] ?? null) ? $raw['version'] : []);

        return new self(
            \is_string($raw['id'] ?? null) ? $raw['id'] : '',
            \is_string($raw['title'] ?? null) ? $raw['title'] : '',
            \is_string($raw['subtitle'] ?? null) ? $raw['subtitle'] : null,
            $bookType,
            $version,
            new DateTimeImmutable(\is_string($raw['createdAt'] ?? null) ? $raw['createdAt'] : 'now'),
            new DateTimeImmutable(\is_string($raw['updatedAt'] ?? null) ? $raw['updatedAt'] : 'now'),
            $pages,
        );
    }
}
