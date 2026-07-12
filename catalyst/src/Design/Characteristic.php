<?php

declare(strict_types=1);

namespace App\Design;

/**
 * The characteristic a skill is tied to. Each maps to a UX icon shown before
 * the skill title. "Any" is the wildcard (asterisk); a skill may carry none,
 * one, or several.
 */
enum Characteristic: string
{
    case Strength = 'strength';
    case Dexterity = 'dexterity';
    case Endurance = 'endurance';
    case Intelligence = 'intelligence';
    case Spirit = 'spirit';
    case Charisma = 'charisma';
    case Any = 'any';

    /** UX icon name (see `ux:icons:import`). */
    public function iconName(): string
    {
        return match ($this) {
            self::Strength => 'game-icons:biceps',
            self::Dexterity => 'game-icons:walking-boot',
            self::Endurance => 'game-icons:heart-organ',
            self::Intelligence => 'game-icons:brain',
            self::Spirit => 'game-icons:spiked-halo',
            self::Charisma => 'game-icons:duality-mask',
            self::Any => 'mdi:asterisk',
        };
    }

    /**
     * Hexagon-badge colour; the stat icon sits on top in white. Muted,
     * print-friendly tones (dark enough for white icons to stay legible).
     */
    public function color(): string
    {
        return match ($this) {
            self::Strength => '#b5372e',      // red
            self::Dexterity => '#4a8b46',     // green
            self::Endurance => '#c8702a',     // amber
            self::Intelligence => '#2f6cae',  // blue
            self::Spirit => '#c15b93',        // pink
            self::Charisma => '#b8901e',      // gold-yellow
            self::Any => '#8a7c5c',           // neutral
        };
    }

    public function labelFr(): string
    {
        return match ($this) {
            self::Strength => 'Force',
            self::Dexterity => 'Dextérité',
            self::Endurance => 'Endurance',
            self::Intelligence => 'Intelligence',
            self::Spirit => 'Esprit',
            self::Charisma => 'Charisme',
            self::Any => 'Toute caractéristique',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::Strength => 'Strength',
            self::Dexterity => 'Dexterity',
            self::Endurance => 'Endurance',
            self::Intelligence => 'Intelligence',
            self::Spirit => 'Spirit',
            self::Charisma => 'Charisma',
            self::Any => 'Any characteristic',
        };
    }

    public function label(string $locale = 'fr'): string
    {
        return 'en' === $locale ? $this->labelEn() : $this->labelFr();
    }
}
