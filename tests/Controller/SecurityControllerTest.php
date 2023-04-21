<?php

namespace App\Tests\Controller;

use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_user_can_log_in(): void
    {
        $this->loginUserThroughForm('test_user', 'test_password_user');

        $crawler = $this->client->request('GET', '/moje-wykazy');
        $response = $this->client->getResponse();
        $content = $crawler->filter('main')->html();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('test_radio_table_name', $content);
    }

    public function test_user_cannot_log_in_using_incorrect_password(): void
    {
        $this->loginUserThroughForm('test_user', 'incorrect password');

        $crawler = $this->client->request('GET', '/moje-wykazy');
        $response = $this->client->getResponse();
        $content = $crawler->html();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringNotContainsString('test_radio_table_name', $content);
    }

    public function test_user_can_log_out(): void
    {
        $this->loginUserThroughForm('test_user', 'test_password_user');

        $this->client->request('GET', '/moje-wykazy');
        $this->client->request('GET', '/wyloguj');

        $crawler = $this->client->request('GET', '/moje-wykazy');
        $response = $this->client->getResponse();
        $content = $crawler->html();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringNotContainsString('test_radio_table_name', $content);
    }

    private function loginUserThroughForm(string $username, $password): void
    {
        $crawler = $this->client->request('GET', '/logowanie');

        $form = $crawler->filter('form')->form();
        $form['security_login[username]'] = $username;
        $form['security_login[password]'] = $password;

        $this->client->submit($form);
    }
}
