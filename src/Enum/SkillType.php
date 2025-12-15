<?php

namespace App\Enum;

enum SkillType: string
{
    case ACTION = 'action';
    case BONUS = 'bonus';
    case REACTION = 'reaction';
    case ATTACK = 'attack';
    case PASSIVE = 'passive';
    case NONE = 'none';

    public function icon(): string
    {
        return match ($this) {
            self::ACTION => 'tabler:run',
            self::BONUS => 'tabler:plus',
            self::REACTION => 'tabler:bolt',
            self::ATTACK => 'tabler:target-arrow',
            self::PASSIVE => 'tabler:circle-dotted',
            self::NONE => 'tabler:ban',
        };
    }
}
