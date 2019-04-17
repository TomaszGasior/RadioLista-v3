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
use FOS\CKEditorBundle\Config\CKEditorConfigurationInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HtmlSanitizer\Bundle\Form\TextTypeExtension as HtmlSanitizerExtension;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\Test\Traits\ValidatorExtensionTrait;
use Symfony\Component\Form\Test\TypeTestCase;

class BasicFormsTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected function getTypes(): array
    {
        return [
            new RadioTableColumnsType(
                $this->createMock(RadioTableColumnsTransformer::class)
            ),
            new CKEditorType(
                $this->createMock(CKEditorConfigurationInterface::class)
            ),
        ];
    }

    protected function getTypeExtensions(): array
    {
        return [
            new HtmlSanitizerExtension(
                $this->createMock(ContainerInterface::class), ''
            ),
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
        ];

        // Don't test RadioStationRemoveType — dependencies on EntityType
        // and Doctrine repository makes it impossible to test.
    }
}
