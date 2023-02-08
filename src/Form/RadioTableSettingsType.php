<?php

namespace App\Form;

use App\Entity\Embeddable\RadioTable\Appearance;
use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\MaxSignalLevelUnit;
use App\Entity\Enum\RadioTable\Width;
use App\Entity\RadioTable;
use App\Form\Type\IntegerUnitType;
use App\Util\Data\RadioTableLabelsTrait;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class RadioTableSettingsType extends RadioTableCreateType
{
    use RadioTableLabelsTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('description', CKEditorType::class, [
                'required' => false,
                'sanitize_html' => true,
            ])
            ->add('frequencyUnit', EnumType::class, [
                'class' => FrequencyUnit::class,
                'choice_label' => function (FrequencyUnit $frequencyUnit): string {
                    return $this->getFrequencyLabel($frequencyUnit);
                },
                'choice_translation_domain' => false,
            ])
            ->add('maxSignalLevelUnit', EnumType::class, [
                'class' => MaxSignalLevelUnit::class,
                'choice_label' => function (MaxSignalLevelUnit $maxSignalLevelUnit): string {
                    return $this->getMaxSignalLevelLabel($maxSignalLevelUnit);
                },
                'choice_translation_domain' => false,
            ])
            ->add('sorting', EnumType::class, [
                'class' => Column::class,
                'choices' => Column::getSortable(),
                'choice_label' => function (Column $column) {
                    return 'column.'.$column->value;
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
            ->add('appearanceWidthType', EnumType::class, [
                'property_path' => 'appearance.widthType',

                'class' => Width::class,
                'choice_label' => function (Width $widthType) {
                    return 'radio_table.settings.form.appearanceWidthType.choice.'.$widthType->value;
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
