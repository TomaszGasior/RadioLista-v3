<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextHintsType extends AbstractType
{
    public function getParent()
    {
        return TextType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['hints'] = $options['hints'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'hints' => [],
        ]);
    }
}
