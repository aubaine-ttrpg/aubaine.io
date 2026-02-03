<?php

namespace App\Form;

use App\Entity\SkillTree;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillTreeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code',
            ])
            ->add('name', TextType::class, [
                'label' => 'Name (FR)',
            ])
            ->add('name_en', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Name (EN)',
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
            ->add('columns', IntegerType::class, [
                'label' => 'Columns',
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('rows', IntegerType::class, [
                'label' => 'Rows',
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('tree_payload', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SkillTree::class,
        ]);
    }
}
