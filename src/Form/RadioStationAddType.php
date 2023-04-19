<?php

namespace App\Form;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioStationAddType extends AbstractType
{
    public function getParent(): string
    {
        return RadioStationEditType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setData(new RadioStation('0.000', '', $options['radio_table']));

        // Display frequency field as empty by default instead of "0.000" for aesthetics.
        // Empty string cannot be used in RadioStation constructor since it's not supported
        // by form type used in frequency field.
        $builder->get('frequency')
            ->setData(null)
            ->setDataLocked(true)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('radio_table')
            ->setAllowedTypes('radio_table', RadioTable::class)
        ;
    }
}
