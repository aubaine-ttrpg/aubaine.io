<?php

namespace App\Entity;

use App\Entity\SkillTreeTranslation;
use App\Repository\SkillTreeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: SkillTreeRepository::class)]
#[ORM\Table(name: 'skill_trees')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\TranslationEntity(class: SkillTreeTranslation::class)]
class SkillTree
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?Ulid $id = null;

    #[ORM\Column(length: 64, unique: true)]
    private string $code = '';

    #[ORM\Column(length: 120)]
    #[Gedmo\Translatable]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    private ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $columns = 9;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $rows = 9;

    /** @var Collection<int, SkillTreeNode> */
    #[ORM\OneToMany(mappedBy: 'tree', targetEntity: SkillTreeNode::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['row' => 'ASC', 'col' => 'ASC'])]
    private Collection $nodes;

    /** @var Collection<int, SkillTreeLink> */
    #[ORM\OneToMany(mappedBy: 'tree', targetEntity: SkillTreeLink::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $links;

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
        $this->nodes = new ArrayCollection();
        $this->links = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function setColumns(int $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function setRows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return Collection<int, SkillTreeNode>
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(SkillTreeNode $node): self
    {
        if (!$this->nodes->contains($node)) {
            $this->nodes->add($node);
            $node->setTree($this);
        }

        return $this;
    }

    public function removeNode(SkillTreeNode $node): self
    {
        if ($this->nodes->removeElement($node)) {
            if ($node->getTree() === $this) {
                $node->setTree(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SkillTreeLink>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(SkillTreeLink $link): self
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
            $link->setTree($this);
        }

        return $this;
    }

    public function removeLink(SkillTreeLink $link): self
    {
        if ($this->links->removeElement($link)) {
            if ($link->getTree() === $this) {
                $link->setTree(null);
            }
        }

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

    public function setTranslatableLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
