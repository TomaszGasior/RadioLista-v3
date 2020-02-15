<?php

namespace App\Form\Extension;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Form\RadioStationEditType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class RadioStationEditExtension extends AbstractTypeExtension
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $radioStation = $form->getData();

        // It's probably impossible but let's check it, just in case.
        if (!($radioStation instanceof RadioStation)) {
            return;
        }

        $visibleColumns = $radioStation->getRadioTable()->getColumns();
        $formChildrenToHide = [];
        $excludedChildren = ['marker'];

        foreach ($form as $childrenName => $children) {
            // Get only first element of property path. Some RadioStation properties
            // (like locality or rds) use additional elements to access array key.
            // To get proper name of RadioTable column it's needed to ignore them.
            $columnName = $children->getPropertyPath()->getElement(0);

            if (false === in_array($columnName, $visibleColumns)) {
                $formChildrenToHide[] = $childrenName;
            }
        }

        foreach ($view as $childrenName => $children) {
            $children->vars['disabled_radio_table_column'] =
                in_array($childrenName, $formChildrenToHide) &&
                false === in_array($childrenName, $excludedChildren);
        }
    }

    static public function getExtendedTypes(): array
    {
        return [RadioStationEditType::class];
    }
}
