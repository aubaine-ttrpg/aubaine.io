<?php

declare(strict_types=1);

namespace App\Page;

use App\Page\Exception\UnknownPageTypeException;

/**
 * Collects every {@see PageTypeInterface} tagged in the container and resolves
 * them by key. This is the single lookup the editor and the print renderer use,
 * so no code carries a switch over page types.
 */
final class PageTypeRegistry
{
    /** @var array<string, PageTypeInterface> */
    private array $byKey = [];

    /**
     * @param iterable<PageTypeInterface> $pageTypes
     */
    public function __construct(iterable $pageTypes)
    {
        foreach ($pageTypes as $pageType) {
            $this->byKey[$pageType->key()] = $pageType;
        }
    }

    public function has(string $key): bool
    {
        return isset($this->byKey[$key]);
    }

    public function get(string $key): PageTypeInterface
    {
        return $this->byKey[$key] ?? throw UnknownPageTypeException::forKey($key);
    }

    /**
     * @return list<PageTypeInterface>
     */
    public function all(): array
    {
        return array_values($this->byKey);
    }

    /**
     * Page types grouped by category, for a sectioned catalog.
     *
     * @return array<string, list<PageTypeInterface>>
     */
    public function byCategory(): array
    {
        $grouped = [];
        foreach ($this->byKey as $pageType) {
            $grouped[$pageType->category()][] = $pageType;
        }

        return $grouped;
    }
}
