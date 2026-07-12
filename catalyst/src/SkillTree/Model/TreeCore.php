<?php

declare(strict_types=1);

namespace App\SkillTree\Model;

use App\Design\DomainSet;

/** The emblem at the centre of a tree. */
final readonly class TreeCore
{
    public function __construct(
        public string $label,
        public ?string $sublabel,
        public DomainSet $domains,
        public ?Position $pos,
    ) {
    }
}
