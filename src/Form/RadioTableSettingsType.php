<?php

namespace App\Form;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\MaxSignalLevelUnit;
use App\Entity\Enum\RadioTable\Status;
use App\Entity\Enum\RadioTable\Width;
use App\Entity\RadioTable;
use App\Form\Type\IntegerUnitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'empty_data' => '',
                'help' => 'radio_table.settings.form.name.help',
            ])
            ->add('status', EnumType::class, [
                'expanded' => true,
                'class' => Status::class,
                'choice_label' => function (Status $status) {
                    return 'radio_table.settings.form.status.choice.'.$status->value;
                },
            ])
            ->add('description', CKEditorType::class, [
                'required' => false,
                'sanitize_html' => true,
            ])
            ->add('frequencyUnit', EnumType::class, [
                'class' => FrequencyUnit::class,
                'choice_label' => function (FrequencyUnit $frequencyUnit): string {
                    return $frequencyUnit->getLabel();
                },
                'choice_translation_domain' => false,
            ])
            ->add('maxSignalLevelUnit', EnumType::class, [
                'class' => MaxSignalLevelUnit::class,
                'choice_label' => function (MaxSignalLevelUnit $maxSignalLevelUnit): string {
                    return $maxSignalLevelUnit->getLabel();
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

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($view['status'] as $children) {
            $children->vars['help'] = 'radio_table.settings.form.status.choice.'.$children->vars['value'].'.help';
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioTable::class,
            'label_format' => 'radio_table.settings.form.%name%',
        ]);
    }
}
