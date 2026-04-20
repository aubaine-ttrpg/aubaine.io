<?php

namespace App\Enum;

enum Ability: string
{
    case NONE = 'none';
    case ANY = 'any';
    case SPECIAL = 'special';
    case STRENGTH = 'strength';
    case DEXTERITY = 'dexterity';
    case CONSTITUTION = 'endurance';
    case INTELLIGENCE = 'intelligence';
    case PERCEPTION = 'perception';
    case CHARISMA = 'charisma';
    case SPIRIT = 'spirit';
    case WEAPON = 'weapon';
    case LUCK = 'luck';

    public function icon(): string
    {
        return match ($this) {
            self::STRENGTH => 'game-icons:biceps',
            self::DEXTERITY => 'game-icons:walking-boot',
            self::LUCK => 'mdi:clover',
            self::CHARISMA => 'game-icons:duality-mask',
            self::INTELLIGENCE => 'game-icons:brain',
            self::PERCEPTION => 'mdi:eye',
            self::CONSTITUTION => 'game-icons:heart-organ',
            self::WEAPON => 'game-icons:axe-sword',
            self::ANY => 'mynaui:asterisk-hexagon-solid',
            self::SPIRIT => 'game-icons:spirit',
            self::NONE => '',
        };
    }
}
