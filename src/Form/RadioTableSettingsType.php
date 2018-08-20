<?php

namespace App\Form;

use App\Entity\RadioTable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nazwa wykazu',
            ])
            ->add('useKhz', ChoiceType::class, [
                'label' => 'Jednostka częstotliwości',
                'choices' => [
                    'kHz' => 1,
                    'MHz' => 0,
                ]
            ])
            ->add('sorting', ChoiceType::class, [
                'label' => 'Domyślne sortowanie',
                'choices' => [
                    'częstotliwość'      => RadioTable::SORTING_FREQUENCY,
                    'nazwa'              => RadioTable::SORTING_NAME,
                    'numer w odbiorniku' => RadioTable::SORTING_PRIVATE_NUMBER,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Opis wykazu',
            ])
            // ->add('status')
            // ->add('columns')
            // ->add('appearance')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RadioTable::class,
        ]);
    }
}
