<?php

declare(strict_types=1);

namespace App\SkillTree\Exception;

use App\Exception\CatalystException;

final class SkillTreeNotFoundException extends CatalystException
{
    public static function forId(string $id): self
    {
        return new self(\sprintf('No skill tree found with id "%s".', $id));
    }

    public function statusCode(): int
    {
        return 404;
    }
}
