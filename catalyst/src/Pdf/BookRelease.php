<?php

declare(strict_types=1);

namespace App\Pdf;

use App\Book\Version;

/**
 * A book's full release identity: the authored {@see Version} plus the two
 * computed hashes. This is what the version stamp `vMAJOR.MINOR.WEBPACK.BOOK`
 * and the cached PDF filename `bookName_webpack_book.pdf` are built from, so the
 * printed cover and the file on disk always agree.
 */
final readonly class BookRelease
{
    public function __construct(
        public Version $version,
        public string $webpackHash,
        public string $bookHash,
    ) {
    }

    /** Front-cover form: major/minor only, no hashes. e.g. "v0.1". */
    public function short(): string
    {
        return $this->version->short();
    }

    /** Back-cover form: the full stamp. e.g. "v0.1.a1b2c3d4.e5f6a7b8". */
    public function full(): string
    {
        return \sprintf('%s.%s.%s', $this->version->short(), $this->webpackHash, $this->bookHash);
    }

    /** Content-addressed cache filename, e.g. "druide_a1b2c3d4_e5f6a7b8.pdf". */
    public function fileName(string $bookName): string
    {
        return \sprintf('%s_%s_%s.pdf', $bookName, $this->webpackHash, $this->bookHash);
    }
}
