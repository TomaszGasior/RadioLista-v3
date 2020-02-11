<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider urlProvider
     */
    public function testAdminPanelSeemsToWork($url): void
    {
        $this->client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'test_user_admin',
            'PHP_AUTH_PW' => 'test_user_admin',
        ]);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testAdminPanelInaccessibleForNonAdmin($url): void
    {
        $this->client->request('GET', $url);
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function urlProvider(): array
    {
        return [
            ['/admin/dziennik'],
            ['/admin/wykazy'],
            ['/admin/uzytkownicy'],
        ];
    }
}
