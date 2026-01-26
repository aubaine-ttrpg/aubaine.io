<?php

namespace App\Entity;

use App\Enum\Ability;
use App\Enum\Aptitude;
use App\Enum\SkillCategory;
use App\Entity\SkillsTranslation;
use App\Repository\SkillsRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SkillsRepository::class)]
#[ORM\Table(name: 'skills')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\TranslationEntity(class: SkillsTranslation::class)]
class Skills
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?Ulid $id = null;

    #[ORM\Column(length: 64, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[A-Za-z0-9]{6}(?:-[0-9]+)?$/', message: 'Code must be 6 letters or numbers optionally followed by "-<numbers>".')]
    private string $code = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    #[Gedmo\Translatable]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Gedmo\Translatable]
    private string $description = '';

    #[ORM\Column]
    private bool $ultimate = false;

    #[ORM\Column(enumType: SkillCategory::class)]
    private SkillCategory $category = SkillCategory::NONE;

    #[ORM\Column(enumType: Ability::class)]
    private Ability $ability = Ability::NONE;

    #[ORM\Column(enumType: Aptitude::class)]
    private Aptitude $aptitude = Aptitude::NONE;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $limitations = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $requirements = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $energy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $prerequisites = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $timing = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $range = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $duration = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $tags = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $icon = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTimeImmutable $updatedAt;

    #[Gedmo\Locale]
    private ?string $locale = null;

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

    public function isUltimate(): bool
    {
        return $this->ultimate;
    }

    public function setUltimate(bool $ultimate): self
    {
        $this->ultimate = $ultimate;

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

    public function getAbility(): Ability
    {
        return $this->ability;
    }

    public function setAbility(Ability $ability): self
    {
        $this->ability = $ability;

        return $this;
    }

    public function getAptitude(): Aptitude
    {
        return $this->aptitude;
    }

    public function setAptitude(Aptitude $aptitude): self
    {
        $this->aptitude = $aptitude;

        return $this;
    }

    public function getLimitations(): ?string
    {
        return $this->limitations;
    }

    public function setLimitations(?string $limitations): self
    {
        $this->limitations = $limitations;

        return $this;
    }

    public function getRequirements(): ?string
    {
        return $this->requirements;
    }

    public function setRequirements(?string $requirements): self
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function getEnergy(): ?string
    {
        return $this->energy;
    }

    public function setEnergy(?string $energy): self
    {
        $this->energy = $energy;

        return $this;
    }

    public function getPrerequisites(): ?string
    {
        return $this->prerequisites;
    }

    public function setPrerequisites(?string $prerequisites): self
    {
        $this->prerequisites = $prerequisites;

        return $this;
    }

    public function getTiming(): ?string
    {
        return $this->timing;
    }

    public function setTiming(?string $timing): self
    {
        $this->timing = $timing;

        return $this;
    }

    public function getRange(): ?string
    {
        return $this->range;
    }

    public function setRange(?string $range): self
    {
        $this->range = $range;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

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

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setTranslatableLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
