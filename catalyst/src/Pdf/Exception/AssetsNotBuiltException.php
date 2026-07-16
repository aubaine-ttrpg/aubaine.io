<?php

declare(strict_types=1);

namespace App\Pdf\Exception;

use App\Exception\CatalystException;

/**
 * The webpack build output is missing, so the webpack hash cannot be computed.
 * Run the Encore build (`npm run dev` / `make build`) first. Maps to 500 since
 * it is a local setup problem, not a bad request.
 */
final class AssetsNotBuiltException extends CatalystException
{
    public static function missing(string $path): self
    {
        return new self(\sprintf('Webpack build output not found at "%s"; run the asset build first.', $path));
    }

    public function statusCode(): int
    {
        return 500;
    }
}
