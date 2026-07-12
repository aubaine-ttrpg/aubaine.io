<?php

declare(strict_types=1);

namespace App\Book\Form;

use App\Book\Dto\BookMeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<mixed>
 */
final class BookMetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'book.title',
            ])
            ->add('subtitle', TextType::class, [
                'required' => false,
                'label' => 'book.subtitle',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookMeta::class,
            'translation_domain' => 'messages',
        ]);
    }
}
