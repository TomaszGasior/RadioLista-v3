<?php

namespace App\Tests\Functional;

use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityPermissionTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testAnonymousCantAccessRestrictedPages(string $url): void
    {
        $crawler = $this->client->request('GET', $url);

        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());
    }

    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testUserCanAccessRestrictedPages(string $url): void
    {
        $crawler = $this->client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testUserCantAccessPagesOfOtherUser(string $url): void
    {
        $crawler = $this->client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'test_user_second',
            'PHP_AUTH_PW' => 'test_user_second',
        ]);

        $response = $this->client->getResponse();
        $this->assertSame(404, $response->getStatusCode());
    }

    public function onlyForLoggedInUrlProvider(): array
    {
        return [
            ['/utworz-wykaz'],
            ['/moje-wykazy'],
            ['/ustawienia-konta'],
        ];
    }

    public function ownedByTestUserUrlProvider(): array
    {
        return [
            ['/wykaz/1/dodaj-stacje'],
            ['/wykaz/1/edytuj-stacje/1'],
            ['/wykaz/1/kopiuj-stacje/1'],
            ['/wykaz/1/usun-stacje/1'],
            ['/wykaz/1/ustawienia'],
            ['/wykaz/1/eksport/html'],
            ['/wykaz/1/eksport/csv'],
            ['/wykaz/1/usun'],
        ];
    }
}
