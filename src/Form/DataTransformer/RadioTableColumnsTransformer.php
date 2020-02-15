<?php

namespace App\Form\DataTransformer;

use App\Entity\RadioTable;
use App\Util\ReflectionUtilsTrait;
use Symfony\Component\Form\DataTransformerInterface;

// The following transformer is designed for RadioTable::$columns field.
// It's used by RadioTableColumnsType form field.
//
// * RadioTable::$columns contains array with values defining names
//   of columns shown in radio table. Order of array items determines
//   columns order in rendered radio table.
// * Array transformed for view purpose contain names of all possible columns
//   as keys and order numbers as values. Order number is defined as unique,
//   positive or negative number other than 0. Columns with negative order
//   number are not visible in radio table and should not be persisted in
//   RadioTable::$columns field.

class RadioTableColumnsTransformer implements DataTransformerInterface
{
    use ReflectionUtilsTrait;

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
        $allColumnsConstants = $this->getPrefixedConstantsOfClass(RadioTable::class, 'COLUMN_');

        return array_values($allColumnsConstants);
    }
}
