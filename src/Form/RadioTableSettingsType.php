<?php

namespace App\Form;

use App\Entity\RadioTable;
use App\Form\Extension\LabeledCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableSettingsType extends RadioTableCreateType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('sorting', ChoiceType::class, [
                'label'   => 'Domyślne sortowanie',
                'choices' => [
                    'częstotliwość'      => RadioTable::SORTING_FREQUENCY,
                    'nazwa'              => RadioTable::SORTING_NAME,
                    'numer w odbiorniku' => RadioTable::SORTING_PRIVATE_NUMBER,
                ],
            ])
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
            ->add('appearanceTheme', ChoiceType::class, [
                'property_path' => 'appearance[th]',

                'expanded' => true,
                'choices'  => [
                    'Bez motywu'    => '',
                    'Górski las'    => 'bieszczady',
                    'Drewno'        => 'wood',
                    'Tęcza i niebo' => 'rainbow',
                    'Widok nocą'    => 'night',
                ],
            ])
            ->add('appearanceBackgroundColor', ColorType::class, [
                'property_path' => 'appearance[bg]',

                'label'    => 'Kolor tła',
                'required' => false,
            ])
            ->add('appearanceColor', ColorType::class, [
                'property_path' => 'appearance[fg]',

                'label'    => 'Kolor tekstu',
                'required' => false,
            ])
            ->add('appearanceBackgroundImage', UrlType::class, [
                'property_path' => 'appearance[img]',

                'label'    => 'Adres URL obrazu tła',
                'required' => false,
            ])
            ->add('appearanceFullWidth', CheckboxType::class, [
                'property_path' => 'appearance[full]',

                'label'    => 'Zawsze wykorzystuj pełną szerokość ekranu',
                'required' => false,
            ])
        ;
    }
}
