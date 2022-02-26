<?php

namespace App\Tests\Functional;

use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class BasicApplicationTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function publicUrlsProvider(): iterable
    {
        $urls = [
            ['/'],
            ['/strona-glowna', '/'],
            ['/o-stronie'],
            ['/regulamin'],
            ['/kontakt'],
            ['/wszystkie-wykazy'],
            ['/wszystkie-wykazy/1'],
            ['/wszystkie-wykazy/2'],
            ['/wszystkie-wykazy/3'],
            ['/wszystkie-wykazy?a=1', '/wszystkie-wykazy'],
            ['/wszystkie-wykazy?a=2', '/wszystkie-wykazy/2'],
            ['/wszystkie-wykazy?a=3', '/wszystkie-wykazy/3'],
            ['/logowanie'],
            ['/rejestracja'],
            ['/wykaz/1'],
            ['/wykaz?id=1', '/wykaz/1'],
            ['/profil/test_user'],
            ['/profil?u=test_user', '/profil/test_user'],
            ['/szukaj?s=test'],
        ];

        foreach ($urls as $data) {
            yield $data[0] => $data;
        }
    }

    public function authenticatedUrlsProvider(): iterable
    {
        $urls = [
            ['/utworz-wykaz'],
            ['/moje-wykazy'],
            ['/ustawienia-konta'],
            ['/wyloguj', 'http://localhost/'],
            ['/wykaz/1/dodaj-stacje'],
            ['/wykaz/1/edytuj-stacje/1'],
            ['/wykaz/1/kopiuj-stacje/1'],
            ['/wykaz/1/ustawienia'],
            ['/wykaz/1/eksport', '/wykaz/1/ustawienia#export'],
            ['/wykaz/1/usun'],
        ];

        foreach ($urls as $data) {
            yield $data[0] => $data;
        }
    }

    /**
     * @dataProvider publicUrlsProvider
     */
    public function testPublicPage(string $url, string $redirectUrl = null): void
    {
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();
        $this->assertResponse($response, $redirectUrl);
    }

    /**
     * @dataProvider authenticatedUrlsProvider
     */
    public function testAuthenticatedPage(string $url, string $redirectUrl = null): void
    {
        $this->client->loginUserByName('test_user');
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();
        $this->assertResponse($response, $redirectUrl);
    }

    private function assertResponse(Response $response, string $redirectUrl = null): void
    {
        if ($redirectUrl) {
            /** @var RedirectResponse $response */
            $this->assertInstanceOf(RedirectResponse::class, $response);
            $this->assertSame($redirectUrl, $response->getTargetUrl());

            return;
        }

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('<!doctype html>', $response->getContent());
        $this->assertStringContainsString('</html>', $response->getContent());
    }
}
