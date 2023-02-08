<?php

namespace App\Tests\Form\DataTransformer;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioTable;
use App\Form\DataTransformer\RadioTableColumnsTransformer;
use PHPUnit\Framework\TestCase;

class RadioTableColumnsTransformerTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'case #1' => [
                [
                    Column::FREQUENCY,
                    Column::NAME,
                    Column::RADIO_GROUP,
                    Column::LOCATION,
                    Column::TYPE,
                    Column::QUALITY,
                    Column::RDS,
                ],
                [
                    Column::FREQUENCY->value => 1,
                    Column::NAME->value => 2,
                    Column::RADIO_GROUP->value => 3,
                    Column::LOCATION->value => 4,
                    Column::TYPE->value => 5,
                    Column::QUALITY->value => 6,
                    Column::RDS->value => 7,

                    Column::POWER->value => -8,
                    Column::POLARIZATION->value => -9,
                    Column::MULTIPLEX->value => -10,
                    Column::DAB_CHANNEL->value => -11,
                    Column::COUNTRY->value => -12,
                    Column::REGION->value => -13,
                    Column::FIRST_LOG_DATE->value => -14,
                    Column::RECEPTION->value => -15,
                    Column::DISTANCE->value => -16,
                    Column::MAX_SIGNAL_LEVEL->value => -17,
                    Column::RDS_PI->value => -18,
                    Column::PRIVATE_NUMBER->value => -19,
                    Column::COMMENT->value => -20,
                    Column::EXTERNAL_ANCHOR->value => -21,
                ],
            ],

            'case #2' => [
                [
                    Column::PRIVATE_NUMBER,
                    Column::FREQUENCY,
                    Column::NAME,
                    Column::LOCATION,
                    Column::POWER,
                    Column::RADIO_GROUP,
                    Column::POLARIZATION,
                    Column::QUALITY,
                    Column::RDS,
                    Column::COMMENT,
                ],
                [
                    Column::PRIVATE_NUMBER->value => 1,
                    Column::FREQUENCY->value => 2,
                    Column::NAME->value => 3,
                    Column::LOCATION->value => 4,
                    Column::POWER->value => 5,
                    Column::RADIO_GROUP->value => 6,
                    Column::POLARIZATION->value => 7,
                    Column::QUALITY->value => 8,
                    Column::RDS->value => 9,
                    Column::COMMENT->value => 10,

                    Column::MULTIPLEX->value => -11,
                    Column::DAB_CHANNEL->value => -12,
                    Column::COUNTRY->value => -13,
                    Column::REGION->value => -14,
                    Column::FIRST_LOG_DATE->value => -15,
                    Column::RECEPTION->value => -16,
                    Column::DISTANCE->value => -17,
                    Column::MAX_SIGNAL_LEVEL->value => -18,
                    Column::RDS_PI->value => -19,
                    Column::TYPE->value => -20,
                    Column::EXTERNAL_ANCHOR->value => -21,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function test_transforms_from_internal_structure_to_user_input(array $visibleColumns, array $sortedColumns): void
    {
        $transformer = new RadioTableColumnsTransformer;

        $this->assertEquals($sortedColumns, $transformer->transform($visibleColumns));
    }

    /**
     * @dataProvider dataProvider
     */
    public function test_transforms_from_user_input_to_internal_structure(array $visibleColumns, array $sortedColumns): void
    {
        $transformer = new RadioTableColumnsTransformer;

        $this->assertEquals($visibleColumns, $transformer->reverseTransform($sortedColumns));
    }
}
