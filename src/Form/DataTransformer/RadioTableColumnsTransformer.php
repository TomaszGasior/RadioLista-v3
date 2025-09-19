<?php

namespace App\Form\DataTransformer;

use App\Entity\Enum\RadioTable\Column;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * The following transformer is designed for RadioTable::$columns field.
 * It's used by RadioTableColumnsType form field.
 *
 * * RadioTable::$columns contains array with values defining names
 *   of columns shown in radio table. Order of array items determines
 *   columns order in rendered radio table.
 * * Array transformed for view purpose contain names of all possible columns
 *   as keys and order numbers as values. Order number is defined as unique,
 *   positive or negative number other than 0. Columns with negative order
 *   number are not visible in radio table and should not be persisted in
 *   RadioTable::$columns field.
 */
class RadioTableColumnsTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): array
    {
        $enabledColumnNames = is_array($value)
            ? array_map(function(Column $column){ return $column->value; }, $value)
            : [];

        $allColumnNames = array_column(Column::cases(), 'value');
        $disabledColumns = array_diff($allColumnNames, $enabledColumnNames);

        $orderNumber = 1;
        $mergedColumns = [];

        foreach ($enabledColumnNames as $columnName) {
            $mergedColumns[$columnName] = $orderNumber++;
        }
        foreach ($disabledColumns as $columnName) {
            $mergedColumns[$columnName] = -($orderNumber++);
        }

        return $mergedColumns;
    }

    public function reverseTransform(mixed $value): array
    {
        $mergedColumnNames = is_array($value) ? $value : [];

        $enabledColumnNames = array_flip(array_filter(
            $mergedColumnNames,
            function($orderNumber){ return $orderNumber > 0; }
        ));
        ksort($enabledColumnNames);

        return array_values(array_map([Column::class, 'from'], $enabledColumnNames));
    }
}
