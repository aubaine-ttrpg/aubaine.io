<?php

namespace App\Form;

use App\Entity\Tag;
use App\Enum\TagCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagFormType extends AbstractType
{
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
            ->add('category', EnumType::class, [
                'class' => TagCategory::class,
                'choice_label' => static fn (TagCategory $category): string => $category->labelKey(),
                'choice_translation_domain' => 'tag',
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
