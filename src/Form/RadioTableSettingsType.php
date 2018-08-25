<?php

namespace App\Form;

use App\Entity\RadioTable;
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
            ->add('columns', null, [
                'entry_type' => IntegerType::class,
                // Label for each children is defined in finishView().
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $columnsNames = [
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
        ];
        foreach ($view->children['columns']->children as $name => $childrenView) {
            $childrenView->vars['label'] = $columnsNames[$name];
        }
    }
}
