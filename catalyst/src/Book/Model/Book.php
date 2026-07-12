<?php

declare(strict_types=1);

namespace App\Book\Model;

use App\Book\Exception\PageNotFoundException;
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

    public function rename(string $title, ?string $subtitle): void
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
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
     * @return array{id: string, title: string, subtitle: ?string, createdAt: string, updatedAt: string, pages: list<array{id: string, type: string, data: array<string, mixed>}>}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'updatedAt' => $this->updatedAt->format(DateTimeInterface::ATOM),
            'pages' => array_map(static fn (Page $page): array => $page->toArray(), $this->pages),
        ];
    }

    /**
     * @param array{id: string, title: string, subtitle?: ?string, createdAt: string, updatedAt: string, pages?: list<array{id: string, type: string, data: array<string, mixed>}>} $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            $raw['id'],
            $raw['title'],
            $raw['subtitle'] ?? null,
            new DateTimeImmutable($raw['createdAt']),
            new DateTimeImmutable($raw['updatedAt']),
            array_map(static fn (array $page): Page => Page::fromArray($page), $raw['pages'] ?? []),
        );
    }
}
