<?php

namespace App\Tests\Entity\Enum\RadioTable;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioStation;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class ColumnTest extends TestCase
{
    private const RADIO_STATION_GETTERS_FOR_NO_COLUMNS = [
        'getId',
        'getRadioTable',
        'getAppearance',
    ];

    public function test_enum_values_match_to_getters_of_radiostation_entity(): void
    {
        foreach (Column::cases() as $column) {
            $methodName = 'get' . ucfirst($column->value);

            $this->assertTrue(
                method_exists(RadioStation::class, $methodName),
                sprintf('Missing getter %s() for %s enum.', $methodName, $column->name)
            );
        }
    }

    public function test_getters_of_radiostation_entity_match_to_enum_values(): void
    {
        $reflection = new ReflectionClass(RadioStation::class);

        $methodNames = array_diff(
            array_filter(
                array_map(
                    function (ReflectionMethod $method) { return $method->getName(); },
                    $reflection->getMethods(ReflectionMethod::IS_PUBLIC)
                ),
                function (string $methodName) { return str_starts_with($methodName, 'get'); }
            ),
            self::RADIO_STATION_GETTERS_FOR_NO_COLUMNS
        );

        foreach ($methodNames as $methodName) {
            $enumValue = lcfirst(substr($methodName, strlen('get')));

            $this->assertNotEmpty(
                Column::tryFrom($enumValue),
                sprintf('Missing enum value "%s" for getter %s().', $enumValue, $methodName)
            );
        }
    }
}
