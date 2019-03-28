<?php

namespace App\Tests\Form;

use App\Form\ContactFormType;
use App\Form\DataTransformer\RadioTableColumnsTransformer;
use App\Form\RadioStationEditType;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableRemoveType;
use App\Form\RadioTableSearchType;
use App\Form\RadioTableSettingsType;
use App\Form\SecurityLoginType;
use App\Form\Type\RadioTableColumnsType;
use App\Form\UserRegisterType;
use App\Form\UserSettingsType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class BasicFormsTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $validator = Validation::createValidator();

        $radioTableColumnsType = new RadioTableColumnsType(
            $this->createMock(RadioTableColumnsTransformer::class)
        );

        return [
            new ValidatorExtension($validator),
            new PreloadedExtension([$radioTableColumnsType], []),
        ];
    }

    /**
     * @dataProvider formTypesProvider
     */
    public function testBuildForms(string $formClass): void
    {
        $this->factory->create($formClass);

        $this->assertTrue(true);
    }

    public function formTypesProvider(): array
    {
        return [
            [RadioTableCreateType::class],
            [RadioTableRemoveType::class],
            [RadioTableSearchType::class],
            [RadioTableSettingsType::class],
            [RadioStationEditType::class],
            [UserRegisterType::class],
            [UserSettingsType::class],
            [SecurityLoginType::class],
            [ContactFormType::class],

            // Don't test RadioStationRemoveType — dependencies
            // on Doctrine form types makes it hard to test.
        ];
    }
}
