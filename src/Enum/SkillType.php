<?php

namespace App\Enum;

enum SkillType: string
{
    case NONE = 'none';
    case ACTION = 'action';
    case BONUS = 'bonus';
    case REACTION = 'reaction';
    case ATTACK = 'attack';
    case PASSIVE = 'passive';
    case EVOLUTION = 'evolution';
    case UPGRADE = 'upgrade';

    public function icon(): string
    {
        return match ($this) {
            self::NONE => 'tabler:hexagon-filled',
            self::ACTION => 'material-symbols:circle',
            self::BONUS => 'mdi:triangle', # 'carbon:circle-filled'
            self::REACTION => 'material-symbols:square-rounded',
            self::ATTACK => 'mingcute:sword-fill',
            self::PASSIVE => 'tabler:hexagon-filled',
            self::EVOLUTION => 'icomoon-free:arrow-up',
            self::UPGRADE => 'mdi:sparkles',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NONE => 'stone',
            self::ACTION => 'red',
            self::BONUS => 'yellow',
            self::REACTION => 'indigo',
            self::ATTACK => 'orange',
            self::PASSIVE => 'emerald',
            self::EVOLUTION => 'purple',
            self::UPGRADE => 'pink',
        };
    }

    public function iconClass(): string
    {
        return match ($this) {
            default => '',
        };
    }
}
