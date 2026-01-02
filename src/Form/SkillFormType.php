<?php

namespace App\Form;

use App\Entity\Skills;
use App\Enum\Ability;
use App\Enum\SkillCategory;
use App\Enum\SkillDuration;
use App\Enum\SkillLimitPeriod;
use App\Enum\SkillRange;
use App\Enum\Source;
use App\Enum\SkillTag;
use App\Enum\SkillType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class)
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('energyCost', IntegerType::class, [
                'required' => false,
            ])
            ->add('ultimate', CheckboxType::class, [
                'required' => false,
                'label' => 'Ultimate',
            ])
            ->add('usageLimitAmount', IntegerType::class, [
                'required' => false,
            ])
            ->add('usageLimitPeriod', EnumType::class, [
                'class' => SkillLimitPeriod::class,
                'choice_label' => static fn (SkillLimitPeriod $period): string => 'skill.limit_period.' . $period->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('category', EnumType::class, [
                'class' => SkillCategory::class,
                'choice_label' => static fn (SkillCategory $category): string => 'skill.category.' . $category->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('type', EnumType::class, [
                'class' => SkillType::class,
                'choice_label' => static fn (SkillType $type): string => 'skill.type.' . $type->value,
                'choice_translation_domain' => 'skills',
                'attr' => [
                    'data-action-types' => json_encode([
                        SkillType::ACTION->value,
                        SkillType::BONUS->value,
                        SkillType::REACTION->value,
                        SkillType::ATTACK->value,
                    ]),
                ],
            ])
            ->add('abilities', EnumType::class, [
                'class' => Ability::class,
                'choice_label' => static fn (Ability $ability): string => 'skill.ability.' . $ability->value,
                'choices' => array_filter(Ability::cases(), static fn (Ability $a): bool => $a !== Ability::NONE),
                'choice_translation_domain' => 'skills',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'data-multi-select' => 'abilities',
                ],
            ])
            ->add('range', EnumType::class, [
                'class' => SkillRange::class,
                'choice_label' => static fn (SkillRange $range): string => 'skill.range.' . $range->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('duration', EnumType::class, [
                'class' => SkillDuration::class,
                'choice_label' => static fn (SkillDuration $duration): string => 'skill.duration.' . $duration->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('concentration', CheckboxType::class, [
                'required' => false,
            ])
            ->add('ritual', CheckboxType::class, [
                'required' => false,
            ])
            ->add('attackRoll', CheckboxType::class, [
                'required' => false,
            ])
            ->add('savingThrow', CheckboxType::class, [
                'required' => false,
            ])
            ->add('abilityCheck', CheckboxType::class, [
                'required' => false,
            ])
            ->add('source', EnumType::class, [
                'class' => Source::class,
                'choice_label' => static fn (Source $source): string => 'skill.source.' . $source->value,
                'choice_translation_domain' => 'skills',
            ])
            ->add('materials', TextareaType::class, [
                'required' => false,
                'label' => 'Materials',
            ])
            ->add('tags', EnumType::class, [
                'class' => SkillTag::class,
                'choice_label' => static fn (SkillTag $tag): string => 'skill.tag.' . $tag->value,
                'choices' => array_filter(SkillTag::cases(), static fn (SkillTag $t): bool => $t !== SkillTag::NONE),
                'choice_translation_domain' => 'skills',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'data-multi-select' => 'tags',
                ],
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
            'translation_domain' => 'skills',
        ]);
    }
}
