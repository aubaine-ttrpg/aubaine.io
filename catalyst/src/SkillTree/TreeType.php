<?php

declare(strict_types=1);

namespace App\SkillTree;

enum TreeType: string
{
    case Species = 'species';
    case Archetype = 'archetype';
    case Domain = 'domain';

    public function labelFr(): string
    {
        return match ($this) {
            self::Species => 'Espèce',
            self::Archetype => 'Archétype',
            self::Domain => 'Domaine',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::Species => 'Species',
            self::Archetype => 'Archetype',
            self::Domain => 'Domain',
        };
    }
}
