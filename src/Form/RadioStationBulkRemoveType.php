<?php

namespace App\Form;

use App\Dto\RadioStationBulkRemoveDto;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioStationBulkRemoveType extends AbstractType
{
    public function __construct(private RadioStationRepository $radioStationRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('radioStationsToRemove', ChoiceType::class, [
                'choices' => $this->radioStationRepository->findForRadioTable($options['radio_table']),
                'choice_label' => 'name',
                'translation_domain' => false,
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $choices = $form->get('radioStationsToRemove')->getConfig()->getAttribute('choice_list')->getChoices();

        foreach ($view['radioStationsToRemove']->children as $name => $childrenView) {
            $radioStation = $choices[$name] ?? null;

            if ($radioStation && false === $radioStation instanceof RadioStation) {
                throw new RuntimeException;
            }

            /** @var RadioStation|null $radioStation */

            $childrenView->vars['frequency'] = $radioStation ? $radioStation->getFrequency() : null;
            $childrenView->vars['frequency_unit'] = $options['radio_table']->getFrequencyUnit();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioStationBulkRemoveDto::class,
            'label_format' => 'radio_station.bulk_remove.form.%name%',
        ]);

        $resolver
            ->setRequired(['radio_table'])
            ->setAllowedTypes('radio_table', RadioTable::class)
        ;
    }
}
