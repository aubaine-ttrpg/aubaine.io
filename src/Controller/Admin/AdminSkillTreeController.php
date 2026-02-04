<?php

namespace App\Controller\Admin;

use App\Entity\SkillTree;
use App\Entity\SkillTreeLink;
use App\Entity\SkillTreeNode;
use App\Entity\SkillTreeTranslation;
use App\Form\SkillTreeFormType;
use App\Enum\SkillCategory;
use App\Enum\Ability;
use App\Enum\Aptitude;
use App\Repository\SkillTreeRepository;
use App\Repository\SkillsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/skill-tree', name: 'admin_skill_tree_', env: 'dev')]
class AdminSkillTreeController extends AdminController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SkillTreeRepository $skillTreeRepository,
        private readonly SkillsRepository $skillsRepository,
        private readonly TranslatableListener $translatableListener,
    ) {
    }

    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $trees = $this->skillTreeRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/skill_tree/index.html.twig', [
            'trees' => $trees,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->useDefaultLocale();
        $tree = new SkillTree();
        $tree->setTranslatableLocale('fr');

        $form = $this->createForm(SkillTreeFormType::class, $tree);
        $this->prefillTranslationFields($form, $tree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncTreeFromPayload($tree, (string) $form->get('tree_payload')->getData());
            $this->entityManager->persist($tree);
            $this->applyTranslations($tree, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill tree created.');

            return $this->redirectToRoute('admin_skill_tree_show', ['id' => $tree->getId()]);
        }

        return $this->render('admin/skill_tree/new.html.twig', [
            'form' => $form->createView(),
            'tree' => $tree,
            'columns' => $tree->getColumns() ?: 9,
            'rows' => $tree->getRows() ?: 9,
            'initialPayload' => $this->buildInitialPayload($tree),
            'abilityOptions' => $this->getAbilityOptions(),
            'aptitudeOptions' => $this->getAptitudeOptions(),
        ]);
    }

    #[Route(
        '/{id}',
        name: 'show',
        methods: ['GET'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function show(SkillTree $tree): Response
    {
        $this->useDefaultLocale();
        $translations = $this->getTranslationRepository()->findTranslations($tree);

        $frName = $translations['fr']['name'] ?? $tree->getName();
        $frDescription = $translations['fr']['description'] ?? $tree->getDescription();
        $enName = $translations['en']['name'] ?? null;
        $enDescription = $translations['en']['description'] ?? null;

        return $this->render('admin/skill_tree/show.html.twig', [
            'tree' => $tree,
            'columns' => $tree->getColumns() ?: 9,
            'rows' => $tree->getRows() ?: 9,
            'initialPayload' => $this->buildInitialPayload($tree),
            'translations' => [
                'fr' => [
                    'name' => $frName,
                    'description' => $frDescription,
                ],
                'en' => [
                    'name' => $enName,
                    'description' => $enDescription,
                ],
            ],
        ]);
    }

    #[Route(
        '/{id}/export',
        name: 'export',
        methods: ['GET'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function export(SkillTree $tree): Response
    {
        $this->useDefaultLocale();

        $skillsById = [];
        $starterIds = [];
        $anonByKey = [];

        foreach ($tree->getNodes() as $node) {
            $skill = $node->getSkill();
            if ($skill === null) {
                    $anon = $node->getAnonPayload();
                    if (is_array($anon)) {
                        $code = trim((string) ($anon['code'] ?? ''));
                        $name = trim((string) ($anon['name'] ?? ''));
                        $description = trim((string) ($anon['description'] ?? ''));
                        $icon = trim((string) ($anon['icon'] ?? ''));
                        $category = trim((string) ($anon['category'] ?? ''));
                        $ability = trim((string) ($anon['ability'] ?? ''));
                        $aptitude = trim((string) ($anon['aptitude'] ?? ''));
                        $key = mb_strtolower($code) . '|' . mb_strtolower($name) . '|' . md5($description . '|' . $icon . '|' . $ability . '|' . $aptitude);
                        if (!isset($anonByKey[$key])) {
                            $anonByKey[$key] = [
                                'code' => $code,
                                'name' => $name,
                                'description' => $description,
                                'icon' => $icon,
                                'category' => $category,
                                'ability' => $ability,
                                'aptitude' => $aptitude,
                                'isStarter' => $node->isStarter(),
                            ];
                        } else {
                            $anonByKey[$key]['isStarter'] = $anonByKey[$key]['isStarter'] || $node->isStarter();
                        }
                    }
                continue;
            }

            $id = (string) $skill->getId();
            $skillsById[$id] = $skill;

            if ($node->isStarter()) {
                $starterIds[$id] = true;
            }
        }

        $starterEntries = [];
        $otherEntries = [];

        foreach ($skillsById as $id => $skill) {
            $entry = [
                'type' => 'skill',
                'code' => $skill->getCode(),
                'name' => $skill->getName(),
                'skill' => $skill,
            ];
            if (isset($starterIds[$id])) {
                $starterEntries[] = $entry;
            } else {
                $otherEntries[] = $entry;
            }
        }

        foreach ($anonByKey as $anon) {
            $entry = [
                'type' => 'anon',
                'code' => $anon['code'] ?? '',
                'name' => $anon['name'] ?? '',
                'description' => $anon['description'] ?? '',
                'icon' => $anon['icon'] ?? '',
                'category' => $anon['category'] ?? '',
                'ability' => $anon['ability'] ?? '',
                'aptitude' => $anon['aptitude'] ?? '',
            ];

            if (!empty($anon['isStarter'])) {
                $starterEntries[] = $entry;
            } else {
                $otherEntries[] = $entry;
            }
        }

        $sortByCode = static function (array $a, array $b): int {
            $aKey = trim((string) ($a['code'] ?? ''));
            $bKey = trim((string) ($b['code'] ?? ''));
            if ($aKey === '') {
                $aKey = trim((string) ($a['name'] ?? ''));
            }
            if ($bKey === '') {
                $bKey = trim((string) ($b['name'] ?? ''));
            }
            return strcasecmp($aKey, $bKey);
        };
        usort($starterEntries, $sortByCode);
        usort($otherEntries, $sortByCode);

        return $this->render('admin/skill_tree/export.html.twig', [
            'tree' => $tree,
            'columns' => $tree->getColumns() ?: 9,
            'rows' => $tree->getRows() ?: 9,
            'initialPayload' => $this->buildInitialPayload($tree),
            'starterEntries' => $starterEntries,
            'otherEntries' => $otherEntries,
            'displayCode' => true,
            'exportLocale' => 'fr',
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function edit(Request $request, SkillTree $tree): Response
    {
        $this->useDefaultLocale();
        $tree->setTranslatableLocale('fr');

        $form = $this->createForm(SkillTreeFormType::class, $tree);
        $this->prefillTranslationFields($form, $tree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncTreeFromPayload($tree, (string) $form->get('tree_payload')->getData());
            $this->applyTranslations($tree, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill tree updated.');

            return $this->redirectToRoute('admin_skill_tree_show', ['id' => $tree->getId()]);
        }

        return $this->render('admin/skill_tree/edit.html.twig', [
            'form' => $form->createView(),
            'tree' => $tree,
            'columns' => $tree->getColumns() ?: 9,
            'rows' => $tree->getRows() ?: 9,
            'initialPayload' => $this->buildInitialPayload($tree),
            'abilityOptions' => $this->getAbilityOptions(),
            'aptitudeOptions' => $this->getAptitudeOptions(),
        ]);
    }

    #[Route(
        '/{id}/delete',
        name: 'delete',
        methods: ['POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function delete(Request $request, SkillTree $tree): Response
    {
        if (!$this->isCsrfTokenValid('delete_skill_tree_' . $tree->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_skill_tree_show', ['id' => $tree->getId()]);
        }

        $this->entityManager->remove($tree);
        $this->entityManager->flush();

        $this->addFlash('success', 'Skill tree removed.');

        return $this->redirectToRoute('admin_skill_tree_index');
    }

    #[Route('/search-skills', name: 'search_skills', methods: ['GET'])]
    public function searchSkills(Request $request): JsonResponse
    {
        $term = trim((string) $request->query->get('q', ''));

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

        return new JsonResponse($payload);
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

    private function prefillTranslationFields(FormInterface $form, SkillTree $tree): void
    {
        if ($tree->getId() === null) {
            $form->get('name_en')->setData(null);
            $form->get('description_en')->setData(null);

            return;
        }

        $translations = $this->getTranslationRepository()->findTranslations($tree);
        $form->get('name_en')->setData($translations['en']['name'] ?? null);
        $form->get('description_en')->setData($translations['en']['description'] ?? null);
    }

    private function applyTranslations(SkillTree $tree, FormInterface $form): void
    {
        $repository = $this->getTranslationRepository();
        $nameEn = $form->get('name_en')->getData();
        $descriptionEn = $form->get('description_en')->getData();

        $this->setTranslationValue($repository, $tree, 'name', 'en', $nameEn);
        $this->setTranslationValue($repository, $tree, 'description', 'en', $descriptionEn);
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
