<?php

namespace App\Enum;

enum TagCategory: int
{
    case MAIN = 0;
    case DOMAIN = 1;
    case SIZE = 2;
    case OTHER = 9;

    public function priority(): int
    {
        return $this->value;
    }
}
