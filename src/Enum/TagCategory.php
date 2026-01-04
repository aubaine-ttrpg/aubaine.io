<?php

namespace App\Enum;

enum TagCategory: int
{
    case OTHER = 999; // Reptiloid / Bloodline:Drake / Totem / etc. — very specific stuff
    case MAIN = 10; // Spell / Maneuver / Shout / Ritual / Stance / Reaction / etc.
    case ELEMENT = 20; // Fire / Water / Ice / Holy / Shadow / etc.
    case ARCANE_SCHOOL = 30; // Evocation / Illusion / …
    case MARTIAL_DISCIPLINE = 31; // Stealth / Swordplay / Archery / …
    case CRAFT_TRADITION = 32; // Alchemy / Engineering / Runes / …
    case SIZE = 40; // Self / Monocible / Line / Cone / Circle / Square / Cleave / Aura …
    case EFFECT = 50; // Buff / Debuff / Control / Movement / Summon
    case COMPONENTS = 60; // Material / Somatic / Verbal

    public function priority(): int
    {
        return $this->value;
    }

    public function labelKey(): string
    {
        return 'tag.category.' . $this->key();
    }

    public function placeholderKey(): string
    {
        return 'tag.category_placeholder.' . $this->key();
    }

    public function icon(): string
    {
        return match ($this) {
            self::MAIN => 'game-icons:scroll-quill',
            self::ELEMENT => 'game-icons:rune-stone',
            self::ARCANE_SCHOOL => 'game-icons:spell-book',
            self::MARTIAL_DISCIPLINE => 'game-icons:sword-spin',
            self::CRAFT_TRADITION => 'game-icons:stone-crafting',
            self::SIZE => 'game-icons:human-target',
            self::EFFECT => 'game-icons:sparkles',
            self::COMPONENTS => 'game-icons:swap-bag',
            self::OTHER => 'game-icons:price-tag',
        };
    }

    private function key(): string
    {
        return strtolower($this->name);
    }
}
