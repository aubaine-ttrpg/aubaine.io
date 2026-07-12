<?php

declare(strict_types=1);

namespace App\Design;

/**
 * The kind of a skill node, and the name of the shape family it renders as.
 *
 * The enum owns only the semantic mapping (active -> circle, passive -> square,
 * evolution -> octagon, special -> concave). The actual geometry (border-radius,
 * clip-path polygons) is CSS: the node template applies `tree-node--<shapeKey>`
 * and the stylesheet draws it.
 */
enum NodeType: string
{
    case Passive = 'passive';
    case Active = 'active';
    case Evolution = 'evolution';
    case Special = 'special';

    public function labelFr(): string
    {
        return match ($this) {
            self::Passive => 'Passive',
            self::Active => 'Active',
            self::Evolution => 'Évolution',
            self::Special => 'Spéciale',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::Passive => 'Passive',
            self::Active => 'Active',
            self::Evolution => 'Evolution',
            self::Special => 'Special',
        };
    }

    public function label(string $locale = 'en'): string
    {
        return 'fr' === $locale ? $this->labelFr() : $this->labelEn();
    }

    /** Shape-family key; the stylesheet defines `.tree-node--<key>`. */
    public function shapeKey(): string
    {
        return match ($this) {
            self::Active => 'circle',
            self::Passive => 'square',
            self::Evolution => 'octagon',
            self::Special => 'concave',
        };
    }
}
