<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecimalUnitType extends AbstractType
{
    public function getParent(): string
    {
        // Do not use NumberType as parent. NumberType defines additional view transformer that formats
        // value according to (Polish) locale settings. This causes comma to be used as decimal point
        // instead dot. `<input type="number" value="...">` tag requires standard formatting with dot.
        return TextType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['step'] = $options['step'];
        $view->vars['unit_label'] = $options['unit_label'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'step' => 0.05,
            'unit_label' => '',
        ]);
    }
}
