<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\TagTranslation;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tag', name: 'admin_tag_', env: 'dev')]
class AdminTagController extends AdminController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TagRepository $tagRepository,
        private readonly TranslatableListener $translatableListener,
    ) {
    }

    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $tags = $this->tagRepository->createOrderedQueryBuilder()
            ->getQuery()
            ->getResult();

        return $this->render('admin/tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->useDefaultLocale();
        $tag = new Tag();
        $tag->setTranslatableLocale('fr');

        $form = $this->createForm(TagFormType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tag);
            $this->applyTranslations($tag, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tag created.');

            return $this->redirectToRoute('admin_tag_show', ['id' => $tag->getId()]);
        }

        return $this->render('admin/tag/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/{id}',
        name: 'show',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function show(Tag $tag): Response
    {
        return $this->render('admin/tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function edit(Request $request, Tag $tag): Response
    {
        $this->useDefaultLocale();
        $tag->setTranslatableLocale('fr');

        $form = $this->createForm(TagFormType::class, $tag);
        $this->prefillTranslationFields($form, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyTranslations($tag, $form);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tag updated.');

            return $this->redirectToRoute('admin_tag_show', ['id' => $tag->getId()]);
        }

        return $this->render('admin/tag/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
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
        $repository = $this->entityManager->getRepository(TagTranslation::class);

        return $repository;
    }

    private function prefillTranslationFields(FormInterface $form, Tag $tag): void
    {
        if ($tag->getId() === null) {
            $form->get('label_en')->setData(null);

            return;
        }

        $translations = $this->getTranslationRepository()->findTranslations($tag);
        $form->get('label_en')->setData($translations['en']['label'] ?? null);
    }

    private function applyTranslations(Tag $tag, FormInterface $form): void
    {
        $repository = $this->getTranslationRepository();
        $labelEn = $form->get('label_en')->getData();

        $this->setTranslationValue($repository, $tag, 'label', 'en', $labelEn);
    }

    private function setTranslationValue(TranslationRepository $repository, Tag $tag, string $field, string $locale, mixed $value): void
    {
        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            $this->removeTranslation($tag, $field, $locale);

            return;
        }

        $repository->translate($tag, $field, $locale, $value);
    }

    private function removeTranslation(Tag $tag, string $field, string $locale): void
    {
        if ($tag->getId() === null) {
            return;
        }

        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\TagTranslation t WHERE t.field = :field AND t.locale = :locale AND t.objectClass = :objectClass AND t.foreignKey = :foreignKey'
        )
            ->setParameter('field', $field)
            ->setParameter('locale', $locale)
            ->setParameter('objectClass', Tag::class)
            ->setParameter('foreignKey', (string) $tag->getId())
            ->execute();
    }
}
