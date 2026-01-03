<?php

namespace App\Enum;

enum SkillCategory: string
{
    case NONE = 'none';
    case COMMON = 'common';
    case GENERAL = 'general';
    case EXCLUSIVE = 'exclusive';
    case RACIAL = 'racial';
    case SIGNATURE = 'signature';
}
