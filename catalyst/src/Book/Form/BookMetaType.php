<?php

declare(strict_types=1);

namespace App\Book\Form;

use App\Book\BookType;
use App\Book\Dto\BookMeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ])
            ->add('bookType', EnumType::class, [
                'class' => BookType::class,
                'label' => 'book.type',
                'choice_label' => static fn (BookType $type): string => $type->labelKey(),
            ])
            ->add('versionMajor', IntegerType::class, [
                'label' => 'book.version.major',
            ])
            ->add('versionMinor', IntegerType::class, [
                'label' => 'book.version.minor',
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
