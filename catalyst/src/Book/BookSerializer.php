<?php

declare(strict_types=1);

namespace App\Book;

use App\Book\Model\Book;

/**
 * The one canonical serialization of a book. Both {@see BookRepository} (when
 * writing the file) and {@see \App\Pdf\BookFingerprint} (when hashing content)
 * go through here, so the bytes on disk and the bytes we hash are identical.
 */
final class BookSerializer
{
    public function toCanonicalJson(Book $book): string
    {
        return json_encode(
            $book->toArray(),
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR,
        )."\n";
    }
}
