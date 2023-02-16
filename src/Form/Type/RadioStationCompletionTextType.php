<?php

namespace App\Form\Type;

use App\Entity\RadioStation;
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
        /** @var RadioStation */
        $radioStation = $form->getRoot()->getData();

        if (false === $radioStation instanceof RadioStation) {
            throw new RuntimeException;
        }

        $radioTable = $radioStation->getRadioTable();
        $columnName = $form->getName();

        $isColumnEnabled = in_array($columnName, $radioTable->getColumns());

        if (false === $isColumnEnabled) {
            return;
        }

        $view->vars['hints'] = $this->radioStationRepository
            ->findColumnAllValuesForRadioTable($radioTable, $columnName);

        // Disable autocompletion from browser's cache,
        // use only completions defined by this application.
        $view->vars['attr']['autocomplete'] = 'off';
    }
}
