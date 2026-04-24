<?php

namespace App\Tests\Entity\Enum\RadioTable;

use App\Entity\Enum\RadioTable\Column;
use App\Model\Row;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

class ColumnTest extends TestCase
{
    private const ROW_PROPERTIES_FOR_NO_COLUMNS = [
        'appearance',
        'rowEditRoute',
    ];

    public function test_enum_values_match_properties_of_row_model(): void
    {
        foreach (Column::cases() as $column) {
            $this->assertTrue(
                property_exists(Row::class, $column->value),
                sprintf('Missing property $%s for %s enum.', $column->value, $column->name)
            );
        }
    }

    public function test_properties_of_row_model_match_enum_values(): void
    {
        $reflection = new ReflectionClass(Row::class);

        $propertyNames = array_diff(
            array_map(
                function (ReflectionProperty $property) { return $property->getName(); },
                $reflection->getProperties(ReflectionProperty::IS_PUBLIC)
            ),
            self::ROW_PROPERTIES_FOR_NO_COLUMNS
        );

        foreach ($propertyNames as $propertyName) {
            $this->assertNotEmpty(
                Column::tryFrom($propertyName),
                sprintf('Missing enum value "%s" for property $%s.', $propertyName, $propertyName)
            );
        }
    }
}
