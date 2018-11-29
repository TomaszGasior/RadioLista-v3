<?php

namespace App\Form\Type;

use App\Form\DataTransformer\RadioTableColumnsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableColumnsType extends AbstractType
{
    private $transformer;

    public function __construct(RadioTableColumnsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function getParent(): string
    {
        return FormType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this->transformer);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event){
            $form = $event->getForm();
            $data = $form->getViewData();

            foreach ($data as $name => $value) {
                $form->add($name, IntegerType::class, [
                    'data' => $value,
                ]);
            }
        });
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($view->children as $name => $childrenView) {
            if (isset($options['column_labels'][$name])) {
                $childrenView->vars['label'] = $options['column_labels'][$name];
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'column_labels' => [],
        ]);
    }
}
