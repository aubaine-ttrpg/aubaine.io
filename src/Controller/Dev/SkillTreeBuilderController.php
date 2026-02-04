<?php

namespace App\Controller\Dev;

use App\Entity\SkillTree;
use App\Entity\SkillTreeLink;
use App\Entity\SkillTreeNode;
use App\Entity\SkillTreeTranslation;
use App\Enum\Ability;
use App\Enum\Aptitude;
use App\Enum\SkillCategory;
use App\Repository\SkillsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dev/skill-tree', name: 'dev_skill_tree_', env: 'dev')]
class SkillTreeBuilderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SkillsRepository $skillsRepository,
        private readonly TranslatableListener $translatableListener,
    ) {
    }

    #[Route('/builder', name: 'builder', methods: ['GET', 'POST'])]
    public function builder(Request $request): Response
    {
        return $this->handleForm($request, new SkillTree());
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '[0-9A-Za-z]{26}'])]
    public function edit(Request $request, SkillTree $tree): Response
    {
        return $this->handleForm($request, $tree);
    }

    private function handleForm(Request $request, SkillTree $tree): Response
    {
        $this->useDefaultLocale();
        $tree->setTranslatableLocale('fr');

        if ($request->isMethod('POST')) {
            $payload = $request->request->all();
            if (!$this->isCsrfTokenValid('skill_tree_builder', (string) ($payload['_token'] ?? ''))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }

            $tree->setCode(trim((string) ($payload['code'] ?? '')));
            $tree->setName(trim((string) ($payload['name'] ?? '')));
            $tree->setDescription($this->normalizeOptionalText($payload['description'] ?? null));
            $tree->setColumns((int) ($payload['columns'] ?? $tree->getColumns()));
            $tree->setRows((int) ($payload['rows'] ?? $tree->getRows()));

            $this->syncTreeFromPayload($tree, (string) ($payload['tree_payload'] ?? ''));
            $this->applyTranslations($tree, $payload);

            $this->entityManager->persist($tree);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill tree saved.');

            return $this->redirectToRoute('dev_skill_tree_edit', ['id' => $tree->getId()]);
        }

        return $this->render('dev/skill_tree_builder.html.twig', [
            'tree' => $tree,
            'columns' => $tree->getColumns() ?: 9,
            'rows' => $tree->getRows() ?: 9,
            'initialPayload' => $this->buildInitialPayload($tree),
            'translations' => $this->buildTranslations($tree),
            'abilityOptions' => $this->getAbilityOptions(),
            'aptitudeOptions' => $this->getAptitudeOptions(),
        ]);
    }

    /**
     * @return array{
     *     nodes: list<array{
     *         row: int,
     *         col: int,
     *         cost: int,
     *         isStarter: bool,
     *         skillId: string|null,
     *         anon: array<string, mixed>|null
     *     }>,
     *     links: list<array{from: array{row: int, col: int}, to: array{row: int, col: int}}>
     * }
     */
    private function buildInitialPayload(SkillTree $tree): array
    {
        $nodes = [];
        foreach ($tree->getNodes() as $node) {
            $skill = $node->getSkill();
            $nodes[] = [
                'row' => $node->getRow(),
                'col' => $node->getCol(),
                'cost' => $node->getCost(),
                'isStarter' => $node->isStarter(),
                'skillId' => $skill ? (string) $skill->getId() : null,
                'anon' => $node->getAnonPayload(),
                'skill' => $skill ? [
                    'id' => (string) $skill->getId(),
                    'code' => $skill->getCode(),
                    'name' => $skill->getName(),
                    'icon' => $skill->getIcon(),
                ] : null,
            ];
        }

        $links = [];
        foreach ($tree->getLinks() as $link) {
            $from = $link->getFromNode();
            $to = $link->getToNode();
            if (!$from || !$to) {
                continue;
            }

            $links[] = [
                'from' => ['row' => $from->getRow(), 'col' => $from->getCol()],
                'to' => ['row' => $to->getRow(), 'col' => $to->getCol()],
            ];
        }

        return [
            'nodes' => $nodes,
            'links' => $links,
        ];
    }

    #[Route('/search-skills', name: 'search_skills', methods: ['GET'])]
    public function searchSkills(Request $request): Response
    {
        $term = trim((string) $request->query->get('q', ''));
        if (mb_strlen($term) < 3) {
            return $this->json([]);
        }

        $skills = $this->skillsRepository->searchByCodeOrName($term, 10, SkillCategory::EXCLUSIVE);
        $payload = array_map(
            static fn ($skill): array => [
                'id' => (string) $skill->getId(),
                'code' => $skill->getCode(),
                'name' => $skill->getName(),
                'icon' => $skill->getIcon(),
            ],
            $skills
        );

        return $this->json($payload);
    }

    private function syncTreeFromPayload(SkillTree $tree, string $rawPayload): void
    {
        $payload = [];
        if ($rawPayload !== '') {
            $payload = json_decode($rawPayload, true);
        }

        if (!is_array($payload)) {
            return;
        }

        $existingNodes = [];
        foreach ($tree->getNodes() as $node) {
            $existingNodes[$node->getRow() . '-' . $node->getCol()] = $node;
        }

        $nodesByKey = [];
        $keptNodeKeys = [];
        $nodesPayload = is_array($payload['nodes'] ?? null) ? $payload['nodes'] : [];
        foreach ($nodesPayload as $nodePayload) {
            if (!is_array($nodePayload)) {
                continue;
            }

            $row = isset($nodePayload['row']) ? (int) $nodePayload['row'] : null;
            $col = isset($nodePayload['col']) ? (int) $nodePayload['col'] : null;
            if ($row === null || $col === null) {
                continue;
            }

            $key = $row . '-' . $col;
            $node = $existingNodes[$key] ?? new SkillTreeNode();
            $node->setRow($row);
            $node->setCol($col);
            $node->setCost((int) ($nodePayload['cost'] ?? 0));
            $node->setIsStarter((bool) ($nodePayload['isStarter'] ?? false));
            $node->setSkill(null);
            $node->setAnonPayload(null);

            $skillId = $nodePayload['skillId'] ?? null;
            if (is_string($skillId) && $skillId !== '') {
                $skill = $this->skillsRepository->find($skillId);
                if ($skill) {
                    $node->setSkill($skill);
                }
            }

            if ($node->getSkill() === null) {
                $anon = $nodePayload['anon'] ?? null;
                if (is_array($anon)) {
                    $normalized = $this->normalizeAnonPayload($anon);
                    if ($normalized !== null) {
                        $node->setAnonPayload($normalized);
                    }
                }
            }

            if ($node->getSkill() === null && $node->getAnonPayload() === null) {
                if (isset($existingNodes[$key])) {
                    $tree->removeNode($node);
                }
                continue;
            }

            if (!$tree->getNodes()->contains($node)) {
                $tree->addNode($node);
            }
            $nodesByKey[$key] = $node;
            $keptNodeKeys[$key] = true;
        }

        foreach ($existingNodes as $key => $node) {
            if (!isset($keptNodeKeys[$key])) {
                $tree->removeNode($node);
            }
        }

        $existingLinks = [];
        foreach ($tree->getLinks() as $link) {
            $from = $link->getFromNode();
            $to = $link->getToNode();
            if (!$from || !$to) {
                $tree->removeLink($link);
                continue;
            }
            $fromKey = $from->getRow() . '-' . $from->getCol();
            $toKey = $to->getRow() . '-' . $to->getCol();
            $a = $fromKey < $toKey ? $fromKey : $toKey;
            $b = $fromKey < $toKey ? $toKey : $fromKey;
            $existingLinks[$a . '|' . $b] = $link;
        }

        $linksPayload = is_array($payload['links'] ?? null) ? $payload['links'] : [];
        $desiredLinks = [];
        foreach ($linksPayload as $linkPayload) {
            if (!is_array($linkPayload)) {
                continue;
            }

            $from = $linkPayload['from'] ?? null;
            $to = $linkPayload['to'] ?? null;
            if (!is_array($from) || !is_array($to)) {
                continue;
            }

            $fromKey = ((int) ($from['row'] ?? 0)) . '-' . ((int) ($from['col'] ?? 0));
            $toKey = ((int) ($to['row'] ?? 0)) . '-' . ((int) ($to['col'] ?? 0));
            if ($fromKey === $toKey) {
                continue;
            }

            $a = $fromKey < $toKey ? $fromKey : $toKey;
            $b = $fromKey < $toKey ? $toKey : $fromKey;
            $key = $a . '|' . $b;

            $fromNode = $nodesByKey[$fromKey] ?? null;
            $toNode = $nodesByKey[$toKey] ?? null;
            if (!$fromNode || !$toNode) {
                continue;
            }

            $desiredLinks[$key] = true;

            if (isset($existingLinks[$key])) {
                continue;
            }

            $link = new SkillTreeLink();
            $link->setFromNode($fromNode);
            $link->setToNode($toNode);
            $link->setIsDirected(false);
            $tree->addLink($link);
        }

        foreach ($existingLinks as $key => $link) {
            if (!isset($desiredLinks[$key])) {
                $tree->removeLink($link);
            }
        }
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>|null
     */
    private function normalizeAnonPayload(array $payload): ?array
    {
        $normalized = [
            'code' => trim((string) ($payload['code'] ?? '')),
            'name' => trim((string) ($payload['name'] ?? '')),
            'icon' => trim((string) ($payload['icon'] ?? '')),
            'description' => trim((string) ($payload['description'] ?? '')),
            'category' => trim((string) ($payload['category'] ?? '')),
            'ability' => trim((string) ($payload['ability'] ?? '')),
            'aptitude' => trim((string) ($payload['aptitude'] ?? '')),
        ];

        foreach ($normalized as $value) {
            if ($value !== '') {
                return $normalized;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function getAbilityOptions(): array
    {
        return array_map(
            static fn (Ability $ability): string => $ability->value,
            Ability::cases()
        );
    }

    /**
     * @return list<string>
     */
    private function getAptitudeOptions(): array
    {
        return array_map(
            static fn (Aptitude $aptitude): string => $aptitude->value,
            Aptitude::cases()
        );
    }

    private function normalizeOptionalText(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            return null;
        }

        return $value;
    }

    private function useDefaultLocale(): void
    {
        $this->translatableListener->setDefaultLocale('fr');
        $this->translatableListener->setTranslatableLocale('fr');
        $this->translatableListener->setTranslationFallback(true);
    }

    private function getTranslationRepository(): TranslationRepository
    {
        /** @var TranslationRepository $repository */
        $repository = $this->entityManager->getRepository(SkillTreeTranslation::class);

        return $repository;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function applyTranslations(SkillTree $tree, array $payload): void
    {
        $repository = $this->getTranslationRepository();
        $nameEn = $payload['name_en'] ?? null;
        $descriptionEn = $payload['description_en'] ?? null;

        $this->setTranslationValue($repository, $tree, 'name', 'en', $nameEn);
        $this->setTranslationValue($repository, $tree, 'description', 'en', $descriptionEn);
    }

    /**
     * @return array{en: array{name: ?string, description: ?string}}
     */
    private function buildTranslations(SkillTree $tree): array
    {
        if ($tree->getId() === null) {
            return [
                'en' => ['name' => null, 'description' => null],
            ];
        }

        $translations = $this->getTranslationRepository()->findTranslations($tree);

        return [
            'en' => [
                'name' => $translations['en']['name'] ?? null,
                'description' => $translations['en']['description'] ?? null,
            ],
        ];
    }

    private function setTranslationValue(
        TranslationRepository $repository,
        SkillTree $tree,
        string $field,
        string $locale,
        mixed $value
    ): void {
        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            $this->removeTranslation($tree, $field, $locale);

            return;
        }

        $repository->translate($tree, $field, $locale, $value);
    }

    private function removeTranslation(SkillTree $tree, string $field, string $locale): void
    {
        if ($tree->getId() === null) {
            return;
        }

        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\SkillTreeTranslation t WHERE t.field = :field AND t.locale = :locale AND t.objectClass = :objectClass AND t.foreignKey = :foreignKey'
        )
            ->setParameter('field', $field)
            ->setParameter('locale', $locale)
            ->setParameter('objectClass', SkillTree::class)
            ->setParameter('foreignKey', (string) $tree->getId())
            ->execute();
    }
}
