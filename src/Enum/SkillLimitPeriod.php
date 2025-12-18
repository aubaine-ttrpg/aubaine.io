<?php

namespace App\Enum;

enum SkillLimitPeriod: string
{
    case NONE = 'none';
    case SHORT_REST = 'short_rest';
    case LONG_REST = 'long_rest';
    case REST = 'rest';
    case DAY = 'day';
    case CAMPAIGN = 'campaign';
    case TURN = 'turn';
    case ROUND = 'round';
}
