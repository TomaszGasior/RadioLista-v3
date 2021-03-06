<?php

namespace App\Form;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioStationRemoveType extends AbstractType
{
    private $radioStationRepository;

    public function __construct(RadioStationRepository $radioStationRepository)
    {
        $this->radioStationRepository = $radioStationRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chosenToRemove', EntityType::class, [
                'class' => RadioStation::class,
                'choice_label' => 'name',
                'translation_domain' => false,
                'query_builder' => $this->radioStationRepository->getQueryBuilderForRadioTable($options['radio_table']),
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $choices = $form->get('chosenToRemove')->getConfig()->getAttribute('choice_list')->getChoices();

        foreach ($view['chosenToRemove']->children as $name => $childrenView) {
            /** @var RadioStation */
            $radioStation = $choices[$name] ?? null;

            $childrenView->vars['frequency'] = $radioStation ? $radioStation->getFrequency() : null;
            $childrenView->vars['frequency_unit'] = $options['radio_table']->getFrequencyUnit();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['radio_table']);
        $resolver->setAllowedTypes('radio_table', RadioTable::class);
        $resolver->setDefaults([
            'label_format' => 'radio_station.remove.form.%name%',
        ]);
    }
}
