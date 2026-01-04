<?php

namespace App\Form;

use App\Entity\Tag;
use App\Enum\TagCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\File;

class TagFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class)
            ->add('label', TextType::class, [
                'label' => 'Label (FR)',
            ])
            ->add('label_en', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Label (EN)',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description (FR)',
            ])
            ->add('description_en', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Description (EN)',
            ])
            ->add('icon', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Icon (SVG)',
                'attr' => [
                    'accept' => 'image/svg+xml',
                ],
                'constraints' => [
                    new File(
                        mimeTypes: [
                            'image/svg',
                            'image/svg+xml',
                        ],
                        mimeTypesMessage: 'Please upload an SVG file.',
                    ),
                ],
            ])
            ->add('category', EnumType::class, [
                'class' => TagCategory::class,
                'choice_label' => static fn (TagCategory $category): string => $category->labelKey(),
                'choice_translation_domain' => 'tag',
                'choice_attr' => fn (TagCategory $category): array => [
                    'title' => $this->translator->trans($category->placeholderKey(), [], 'tag'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
