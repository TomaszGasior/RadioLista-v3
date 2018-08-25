<?php

namespace App\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LabeledCollectionType extends CollectionType
{
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        foreach ($view->children as $name => $childrenView) {
            if (isset($options['entry_labels'][$name])) {
                $childrenView->vars['label'] = $options['entry_labels'][$name];
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'entry_labels' => [],
        ]);
    }
}
