<?php

declare(strict_types=1);

namespace App\SkillTree\Model;

use App\SkillTree\TreeType;

/** A whole skill tree: a core emblem plus positioned, linked nodes. */
final readonly class SkillTree
{
    /**
     * @param list<SkillNode> $nodes
     */
    public function __construct(
        public string $id,
        public string $name,
        public TreeType $treeType,
        public int $size,
        public TreeCore $core,
        public array $nodes,
    ) {
    }

    /**
     * Nodes that appear on the planche graph: those with a position. A
     * position-less node is an entry-only skill (rendered in the ability list
     * but not as a dot), e.g. a sub-skill granted by another node.
     *
     * @return list<SkillNode>
     */
    public function positionedNodes(): array
    {
        return array_values(array_filter($this->nodes, static fn (SkillNode $node): bool => null !== $node->pos));
    }

    /**
     * Straight links to draw behind the nodes, resolved from each node's
     * `linked` list to planche coordinates (percent).
     *
     * @return list<array{x1: float, y1: float, x2: float, y2: float}>
     */
    public function edges(): array
    {
        $positions = [];
        foreach ($this->nodes as $node) {
            if (null !== $node->pos) {
                $positions[$node->id] = $node->pos;
            }
        }

        $edges = [];
        foreach ($this->nodes as $node) {
            if (null === $node->pos) {
                continue;
            }
            foreach ($node->linked as $target) {
                $from = 'CORE' === $target ? $this->core->pos : ($positions[$target] ?? null);
                if (null === $from) {
                    continue;
                }
                $edges[] = ['x1' => $from->x, 'y1' => $from->y, 'x2' => $node->pos->x, 'y2' => $node->pos->y];
            }
        }

        return $edges;
    }
}
