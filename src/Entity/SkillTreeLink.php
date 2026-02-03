<?php

namespace App\Entity;

use App\Repository\SkillTreeLinkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: SkillTreeLinkRepository::class)]
#[ORM\Table(
    name: 'skill_tree_links',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'skill_tree_link_unique', columns: ['tree_id', 'from_node_id', 'to_node_id']),
    ],
)]
class SkillTreeLink
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?Ulid $id = null;

    #[ORM\ManyToOne(targetEntity: SkillTree::class, inversedBy: 'links')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SkillTree $tree = null;

    #[ORM\ManyToOne(targetEntity: SkillTreeNode::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SkillTreeNode $fromNode = null;

    #[ORM\ManyToOne(targetEntity: SkillTreeNode::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?SkillTreeNode $toNode = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDirected = false;

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

    public function getFromNode(): ?SkillTreeNode
    {
        return $this->fromNode;
    }

    public function setFromNode(?SkillTreeNode $fromNode): self
    {
        $this->fromNode = $fromNode;

        return $this;
    }

    public function getToNode(): ?SkillTreeNode
    {
        return $this->toNode;
    }

    public function setToNode(?SkillTreeNode $toNode): self
    {
        $this->toNode = $toNode;

        return $this;
    }

    public function isDirected(): bool
    {
        return $this->isDirected;
    }

    public function setIsDirected(bool $isDirected): self
    {
        $this->isDirected = $isDirected;

        return $this;
    }
}
