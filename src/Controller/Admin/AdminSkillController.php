<?php

namespace App\Controller\Admin;

use App\Entity\Skills;
use App\Form\SkillFormType;
use App\Form\SkillExportFilterType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SkillsRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/skill', name: 'admin_skill_', env: 'dev')]
class AdminSkillController extends AdminController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SkillsRepository $skillsRepository,
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
        $skills = $this->skillsRepository->findAll();

        return $this->render('admin/skill/index.html.twig', [
            'skills' => $skills,
        ]);
    }

    #[Route(
        '/{id}',
        name: 'show',
        methods: ['GET'],
        requirements: ['id' => '[0-9A-Za-z]{26}']
    )]
    public function show(Skills $skill): Response
    {
        return $this->render('admin/skill/show.html.twig', [
            'skill' => $skill,
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
        $form = $this->createForm(SkillFormType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $skill,
                (bool) $form->get('renameIcon')->getData()
            );

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
        $clone = $this->cloneSkill($skill);

        $form = $this->createForm(SkillFormType::class, $clone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $clone,
                (bool) $form->get('renameIcon')->getData()
            );

            $this->entityManager->persist($clone);
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

        $form = $this->createForm(SkillFormType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload(
                $form->get('icon')->getData(),
                $skill,
                (bool) $form->get('renameIcon')->getData()
            );

            $this->entityManager->persist($skill);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill created.');

            return $this->redirectToRoute('admin_skill_show', ['id' => $skill->getId()]);
        }

        return $this->render('admin/factory/skill.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function handleIconUpload(?UploadedFile $uploadedFile, Skills $skill, bool $rename): void
    {
        if (!$uploadedFile) {
            return;
        }

        $safeName = $this->slugger->slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        if ($rename) {
            $slugCode = $this->slugger->slug($skill->getCode() ?: 'icon');
            $ulid = $skill->getId()?->toBase32() ?? (new Ulid())->toBase32();
            $safeName = $slugCode . '-' . $ulid;
        }
        $extension = $uploadedFile->guessExtension() ?: 'bin';
        $fileName = sprintf('%s-%s.%s', $safeName, uniqid('', true), $extension);

        $targetDir = $this->projectDir . '/public/uploads/skills';
        if (!$this->filesystem->exists($targetDir)) {
            $this->filesystem->mkdir($targetDir, 0775);
        }

        $uploadedFile->move($targetDir, $fileName);

        $skill->setIcon('/uploads/skills/' . $fileName);
    }

    private function cloneSkill(Skills $skill): Skills
    {
        $clone = new Skills();

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
        $translator->setLocale($exportLocale);

        $skills = $this->skillsRepository->findByFilters($normalizedFilters);

        return $this->render('admin/skill/export_result.html.twig', [
            'skills' => $skills,
            'export_locale' => $exportLocale,
            'export_filters' => $filters,
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
            if (is_array($value)) {
                $normalized[$key] = array_values(array_map(
                    static fn ($item): string => $item instanceof \BackedEnum ? $item->value : (string) $item,
                    $value
                ));
            } elseif ($value !== null) {
                $normalized[$key] = $value instanceof \BackedEnum ? $value->value : (string) $value;
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
     * @return array{
     *     category?: list<\App\Enum\SkillCategory>,
     *     type?: list<\App\Enum\SkillType>,
     *     source?: list<\App\Enum\Source>,
     *     range?: list<\App\Enum\SkillRange>,
     *     duration?: list<\App\Enum\SkillDuration>,
     *     abilities?: list<\App\Enum\Ability>,
     *     tags?: list<\App\Enum\SkillTag>,
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
            $normalized['tags'] = $mapList($filters['tags'], \App\Enum\SkillTag::class);
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
