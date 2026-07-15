<?php

declare(strict_types=1);

namespace App\Page\Type;

use App\Design\Domain;
use App\Design\NodeType;
use App\Design\Paper;
use App\Page\Form\SkillTreeType;
use App\Page\PageTypeInterface;
use App\SkillTree\Exception\SkillTreeNotFoundException;
use App\SkillTree\Model\SkillNode;
use App\SkillTree\Model\SkillTree;
use App\SkillTree\SkillTreeRepository;

/**
 * Skill-tree bundle: renders a chosen tree's planche plus paginated ability
 * pages. Selecting a tree JSON and a paper is all the user does; the rest is
 * derived here from the loaded {@see SkillTree}.
 */
final class SkillTreePageType implements PageTypeInterface
{
    /** Planche canvas the design authored against, scaled to A4 at print. */
    private const int CANVAS_WIDTH = 960;
    private const int CANVAS_HEIGHT = 1358;

    public function __construct(private readonly SkillTreeRepository $trees)
    {
    }

    public function key(): string
    {
        return 'skill-tree';
    }

    public function category(): string
    {
        return 'trees';
    }

    public function labelKey(): string
    {
        return 'page.skill_tree.label';
    }

    public function descriptionKey(): string
    {
        return 'page.skill_tree.description';
    }

    public function defaultData(): array
    {
        return [
            'tree' => $this->trees->ids()[0] ?? 'feu',
            'paper' => Paper::Parchment->value,
            'legend' => true,
        ];
    }

    public function formType(): string
    {
        return SkillTreeType::class;
    }

    public function template(): string
    {
        return 'print/pages/skill_tree.html.twig';
    }

    public function buildViewModel(array $data): array
    {
        $treeId = \is_string($data['tree'] ?? null) ? $data['tree'] : '';
        if (!$this->trees->has($treeId)) {
            $ids = $this->trees->ids();
            if ([] === $ids) {
                throw SkillTreeNotFoundException::forId($treeId);
            }
            $treeId = $ids[0];
        }

        $tree = $this->trees->load($treeId);

        $paper = Paper::tryFrom(\is_string($data['paper'] ?? null) ? $data['paper'] : '') ?? Paper::Parchment;

        $edges = array_map(
            fn (array $edge): array => [
                'x1' => $edge['x1'] / 100 * self::CANVAS_WIDTH,
                'y1' => $edge['y1'] / 100 * self::CANVAS_HEIGHT,
                'x2' => $edge['x2'] / 100 * self::CANVAS_WIDTH,
                'y2' => $edge['y2'] / 100 * self::CANVAS_HEIGHT,
            ],
            $tree->edges(),
        );

        $titles = [];
        foreach ($tree->nodes as $node) {
            $titles[$node->id] = $node->title;
        }

        // Ability entries read in ID order; the print bundle measures rendered
        // heights and paginates them left-column-first across pages.
        $ordered = $tree->nodes;
        usort($ordered, static fn (SkillNode $a, SkillNode $b): int => strcmp($a->id, $b->id));

        return [
            'tree' => $tree,
            'paper' => $paper,
            'legend' => ($data['legend'] ?? true) === true,
            'legendTypes' => $this->usedTypes($tree),
            'legendDomains' => $this->usedDomains($tree),
            'edges' => $edges,
            'canvasWidth' => self::CANVAS_WIDTH,
            'canvasHeight' => self::CANVAS_HEIGHT,
            'titles' => $titles,
            'abilities' => $ordered,
        ];
    }

    /**
     * Node types present in the tree, in a fixed reading order.
     *
     * @return list<NodeType>
     */
    private function usedTypes(SkillTree $tree): array
    {
        $used = [];
        foreach ([NodeType::Active, NodeType::Passive, NodeType::Evolution, NodeType::Special] as $type) {
            foreach ($tree->nodes as $node) {
                if ($node->type === $type) {
                    $used[] = $type;
                    break;
                }
            }
        }

        return $used;
    }

    /**
     * Domains present across the tree's nodes, in enum order.
     *
     * @return list<Domain>
     */
    private function usedDomains(SkillTree $tree): array
    {
        $used = [];
        foreach (Domain::cases() as $domain) {
            foreach ($tree->nodes as $node) {
                if (\in_array($domain, $node->domains->all(), true)) {
                    $used[] = $domain;
                    break;
                }
            }
        }

        return $used;
    }
}
