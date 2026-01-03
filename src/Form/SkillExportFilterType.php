<?php

namespace App\Form;

use App\Entity\Tag;
use App\Enum\Ability;
use App\Enum\SkillCategory;
use App\Enum\SkillDuration;
use App\Enum\SkillRange;
use App\Enum\SkillType;
use App\Enum\Source;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillExportFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EnumType::class, [
                'class' => SkillCategory::class,
                'choice_label' => static fn (SkillCategory $category): string => 'skill.category.' . $category->value,
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'category'],
            ])
            ->add('type', EnumType::class, [
                'class' => SkillType::class,
                'choice_label' => static fn (SkillType $type): string => 'skill.type.' . $type->value,
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'type'],
            ])
            ->add('source', EnumType::class, [
                'class' => Source::class,
                'choice_label' => static fn (Source $source): string => 'skill.source.' . $source->value,
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'source'],
            ])
            ->add('range', EnumType::class, [
                'class' => SkillRange::class,
                'choice_label' => static fn (SkillRange $range): string => 'skill.range.' . $range->value,
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'range'],
            ])
            ->add('duration', EnumType::class, [
                'class' => SkillDuration::class,
                'choice_label' => static fn (SkillDuration $duration): string => 'skill.duration.' . $duration->value,
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'duration'],
            ])
            ->add('abilities', EnumType::class, [
                'class' => Ability::class,
                'choice_label' => static fn (Ability $ability): string => 'skill.ability.' . $ability->value,
                'choices' => array_filter(Ability::cases(), static fn (Ability $a): bool => $a !== Ability::NONE),
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'abilities'],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => static fn (Tag $tag): string => $tag->getLabel(),
                'query_builder' => static fn (TagRepository $tagRepository) => $tagRepository->createOrderedQueryBuilder(),
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'tags'],
            ])
            ->add('locale', ChoiceType::class, [
                'choices' => [
                    'English' => 'en',
                    'Français' => 'fr',
                ],
                'data' => 'en',
                'required' => true,
                'label' => 'Language',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
