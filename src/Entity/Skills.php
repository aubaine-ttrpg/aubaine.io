<?php

namespace App\Entity;

use App\Enum\Ability;
use App\Enum\SkillCategory;
use App\Enum\SkillDuration;
use App\Enum\SkillLimitPeriod;
use App\Enum\SkillRange;
use App\Enum\Source;
use App\Enum\SkillTag;
use App\Enum\SkillType;
use App\Repository\SkillsRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: SkillsRepository::class)]
#[ORM\Table(name: 'skills')]
class Skills
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?Ulid $id = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[A-Za-z]{6}(?:-[0-9]+)?$/', message: 'Code must be 6 letters optionally followed by "-<numbers>".')]
    private string $code = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $description = '';

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\PositiveOrZero]
    private int $energyCost = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\PositiveOrZero]
    private int $usageLimitAmount = 0;

    #[ORM\Column(enumType: SkillLimitPeriod::class)]
    private SkillLimitPeriod $usageLimitPeriod = SkillLimitPeriod::DAY;

    #[ORM\Column(enumType: SkillCategory::class)]
    private SkillCategory $category = SkillCategory::COMMON;

    #[ORM\Column(enumType: SkillType::class)]
    private SkillType $type = SkillType::NONE;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $abilities = [Ability::NONE->value];

    #[ORM\Column(enumType: SkillRange::class)]
    private SkillRange $range = SkillRange::SPECIAL;

    #[ORM\Column(enumType: SkillDuration::class)]
    private SkillDuration $duration = SkillDuration::SPECIAL;

    #[ORM\Column]
    private bool $concentration = false;

    #[ORM\Column]
    private bool $ritual = false;

    #[ORM\Column]
    private bool $attackRoll = false;

    #[ORM\Column]
    private bool $savingThrow = false;

    #[ORM\Column]
    private bool $abilityCheck = false;

    #[ORM\Column(enumType: Source::class)]
    private Source $source = Source::AUBAINE_BASE_RULES;

    #[ORM\Column]
    private bool $verbal = false;

    #[ORM\Column]
    private bool $somatic = false;

    #[ORM\Column]
    private bool $material = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $materialString = null;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $tags = [SkillTag::NONE->value];

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $icon = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEnergyCost(): int
    {
        return $this->energyCost;
    }

    public function setEnergyCost(int $energyCost): self
    {
        $this->energyCost = $energyCost;

        return $this;
    }

    public function getUsageLimitAmount(): int
    {
        return $this->usageLimitAmount;
    }

    public function setUsageLimitAmount(int $usageLimitAmount): self
    {
        $this->usageLimitAmount = $usageLimitAmount;

        return $this;
    }

    public function getUsageLimitPeriod(): SkillLimitPeriod
    {
        return $this->usageLimitPeriod;
    }

    public function setUsageLimitPeriod(SkillLimitPeriod $usageLimitPeriod): self
    {
        $this->usageLimitPeriod = $usageLimitPeriod;

        return $this;
    }

    public function getCategory(): SkillCategory
    {
        return $this->category;
    }

    public function setCategory(SkillCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): SkillType
    {
        return $this->type;
    }

    public function setType(SkillType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return list<Ability>
     */
    public function getAbilities(): array
    {
        return array_map(static fn (string $ability): Ability => Ability::from($ability), $this->abilities);
    }

    /**
     * @param list<Ability> $abilities
     */
    public function setAbilities(array $abilities): self
    {
        $values = array_values(array_unique(array_map(
            static fn (Ability $ability): string => $ability->value,
            $abilities
        )));

        // If no abilities provided, default to NONE.
        if ($values === []) {
            $values = [Ability::NONE->value];
        }

        // If something other than NONE is selected, drop NONE.
        if (count($values) > 1) {
            $values = array_values(array_filter(
                $values,
                static fn (string $val): bool => $val !== Ability::NONE->value
            ));
        }

        $this->abilities = $values;

        return $this;
    }

    public function addAbility(Ability $ability): self
    {
        if ($ability === Ability::NONE) {
            $this->abilities = [Ability::NONE->value];

            return $this;
        }

        $this->abilities = array_values(array_filter(
            $this->abilities,
            static fn (string $val): bool => $val !== Ability::NONE->value
        ));

        if (!in_array($ability->value, $this->abilities, true)) {
            $this->abilities[] = $ability->value;
        }

        return $this;
    }

    public function removeAbility(Ability $ability): self
    {
        $this->abilities = array_values(array_filter(
            $this->abilities,
            static fn (string $value): bool => $value !== $ability->value
        ));

        if ($this->abilities === []) {
            $this->abilities = [Ability::NONE->value];
        }

        return $this;
    }

    public function getRange(): SkillRange
    {
        return $this->range;
    }

    public function setRange(SkillRange $range): self
    {
        $this->range = $range;

        return $this;
    }

    public function getDuration(): SkillDuration
    {
        return $this->duration;
    }

    public function setDuration(SkillDuration $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function hasConcentration(): bool
    {
        return $this->concentration;
    }

    public function setConcentration(bool $concentration): self
    {
        $this->concentration = $concentration;

        return $this;
    }

    public function hasRitual(): bool
    {
        return $this->ritual;
    }

    public function setRitual(bool $ritual): self
    {
        $this->ritual = $ritual;

        return $this;
    }

    public function hasAttackRoll(): bool
    {
        return $this->attackRoll;
    }

    public function setAttackRoll(bool $attackRoll): self
    {
        $this->attackRoll = $attackRoll;

        return $this;
    }

    public function hasSavingThrow(): bool
    {
        return $this->savingThrow;
    }

    public function setSavingThrow(bool $savingThrow): self
    {
        $this->savingThrow = $savingThrow;

        return $this;
    }

    public function hasAbilityCheck(): bool
    {
        return $this->abilityCheck;
    }

    public function setAbilityCheck(bool $abilityCheck): self
    {
        $this->abilityCheck = $abilityCheck;

        return $this;
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function setSource(Source $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function hasVerbal(): bool
    {
        return $this->verbal;
    }

    public function setVerbal(bool $verbal): self
    {
        $this->verbal = $verbal;

        return $this;
    }

    public function hasSomatic(): bool
    {
        return $this->somatic;
    }

    public function setSomatic(bool $somatic): self
    {
        $this->somatic = $somatic;

        return $this;
    }

    public function hasMaterial(): bool
    {
        return $this->material;
    }

    public function setMaterial(bool $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getMaterialString(): ?string
    {
        return $this->materialString;
    }

    public function setMaterialString(?string $materialString): self
    {
        $this->materialString = $materialString;

        return $this;
    }

    /**
     * @return list<SkillTag>
     */
    public function getTags(): array
    {
        return array_map(static fn (string $tag): SkillTag => SkillTag::from($tag), $this->tags);
    }

    /**
     * @param list<SkillTag> $tags
     */
    public function setTags(array $tags): self
    {
        $values = array_values(array_unique(array_map(
            static fn (SkillTag $tag): string => $tag->value,
            $tags
        )));

        if ($values === []) {
            $values = [SkillTag::NONE->value];
        }

        if (count($values) > 1) {
            $values = array_values(array_filter(
                $values,
                static fn (string $val): bool => $val !== SkillTag::NONE->value
            ));
        }

        $this->tags = $values;

        return $this;
    }

    public function addTag(SkillTag $tag): self
    {
        if ($tag === SkillTag::NONE) {
            $this->tags = [SkillTag::NONE->value];

            return $this;
        }

        $this->tags = array_values(array_filter(
            $this->tags,
            static fn (string $val): bool => $val !== SkillTag::NONE->value
        ));

        if (!in_array($tag->value, $this->tags, true)) {
            $this->tags[] = $tag->value;
        }

        return $this;
    }

    public function removeTag(SkillTag $tag): self
    {
        $this->tags = array_values(array_filter(
            $this->tags,
            static fn (string $value): bool => $value !== $tag->value
        ));

        if ($this->tags === []) {
            $this->tags = [SkillTag::NONE->value];
        }

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
