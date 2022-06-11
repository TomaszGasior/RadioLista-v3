<?php

namespace App\Form\Extension;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Form\RadioStationEditType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * This extension is designed for RadioStationEditType.
 *
 * It adds extra information into form view to make it possible
 * to render form fields for radio table's disabled columns different way.
 */
class RadioStationEditExtension extends AbstractTypeExtension
{
    /**
     * These form fields aren't radio table columns
     * and should never be rendered as disabled.
     */
    private const PROPERTY_PATH_NON_COLUMNS = [
        'appearance.background',
        'appearance.bold',
        'appearance.italic',
        'appearance.strikethrough',
    ];

    /**
     * In most cases property path is the same as column name
     * but these form fields contain values from embeddable objects
     * and require special handling of property path.
     */
    private const PROPERTY_PATH_TO_COLUMN = [
        'rds.pi' => RadioTable::COLUMN_RDS_PI,
        'rds.ps' => RadioTable::COLUMN_RDS,
        'rds.pty' => RadioTable::COLUMN_RDS,
        'rds.rt' => RadioTable::COLUMN_RDS,
    ];

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var RadioStation */
        $radioStation = $form->getData();

        $visibleColumns = $radioStation->getRadioTable()->getColumns();
        $formChildrenToHide = [];

        /** @var FormInterface[] $form */
        foreach ($form as $childName => $child) {
            $propertyPath = (string) $child->getPropertyPath();
            $columnName = self::PROPERTY_PATH_TO_COLUMN[$propertyPath] ?? $propertyPath;

            if (in_array($columnName, self::PROPERTY_PATH_NON_COLUMNS)) {
                continue;
            }

            if (false === in_array($columnName, $visibleColumns)) {
                $formChildrenToHide[] = $childName;
            }
        }

        foreach ($view as $childName => $child) {
            $child->vars['disabled_radio_table_column'] =
                in_array($childName, $formChildrenToHide);
        }
    }

    static public function getExtendedTypes(): array
    {
        return [RadioStationEditType::class];
    }
}
