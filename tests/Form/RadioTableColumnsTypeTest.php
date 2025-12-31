<?php

namespace App\Tests\Form;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Form\DataTransformer\RadioTableColumnsTransformer;
use App\Form\RadioTableColumnsType;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
class RadioTableColumnsTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $radioTableColumnsType = new RadioTableColumnsType(
            new RadioTableColumnsTransformer
        );

        return [
            new PreloadedExtension([$radioTableColumnsType], []),
        ];
    }

    public function test_form_contains_fields_for_all_columns(): void
    {
        [$visibleColumns, $allColumnNames] = $this->getVisibleColumnsAndAllColumns();

        $radioTable = (new RadioTable('Name', new User('Name')))
            ->setColumns($visibleColumns)
        ;
        $form = $this->factory->create(RadioTableColumnsType::class, $radioTable);

        $view = $form->createView();
        $children = $view->children['columns'];

        foreach ($allColumnNames as $columnName) {
            $this->assertArrayHasKey($columnName, $children);
        }
    }

    public function test_field_values_are_negative_for_hidden_and_positive_for_visible_columns(): void
    {
        [$visibleColumns, $allColumnNames] = $this->getVisibleColumnsAndAllColumns();

        $radioTable = (new RadioTable('Name', new User('Name')))
            ->setColumns($visibleColumns)
        ;
        $form = $this->factory->create(RadioTableColumnsType::class, $radioTable);

        $view = $form->createView();
        $children = $view->children['columns'];

        foreach ($allColumnNames as $columnName) {
            $value = $children[$columnName]->vars['value'];

            if (in_array(Column::from($columnName), $visibleColumns)) {
                $this->assertGreaterThan(0, $value, $columnName);
            }
            else {
                $this->assertLessThan(0, $value, $columnName);
            }
        }
    }

    private function getVisibleColumnsAndAllColumns(): array
    {
        return [
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
                Column::PRIVATE_NUMBER->value,
                Column::FREQUENCY->value,
                Column::NAME->value,
                Column::RADIO_GROUP->value,
                Column::COUNTRY->value,
                Column::LOCATION->value,
                Column::POWER->value,
                Column::POLARIZATION->value,
                Column::TYPE->value,
                Column::QUALITY->value,
                Column::RDS->value,
                Column::COMMENT->value,
            ],
        ];
    }
}
