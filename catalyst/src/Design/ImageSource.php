<?php

declare(strict_types=1);

namespace App\Design;

/**
 * Resolves a content image (cover, paper texture, node icon) to its source file
 * on disk. The source-side counterpart of the `catalyst_image()` Twig helper,
 * which resolves the same files to their built, fingerprinted URLs. Used to
 * fingerprint the assets a book links to.
 */
final class ImageSource
{
    public function __construct(private readonly string $imagesDirectory)
    {
    }

    public function path(string $category, string $filename): string
    {
        return $this->imagesDirectory.'/'.$category.'/'.$filename;
    }
}
