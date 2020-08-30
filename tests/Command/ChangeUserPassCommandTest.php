<?php

namespace App\Tests\Command;

use App\Entity\User;
use Deployer\Component\PharUpdate\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ChangeUserPassCommandTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCommandChangesUserPassword()
    {
        $application = new Application(static::$kernel);

        // Make test faster by skipping cache:clear command.
        $application->add(new Command('cache:clear'));
        $application->setAutoExit(false);

        $command = $application->find('app:change-user-pass');

        $commandTester = new CommandTester($command);
        $commandTester->setInputs([
            'test_user',
            'NEW_PASSWORD_OF_TEST_USER',
        ]);

        // ChangeUserPassCommand changes User entity cache configuration
        // to workaround APCu cache warnings. This breaks other tests.
        $cache = static::$container->get(EntityManagerInterface::class)
            ->getClassMetadata(User::class)->cache;

        $commandTester->execute([]);

        static::$container->get(EntityManagerInterface::class)
            ->getClassMetadata(User::class)->cache = $cache;

        $crawler = $this->client->request('GET', '/moje-wykazy', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'NEW_PASSWORD_OF_TEST_USER',
        ]);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}
