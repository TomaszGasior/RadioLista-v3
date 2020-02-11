<?php

namespace App\Tests\Functional;

use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
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
        yield ['/wykaz/1'];
        yield ['/profil/test_user'];
    }

    /**
     * @dataProvider authenticatedUrlsProvider
     */
    public function testAuthenticatedPagesSeemToWork(string $url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('<!doctype html>', $response->getContent());
        $this->assertContains('</html>', $response->getContent());
    }

    public function authenticatedUrlsProvider(): array
    {
        // Radiotable and radiostation specific pages are tested in SecurityPermissionTest.
        return [
            ['/utworz-wykaz'],
            ['/moje-wykazy'],
            ['/ustawienia-konta'],
        ];
    }

    public function testSitemapSeemsToWork(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $client->request('GET', '/sitemap.xml');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }
}
