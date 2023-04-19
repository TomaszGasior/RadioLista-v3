<?php

namespace App\Form\Type;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class RadioStationCompletionTextType extends AbstractType
{
    public function __construct(private RadioStationRepository $radioStationRepository) {}

    public function getParent(): string
    {
        return TextHintsType::class;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $radioTable = $this->getRadioTable($form->getRoot());
        $column = Column::tryFrom($form->getName());

        if (!$column) {
            return;
        }

        $isColumnEnabled = in_array($column, $radioTable->getColumns());

        if (!$isColumnEnabled) {
            return;
        }

        $view->vars['hints'] = $this->radioStationRepository
            ->findColumnAllValuesForRadioTable($radioTable, $column);

        // Disable autocompletion from browser's cache,
        // use only completions defined by this application.
        $view->vars['attr']['autocomplete'] = 'off';
    }

    private function getRadioTable(FormInterface $form): RadioTable
    {
        $radioStation = $form->getData();

        if (!$radioStation instanceof RadioStation) {
            throw new RuntimeException;
        }

        return $radioStation->getRadioTable();
    }
}
