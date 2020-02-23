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

    /**
     * @dataProvider dataProvider
     */
    public function testRenderedFields(array $visibleColumns, array $columnLabels): void
    {
        $form = $this->factory->create(
            RadioTableColumnsType::class, $visibleColumns
        );

        $view = $form->createView();
        $children = $view->children;

        foreach ($columnLabels as $columnName => $columnLabel) {
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
                RadioTable::COLUMN_PRIVATE_NUMBER  => 'Numer w odbiorniku',
                RadioTable::COLUMN_FREQUENCY  => 'Częstotliwość',
                RadioTable::COLUMN_NAME  => 'Nazwa',
                RadioTable::COLUMN_RADIO_GROUP  => 'Grupa medialna',
                RadioTable::COLUMN_COUNTRY  => 'Kraj',
                RadioTable::COLUMN_LOCATION  => 'Lokalizacja nadajnika',
                RadioTable::COLUMN_POWER  => 'Moc nadajnika',
                RadioTable::COLUMN_POLARIZATION  => 'Polaryzacja',
                RadioTable::COLUMN_TYPE  => 'Rodzaj programu',
                RadioTable::COLUMN_LOCALITY  => 'Lokalność programu',
                RadioTable::COLUMN_QUALITY  => 'Jakość odbioru',
                RadioTable::COLUMN_RDS  => 'RDS',
                RadioTable::COLUMN_COMMENT  => 'Komentarz',
            ],
        ]];
    }
}
