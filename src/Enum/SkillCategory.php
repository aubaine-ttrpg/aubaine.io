<?php

namespace App\Enum;

enum SkillCategory: string
{
    case COMMON = 'common';
    case GENERAL = 'general';
    case EXCLUSIVE = 'exclusive';
    case RACIAL = 'racial';
    case SIGNATURE = 'signature';
}
