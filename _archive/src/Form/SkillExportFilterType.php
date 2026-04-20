<?php

namespace App\Form;

use App\Enum\Ability;
use App\Enum\Aptitude;
use App\Enum\SkillCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('ability', EnumType::class, [
                'class' => Ability::class,
                'choice_label' => static fn (Ability $ability): string => 'skill.ability.' . $ability->value,
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'ability'],
            ])
            ->add('aptitude', EnumType::class, [
                'class' => Aptitude::class,
                'choice_label' => static fn (Aptitude $aptitude): string => 'skill.aptitude.' . $aptitude->value,
                'choice_translation_domain' => 'skills',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'attr' => ['data-multi-select' => 'aptitude'],
            ])
            ->add('ultimate', CheckboxType::class, [
                'required' => false,
                'label' => 'Ultimate only',
            ])
            ->add('displayCode', CheckboxType::class, [
                'required' => false,
                'label' => 'Display code',
            ])
            ->add('exportName', TextType::class, [
                'required' => false,
                'label' => 'Export name',
            ])
            ->add('sortBy', ChoiceType::class, [
                'choices' => [
                    'Name' => 'name',
                    'Code' => 'code',
                    'Energy' => 'energy',
                    'Tags' => 'tags',
                    'Category' => 'category',
                    'Ability' => 'ability',
                    'Aptitude' => 'aptitude',
                    'Timing' => 'timing',
                    'Range' => 'range',
                    'Duration' => 'duration',
                    'Ultimate' => 'ultimate',
                ],
                'data' => 'name',
                'required' => true,
                'label' => 'Sort by',
            ])
            ->add('sortOrder', ChoiceType::class, [
                'choices' => [
                    'Ascending' => 'asc',
                    'Descending' => 'desc',
                ],
                'data' => 'asc',
                'required' => true,
                'label' => 'Order',
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
