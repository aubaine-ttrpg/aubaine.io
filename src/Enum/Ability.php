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
    case ANY = 'any';
    case WEAPON = 'weapon';
    case NONE = 'none';

    public function icon(): string
    {
        return match ($this) {
            self::STRENGTH => 'tabler:arm-biceps',
            self::DEXTERITY => 'tabler:feather',
            self::ENDURANCE => 'tabler:shield',
            self::INTELLIGENCE => 'tabler:brain',
            self::PERCEPTION => 'tabler:eye',
            self::CHARISMA => 'tabler:message',
            self::ANY => 'tabler:asterisk',
            self::WEAPON => 'tabler:sword',
            self::NONE => 'tabler:ban',
        };
    }
}
