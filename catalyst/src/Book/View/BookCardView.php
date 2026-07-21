<?php

declare(strict_types=1);

namespace App\Book\View;

/**
 * Render-ready data for one book preview card. All derivation (cover lookup,
 * monogram, type label key, relative time) happens in the factory so the
 * template only prints.
 */
final class BookCardView
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $titleSizeClass,
        public readonly string $typeLabelKey,
        public readonly ?string $coverImage,
        public readonly string $monogram,
        public readonly int $pageCount,
        public readonly string $updatedRelative,
        public readonly string $updatedIso,
        public readonly string $updatedTooltip,
    ) {
    }
}
