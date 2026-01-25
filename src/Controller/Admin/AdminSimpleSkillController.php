<?php

namespace App\Controller\Admin;

use App\Entity\SimpleSkills;
use App\Entity\SimpleSkillsTranslation;
use App\Form\SimpleSkillFormType;
use App\Repository\SimpleSkillsRepository;
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

#[Route('/admin/simple-skill', name: 'admin_simple_skill_', env: 'dev')]
class AdminSimpleSkillController extends AdminController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SimpleSkillsRepository $simpleSkillsRepository,
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
        $simpleSkills = $this->simpleSkillsRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/simple_skill/index.html.twig', [
            'simple_skills' => $simpleSkills,
        ]);
    }

    #[Route('/help', name: 'help', methods: ['GET'])]
    public function help(): Response
    {
        return $this->render('admin/simple_skill/help.html.twig');
    }

    #[Route(
        '/{id}',
        name: 'show',
        methods: ['GET'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function show(SimpleSkills $simpleSkill): Response
    {
        $this->useDefaultLocale();
        $translations = $this->getTranslationRepository()->findTranslations($simpleSkill);

        $frName = $translations['fr']['name'] ?? $simpleSkill->getName();
        $frDescription = $translations['fr']['description'] ?? $simpleSkill->getDescription();
        $frLimitations = $translations['fr']['limitations'] ?? $simpleSkill->getLimitations();
        $frRequirements = $translations['fr']['requirements'] ?? $simpleSkill->getRequirements();
        $frEnergy = $translations['fr']['energy'] ?? $simpleSkill->getEnergy();
        $frPrerequisites = $translations['fr']['prerequisites'] ?? $simpleSkill->getPrerequisites();
        $frTiming = $translations['fr']['timing'] ?? $simpleSkill->getTiming();
        $frRange = $translations['fr']['range'] ?? $simpleSkill->getRange();
        $frDuration = $translations['fr']['duration'] ?? $simpleSkill->getDuration();
        $frTags = $translations['fr']['tags'] ?? $simpleSkill->getTags();
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

        return $this->render('admin/simple_skill/show.html.twig', [
            'simple_skill' => $simpleSkill,
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
    public function edit(Request $request, SimpleSkills $simpleSkill): Response
    {
        $this->useDefaultLocale();
        $simpleSkill->setTranslatableLocale('fr');

        $form = $this->createForm(SimpleSkillFormType::class, $simpleSkill);
        $this->prefillTranslationFields($form, $simpleSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $simpleSkill
            );
            $this->applyTranslations($simpleSkill, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Simple skill updated.');

            return $this->redirectToRoute('admin_simple_skill_show', ['id' => $simpleSkill->getId()]);
        }

        return $this->render('admin/simple_skill/edit.html.twig', [
            'form' => $form->createView(),
            'simple_skill' => $simpleSkill,
        ]);
    }

    #[Route(
        '/{id}/clone',
        name: 'clone',
        methods: ['GET', 'POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function clone(Request $request, SimpleSkills $simpleSkill): Response
    {
        $this->useDefaultLocale();
        $clone = $this->cloneSimpleSkill($simpleSkill);
        $clone->setTranslatableLocale('fr');

        $form = $this->createForm(SimpleSkillFormType::class, $clone);
        $this->prefillTranslationFields($form, $simpleSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $clone
            );
            $this->entityManager->persist($clone);
            $this->applyTranslations($clone, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Simple skill cloned.');

            return $this->redirectToRoute('admin_simple_skill_show', ['id' => $clone->getId()]);
        }

        return $this->render('admin/factory/simple_skill.html.twig', [
            'form' => $form->createView(),
            'iconSrc' => $clone->getIcon(),
        ]);
    }

    #[Route('/factory', name: 'factory', methods: ['GET', 'POST'])]
    public function factory(Request $request): Response
    {
        $simpleSkill = new SimpleSkills();
        $this->useDefaultLocale();
        $simpleSkill->setTranslatableLocale('fr');

        $form = $this->createForm(SimpleSkillFormType::class, $simpleSkill);
        $this->prefillTranslationFields($form, $simpleSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $simpleSkill
            );
            $this->entityManager->persist($simpleSkill);
            $this->applyTranslations($simpleSkill, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Simple skill created.');

            return $this->redirectToRoute('admin_simple_skill_show', ['id' => $simpleSkill->getId()]);
        }

        return $this->render('admin/factory/simple_skill.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/{id}/delete',
        name: 'delete',
        methods: ['POST'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function delete(Request $request, SimpleSkills $simpleSkill): Response
    {
        $token = (string) $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_simple_skill_' . $simpleSkill->getId(), $token)) {
            $this->addFlash('error', 'Invalid delete request.');

            return $this->redirectToRoute('admin_simple_skill_show', ['id' => $simpleSkill->getId()]);
        }

        $this->removeAllTranslations($simpleSkill);
        $this->entityManager->remove($simpleSkill);
        $this->entityManager->flush();

        $this->addFlash('success', 'Simple skill removed.');

        return $this->redirectToRoute('admin_simple_skill_index');
    }

    private function cloneSimpleSkill(SimpleSkills $simpleSkill): SimpleSkills
    {
        $clone = new SimpleSkills();

        $clone
            ->setName($simpleSkill->getName())
            ->setCode($simpleSkill->getCode())
            ->setDescription($simpleSkill->getDescription())
            ->setUltimate($simpleSkill->isUltimate())
            ->setCategory($simpleSkill->getCategory())
            ->setAbility($simpleSkill->getAbility())
            ->setAptitude($simpleSkill->getAptitude())
            ->setLimitations($simpleSkill->getLimitations())
            ->setRequirements($simpleSkill->getRequirements())
            ->setEnergy($simpleSkill->getEnergy())
            ->setPrerequisites($simpleSkill->getPrerequisites())
            ->setTiming($simpleSkill->getTiming())
            ->setRange($simpleSkill->getRange())
            ->setDuration($simpleSkill->getDuration())
            ->setTags($simpleSkill->getTags())
            ->setIcon($simpleSkill->getIcon());

        return $clone;
    }

    private function handleIconUpload(?UploadedFile $uploadedFile, SimpleSkills $simpleSkill): void
    {
        if (!$uploadedFile) {
            return;
        }

        $safeName = $this->slugger->slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $slugCode = $this->slugger->slug($simpleSkill->getCode() ?: 'icon');
        $ulid = $simpleSkill->getId()?->toBase32() ?? (new Ulid())->toBase32();
        $safeName = $slugCode . '-' . $ulid;
        $extension = $uploadedFile->guessExtension() ?: 'bin';
        $fileName = sprintf('%s-%s.%s', $safeName, uniqid('', true), $extension);

        $targetDir = $this->projectDir . '/public/uploads/skills';
        if (!$this->filesystem->exists($targetDir)) {
            $this->filesystem->mkdir($targetDir, 0775);
        }

        $uploadedFile->move($targetDir, $fileName);

        $simpleSkill->setIcon('/uploads/skills/' . $fileName);
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
        $repository = $this->entityManager->getRepository(SimpleSkillsTranslation::class);

        return $repository;
    }

    private function prefillTranslationFields(FormInterface $form, SimpleSkills $simpleSkill): void
    {
        if ($simpleSkill->getId() === null) {
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

        $translations = $this->getTranslationRepository()->findTranslations($simpleSkill);
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

    private function applyTranslations(SimpleSkills $simpleSkill, FormInterface $form): void
    {
        $repository = $this->getTranslationRepository();
        $this->setTranslationValue($repository, $simpleSkill, 'name', 'en', $form->get('name_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'description', 'en', $form->get('description_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'limitations', 'en', $form->get('limitations_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'requirements', 'en', $form->get('requirements_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'energy', 'en', $form->get('energy_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'prerequisites', 'en', $form->get('prerequisites_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'timing', 'en', $form->get('timing_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'range', 'en', $form->get('range_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'duration', 'en', $form->get('duration_en')->getData());
        $this->setTranslationValue($repository, $simpleSkill, 'tags', 'en', $form->get('tags_en')->getData());
    }

    private function setTranslationValue(
        TranslationRepository $repository,
        SimpleSkills $simpleSkill,
        string $field,
        string $locale,
        mixed $value
    ): void {
        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            $this->removeTranslation($simpleSkill, $field, $locale);

            return;
        }

        $repository->translate($simpleSkill, $field, $locale, $value);
    }

    private function removeTranslation(SimpleSkills $simpleSkill, string $field, string $locale): void
    {
        if ($simpleSkill->getId() === null) {
            return;
        }

        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\SimpleSkillsTranslation t WHERE t.field = :field AND t.locale = :locale AND t.objectClass = :objectClass AND t.foreignKey = :foreignKey'
        )
            ->setParameter('field', $field)
            ->setParameter('locale', $locale)
            ->setParameter('objectClass', SimpleSkills::class)
            ->setParameter('foreignKey', (string) $simpleSkill->getId())
            ->execute();
    }

    private function removeAllTranslations(SimpleSkills $simpleSkill): void
    {
        if ($simpleSkill->getId() === null) {
            return;
        }

        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\SimpleSkillsTranslation t WHERE t.objectClass = :objectClass AND t.foreignKey = :foreignKey'
        )
            ->setParameter('objectClass', SimpleSkills::class)
            ->setParameter('foreignKey', (string) $simpleSkill->getId())
            ->execute();
    }
}
