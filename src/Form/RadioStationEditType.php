<?php

namespace App\Form;

use App\Entity\RadioStation;
use App\Form\Extension\DecimalUnitType;
use App\Form\Extension\TextHintsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioStationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('frequency', DecimalUnitType::class, [
                'label'      => 'Częstotliwość',
                'unit_label' => 'MHz',
            ])
            ->add('name', TextHintsType::class, [
                'label' => 'Nazwa',
                'hints' => [
                    'Polskie Radio Jedynka',
                    'Polskie Radio Dwójka',
                    'Polskie Radio Trójka',
                    'Polskie Radio Czwórka',
                    'Polskie Radio 24',
                    'RMF FM',
                    'RMF Classic',
                    'RMF Maxxx',
                    'Zet',
                    'Chillizet',
                    'Meloradio',
                    'AntyRadio',
                    'Eska',
                    'Wawa',
                    'Vox FM',
                    'Plus',
                    'Tok FM',
                    'Złote Przeboje',
                    'Pogoda',
                    'Rock Radio',
                    'Muzo.FM',
                    'Maryja',
                ],
            ])
            ->add('radioGroup', TextHintsType::class, [
                'label'    => 'Grupa medialna',
                'required' => false,
                'hints'    => [
                    'Polskie Radio',
                    'Audytorium 17',
                    'Grupa RMF',
                    'Eurozet',
                    'Grupa Radiowa Time',
                    'Grupa Radiowa Agory',
                ],
            ])
            ->add('country', TextHintsType::class, [
                'label'    => 'Kraj',
                'required' => false,
                'hints'    => [
                    'Polska',
                    'Białoruś',
                    'Czechy',
                    'Litwa',
                    'Niemcy',
                    'Ukraina',
                    'Rosja',
                    'Słowacja',
                ],
            ])
            ->add('quality', ChoiceType::class, [
                'label'   => 'Jakość odbioru',
                'choices' => [
                    'bardzo dobra' => RadioStation::QUALITY_VERY_GOOD,
                    'dobra'        => RadioStation::QUALITY_GOOD,
                    'dostateczna'  => RadioStation::QUALITY_MIDDLE,
                    'zła'          => RadioStation::QUALITY_BAD,
                    'bardzo zła'   => RadioStation::QUALITY_VERY_BAD,
                ],
            ])
            ->add('location', null, [
                'label' => 'Lokalizacja nadajnika',
            ])
            ->add('power', DecimalUnitType::class, [
                'label'      => 'Moc nadajnika',
                'unit_label' => 'kW',
                'required'   => false,
            ])
            ->add('polarization', ChoiceType::class, [
                'label'    => 'Polaryzacja',
                'required' => false,
                'choices'  => [
                    '(brak informacji)' => RadioStation::POLARIZATION_NONE,
                    'pozioma'           => RadioStation::POLARIZATION_HORIZONTAL,
                    'pionowa'           => RadioStation::POLARIZATION_VERTICAL,
                    'kołowa'            => RadioStation::POLARIZATION_CIRCULAR,
                    'różne'             => RadioStation::POLARIZATION_VARIOUS,
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label'   => 'Rodzaj programu',
                'choices' => [
                    'muzyczny'     => RadioStation::TYPE_MUSIC,
                    'uniwersalny'  => RadioStation::TYPE_UNIVERSAL,
                    'informacyjny' => RadioStation::TYPE_INFORMATION,
                    'katolicki'    => RadioStation::TYPE_RELIGIOUS,
                    'inny'         => RadioStation::TYPE_OTHER,
                ],
            ])
            ->add('localityType', ChoiceType::class, [
                'property_path' => 'locality[type]',

                'label'   => 'Lokalność programu',
                'choices' => [
                    'ogólnokrajowy' => RadioStation::LOCALITY_COUNTRY,
                    'lokalny'       => RadioStation::LOCALITY_LOCAL,
                    'sieciowy'      => RadioStation::LOCALITY_NETWORK,
                ],
            ])
            ->add('localityCity', TextHintsType::class, [
                'property_path' => 'locality[city]',

                'label'    => 'Lokalność — miasto/województwo',
                'required' => false,
                'hints'    => [
                    'dolnośląskie',
                    'kujawsko-pomorskie',
                    'lubelskie',
                    'lubuskie',
                    'łódzkie',
                    'małopolskie',
                    'mazowieckie',
                    'opolskie',
                    'podkarpackie',
                    'podlaskie',
                    'pomorskie',
                    'śląskie',
                    'świętokrzyskie',
                    'warmińsko-mazurskie',
                    'wielkopolskie',
                    'zachodniopomorskie',
                ],
            ])
            ->add('privateNumber', null, [
                'label' => 'Numer w odbiorniku',
            ])
            ->add('marker', ChoiceType::class, [
                'label'    => 'Wyróżnienie wizualne',
                'required' => false,
                'choices'  => [
                    '(brak wyróżnienia)'  => RadioStation::MARKER_NONE,
                    'pogrubienie'         => RadioStation::MARKER_1,
                    'pochylenie'          => RadioStation::MARKER_2,
                    'przekreślenie'       => RadioStation::MARKER_3,
                    'czerwony kolor tła'  => RadioStation::MARKER_4,
                    'zielony kolor tła'   => RadioStation::MARKER_5,
                    'niebieski kolor tła' => RadioStation::MARKER_6,
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label'    => 'Komentarz',
                'required' => false,
            ])
            ->add('rdsPs', TextareaType::class, [
                'property_path' => 'rds[ps]',

                'label'    => 'Programme Service',
                'required' => false,
            ])
            ->add('rdsRt', TextareaType::class, [
                'property_path' => 'rds[Rt]',

                'label'    => 'Radio Text',
                'required' => false,
            ])
            ->add('rdsPty', null, [
                'property_path' => 'rds[Pty]',

                'label' => 'Program Type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RadioStation::class,
        ]);
    }
}
