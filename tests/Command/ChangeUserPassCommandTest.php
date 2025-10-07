<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ChangeUserPassCommandTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_command_changes_user_password(): void
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

        $commandTester->execute([]);

        $this->assertUserLogsIn('test_user', 'NEW_PASSWORD_OF_TEST_USER');
    }

    private function assertUserLogsIn(string $username, string $password): void
    {
        $crawler = $this->client->request('GET', '/logowanie');

        $form = $crawler->filter('form')->form();
        $form['security_login[username]'] = $username;
        $form['security_login[password]'] = $password;

        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/moje-wykazy');
        $response = $this->client->getResponse();
        $content = $crawler->filter('main')->html();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('test_radio_table_name', $content);
    }
}
