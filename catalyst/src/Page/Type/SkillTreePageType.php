<?php

declare(strict_types=1);

namespace App\Page\Type;

use App\Design\Paper;
use App\Page\Form\SkillTreeType;
use App\Page\PageTypeInterface;
use App\SkillTree\Exception\SkillTreeNotFoundException;
use App\SkillTree\SkillTreeRepository;

/**
 * Skill-tree bundle: renders a chosen tree's planche plus paginated ability
 * pages. Selecting a tree JSON and a paper is all the user does; the rest is
 * derived here from the loaded {@see \App\SkillTree\Model\SkillTree}.
 */
final class SkillTreePageType implements PageTypeInterface
{
    /** Planche canvas the design authored against, scaled to A4 at print. */
    private const int CANVAS_WIDTH = 960;
    private const int CANVAS_HEIGHT = 1358;

    /** Ability entries per printed page (two columns). */
    private const int ENTRIES_PER_PAGE = 8;

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

        return [
            'tree' => $tree,
            'paper' => $paper,
            'legend' => ($data['legend'] ?? true) == true,
            'edges' => $edges,
            'canvasWidth' => self::CANVAS_WIDTH,
            'canvasHeight' => self::CANVAS_HEIGHT,
            'abilityPages' => array_chunk($tree->nodes, self::ENTRIES_PER_PAGE),
        ];
    }
}
