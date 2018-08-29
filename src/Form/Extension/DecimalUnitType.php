<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecimalUnitType extends AbstractType
{
    public function getParent()
    {
        return NumberType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['unit_label'] = $options['unit_label'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'unit_label' => '',
        ]);

        $resolver->setNormalizer('scale', function(){
            return 2;
        });
    }

    public function getBlockPrefix()
    {
        return 'decimal_unit';
    }
}
