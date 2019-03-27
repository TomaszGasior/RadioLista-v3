<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityPermissionTest extends WebTestCase
{
    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testAnonymousCantAccessRestrictedPages(string $url): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());
    }

    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testUserCanAccessRestrictedPages(string $url): void
    {
        $client = static::createClient([]);
        $crawler = $client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testUserCantAccessPagesOfOtherUser(string $url): void
    {
        $client = static::createClient([]);
        $crawler = $client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'radiolista',
            'PHP_AUTH_PW' => 'radiolista',
        ]);

        $response = $client->getResponse();
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
        self::bootKernel();
        $userRepository = self::$container->get('App\Repository\UserRepository');
        $radioTableRepository = self::$container->get('App\Repository\RadioTableRepository');
        $radioStationRepository = self::$container->get('App\Repository\RadioStationRepository');

        $user = $userRepository->findOneByName('test_user');
        $radioTable = $radioTableRepository->findAllOwnedByUser($user)[0];
        $radioStation = $radioStationRepository->findForRadioTable($radioTable)[0];

        return [
            ['/wykaz/' . $radioTable->getId() . '/dodaj-stacje'],
            ['/wykaz/' . $radioTable->getId() . '/edytuj-stacje/' . $radioStation->getId()],
            ['/wykaz/' . $radioTable->getId() . '/kopiuj-stacje/' . $radioStation->getId()],
            ['/wykaz/' . $radioTable->getId() . '/usun-stacje/' . $radioStation->getId()],
            ['/wykaz/' . $radioTable->getId() . '/ustawienia'],
            ['/wykaz/' . $radioTable->getId() . '/eksport/html'],
            ['/wykaz/' . $radioTable->getId() . '/eksport/csv'],
            ['/wykaz/' . $radioTable->getId() . '/usun'],
        ];
    }
}
