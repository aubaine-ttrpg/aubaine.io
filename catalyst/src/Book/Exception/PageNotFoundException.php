<?php

declare(strict_types=1);

namespace App\Book\Exception;

use App\Exception\CatalystException;

final class PageNotFoundException extends CatalystException
{
    public static function forId(string $bookId, string $pageId): self
    {
        return new self(\sprintf('Book "%s" has no page "%s".', $bookId, $pageId));
    }

    public function statusCode(): int
    {
        return 404;
    }
}
