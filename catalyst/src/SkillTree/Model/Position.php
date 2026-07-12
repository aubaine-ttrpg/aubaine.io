<?php

declare(strict_types=1);

namespace App\SkillTree\Model;

/** A point on the tree planche, in percent of the canvas (0..100). */
final readonly class Position
{
    public function __construct(
        public float $x,
        public float $y,
    ) {
    }
}
