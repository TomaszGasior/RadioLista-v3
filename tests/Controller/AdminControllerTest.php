<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testAdminPanelSeemsToWork($url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user_admin',
            'PHP_AUTH_PW' => 'test_user_admin',
        ]);

        $client->request('GET', $url);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testAdminPanelInaccessibleForNonAdmin($url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $client->request('GET', $url);
        $this->assertSame(404, $client->getResponse()->getStatusCode());

        $client = static::createClient();

        $client->request('GET', $url);
        $this->assertSame(404, $client->getResponse()->getStatusCode());
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
