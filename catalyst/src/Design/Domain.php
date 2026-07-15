<?php

declare(strict_types=1);

namespace App\Design;

/**
 * A source of power a skill node can belong to.
 *
 * Case values are English slugs (the single vocabulary used in data and code);
 * display names come from {@see labelFr()} / {@see labelEn()}. A node carries
 * zero, one, or two domains; zero renders as "Neutre"/"Neutral" (see the neutral
 * constants and {@see DomainSet}).
 */
enum Domain: string
{
    case Fire = 'fire';
    case Water = 'water';
    case Wind = 'wind';
    case Earth = 'earth';
    case Thunder = 'thunder';
    case Slime = 'slime';
    case Physical = 'physical';
    case Psychic = 'psychic';
    case Light = 'light';
    case Void = 'void';
    case Blood = 'blood';

    /** Rendering for a node with no domain. */
    public const string NEUTRAL_COLOR = '#2a2a2e';
    public const string NEUTRAL_LABEL_FR = 'Neutre';
    public const string NEUTRAL_LABEL_EN = 'Neutral';

    public function color(): string
    {
        return match ($this) {
            self::Fire => '#c0392b',
            self::Water => '#1f7fc0',
            self::Wind => '#2e9d8f',
            self::Earth => '#8a6d3b',
            self::Thunder => '#6d4db0',
            self::Slime => '#6f9b1e',
            self::Physical => '#e67e22',
            self::Psychic => '#c0397f',
            self::Light => '#f4c84e',
            self::Void => '#201460',
            self::Blood => '#9c1527',
        };
    }

    public function labelFr(): string
    {
        return match ($this) {
            self::Fire => 'Feu',
            self::Water => 'Eau',
            self::Wind => 'Vent',
            self::Earth => 'Terre',
            self::Thunder => 'Foudre',
            self::Slime => 'Poisse',
            self::Physical => 'Physique',
            self::Psychic => 'Psychique',
            self::Light => 'Lumière',
            self::Void => 'Vide',
            self::Blood => 'Sang',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::Fire => 'Fire',
            self::Water => 'Water',
            self::Wind => 'Wind',
            self::Earth => 'Earth',
            self::Thunder => 'Thunder',
            self::Slime => 'Slime',
            self::Physical => 'Physical',
            self::Psychic => 'Psychic',
            self::Light => 'Light',
            self::Void => 'Void',
            self::Blood => 'Blood',
        };
    }

    public function label(string $locale = 'fr'): string
    {
        return 'en' === $locale ? $this->labelEn() : $this->labelFr();
    }
}
