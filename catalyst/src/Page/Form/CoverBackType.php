<?php

declare(strict_types=1);

namespace App\Page\Form;

use App\Design\CoverImageLibrary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Customizes a back-cover page. The blurb is a single multi-line string; the
 * template renders one line per row.
 *
 * @extends AbstractType<mixed>
 */
final class CoverBackType extends AbstractType
{
    public function __construct(private readonly CoverImageLibrary $covers)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eyebrow', TextType::class, [
                'required' => false,
                'label' => 'page.cover_back.eyebrow',
            ])
            ->add('tagline', TextType::class, [
                'required' => false,
                'label' => 'page.cover_back.tagline',
            ])
            ->add('bodyText', TextareaType::class, [
                'required' => false,
                'label' => 'page.cover_back.body',
                'help' => 'page.cover_back.body_help',
            ])
            ->add('cta', TextType::class, [
                'required' => false,
                'label' => 'page.cover_back.cta',
            ])
            ->add('url', TextType::class, [
                'required' => false,
                'label' => 'page.cover_back.url',
            ])
            ->add('image', ChoiceType::class, [
                'required' => false,
                'choices' => $this->covers->choices(),
                'placeholder' => 'page.cover_back.image_none',
                'label' => 'page.cover_back.image',
            ])
            ->add('showQr', CheckboxType::class, [
                'required' => false,
                'label' => 'page.cover_back.show_qr',
            ])
            ->add('ornaments', CheckboxType::class, [
                'required' => false,
                'label' => 'page.cover_back.ornaments',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'translation_domain' => 'messages',
        ]);
    }
}
