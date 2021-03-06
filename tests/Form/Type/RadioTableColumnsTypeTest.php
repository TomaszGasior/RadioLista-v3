<?php

namespace App\Tests\Form\Type;

use App\Entity\RadioTable;
use App\Form\DataTransformer\RadioTableColumnsTransformer;
use App\Form\Type\RadioTableColumnsType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class RadioTableColumnsTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $radioTableColumnsType = new RadioTableColumnsType(
            new RadioTableColumnsTransformer
        );

        return [
            new PreloadedExtension([$radioTableColumnsType], []),
        ];
    }

    public function dataProvider(): array
    {
        return [[
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
                RadioTable::COLUMN_PRIVATE_NUMBER,
                RadioTable::COLUMN_FREQUENCY,
                RadioTable::COLUMN_NAME,
                RadioTable::COLUMN_RADIO_GROUP,
                RadioTable::COLUMN_COUNTRY,
                RadioTable::COLUMN_LOCATION,
                RadioTable::COLUMN_POWER,
                RadioTable::COLUMN_POLARIZATION,
                RadioTable::COLUMN_TYPE,
                RadioTable::COLUMN_LOCALITY,
                RadioTable::COLUMN_QUALITY,
                RadioTable::COLUMN_RDS,
                RadioTable::COLUMN_COMMENT,
            ],
        ]];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRenderedFields(array $visibleColumns, array $allColumns): void
    {
        $form = $this->factory->create(RadioTableColumnsType::class, $visibleColumns);

        $view = $form->createView();
        $children = $view->children;

        foreach ($allColumns as $columnName) {
            $this->assertArrayHasKey($columnName, $children);

            $value = $view->children[$columnName]->vars['value'];

            if (in_array($columnName, $visibleColumns)) {
                $this->assertGreaterThan(0, $value);
            }
            else {
                $this->assertLessThan(0, $value);
            }
        }
    }
}
