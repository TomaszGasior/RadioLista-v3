<?php

namespace App\Form\Type;

use App\Entity\Enum\RadioTable\Column;
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
    public function __construct(private RadioTableColumnsTransformer $transformer) {}

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
            // Frequency and name columns have to be always visible.
            if (in_array($name, [Column::FREQUENCY->value, Column::NAME->value])) {
                $childrenView->vars['attr']['min'] = 1;
            }
        }
    }
}
