<?php

namespace App\Enum;

enum Ability: string
{
    case STRENGTH = 'strength';
    case DEXTERITY = 'dexterity';
    case ENDURANCE = 'endurance';
    case INTELLIGENCE = 'intelligence';
    case PERCEPTION = 'perception';
    case CHARISMA = 'charisma';
    case LUCK = 'luck';
    case ANY = 'any';
    case WEAPON = 'weapon';
    case NONE = 'none';

    public function icon(): string
    {
        return match ($this) {
            self::STRENGTH => 'game-icons:biceps',
            self::DEXTERITY => 'game-icons:walking-boot',
            self::LUCK => 'mdi:clover',
            self::CHARISMA => 'game-icons:duality-mask',
            self::INTELLIGENCE => 'game-icons:brain',
            self::PERCEPTION => 'mdi:eye',
            self::ENDURANCE => 'game-icons:heart-organ',
            self::WEAPON => 'game-icons:axe-sword',
            self::ANY => 'mynaui:asterisk-hexagon-solid',
            self::NONE => '',
        };
    }
}
