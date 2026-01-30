<?php

namespace App\Controller\Admin;

use App\Entity\Skills;
use App\Entity\SkillsTranslation;
use App\Form\SkillExportFilterType;
use App\Form\SkillFormType;
use App\Repository\SkillsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/skill', name: 'admin_skill_', env: 'dev')]
class AdminSkillController extends AdminController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SkillsRepository $skillsRepository,
        private readonly TranslatableListener $translatableListener,
        private readonly SluggerInterface $slugger,
        private readonly Filesystem $filesystem,
        KernelInterface $kernel,
    ) {
        $this->projectDir = $kernel->getProjectDir();
    }

    private string $projectDir;

    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $skills = $this->skillsRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/skill/index.html.twig', [
            'skills' => $skills,
        ]);
    }

    #[Route('/help', name: 'help', methods: ['GET'])]
    public function help(): Response
    {
        return $this->render('admin/skill/help.html.twig');
    }

    #[Route(
        '/{id}',
        name: 'show',
        methods: ['GET'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function show(Skills $skill): Response
    {
        $this->useDefaultLocale();
        $translations = $this->getTranslationRepository()->findTranslations($skill);

        $frName = $translations['fr']['name'] ?? $skill->getName();
        $frDescription = $translations['fr']['description'] ?? $skill->getDescription();
        $frLimitations = $translations['fr']['limitations'] ?? $skill->getLimitations();
        $frRequirements = $translations['fr']['requirements'] ?? $skill->getRequirements();
        $frEnergy = $translations['fr']['energy'] ?? $skill->getEnergy();
        $frPrerequisites = $translations['fr']['prerequisites'] ?? $skill->getPrerequisites();
        $frTiming = $translations['fr']['timing'] ?? $skill->getTiming();
        $frRange = $translations['fr']['range'] ?? $skill->getRange();
        $frDuration = $translations['fr']['duration'] ?? $skill->getDuration();
        $frTags = $translations['fr']['tags'] ?? $skill->getTags();
        $enName = $translations['en']['name'] ?? null;
        $enDescription = $translations['en']['description'] ?? null;
        $enLimitations = $translations['en']['limitations'] ?? null;
        $enRequirements = $translations['en']['requirements'] ?? null;
        $enEnergy = $translations['en']['energy'] ?? null;
        $enPrerequisites = $translations['en']['prerequisites'] ?? null;
        $enTiming = $translations['en']['timing'] ?? null;
        $enRange = $translations['en']['range'] ?? null;
        $enDuration = $translations['en']['duration'] ?? null;
        $enTags = $translations['en']['tags'] ?? null;

        return $this->render('admin/skill/show.html.twig', [
            'skill' => $skill,
            'translations' => [
                'fr' => [
                    'name' => $frName,
                    'description' => $frDescription,
                    'limitations' => $frLimitations,
                    'requirements' => $frRequirements,
                    'energy' => $frEnergy,
                    'prerequisites' => $frPrerequisites,
                    'timing' => $frTiming,
                    'range' => $frRange,
                    'duration' => $frDuration,
                    'tags' => $frTags,
                ],
                'en' => [
                    'name' => $enName,
                    'description' => $enDescription,
                    'limitations' => $enLimitations,
                    'requirements' => $enRequirements,
                    'energy' => $enEnergy,
                    'prerequisites' => $enPrerequisites,
                    'timing' => $enTiming,
                    'range' => $enRange,
                    'duration' => $enDuration,
                    'tags' => $enTags,
                ],
            ],
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function edit(Request $request, Skills $skill): Response
    {
        $this->useDefaultLocale();
        $skill->setTranslatableLocale('fr');

        $form = $this->createForm(SkillFormType::class, $skill);
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

            return $this->redirectToRoute('admin_skill_show', ['id' => $skill->getId()]);
        }

        return $this->render('admin/skill/edit.html.twig', [
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
    public function clone(Request $request, Skills $skill): Response
    {
        $this->useDefaultLocale();
        $clone = $this->cloneSkill($skill);
        $clone->setTranslatableLocale('fr');

        $form = $this->createForm(SkillFormType::class, $clone);
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

            return $this->redirectToRoute('admin_skill_show', ['id' => $clone->getId()]);
        }

        return $this->render('admin/factory/skill.html.twig', [
            'form' => $form->createView(),
            'iconSrc' => $clone->getIcon(),
        ]);
    }

    #[Route('/factory', name: 'factory', methods: ['GET', 'POST'])]
    public function factory(Request $request): Response
    {
        $skill = new Skills();
        $this->useDefaultLocale();
        $skill->setTranslatableLocale('fr');

        $form = $this->createForm(SkillFormType::class, $skill);
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

            return $this->redirectToRoute('admin_skill_show', ['id' => $skill->getId()]);
        }

        return $this->render('admin/factory/skill.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/{id}/delete',
        name: 'delete',
        methods: ['POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function delete(Request $request, Skills $skill): Response
    {
        $token = (string) $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_skill_' . $skill->getId(), $token)) {
            $this->addFlash('error', 'Invalid delete request.');

            return $this->redirectToRoute('admin_skill_show', ['id' => $skill->getId()]);
        }

        $this->removeAllTranslations($skill);
        $this->entityManager->remove($skill);
        $this->entityManager->flush();

        $this->addFlash('success', 'Skill removed.');

        return $this->redirectToRoute('admin_skill_index');
    }

    #[Route('/export', name: 'export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        $form = $this->createForm(SkillExportFilterType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->encodeFilters($form->getData());

            return $this->redirectToRoute('admin_skill_export_hash', ['hash' => $hash]);
        }

        return $this->render('admin/skill/export_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export/{hash}', name: 'export_hash', methods: ['GET'])]
    public function exportHash(string $hash, Request $request, TranslatorInterface $translator): Response
    {
        $decoded = $this->decodeFilters($hash);
        $initialData = $this->normalizeFilters($decoded);
        $form = $this->createForm(SkillExportFilterType::class, $initialData, ['method' => 'GET']);
        $form->handleRequest($request);

        $filters = $initialData;
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $newHash = $this->encodeFilters($filters);

            if ($newHash !== $hash) {
                return $this->redirectToRoute('admin_skill_export_hash', ['hash' => $newHash]);
            }
        }

        $normalizedFilters = $this->normalizeFilters($filters);
        $exportLocale = $normalizedFilters['locale'] ?? 'en';
        $displayCode = (bool) ($normalizedFilters['displayCode'] ?? false);
        $translator->setLocale($exportLocale);
        $this->translatableListener->setDefaultLocale('fr');
        $this->translatableListener->setTranslatableLocale($exportLocale);
        $this->translatableListener->setTranslationFallback(true);

        $skills = $this->skillsRepository->findByFilters($normalizedFilters);

        return $this->render('admin/skill/export_result.html.twig', [
            'skills' => $skills,
            'export_locale' => $exportLocale,
            'export_filters' => $this->formatFiltersForView($filters),
            'display_code' => $displayCode,
            'hash' => $hash,
        ]);
    }

    private function cloneSkill(Skills $skill): Skills
    {
        $clone = new Skills();

        $clone
            ->setName($skill->getName())
            ->setCode($skill->getCode())
            ->setDescription($skill->getDescription())
            ->setUltimate($skill->isUltimate())
            ->setCategory($skill->getCategory())
            ->setAbility($skill->getAbility())
            ->setAptitude($skill->getAptitude())
            ->setLimitations($skill->getLimitations())
            ->setRequirements($skill->getRequirements())
            ->setEnergy($skill->getEnergy())
            ->setPrerequisites($skill->getPrerequisites())
            ->setTiming($skill->getTiming())
            ->setRange($skill->getRange())
            ->setDuration($skill->getDuration())
            ->setTags($skill->getTags())
            ->setIcon($skill->getIcon());

        return $clone;
    }

    private function handleIconUpload(?UploadedFile $uploadedFile, Skills $skill): void
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

    private function useDefaultLocale(): void
    {
        $this->translatableListener->setDefaultLocale('fr');
        $this->translatableListener->setTranslatableLocale('fr');
        $this->translatableListener->setTranslationFallback(true);
    }

    private function getTranslationRepository(): TranslationRepository
    {
        /** @var TranslationRepository $repository */
        $repository = $this->entityManager->getRepository(SkillsTranslation::class);

        return $repository;
    }

    private function prefillTranslationFields(FormInterface $form, Skills $skill): void
    {
        if ($skill->getId() === null) {
            $form->get('name_en')->setData(null);
            $form->get('description_en')->setData(null);
            $form->get('limitations_en')->setData(null);
            $form->get('requirements_en')->setData(null);
            $form->get('energy_en')->setData(null);
            $form->get('prerequisites_en')->setData(null);
            $form->get('timing_en')->setData(null);
            $form->get('range_en')->setData(null);
            $form->get('duration_en')->setData(null);
            $form->get('tags_en')->setData(null);

            return;
        }

        $translations = $this->getTranslationRepository()->findTranslations($skill);
        $form->get('name_en')->setData($translations['en']['name'] ?? null);
        $form->get('description_en')->setData($translations['en']['description'] ?? null);
        $form->get('limitations_en')->setData($translations['en']['limitations'] ?? null);
        $form->get('requirements_en')->setData($translations['en']['requirements'] ?? null);
        $form->get('energy_en')->setData($translations['en']['energy'] ?? null);
        $form->get('prerequisites_en')->setData($translations['en']['prerequisites'] ?? null);
        $form->get('timing_en')->setData($translations['en']['timing'] ?? null);
        $form->get('range_en')->setData($translations['en']['range'] ?? null);
        $form->get('duration_en')->setData($translations['en']['duration'] ?? null);
        $form->get('tags_en')->setData($translations['en']['tags'] ?? null);
    }

    private function applyTranslations(Skills $skill, FormInterface $form): void
    {
        $repository = $this->getTranslationRepository();
        $this->setTranslationValue($repository, $skill, 'name', 'en', $form->get('name_en')->getData());
        $this->setTranslationValue($repository, $skill, 'description', 'en', $form->get('description_en')->getData());
        $this->setTranslationValue($repository, $skill, 'limitations', 'en', $form->get('limitations_en')->getData());
        $this->setTranslationValue($repository, $skill, 'requirements', 'en', $form->get('requirements_en')->getData());
        $this->setTranslationValue($repository, $skill, 'energy', 'en', $form->get('energy_en')->getData());
        $this->setTranslationValue($repository, $skill, 'prerequisites', 'en', $form->get('prerequisites_en')->getData());
        $this->setTranslationValue($repository, $skill, 'timing', 'en', $form->get('timing_en')->getData());
        $this->setTranslationValue($repository, $skill, 'range', 'en', $form->get('range_en')->getData());
        $this->setTranslationValue($repository, $skill, 'duration', 'en', $form->get('duration_en')->getData());
        $this->setTranslationValue($repository, $skill, 'tags', 'en', $form->get('tags_en')->getData());
    }

    private function setTranslationValue(
        TranslationRepository $repository,
        Skills $skill,
        string $field,
        string $locale,
        mixed $value
    ): void {
        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            $this->removeTranslation($skill, $field, $locale);

            return;
        }

        $repository->translate($skill, $field, $locale, $value);
    }

    private function removeTranslation(Skills $skill, string $field, string $locale): void
    {
        if ($skill->getId() === null) {
            return;
        }

        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\SkillsTranslation t WHERE t.field = :field AND t.locale = :locale AND t.objectClass = :objectClass AND t.foreignKey = :foreignKey'
        )
            ->setParameter('field', $field)
            ->setParameter('locale', $locale)
            ->setParameter('objectClass', Skills::class)
            ->setParameter('foreignKey', (string) $skill->getId())
            ->execute();
    }

    private function removeAllTranslations(Skills $skill): void
    {
        if ($skill->getId() === null) {
            return;
        }

        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\SkillsTranslation t WHERE t.objectClass = :objectClass AND t.foreignKey = :foreignKey'
        )
            ->setParameter('objectClass', Skills::class)
            ->setParameter('foreignKey', (string) $skill->getId())
            ->execute();
    }

    /**
     * @param array<string,mixed> $filters
     */
    private function encodeFilters(array $filters): string
    {
        $order = ['category', 'ability', 'aptitude', 'ultimate', 'displayCode', 'locale'];
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
     *     ability?: list<\App\Enum\Ability>,
     *     aptitude?: list<\App\Enum\Aptitude>,
     *     ultimate?: bool,
     *     displayCode?: bool,
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
        if (!empty($filters['ability']) && is_array($filters['ability'])) {
            $normalized['ability'] = $mapList($filters['ability'], \App\Enum\Ability::class);
        }
        if (!empty($filters['aptitude']) && is_array($filters['aptitude'])) {
            $normalized['aptitude'] = $mapList($filters['aptitude'], \App\Enum\Aptitude::class);
        }

        if (!empty($filters['ultimate'])) {
            $normalized['ultimate'] = true;
        }

        if (!empty($filters['displayCode'])) {
            $normalized['displayCode'] = true;
        }

        $locale = $filters['locale'] ?? 'en';
        if (is_string($locale) && in_array($locale, ['en', 'fr'], true)) {
            $normalized['locale'] = $locale;
        } else {
            $normalized['locale'] = 'en';
        }

        return $normalized;
    }
}
