<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicApplicationTest extends WebTestCase
{
    /**
     * @dataProvider publicUrlsProvider
     */
    public function testPublicPagesSeemToWork(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('<!doctype html>', $response->getContent());
        $this->assertContains('</html>', $response->getContent());
    }

    public function publicUrlsProvider(): iterable
    {
        self::bootKernel();
        $userRepository = self::$container->get('App\Repository\UserRepository');
        $radioTableRepository = self::$container->get('App\Repository\RadioTableRepository');

        $radioTables = $radioTableRepository->findPublicOrderedByLastUpdateTime(10);
        $users = $userRepository->findByPublicProfile(true, null, 10);

        yield [''];
        yield ['/strona-glowna'];
        yield ['/o-stronie'];
        yield ['/regulamin'];
        yield ['/kontakt'];
        yield ['/wszystkie-wykazy'];
        yield ['/wszystkie-wykazy/2'];
        yield ['/wszystkie-wykazy/3'];
        yield ['/logowanie'];
        yield ['/rejestracja'];

        foreach ($radioTables as $radioTable) {
            yield ['/wykaz/' . $radioTable->getId()];
        }
        foreach ($users as $user) {
            yield ['/profil/' . $user->getName()];
        }
    }

    /**
     * @dataProvider authenticatedUrlsProvider
     */
    public function testAuthenticatedPagesSeemToWork(string $url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'radiolista',
            'PHP_AUTH_PW' => 'radiolista',
        ]);
        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('<!doctype html>', $response->getContent());
        $this->assertContains('</html>', $response->getContent());
    }

    public function authenticatedUrlsProvider(): array
    {
        return [
            ['/utworz-wykaz'],
            ['/moje-wykazy'],
            ['/ustawienia-konta'],
        ];
    }

    public function testSitemapSeemsToWork(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'radiolista',
            'PHP_AUTH_PW' => 'radiolista',
        ]);
        $client->request('GET', '/sitemap.xml');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }
}
