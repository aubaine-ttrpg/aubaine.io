<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

/**
 * Base for domain failures. The central exception listener maps
 * {@see statusCode()} to the HTTP response, so controllers never translate
 * these by hand.
 */
abstract class CatalystException extends RuntimeException
{
    abstract public function statusCode(): int;
}
