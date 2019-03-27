<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicApplicationTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testApplicationSeemsToWork(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('<!doctype html>', $response->getContent());
        $this->assertContains('<html', $response->getContent());
        $this->assertContains('<body', $response->getContent());
        $this->assertContains('</body>', $response->getContent());
        $this->assertContains('</html>', $response->getContent());
    }

    public function urlProvider(): array
    {
        self::bootKernel();
        $userRepository = self::$container->get('App\Repository\UserRepository');
        $radioTableRepository = self::$container->get('App\Repository\RadioTableRepository');

        $radioTables = $radioTableRepository->findPublicOrderedByLastUpdateTime(10);
        $users = $userRepository->findByPublicProfile(true, null, 10);

        $basicURLs = [
            [''],
            ['/strona-glowna'],
            ['/o-stronie'],
            ['/regulamin'],
            ['/kontakt'],
            ['/wszystkie-wykazy'],
            ['/wszystkie-wykazy/2'],
            ['/wszystkie-wykazy/3'],
            ['/logowanie'],
            ['/rejestracja'],
        ];

        $radioTableURLs = [];
        $usersURLs = [];

        foreach ($radioTables as $radioTable) {
            $radioTableURLs[] = ['/wykaz/' . $radioTable->getId()];
        }
        foreach ($users as $user) {
            $userURLs[] = ['/profil/' . $user->getName()];
        }

        return array_merge($basicURLs, $radioTableURLs, $userURLs);
    }
}
