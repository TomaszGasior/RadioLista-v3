<?php

namespace App\Form;

use App\Entity\RadioTable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nazwa wykazu',
            ])
            ->add('useKhz', ChoiceType::class, [
                'label'   => 'Jednostka częstotliwości',
                'choices' => [
                    'kHz' => 1,
                    'MHz' => 0,
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Opis wykazu',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'expanded' => true,
                'choices'  => [
                    'Publiczny — wykaz może zobaczyć każdy'     => RadioTable::STATUS_PUBLIC,
                    'Niepubliczny — wykaz mogą zobaczyć jedynie osoby, które otrzymają odnośnik'
                        => RadioTable::STATUS_UNLISTED,
                    'Prywatny — wykaz możesz zobaczyć tylko ty' => RadioTable::STATUS_PRIVATE,
                ],
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
