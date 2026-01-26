<?php

namespace App\Controller\Admin;

use App\Entity\OldSkills;
use App\Entity\OldSkillsTranslation;
use App\Entity\Tag;
use App\Form\OldSkillFormType;
use App\Form\OldSkillExportFilterType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OldSkillsRepository;
use App\Repository\TagRepository;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/old-skill', name: 'admin_old_skill_', env: 'dev')]
class AdminOldSkillController extends AdminController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OldSkillsRepository $skillsRepository,
        private readonly TagRepository $tagRepository,
        private readonly SluggerInterface $slugger,
        private readonly Filesystem $filesystem,
        private readonly TranslatableListener $translatableListener,
        KernelInterface $kernel,
    ) {
        $this->projectDir = $kernel->getProjectDir();
    }

    private string $projectDir;

    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $skills = $this->skillsRepository->findAll();

        return $this->render('admin/old_skill/index.html.twig', [
            'skills' => $skills,
        ]);
    }

    #[Route(
        '/{id}',
        name: 'show',
        methods: ['GET'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function show(OldSkills $skill): Response
    {
        return $this->render('admin/old_skill/show.html.twig', [
            'skill' => $skill,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function edit(Request $request, OldSkills $skill): Response
    {
        $this->useDefaultLocale();
        $skill->setTranslatableLocale('fr');

        $form = $this->createForm(OldSkillFormType::class, $skill);
        $this->prefillTranslationFields($form, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $skill
            );

            $this->applyTranslations($skill, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill updated.');

            return $this->redirectToRoute('admin_old_skill_show', ['id' => $skill->getId()]);
        }

        return $this->render('admin/old_skill/edit.html.twig', [
            'form' => $form->createView(),
            'skill' => $skill,
        ]);
    }

    #[Route(
        '/{id}/clone',
        name: 'clone',
        methods: ['GET', 'POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function clone(Request $request, OldSkills $skill): Response
    {
        $this->useDefaultLocale();
        $clone = $this->cloneSkill($skill);
        $clone->setTranslatableLocale('fr');

        $form = $this->createForm(OldSkillFormType::class, $clone);
        $this->prefillTranslationFields($form, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $clone
            );

            $this->entityManager->persist($clone);
            $this->applyTranslations($clone, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill cloned.');

            return $this->redirectToRoute('admin_old_skill_show', ['id' => $clone->getId()]);
        }

        return $this->render('admin/factory/old_skill.html.twig', [
            'form' => $form->createView(),
            'iconSrc' => $clone->getIcon(),
        ]);
    }

    #[Route('/factory', name: 'factory', methods: ['GET', 'POST'])]
    public function factory(Request $request): Response
    {
        $skill = new OldSkills();
        $this->useDefaultLocale();
        $skill->setTranslatableLocale('fr');

        $form = $this->createForm(OldSkillFormType::class, $skill);
        $this->prefillTranslationFields($form, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $skill
            );

            $this->entityManager->persist($skill);
            $this->applyTranslations($skill, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill created.');

            return $this->redirectToRoute('admin_old_skill_show', ['id' => $skill->getId()]);
        }

        return $this->render('admin/factory/old_skill.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function handleIconUpload(?UploadedFile $uploadedFile, OldSkills $skill, bool $rename = true): void
    {
        if (!$uploadedFile) {
            return;
        }

        $safeName = $this->slugger->slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $slugCode = $this->slugger->slug($skill->getCode() ?: 'icon');
        $ulid = $skill->getId()?->toBase32() ?? (new Ulid())->toBase32();
        $safeName = $slugCode . '-' . $ulid;
        $extension = $uploadedFile->guessExtension() ?: 'bin';
        $fileName = sprintf('%s-%s.%s', $safeName, uniqid('', true), $extension);

        $targetDir = $this->projectDir . '/public/uploads/skills';
        if (!$this->filesystem->exists($targetDir)) {
            $this->filesystem->mkdir($targetDir, 0775);
        }

        $uploadedFile->move($targetDir, $fileName);

        $skill->setIcon('/uploads/skills/' . $fileName);
    }

    private function cloneSkill(OldSkills $skill): OldSkills
    {
        $clone = new OldSkills();

        $clone
            ->setName($skill->getName())
            ->setCode($skill->getCode())
            ->setDescription($skill->getDescription())
            ->setUltimate($skill->isUltimate())
            ->setUsageLimitAmount($skill->getUsageLimitAmount())
            ->setUsageLimitPeriod($skill->getUsageLimitPeriod())
            ->setCategory($skill->getCategory())
            ->setType($skill->getType())
            ->setRange($skill->getRange())
            ->setDuration($skill->getDuration())
            ->setSource($skill->getSource())
            ->setTags($skill->getTags())
            ->setIcon($skill->getIcon());

        if ($clone->isActionLike()) {
            $clone
                ->setEnergyCost($skill->getEnergyCost())
                ->setAbilities($skill->getAbilities())
                ->setConcentration($skill->hasConcentration())
                ->setRitual($skill->hasRitual())
                ->setAttackRoll($skill->hasAttackRoll())
                ->setSavingThrow($skill->hasSavingThrow())
                ->setAbilityCheck($skill->hasAbilityCheck())
                ->setMaterials($skill->getMaterials());
        }

        return $clone;
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
        $repository = $this->entityManager->getRepository(OldSkillsTranslation::class);

        return $repository;
    }

    private function prefillTranslationFields(FormInterface $form, OldSkills $skill): void
    {
        if ($skill->getId() === null) {
            $form->get('name_en')->setData(null);
            $form->get('description_en')->setData(null);
            $form->get('materials_en')->setData(null);

            return;
        }

        $translations = $this->getTranslationRepository()->findTranslations($skill);

        $form->get('name_en')->setData($translations['en']['name'] ?? null);
        $form->get('description_en')->setData($translations['en']['description'] ?? null);
        $form->get('materials_en')->setData($translations['en']['materials'] ?? null);
    }

    private function applyTranslations(OldSkills $skill, FormInterface $form): void
    {
        $repository = $this->getTranslationRepository();

        $this->setTranslationValue($repository, $skill, 'name', 'en', $form->get('name_en')->getData());
        $this->setTranslationValue($repository, $skill, 'description', 'en', $form->get('description_en')->getData());
        $this->setTranslationValue($repository, $skill, 'materials', 'en', $form->get('materials_en')->getData());
    }

    private function setTranslationValue(TranslationRepository $repository, OldSkills $skill, string $field, string $locale, mixed $value): void
    {
        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            $this->removeTranslation($skill, $field, $locale);

            return;
        }

        $repository->translate($skill, $field, $locale, $value);
    }

    private function removeTranslation(OldSkills $skill, string $field, string $locale): void
    {
        if ($skill->getId() === null) {
            return;
        }

        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\OldSkillsTranslation t WHERE t.field = :field AND t.locale = :locale AND t.objectClass = :objectClass AND t.foreignKey = :foreignKey'
        )
            ->setParameter('field', $field)
            ->setParameter('locale', $locale)
            ->setParameter('objectClass', OldSkills::class)
            ->setParameter('foreignKey', (string) $skill->getId())
            ->execute();
    }

    #[Route('/export', name: 'export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        $form = $this->createForm(OldSkillExportFilterType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->encodeFilters($form->getData());

            return $this->redirectToRoute('admin_old_skill_export_hash', ['hash' => $hash]);
        }

        return $this->render('admin/old_skill/export_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export/{hash}', name: 'export_hash', methods: ['GET'])]
    public function exportHash(string $hash, Request $request, TranslatorInterface $translator): Response
    {
        $decoded = $this->decodeFilters($hash);
        $initialData = $this->normalizeFilters($decoded);
        $form = $this->createForm(OldSkillExportFilterType::class, $initialData, ['method' => 'GET']);
        $form->handleRequest($request);

        $filters = $initialData;
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $newHash = $this->encodeFilters($filters);

            if ($newHash !== $hash) {
                return $this->redirectToRoute('admin_old_skill_export_hash', ['hash' => $newHash]);
            }
        }

        $normalizedFilters = $this->normalizeFilters($filters);
        $exportLocale = $normalizedFilters['locale'] ?? 'en';
        $translator->setLocale($exportLocale);
        $this->translatableListener->setDefaultLocale('fr');
        $this->translatableListener->setTranslatableLocale($exportLocale);
        $this->translatableListener->setTranslationFallback(true);

        $skills = $this->skillsRepository->findByFilters($normalizedFilters);

        return $this->render('admin/old_skill/export_result.html.twig', [
            'skills' => $skills,
            'export_locale' => $exportLocale,
            'export_filters' => $this->formatFiltersForView($filters),
            'hash' => $hash,
        ]);
    }

    /**
     * @param array<string,mixed> $filters
     */
    private function encodeFilters(array $filters): string
    {
        $order = ['category', 'type', 'source', 'range', 'duration', 'abilities', 'tags', 'locale'];
        $normalized = [];
        foreach ($order as $key) {
            $value = $filters[$key] ?? null;
            if ($value !== null) {
                $normalized[$key] = $this->normalizeFilterValue($value);
            }
        }

        $json = json_encode($normalized);
        $base64 = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');

        return $base64;
    }

    /**
     * @return array<string,mixed>
     */
    private function decodeFilters(string $hash): array
    {
        $padded = strtr($hash, '-_', '+/');
        $padded .= str_repeat('=', (4 - strlen($padded) % 4) % 4);

        $decoded = base64_decode($padded, true);
        if ($decoded === false) {
            return [];
        }

        $data = json_decode($decoded, true);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    /**
     * @param array<string,mixed> $filters
     *
     * @return array<string,mixed>
     */
    private function formatFiltersForView(array $filters): array
    {
        $normalized = [];
        foreach ($filters as $key => $value) {
            $normalized[$key] = $this->normalizeFilterValue($value);
        }

        return $normalized;
    }

    private function normalizeFilterValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_values(array_map(
                fn ($item) => $this->normalizeFilterValue($item),
                $value
            ));
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof Tag) {
            return $value->getCode();
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        return (string) $value;
    }

    /**
     * @param array<string,mixed> $filters
     *
     * @return array{
     *     category?: list<\App\Enum\SkillCategory>,
     *     type?: list<\App\Enum\SkillType>,
     *     source?: list<\App\Enum\Source>,
     *     range?: list<\App\Enum\SkillRange>,
     *     duration?: list<\App\Enum\SkillDuration>,
     *     abilities?: list<\App\Enum\Ability>,
     *     tags?: list<\App\Entity\Tag>,
     *     locale?: string
     * }
     */
    private function normalizeFilters(array $filters): array
    {
        $mapList = static function (array $values, string $enumClass): array {
            return array_values(array_filter(array_map(
                static function ($val) use ($enumClass) {
                    if ($val instanceof \BackedEnum) {
                        return $val;
                    }

                    return $enumClass::tryFrom($val);
                },
                $values
            )));
        };

        $normalized = [];

        if (!empty($filters['category']) && is_array($filters['category'])) {
            $normalized['category'] = $mapList($filters['category'], \App\Enum\SkillCategory::class);
        }
        if (!empty($filters['type']) && is_array($filters['type'])) {
            $normalized['type'] = $mapList($filters['type'], \App\Enum\SkillType::class);
        }
        if (!empty($filters['source']) && is_array($filters['source'])) {
            $normalized['source'] = $mapList($filters['source'], \App\Enum\Source::class);
        }
        if (!empty($filters['range']) && is_array($filters['range'])) {
            $normalized['range'] = $mapList($filters['range'], \App\Enum\SkillRange::class);
        }
        if (!empty($filters['duration']) && is_array($filters['duration'])) {
            $normalized['duration'] = $mapList($filters['duration'], \App\Enum\SkillDuration::class);
        }
        if (!empty($filters['abilities']) && is_array($filters['abilities'])) {
            $normalized['abilities'] = $mapList($filters['abilities'], \App\Enum\Ability::class);
        }
        if (!empty($filters['tags']) && is_array($filters['tags'])) {
            $normalized['tags'] = $this->normalizeTags($filters['tags']);
        }

        $locale = $filters['locale'] ?? 'en';
        if (is_string($locale) && in_array($locale, ['en', 'fr'], true)) {
            $normalized['locale'] = $locale;
        } else {
            $normalized['locale'] = 'en';
        }

        return $normalized;
    }

    /**
     * @param array<int, string|Tag> $tags
     *
     * @return list<Tag>
     */
    private function normalizeTags(array $tags): array
    {
        $entitiesByCode = [];
        $codes = [];

        foreach ($tags as $tag) {
            if ($tag instanceof Tag) {
                $entitiesByCode[$tag->getCode()] = $tag;
            } elseif (is_string($tag)) {
                $codes[] = $tag;
            }
        }

        if ($codes !== []) {
            foreach ($this->tagRepository->findByCodes($codes) as $tagEntity) {
                $entitiesByCode[$tagEntity->getCode()] = $tagEntity;
            }
        }

        return array_values($entitiesByCode);
    }
}
