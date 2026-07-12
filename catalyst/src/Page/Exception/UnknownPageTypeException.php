<?php

declare(strict_types=1);

namespace App\Page\Exception;

use App\Exception\CatalystException;

final class UnknownPageTypeException extends CatalystException
{
    public static function forKey(string $key): self
    {
        return new self(\sprintf('Unknown page type "%s".', $key));
    }

    public function statusCode(): int
    {
        return 404;
    }
}
