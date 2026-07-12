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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Customizes a front-cover page. Data is the page's array map; each field
 * whitelists and validates one key.
 */
final class CoverFrontType extends AbstractType
{
    public function __construct(private readonly CoverImageLibrary $covers)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eyebrow', TextType::class, [
                'required' => false,
                'label' => 'page.cover_front.eyebrow',
            ])
            ->add('title', TextType::class, [
                'label' => 'page.cover_front.title',
                'constraints' => [new NotBlank(), new Length(max: 40)],
            ])
            ->add('subtitle', TextType::class, [
                'required' => false,
                'label' => 'page.cover_front.subtitle',
            ])
            ->add('version', TextType::class, [
                'required' => false,
                'label' => 'page.cover_front.version',
            ])
            ->add('image', ChoiceType::class, [
                'required' => false,
                'choices' => $this->covers->choices(),
                'placeholder' => 'page.cover_front.image_none',
                'label' => 'page.cover_front.image',
            ])
            ->add('ornaments', CheckboxType::class, [
                'required' => false,
                'label' => 'page.cover_front.ornaments',
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
