<?php

namespace App\Util;

use App\Entity\Enum\RadioTable\Column;
use App\Model\Row;
use Collator;

class RadioTableRowSorter
{
    /**
     * @param Row[] $rows
     */
    public function sort(array &$rows, Column $sorting): void
    {
        $collator = new Collator('');
        $collator->setStrength(Collator::SECONDARY);

        usort($rows, function (Row $a, Row $b) use ($sorting, $collator): int {
            switch ($sorting) {
                case Column::NAME:
                    $result = $collator->compare($a->name, $b->name);

                    if (0 !== $result) {
                        return $result;
                    }
                    break;

                case Column::PRIVATE_NUMBER:
                    // Move radio stations without private number to the end of the radio table.
                    $result = ($a->privateNumber ?? PHP_INT_MAX) <=> ($b->privateNumber ?? PHP_INT_MAX);

                    if (0 !== $result) {
                        return $result;
                    }
                    break;
            }

            return (float) $a->frequency <=> (float) $b->frequency;
        });
    }
}
