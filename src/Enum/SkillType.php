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
            self::ACTION => 'material-symbols:circle',
            self::BONUS => 'carbon:circle-filled',
            self::REACTION => 'material-symbols:square-rounded',
            self::ATTACK => 'mingcute:sword-fill',
            self::PASSIVE => 'tabler:hexagon-filled',
            self::NONE => 'tabler:hexagon-filled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTION => 'red',
            self::BONUS => 'yellow',
            self::REACTION => 'indigo',
            self::ATTACK => 'orange',
            self::PASSIVE => 'emerald',
            self::NONE => 'stone',
        };
    }

    public function iconClass(): string
    {
        return match ($this) {
            self::ATTACK => '-rotate-45',
            default => '',
        };
    }
}
