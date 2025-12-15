<?php

namespace App\Controller\Admin;

use App\Entity\Skills;
use App\Form\SkillFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SkillsRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

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
            $this->handleIconUpload($form->get('icon')->getData(), $skill);

            $this->entityManager->flush();

            $this->addFlash('success', 'Skill updated.');

            return $this->redirectToRoute('admin_skill_show', ['id' => $skill->getId()]);
        }

        return $this->render('admin/skill/edit.html.twig', [
            'form' => $form->createView(),
            'skill' => $skill,
        ]);
    }

    #[Route('/factory', name: 'factory', methods: ['GET', 'POST'])]
    public function factory(Request $request): Response
    {
        $skill = new Skills();

        $form = $this->createForm(SkillFormType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleIconUpload($form->get('icon')->getData(), $skill);

            $this->entityManager->persist($skill);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill created.');

            return $this->redirectToRoute('admin_skill_factory');
        }

        return $this->render('admin/factory/skill.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function handleIconUpload(?UploadedFile $uploadedFile, Skills $skill): void
    {
        if (!$uploadedFile) {
            return;
        }

        $safeName = $this->slugger->slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $uploadedFile->guessExtension() ?: 'bin';
        $fileName = sprintf('%s-%s.%s', $safeName, uniqid('', true), $extension);

        $targetDir = $this->projectDir . '/public/uploads/skills';
        if (!$this->filesystem->exists($targetDir)) {
            $this->filesystem->mkdir($targetDir, 0775);
        }

        $uploadedFile->move($targetDir, $fileName);

        $skill->setIcon('/uploads/skills/' . $fileName);
    }
}
