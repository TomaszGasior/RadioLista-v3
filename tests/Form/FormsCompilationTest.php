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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class FormsCompilationTest extends KernelTestCase
{
    /** @var FormFactoryInterface */
    private $factory;

    /** @var RequestInterface */
    private $request;

    public function setUp(): void
    {
        $this->factory = self::getContainer()->get(FormFactoryInterface::class);
        $this->request = new Request;

        /** @var RequestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);

        // This is required for CSRF form extension.
        $this->request->setSession(new Session(new MockArraySessionStorage));
        $requestStack->push($this->request);
    }

    static public function formTypeAndEntityProvider(): iterable
    {
        /** @var EntityManagerInterface */
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

    /**
     * @dataProvider formTypeAndEntityProvider
     * @doesNotPerformAssertions
     */
    public function test_form_compiles_without_errors(string $formClass, $data = null, array $options = []): void
    {
        $form = $this->factory->create($formClass, $data, $options);

        $form->handleRequest($this->request);
        $form->createView();
    }
}
