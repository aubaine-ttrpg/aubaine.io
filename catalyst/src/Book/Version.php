<?php

declare(strict_types=1);

namespace App\Book;

/**
 * The authored version of a book: a major and a minor number. This is the
 * human-owned part of the full version stamp; the webpack and book hashes are
 * computed at render time and live in {@see \App\Pdf\BookRelease}.
 */
final readonly class Version
{
    public function __construct(
        public int $major,
        public int $minor,
    ) {
    }

    /** e.g. "v0.1". */
    public function short(): string
    {
        return \sprintf('v%d.%d', $this->major, $this->minor);
    }

    /**
     * @return array{major: int, minor: int}
     */
    public function toArray(): array
    {
        return ['major' => $this->major, 'minor' => $this->minor];
    }

    /**
     * @param array<mixed, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            \is_int($raw['major'] ?? null) ? $raw['major'] : 0,
            \is_int($raw['minor'] ?? null) ? $raw['minor'] : 1,
        );
    }
}
