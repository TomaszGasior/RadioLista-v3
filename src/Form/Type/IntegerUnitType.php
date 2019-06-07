<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntegerUnitType extends AbstractType
{
    public function getParent(): string
    {
        return IntegerType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['unit_label'] = $options['unit_label'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'unit_label' => '',
        ]);
    }
}
