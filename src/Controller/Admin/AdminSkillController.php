<?php

namespace App\Controller\Admin;

use App\Entity\Skills;
use App\Form\SkillFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SkillsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/skill', name: 'admin_skill_', env: 'dev')]
class AdminSkillController extends AdminController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SkillsRepository $skillsRepository,
    ) {
    }

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

    #[Route('/factory', name: 'factory', methods: ['GET', 'POST'])]
    public function factory(Request $request): Response
    {
        $skill = new Skills();

        $form = $this->createForm(SkillFormType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($skill);
            $this->entityManager->flush();

            $this->addFlash('success', 'Skill created.');

            return $this->redirectToRoute('admin_skill_factory');
        }

        return $this->render('admin/factory/skill.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
