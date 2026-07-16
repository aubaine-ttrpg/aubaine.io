<?php

declare(strict_types=1);

namespace App\SkillTree\Model;

use App\Design\Characteristic;
use App\Design\DomainSet;
use App\Design\NodeType;

/** One unlockable node of a skill tree. */
final readonly class SkillNode
{
    /** Icon filename rendered when a node has none assigned. */
    public const string PLACEHOLDER_ICON = '_placeholder.png';

    /**
     * @param list<string>         $linked          ids of visual parents; "CORE" points at the tree core
     * @param list<string>         $tags
     * @param list<Characteristic> $characteristics zero, one, or several; drives the title icons
     * @param bool                 $showXp          whether the XP coin renders; false for skills granted by
     *                                              another node rather than bought directly
     */
    public function __construct(
        public string $id,
        public string $title,
        public NodeType $type,
        public int $tier,
        public DomainSet $domains,
        public string $description,
        public ?Position $pos,
        public array $linked,
        public ?string $icon,
        public ?string $activation,
        public ?string $range,
        public ?string $duration,
        public bool $concentration,
        public ?int $energy,
        public array $tags,
        public ?string $evolvesFrom,
        public array $characteristics = [],
        public bool $showXp = true,
    ) {
    }

    /** The icon filename to render, falling back to the shared placeholder when none is assigned. */
    public function iconFile(): string
    {
        return $this->icon ?? self::PLACEHOLDER_ICON;
    }

    /** XP cost shown on the nameplate coin: 5 per tier. */
    public function xp(): int
    {
        return 5 * $this->tier;
    }

    /** Duration line, prefixed per the D&D convention when it needs concentration. */
    public function durationLine(): ?string
    {
        if (null === $this->duration) {
            return null;
        }

        return $this->concentration ? 'Concentration · '.$this->duration : $this->duration;
    }
}
