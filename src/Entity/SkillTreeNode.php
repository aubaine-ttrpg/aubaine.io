<?php

namespace App\Entity;

use App\Entity\Skills;
use App\Repository\SkillTreeNodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: SkillTreeNodeRepository::class)]
#[ORM\Table(
    name: 'skill_tree_nodes',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'skill_tree_node_cell_unique', columns: ['tree_id', 'row', 'col']),
    ],
)]
class SkillTreeNode
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?Ulid $id = null;

    #[ORM\ManyToOne(targetEntity: SkillTree::class, inversedBy: 'nodes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SkillTree $tree = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $row = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $col = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $cost = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isStarter = false;

    #[ORM\ManyToOne(targetEntity: Skills::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Skills $skill = null;

    /** @var array<string, mixed>|null */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $anonPayload = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getTree(): ?SkillTree
    {
        return $this->tree;
    }

    public function setTree(?SkillTree $tree): self
    {
        $this->tree = $tree;

        return $this;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function setRow(int $row): self
    {
        $this->row = $row;

        return $this;
    }

    public function getCol(): int
    {
        return $this->col;
    }

    public function setCol(int $col): self
    {
        $this->col = $col;

        return $this;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function isStarter(): bool
    {
        return $this->isStarter;
    }

    public function setIsStarter(bool $isStarter): self
    {
        $this->isStarter = $isStarter;

        return $this;
    }

    public function getSkill(): ?Skills
    {
        return $this->skill;
    }

    public function setSkill(?Skills $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getAnonPayload(): ?array
    {
        return $this->anonPayload;
    }

    /**
     * @param array<string, mixed>|null $anonPayload
     */
    public function setAnonPayload(?array $anonPayload): self
    {
        $this->anonPayload = $anonPayload;

        return $this;
    }
}
