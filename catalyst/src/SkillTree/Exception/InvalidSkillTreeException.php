<?php

declare(strict_types=1);

namespace App\SkillTree\Exception;

use App\Exception\CatalystException;

/**
 * A tree JSON file does not match skilltree.schema.json. This is bad app data,
 * not user input, so it surfaces as a server error.
 */
final class InvalidSkillTreeException extends CatalystException
{
    public static function forId(string $id, string $detail): self
    {
        return new self(\sprintf('Skill tree "%s" does not match the schema: %s', $id, $detail));
    }

    public function statusCode(): int
    {
        return 500;
    }
}
