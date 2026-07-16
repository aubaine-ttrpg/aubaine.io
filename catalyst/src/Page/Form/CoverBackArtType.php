<?php

declare(strict_types=1);

namespace App\Page\Form;

use App\Design\CoverImageLibrary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Customizes an épuré back-cover page: the illustration carries the page, with
 * only a tagline, a call to action, the URL and the QR over it (no blurb, no frame).
 *
 * @extends AbstractType<mixed>
 */
final class CoverBackArtType extends AbstractType
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
