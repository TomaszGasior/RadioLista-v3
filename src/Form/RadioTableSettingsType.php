<?php

namespace App\Form;

use App\Entity\RadioTable;
use App\Form\Extension\LabeledCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableSettingsType extends RadioTableCreateType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('columns', LabeledCollectionType::class, [
                'entry_type'   => IntegerType::class,
                'entry_labels' => [
                    'privateNumber' => 'Numer w odbiorniku',
                    'frequency'     => 'Częstotliwość',
                    'name'          => 'Nazwa',
                    'radioGroup'    => 'Grupa medialna',
                    'country'       => 'Kraj',
                    'location'      => 'Lokalizacja nadajnika',
                    'power'         => 'Moc nadajnika',
                    'polarization'  => 'Polaryzacja',
                    'type'          => 'Rodzaj programu',
                    'locality'      => 'Lokalność programu',
                    'quality'       => 'Jakość odbioru',
                    'rds'           => 'RDS',
                    'comment'       => 'Komentarz',
                ],
            ])
        ;
    }
}
