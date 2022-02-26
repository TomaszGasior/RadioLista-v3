<?php

namespace App\Tests\Form;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Form\RadioStationEditType;
use App\Form\RadioStationBulkRemoveType;
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

    public function formTypeAndEntityProvider(): iterable
    {
        self::bootKernel();
        $entityManager = self::$container->get(EntityManagerInterface::class);

        $radioTable = $entityManager->find(RadioTable::class, 1);
        $radioStation = $entityManager->find(RadioStation::class, 1);
        $user = $entityManager->find(User::class, 1);

        self::ensureKernelShutdown();

        yield 'RadioStationEditType' => [RadioStationEditType::class, $radioStation];
        yield 'RadioStationBulkRemoveType' => [RadioStationBulkRemoveType::class, null, ['radio_table' => $radioTable]];
        yield 'RadioTableCreateType' => [RadioTableCreateType::class, $radioTable];
        yield 'RadioTableRemoveType' => [RadioTableRemoveType::class];
        yield 'RadioTableSearchType' => [RadioTableSearchType::class];
        yield 'RadioTableSettingsType' => [RadioTableSettingsType::class, $radioTable];
        yield 'SecurityLoginType' => [SecurityLoginType::class];
        yield 'UserRegisterType' => [UserRegisterType::class, new User];
        yield 'UserSettingsType' => [UserSettingsType::class, $user];
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
}
