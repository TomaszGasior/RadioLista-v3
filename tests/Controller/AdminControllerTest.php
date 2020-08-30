<?php

namespace App\Tests\Controller;

use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function urlProvider(): iterable
    {
        $urls = [
            '/admin',
            '/admin/dziennik',
            '/admin/wykazy',
            '/admin/uzytkownicy',
        ];

        foreach ($urls as $url) {
            yield $url => [$url];
        }
    }

    /**
     * @dataProvider urlProvider
     */
    public function testAdminPanelPage($url): void
    {
        $this->client->loginUserByName('test_user_admin');
        $this->client->request('GET', $url);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testAdminPanelInaccessibleForNonAdmin($url): void
    {
        $this->client->request('GET', $url);
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());

        $this->client->loginUserByName('test_user');
        $this->client->request('GET', $url);
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}
