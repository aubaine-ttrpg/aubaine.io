<?php

declare(strict_types=1);

namespace App\Design;

/**
 * Interior paper stock for a printed page. Case values are English slugs; the
 * colour alone is a valid fallback when the optional texture file is absent.
 */
enum Paper: string
{
    case White = 'white';
    case Parchment = 'parchment';
    case Ivory = 'ivory';
    case Kraft = 'kraft';
    case Cotton = 'cotton';
    case Cream = 'cream';
    case Ember = 'ember';

    public function color(): string
    {
        return match ($this) {
            self::White => '#ffffff',
            self::Parchment => '#efe4cb',
            self::Ivory => '#f8f1e4',
            self::Kraft => '#f1e6ce',
            self::Cotton => '#f2efe8',
            self::Cream => '#f2ebdd',
            self::Ember => '#e7d6b0',
        };
    }

    /** Texture filename under assets/images/paper, or null for a flat paper. */
    public function textureFile(): ?string
    {
        return match ($this) {
            self::White => null,
            self::Parchment => 'parchemin.png',
            self::Ivory => 'ivoire.png',
            self::Kraft => 'paper-na.png',
            self::Cotton => 'paper-nb.png',
            self::Cream => 'paper-nc.png',
            self::Ember => 'feu.png',
        };
    }

    public function labelFr(): string
    {
        return match ($this) {
            self::White => 'Blanc',
            self::Parchment => 'Parchemin',
            self::Ivory => 'Ivoire',
            self::Kraft => 'Kraft',
            self::Cotton => 'Coton',
            self::Cream => 'Crème',
            self::Ember => 'Feu',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::White => 'White',
            self::Parchment => 'Parchment',
            self::Ivory => 'Ivory',
            self::Kraft => 'Kraft',
            self::Cotton => 'Cotton',
            self::Cream => 'Cream',
            self::Ember => 'Ember',
        };
    }

    public function label(string $locale = 'en'): string
    {
        return 'fr' === $locale ? $this->labelFr() : $this->labelEn();
    }
}
