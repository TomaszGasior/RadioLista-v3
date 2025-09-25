<?php

namespace App\Tests\Functional;

use App\Entity\RadioStation;
use App\Util\ReflectionUtilsTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class DateTimeRefreshTest extends WebTestCase
{
    use ReflectionUtilsTrait;

    public function test_radio_table_last_update_time_is_refreshed_when_modifying_radio_station_through_web_page(): void
    {
        [$radioTableOldLastUpdateTime, $radioTableNewLastUpdateTime] = $this->executeRadioStationUpdate('WebRequest');

        $this->assertGreaterThan($radioTableOldLastUpdateTime, $radioTableNewLastUpdateTime);
        $this->assertEquals((new DateTimeImmutable('now'))->format('Y-m-d'), $radioTableNewLastUpdateTime->format('Y-m-d'));
    }

    public function test_radio_table_last_update_time_is_not_refreshed_when_modifying_radio_station_through_console_command(): void
    {
        [$radioTableOldLastUpdateTime, $radioTableNewLastUpdateTime] = $this->executeRadioStationUpdate('CliCommand');

        $this->assertEquals($radioTableOldLastUpdateTime, $radioTableNewLastUpdateTime);
    }

    public function test_user_last_activity_date_is_refreshed_when_modifying_radio_station_through_web_page(): void
    {
        [,, $userOldLastActivityDate, $userNewLastActivityDate] = $this->executeRadioStationUpdate('WebRequest');

        $this->assertGreaterThan($userOldLastActivityDate, $userNewLastActivityDate);
        $this->assertEquals((new DateTimeImmutable('now'))->format('Y-m-d'), $userNewLastActivityDate->format('Y-m-d'));
    }

    public function test_user_last_activity_date_is_not_refreshed_when_modifying_radio_station_through_console_command(): void
    {
        [,, $userOldLastActivityDate, $userNewLastActivityDate] = $this->executeRadioStationUpdate('CliCommand');

        $this->assertEquals($userOldLastActivityDate, $userNewLastActivityDate);
    }

    private function executeRadioStationUpdate(string $executeThroughMethod): array
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        /** @var RadioStation */
        $radioStation = $entityManager->find(RadioStation::class, 1);

        $radioTableOldLastUpdateTime = new DateTimeImmutable('2012-07-01');
        $this->setPrivateFieldOfObject($radioStation->getRadioTable(), 'lastUpdateTime', $radioTableOldLastUpdateTime);

        $userOldLastActivityDate = new DateTimeImmutable('2012-06-26');
        $this->setPrivateFieldOfObject($radioStation->getRadioTable()->getOwner(), 'lastActivityDate', $userOldLastActivityDate);

        $entityManager->flush();

        self::ensureKernelShutdown();

        $this->{'executeThrough' . $executeThroughMethod}(
            function (ContainerInterface $container) {
                $entityManager = $container->get(EntityManagerInterface::class);

                /** @var RadioStation */
                $radioStation = $entityManager->find(RadioStation::class, 1);

                $radioStation->setFrequency('93.5');
                $radioStation->setName('CHANGED_RADIO_STATION_NAME');

                $entityManager->flush();
            }
        );

        self::bootKernel();

        /** @var RadioStation */
        $radioStation = $entityManager->find(RadioStation::class, 1);

        $this->assertEquals('93.5', $radioStation->getFrequency());
        $this->assertEquals('CHANGED_RADIO_STATION_NAME', $radioStation->getName());

        $radioTableNewLastUpdateTime = $radioStation->getRadioTable()->getLastUpdateTime();
        $userNewLastActivityDate = $radioStation->getRadioTable()->getOwner()->getLastActivityDate();

        return [$radioTableOldLastUpdateTime, $radioTableNewLastUpdateTime, $userOldLastActivityDate, $userNewLastActivityDate];
    }

    private function executeThroughWebRequest(callable $callback): void
    {
        $client = static::createClient();

        /** @var Router */
        $router = self::getContainer()->get(RouterInterface::class);

        $router->setOption('cache_dir', null);

        $router->getRouteCollection()->add(
            'test_route',
            new Route('/test_route', ['_controller' => function () use ($callback) {
                $callback(self::getContainer());

                return new Response('');
            }])
        );

        $client->request('GET', '/test_route');

        self::ensureKernelShutdown();
    }

    private function executeThroughCliCommand(callable $callback): void
    {
        self::bootKernel();

        $command = new Command('test_command');
        $command->setCode(function () use ($callback) {
            $callback(self::getContainer());
        });

        $application = new Application(static::$kernel);
        $application->add($command);
        $application->setAutoExit(false);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        self::ensureKernelShutdown();
    }
}
