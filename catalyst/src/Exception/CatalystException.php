<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Base for domain failures. Implementing HttpExceptionInterface lets Symfony
 * map each one to the right HTTP status (e.g. a missing book to 404) without a
 * per-controller catch.
 */
abstract class CatalystException extends RuntimeException implements HttpExceptionInterface
{
    abstract public function statusCode(): int;

    public function getStatusCode(): int
    {
        return $this->statusCode();
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return [];
    }
}
