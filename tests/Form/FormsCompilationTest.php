<?php

namespace App\Tests\Form;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Form\RadioStationEditType;
use App\Form\RadioStationRemoveType;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableRemoveType;
use App\Form\RadioTableSearchType;
use App\Form\RadioTableSettingsType;
use App\Form\SecurityLoginType;
use App\Form\UserRegisterType;
use App\Form\UserSettingsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FormsCompilationTest extends KernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();

        $this->factory = self::$container->get(FormFactoryInterface::class);
    }

    /**
     * @dataProvider formTypeAndEntityProvider
     * @doesNotPerformAssertions
     */
    public function testCompileForm(string $formClass, $data = null, array $options = []): void
    {
        $form = $this->factory->create($formClass, $data, $options);

        $form->handleRequest(new Request);
        $form->createView();
    }

    public function formTypeAndEntityProvider(): iterable
    {
        $radioStation = (new RadioStation)->setRadioTable(new RadioTable);
        yield [RadioStationEditType::class, $radioStation];

        self::bootKernel();
        $radioTable = self::$container->get(EntityManagerInterface::class)->find(RadioTable::class, 1);
        self::ensureKernelShutdown();
        yield [RadioStationRemoveType::class, null, ['radio_table' => $radioTable]];

        yield [RadioTableCreateType::class, new RadioTable];
        yield [RadioTableRemoveType::class];
        yield [RadioTableSearchType::class];
        yield [RadioTableSettingsType::class, new RadioTable];
        yield [SecurityLoginType::class];
        yield [UserRegisterType::class, new User];
        yield [UserSettingsType::class, new User];
    }
}
