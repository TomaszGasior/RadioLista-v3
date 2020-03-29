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
                'choices' => [
                    RadioTable::SORTING_FREQUENCY,
                    RadioTable::SORTING_NAME,
                    RadioTable::SORTING_PRIVATE_NUMBER,
                ],
                'choice_label' => function ($choice) {
                    return 'column.'.$choice;
                },
                'choice_translation_domain' => 'radio_table',
            ])
            ->add('columns', RadioTableColumnsType::class, [
                'label_format' => 'column.%name%',
                'translation_domain' => 'radio_table',
            ])
            ->add('appearanceTheme', ChoiceType::class, [
                'property_path' => 'appearance.theme',

                'required' => false,
                'choices' => [
                    '',
                    'bieszczady',
                    'wood',
                    'rainbow',
                    'night',
                ],
                'choice_label' => function ($choice) {
                    return 'radio_table.settings.form.appearanceTheme.choice.'.$choice;
                },
            ])
            ->add('appearanceBackgroundColor', TextType::class, [
                'property_path' => 'appearance.backgroundColor',

                'required' => false,
            ])
            ->add('appearanceColor', TextType::class, [
                'property_path' => 'appearance.textColor',

                'required' => false,
            ])
            ->add('appearanceBackgroundImage', UrlType::class, [
                'property_path' => 'appearance.backgroundImage',

                'required' => false,
            ])
            ->add('appearanceFullWidth', CheckboxType::class, [
                'property_path' => 'appearance.fullWidth',

                'required' => false,
            ])
        ;
    }
}
