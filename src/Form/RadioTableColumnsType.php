<?php

namespace App\Form;

use App\Entity\RadioTable;
use App\Form\Type\RadioTableColumnsType as RadioTableColumnsUIType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableColumnsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('columns', RadioTableColumnsUIType::class, [
                'label_format' => 'column.%name%',
                'translation_domain' => 'radio_table',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioTable::class,
        ]);
    }
}
