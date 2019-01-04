<?php

namespace App\Form;

use App\Entity\RadioStation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioStationRemoveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chosenToRemove', EntityType::class, [
                'label' => 'Wskaż stacje do usunięcia',

                'class'        => RadioStation::class,
                'choices'      => $options['radiostations'],
                'choice_label' => 'name',
                'expanded'     => true,
                'multiple'     => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['radiostations']);
        $resolver->setAllowedTypes('radiostations', RadioStation::class . '[]');
    }
}
