<?php

namespace App\Enum;

enum SkillCategory: string
{
    case NONE = 'none';
    case BASIC = 'basic';
    case COMMON = 'common';
    case EXCLUSIVE = 'exclusive';
    case RACIAL = 'racial';
    case SIGNATURE = 'signature';
}
