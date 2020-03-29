<?php

namespace App\Form;

use App\Entity\RadioTable;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('frequencyUnit', ChoiceType::class, [
                'choices' => [
                    'MHz' => RadioTable::FREQUENCY_MHZ,
                    'kHz' => RadioTable::FREQUENCY_KHZ,
                ],
                'choice_translation_domain' => false,
            ])
            ->add('description', CKEditorType::class, [
                'required' => false,
                'sanitize_html' => true,
            ])
            ->add('status', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    RadioTable::STATUS_PUBLIC,
                    RadioTable::STATUS_UNLISTED,
                    RadioTable::STATUS_PRIVATE,
                ],
                'choice_label' => function ($choice) {
                    return 'radio_table.settings.form.status.choice.'.$choice;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioTable::class,
            'label_format' => 'radio_table.settings.form.%name%',
        ]);
    }
}
