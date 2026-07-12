<?php

declare(strict_types=1);

namespace App\Book\Model;

/**
 * One page placed in a book: which page type renders it, plus the customizable
 * data for this instance (text, chosen image, options). The data shape is owned
 * and validated by the page type, so it stays an opaque map here.
 */
final class Page
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private readonly string $id,
        private readonly string $type,
        private array $data,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array{id: string, type: string, data: array<string, mixed>}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }

    /**
     * @param array{id: string, type: string, data: array<string, mixed>} $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self($raw['id'], $raw['type'], $raw['data']);
    }
}
