<?php

namespace App\Enum;

enum SkillRange: string
{
    case SPECIAL = 'special';
    case PERSONAL = 'personal';
    case WEAPONS_REACH = 'weapons_reach';
    case TOUCH = 'touch';
    case ONE_POINT_FIVE_M = '1_5m';
    case FOUR_POINT_FIVE_M = '4_5m';
    case NINE_M = '9m';
    case EIGHTEEN_M = '18m';
    case TWENTY_SEVEN_M = '27m';
    case THIRTY_SIX_M = '36m';
    case FORTY_FIVE_M = '45m';
    case NINETY_SIX_M = '96m';
}
