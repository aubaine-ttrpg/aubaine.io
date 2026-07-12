<?php

declare(strict_types=1);

namespace App\Page\Form;

use App\Design\Paper;
use App\SkillTree\SkillTreeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Customizes a skill-tree page: which tree JSON to render, the paper stock, and
 * whether to show the legend. Choices come from the seeded trees and the Paper
 * enum, so both value sets stay single-sourced.
 */
final class SkillTreeType extends AbstractType
{
    public function __construct(private readonly SkillTreeRepository $trees)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $treeChoices = [];
        foreach ($this->trees->ids() as $id) {
            $treeChoices[ucfirst($id)] = $id;
        }

        $paperChoices = [];
        foreach (Paper::cases() as $paper) {
            $paperChoices[$paper->labelFr()] = $paper->value;
        }

        $builder
            ->add('tree', ChoiceType::class, [
                'choices' => $treeChoices,
                'placeholder' => false,
                'label' => 'page.skill_tree.tree',
                'constraints' => [new NotBlank()],
            ])
            ->add('paper', ChoiceType::class, [
                'choices' => $paperChoices,
                'label' => 'page.skill_tree.paper',
            ])
            ->add('legend', CheckboxType::class, [
                'required' => false,
                'label' => 'page.skill_tree.legend',
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
