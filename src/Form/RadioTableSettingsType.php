<?php

namespace App\Form;

use App\Entity\Embeddable\RadioTable\Appearance;
use App\Entity\RadioTable;
use App\Form\Type\IntegerUnitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
            ->add('description', CKEditorType::class, [
                'required' => false,
                'sanitize_html' => true,
            ])
            ->add('frequencyUnit', ChoiceType::class, [
                'choices' => [
                    'MHz' => RadioTable::FREQUENCY_MHZ,
                    'kHz' => RadioTable::FREQUENCY_KHZ,
                ],
                'choice_translation_domain' => false,
            ])
            ->add('maxSignalLevelUnit', ChoiceType::class, [
                'choices' => [
                    'dB' => RadioTable::MAX_SIGNAL_LEVEL_DB,
                    'dBf' => RadioTable::MAX_SIGNAL_LEVEL_DBF,
                    'dBµV' => RadioTable::MAX_SIGNAL_LEVEL_DBUV,
                    'dBm' => RadioTable::MAX_SIGNAL_LEVEL_DBM,
                ],
                'choice_translation_domain' => false,
            ])
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
            ->add('appearanceBackgroundColor', TextType::class, [
                'property_path' => 'appearance.backgroundColor',

                'required' => false,
            ])
            ->add('appearanceColor', TextType::class, [
                'property_path' => 'appearance.textColor',

                'required' => false,
            ])
            ->add('appearanceWidthType', ChoiceType::class, [
                'property_path' => 'appearance.widthType',

                'choices' => [
                    Appearance::WIDTH_STANDARD,
                    Appearance::WIDTH_FULL,
                    Appearance::WIDTH_CUSTOM,
                ],
                'choice_label' => function ($choice) {
                    return 'radio_table.settings.form.appearanceWidthType.choice.'.$choice;
                },
            ])
            ->add('appearanceCustomWidth', IntegerUnitType::class, [
                'property_path' => 'appearance.customWidth',

                'attr' => ['min' => '900'],
                'required' => false,
            ])
            ->add('appearanceCollapsedComments', CheckboxType::class, [
                'property_path' => 'appearance.collapsedComments',

                'required' => false,
            ])
        ;
    }
}
