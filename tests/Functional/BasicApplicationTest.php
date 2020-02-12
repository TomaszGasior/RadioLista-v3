<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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

    /**
     * @dataProvider publicUrlsProvider
     */
    public function testPublicPagesSeemToWork(string $url, string $redirectUrl = null): void
    {
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();
        $this->assertResponse($response, $redirectUrl);
    }

    public function publicUrlsProvider(): iterable
    {
        return [
            [''],
            ['/strona-glowna'],
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
    }

    /**
     * @dataProvider authenticatedUrlsProvider
     */
    public function testAuthenticatedPagesSeemToWork(string $url, string $redirectUrl = null): void
    {
        $this->client->request('GET', $url, [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, $redirectUrl);
    }

    public function authenticatedUrlsProvider(): array
    {
        return [
            ['/utworz-wykaz'],
            ['/moje-wykazy'],
            ['/ustawienia-konta'],
            ['/wykaz/1/dodaj-stacje'],
            ['/wykaz/1/edytuj-stacje/1'],
            ['/wykaz/1/kopiuj-stacje/1'],
            ['/wykaz/1/usun-stacje/1'],
            ['/wykaz/1/ustawienia'],
            ['/wykaz/1/eksport', '/wykaz/1/ustawienia#export'],
            ['/wykaz/1/usun'],
        ];
    }

    private function assertResponse(Response $response, string $redirectUrl = null): void
    {
        if ($redirectUrl) {
            /** @var RedirectResponse */
            $response = $response;

            $this->assertInstanceOf(RedirectResponse::class, $response);
            $this->assertSame($redirectUrl, $response->getTargetUrl());

            return;
        }

        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('<!doctype html>', $response->getContent());
        $this->assertContains('</html>', $response->getContent());
    }
}
