<?php

namespace App\Form\DataTransformer;

use App\Entity\RadioTable;
use Symfony\Component\Form\DataTransformerInterface;

// The following transformer is designed for RadioTable::$columns field.
// It's used by RadioTableColumnsType form field.
//
// * RadioTable::$columns contains array with values defining names
//   of columns shown in radiotable. Order of array items determines
//   columns order in rendered radiotable.
// * Array transformed for view purpose contain names of all possible columns
//   as keys and order numbers as values. Order number is defined as unique,
//   positive or negative number other than 0. Columns with negative order
//   number are not visible in radiotable and should not be persisted in
//   RadioTable::$columns field.

class RadioTableColumnsTransformer implements DataTransformerInterface
{
    public function transform($value): array
    {
        $enabledColumns  = is_array($value) ? $value : [];
        $disabledColumns = array_diff($this->getAllPossibleColumnsNames(), $enabledColumns);

        $orderNumber = 1;
        $mergedColumns = [];

        foreach ($enabledColumns as $columnName) {
            $mergedColumns[$columnName] = $orderNumber++;
        }
        foreach ($disabledColumns as $columnName) {
            $mergedColumns[$columnName] = -($orderNumber++);
        }

        return $mergedColumns;
    }

    public function reverseTransform($value): array
    {
        $mergedColumns = is_array($value) ? $value : [];

        $enabledColumns = array_flip(array_filter(
            $mergedColumns,
            function($orderNumber){ return $orderNumber > 0; }
        ));
        ksort($enabledColumns);

        return array_values($enabledColumns);
    }

    private function getAllPossibleColumnsNames(): array
    {
        $allColumnsConstants = array_filter(
            (new \ReflectionClass(RadioTable::class))->getConstants(),
            function($constantName){ return (0 === strpos($constantName, 'COLUMN_')); },
            ARRAY_FILTER_USE_KEY
        );

        return array_values($allColumnsConstants);
    }
}
