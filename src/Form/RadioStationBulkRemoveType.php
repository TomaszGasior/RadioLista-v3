<?php

namespace App\Form;

use App\Dto\RadioStationBulkRemoveDto;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioStationBulkRemoveType extends AbstractType
{
    public function __construct(
        private RadioStationRepository $radioStationRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('radioStationsToRemove', ChoiceType::class, [
                'choices' => $this->radioStationRepository->findForRadioTable($options['radio_table']),
                'choice_value' => 'id',
                'translation_domain' => false,
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $choiceList = $form->get('radioStationsToRemove')->getConfig()->getAttribute('choice_list');

        if (!$choiceList instanceof ChoiceListInterface) {
            throw new RuntimeException;
        }

        foreach ($view['radioStationsToRemove']->children as $i => $childView) {
            $radioStation = $choiceList->getChoicesForValues([$childView->vars['value']])[0];

            if (!$radioStation instanceof RadioStation) {
                throw new RuntimeException;
            }

            // Don't render checkboxes for already removed radio stations.
            // This is needed because choices are fetched before flushing entity removal to database.
            if (!$this->entityManager->contains($radioStation)) {
                unset($view['radioStationsToRemove']->children[$i]);
                continue;
            }

            $childView->vars['radio_station'] = $radioStation;
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
