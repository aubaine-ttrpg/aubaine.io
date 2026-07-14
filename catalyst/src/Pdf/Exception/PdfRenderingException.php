<?php

declare(strict_types=1);

namespace App\Pdf\Exception;

use App\Exception\CatalystException;
use Throwable;

/**
 * The PDF renderer (Gotenberg) could not produce the document: the container is
 * unreachable, timed out, or returned an error. Maps to 502 (bad upstream).
 */
final class PdfRenderingException extends CatalystException
{
    public static function forBook(string $id, Throwable $previous): self
    {
        return new self(\sprintf('Could not render the PDF for book "%s".', $id), previous: $previous);
    }

    public function statusCode(): int
    {
        return 502;
    }
}
