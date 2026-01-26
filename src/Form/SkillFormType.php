<?php

namespace App\Form;

use App\Enum\Ability;
use App\Enum\Aptitude;
use App\Enum\SkillCategory;
use App\Entity\Skills;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class)
            ->add('category', EnumType::class, [
                'class' => SkillCategory::class,
                'choice_label' => static fn (SkillCategory $category): string => 'skill.category.' . $category->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('ability', EnumType::class, [
                'class' => Ability::class,
                'choice_label' => static fn (Ability $ability): string => 'skill.ability.' . $ability->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('aptitude', EnumType::class, [
                'class' => Aptitude::class,
                'choice_label' => static fn (Aptitude $aptitude): string => 'skill.aptitude.' . $aptitude->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('ultimate', CheckboxType::class, [
                'required' => false,
                'label' => 'Ultimate',
            ])
            ->add('name', TextType::class)
            ->add('name_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('description', TextareaType::class)
            ->add('description_en', TextareaType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('limitations', TextType::class, [
                'required' => false,
            ])
            ->add('limitations_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('requirements', TextType::class, [
                'required' => false,
            ])
            ->add('requirements_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('energy', TextType::class, [
                'required' => false,
            ])
            ->add('energy_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('prerequisites', TextType::class, [
                'required' => false,
            ])
            ->add('prerequisites_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('timing', TextType::class, [
                'required' => false,
            ])
            ->add('timing_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('range', TextType::class, [
                'required' => false,
            ])
            ->add('range_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('duration', TextType::class, [
                'required' => false,
            ])
            ->add('duration_en', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('tags', TextareaType::class, [
                'required' => false,
            ])
            ->add('tags_en', TextareaType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('icon', FileType::class, [
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Skills::class,
        ]);
    }
}
