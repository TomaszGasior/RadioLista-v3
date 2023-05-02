<?php

namespace App\Form;

use App\Dto\RadioTableSearchDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('s', SearchType::class, [
                'property_path' => 'searchTerm',
                'attr' => [
                    'placeholder' => 'common.search_form.form.s.help',
                ],
            ])
            ->setMethod('GET')
        ;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioTableSearchDto::class,
            'label_format' => 'common.search_form.form.%name%',
            'csrf_protection' => false,
            'validation_error_generic_notification' => false,
        ]);
    }
}
