<?php

declare(strict_types=1);

namespace App\Book\Exception;

use App\Exception\CatalystException;

final class BookNotFoundException extends CatalystException
{
    public static function forId(string $id): self
    {
        return new self(\sprintf('No book found with id "%s".', $id));
    }

    public function statusCode(): int
    {
        return 404;
    }
}
