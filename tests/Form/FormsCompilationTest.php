<?php

namespace App\Tests\Form;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Form\RadioStationAddType;
use App\Form\RadioStationEditType;
use App\Form\RadioStationBulkRemoveType;
use App\Form\RadioTableCreateType;
use App\Form\RadioTableSearchType;
use App\Form\RadioTableSettingsType;
use App\Form\SecurityLoginType;
use App\Form\UserRegisterType;
use App\Form\UserSettingsType;
use App\Tests\CsrfTokenTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FormsCompilationTest extends KernelTestCase
{
    use CsrfTokenTrait;

    static public function formTypeAndEntityProvider(): iterable
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $radioTable = $entityManager->find(RadioTable::class, 1);
        $radioStation = $entityManager->find(RadioStation::class, 1);
        $user = $entityManager->find(User::class, 1);

        self::ensureKernelShutdown();

        yield 'RadioStationAddType' => [RadioStationAddType::class, null, ['radio_table' => $radioTable]];
        yield 'RadioStationEditType' => [RadioStationEditType::class, $radioStation];
        yield 'RadioStationBulkRemoveType' => [RadioStationBulkRemoveType::class, null, ['radio_table' => $radioTable]];
        yield 'RadioTableCreateType' => [RadioTableCreateType::class, null, ['owner' => $user]];
        yield 'RadioTableSearchType' => [RadioTableSearchType::class];
        yield 'RadioTableSettingsType' => [RadioTableSettingsType::class, $radioTable];
        yield 'SecurityLoginType' => [SecurityLoginType::class];
        yield 'UserRegisterType' => [UserRegisterType::class];
        yield 'UserSettingsType' => [UserSettingsType::class, $user];
    }

    #[DataProvider('formTypeAndEntityProvider')]
    #[DoesNotPerformAssertions]
    public function test_form_compiles_without_errors(string $formClass, $data = null, array $options = []): void
    {
        $factory = self::getContainer()->get(FormFactoryInterface::class);
        $this->stubCsrfTokenManager();

        $form = $factory->create($formClass, $data, $options);

        $form->handleRequest(new Request);
        $form->createView();
    }
}
