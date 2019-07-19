<?php

namespace App\Tests\Form\DataTransformer;

use App\Entity\RadioTable;
use App\Form\DataTransformer\RadioTableColumnsTransformer;
use PHPUnit\Framework\TestCase;

class RadioTableColumnsTransformerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testTransformFromDataToUi(array $visibleColumns, array $sortedColumns): void
    {
        $transformer = new RadioTableColumnsTransformer;

        $this->assertEquals($sortedColumns, $transformer->transform($visibleColumns));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testTransformFromUiToData(array $visibleColumns, array $sortedColumns): void
    {
        $transformer = new RadioTableColumnsTransformer;

        $this->assertEquals($visibleColumns, $transformer->reverseTransform($sortedColumns));
    }

    public function dataProvider(): array
    {
        return [
            // case #1
            [
                [
                    RadioTable::COLUMN_FREQUENCY,
                    RadioTable::COLUMN_NAME,
                    RadioTable::COLUMN_RADIO_GROUP,
                    RadioTable::COLUMN_LOCATION,
                    RadioTable::COLUMN_TYPE,
                    RadioTable::COLUMN_LOCALITY,
                    RadioTable::COLUMN_QUALITY,
                    RadioTable::COLUMN_RDS,
                ],
                [
                    RadioTable::COLUMN_FREQUENCY => 1,
                    RadioTable::COLUMN_NAME => 2,
                    RadioTable::COLUMN_RADIO_GROUP => 3,
                    RadioTable::COLUMN_LOCATION => 4,
                    RadioTable::COLUMN_TYPE => 5,
                    RadioTable::COLUMN_LOCALITY => 6,
                    RadioTable::COLUMN_QUALITY => 7,
                    RadioTable::COLUMN_RDS => 8,

                    RadioTable::COLUMN_POWER => -9,
                    RadioTable::COLUMN_POLARIZATION => -10,
                    RadioTable::COLUMN_COUNTRY => -11,
                    RadioTable::COLUMN_FIRST_LOG_DATE => -12,
                    RadioTable::COLUMN_RECEPTION => -13,
                    RadioTable::COLUMN_DISTANCE => -14,
                    RadioTable::COLUMN_RDS_PI => -15,
                    RadioTable::COLUMN_PRIVATE_NUMBER => -16,
                    RadioTable::COLUMN_COMMENT => -17,
                    RadioTable::COLUMN_EXTERNAL_ANCHOR => -18,
                ],
            ],

            // case #2
            [
                [
                    RadioTable::COLUMN_PRIVATE_NUMBER,
                    RadioTable::COLUMN_FREQUENCY,
                    RadioTable::COLUMN_NAME,
                    RadioTable::COLUMN_LOCATION,
                    RadioTable::COLUMN_POWER,
                    RadioTable::COLUMN_RADIO_GROUP,
                    RadioTable::COLUMN_POLARIZATION,
                    RadioTable::COLUMN_QUALITY,
                    RadioTable::COLUMN_RDS,
                    RadioTable::COLUMN_COMMENT,
                ],
                [
                    RadioTable::COLUMN_PRIVATE_NUMBER => 1,
                    RadioTable::COLUMN_FREQUENCY => 2,
                    RadioTable::COLUMN_NAME => 3,
                    RadioTable::COLUMN_LOCATION => 4,
                    RadioTable::COLUMN_POWER => 5,
                    RadioTable::COLUMN_RADIO_GROUP => 6,
                    RadioTable::COLUMN_POLARIZATION => 7,
                    RadioTable::COLUMN_QUALITY => 8,
                    RadioTable::COLUMN_RDS => 9,
                    RadioTable::COLUMN_COMMENT => 10,

                    RadioTable::COLUMN_COUNTRY => -11,
                    RadioTable::COLUMN_FIRST_LOG_DATE => -12,
                    RadioTable::COLUMN_RECEPTION => -13,
                    RadioTable::COLUMN_DISTANCE => -14,
                    RadioTable::COLUMN_RDS_PI => -15,
                    RadioTable::COLUMN_TYPE => -16,
                    RadioTable::COLUMN_LOCALITY => -17,
                    RadioTable::COLUMN_EXTERNAL_ANCHOR => -18,
                ],
            ],
        ];
    }
}
