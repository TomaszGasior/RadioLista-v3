<?php

namespace App\Form;

use App\Entity\RadioTable;
use App\Form\Type\RadioTableColumnsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class RadioTableSettingsType extends RadioTableCreateType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('sorting', ChoiceType::class, [
                'label' => 'Domyślne sortowanie',
                'choices' => [
                    'częstotliwość' => RadioTable::SORTING_FREQUENCY,
                    'nazwa' => RadioTable::SORTING_NAME,
                    'numer w odbiorniku' => RadioTable::SORTING_PRIVATE_NUMBER,
                ],
            ])
            ->add('columns', RadioTableColumnsType::class, [
                'column_labels' => [
                    RadioTable::COLUMN_PRIVATE_NUMBER  => 'Numer w odbiorniku',
                    RadioTable::COLUMN_FREQUENCY  => 'Częstotliwość',
                    RadioTable::COLUMN_NAME  => 'Nazwa',
                    RadioTable::COLUMN_RADIO_GROUP  => 'Grupa medialna',
                    RadioTable::COLUMN_COUNTRY  => 'Kraj',
                    RadioTable::COLUMN_LOCATION  => 'Lokalizacja nadajnika',
                    RadioTable::COLUMN_POWER  => 'Moc nadajnika',
                    RadioTable::COLUMN_POLARIZATION  => 'Polaryzacja',
                    RadioTable::COLUMN_TYPE  => 'Rodzaj programu',
                    RadioTable::COLUMN_LOCALITY  => 'Lokalność programu',
                    RadioTable::COLUMN_QUALITY  => 'Jakość odbioru',
                    RadioTable::COLUMN_RDS  => 'RDS',
                    RadioTable::COLUMN_COMMENT  => 'Komentarz',
                ],
            ])
            ->add('appearanceTheme', ChoiceType::class, [
                'property_path' => 'appearance[th]',

                'label' => 'Motyw',
                'required' => false,
                'choices' => [
                    '(brak motywu, własne kolory)' => '',
                    'Górski las' => 'bieszczady',
                    'Drewno' => 'wood',
                    'Tęcza i niebo' => 'rainbow',
                    'Widok nocą' => 'night',
                ],
            ])
            ->add('appearanceBackgroundColor', TextType::class, [
                'property_path' => 'appearance[bg]',

                'label' => 'Kolor tła',
                'required' => false,
            ])
            ->add('appearanceColor', TextType::class, [
                'property_path' => 'appearance[fg]',

                'label' => 'Kolor tekstu',
                'required' => false,
            ])
            ->add('appearanceBackgroundImage', UrlType::class, [
                'property_path' => 'appearance[img]',

                'label' => 'Adres URL obrazu tła',
                'required' => false,
            ])
            ->add('appearanceFullWidth', CheckboxType::class, [
                'property_path' => 'appearance[full]',

                'label' => 'Rozciągnij wykaz na pełną szerokość ekranu',
                'required' => false,
            ])
        ;
    }
}
