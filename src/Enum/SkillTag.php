<?php

namespace App\Enum;

enum SkillTag: string
{
    case NONE = 'none';
    case FIRE = 'fire';
    case WATER = 'water';
    case ICE = 'ice';
    case LIGHTNING = 'lightning';
    case EARTH = 'earth';
    case WIND = 'wind';
    case HOLY = 'holy';
    case SHADOW = 'shadow';
    case POISON = 'poison';
    case ACID = 'acid';
    case FORCE = 'force';
    case AREA = 'area';
    case SPELL = 'spell';
    case MANEUVERS = 'maneuvers';
    case MOVEMENT = 'movement';
    case SUMMONING = 'summoning';
    case CONTROL = 'control';
    case HEALING = 'healing';
    case BUFF = 'buff';
    case DEBUFF = 'debuff';
    case RANGED = 'ranged';
    case MELEE = 'melee';
    case WEAPON = 'weapon';
    case AURA = 'aura';
    case TRAP = 'trap';
    case UTILITY = 'utility';
    case CROWD_CONTROL = 'crowd_control';
}
