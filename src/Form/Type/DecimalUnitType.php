<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecimalUnitType extends AbstractType
{
    public function getParent(): string
    {
        return NumberType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['step'] = $options['step'];
        $view->vars['unit_label'] = $options['unit_label'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['unit_label' => ''])
            ->setDefaults(['input' => 'string'])
            ->setDefaults(['html5' => true])
            ->setRequired(['step'])
            ->setAllowedTypes('step', 'float')
            ->setAllowedValues('html5', true)
        ;
    }
}
