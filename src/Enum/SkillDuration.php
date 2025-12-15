<?php

namespace App\Enum;

enum SkillDuration: string
{
    case SPECIAL = 'special';
    case INSTANTANEOUS = 'instantaneous';
    case ONE_ROUND = '1_round';
    case ONE_MINUTE = '1_minute';
    case TEN_MINUTES = '10_minutes';
    case ONE_HOUR = '1_hour';
    case FOUR_HOURS = '4_hours';
    case EIGHT_HOURS = '8_hours';
    case SIXTEEN_HOURS = '16_hours';
    case TWENTY_FOUR_HOURS = '24_hours';
}
