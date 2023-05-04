<?php

namespace App\Form\Type;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioStation;
use App\Repository\RadioStationRepository;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The following form type is designed for RadioStationEditType.
 */
class RadioStationCompletionTextType extends AbstractType
{
    public function __construct(private RadioStationRepository $radioStationRepository) {}

    public function getParent(): string
    {
        return CompletionTextType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $radioStation = $form->getRoot()->getData();
        if (!$radioStation instanceof RadioStation) {
            throw new RuntimeException;
        }

        $radioTable = $radioStation->getRadioTable();

        $column = Column::getByRadioStationPropertyPath((string) $form->getPropertyPath());
        if (!$column) {
            return;
        }

        $isColumnEnabled = in_array($column, $radioTable->getColumns());
        if (!$isColumnEnabled) {
            return;
        }

        $view->vars['completions'] = $this->radioStationRepository
            ->findColumnAllValuesForRadioTable($radioTable, $column);

        // Disable autocompletion from browser's cache,
        // use only completions defined by this application.
        $view->vars['attr']['autocomplete'] = 'off';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('completions', []);
    }
}
