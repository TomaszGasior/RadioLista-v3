<?php

namespace App\Form;

use App\Entity\Enum\RadioTable\Status;
use App\Entity\RadioTable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'help' => 'radio_table.settings.form.name.help',
            ])
            ->add('status', EnumType::class, [
                'expanded' => true,
                'class' => Status::class,
                'choice_label' => function (Status $status) {
                    return 'radio_table.settings.form.status.choice.'.$status->value;
                },
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($view['status'] as $children) {
            $children->vars['help'] = 'radio_table.settings.form.status.choice.'.$children->vars['value'].'.help';
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioTable::class,
            'label_format' => 'radio_table.settings.form.%name%',
        ]);
    }
}
