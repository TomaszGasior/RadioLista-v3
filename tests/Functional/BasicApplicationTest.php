<?php

namespace App\Tests\Functional;

use App\Tests\KernelBrowser;
use PHPUnit\Framework\Attributes\DataProvider;
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

    static public function publicUrlsProvider(): iterable
    {
        $urls = [
            ['/'],
            ['/en'],

            ['/o-stronie'],
            ['/about'],

            ['/regulamin'],

            ['/kontakt'],
            ['/contact'],

            ['/wszystkie-wykazy'],
            ['/all-lists'],

            ['/wszystkie-wykazy/1'],
            ['/all-lists/1'],

            ['/wszystkie-wykazy/2'],
            ['/all-lists/2'],

            ['/wszystkie-wykazy/3'],
            ['/all-lists/3'],

            ['/logowanie'],
            ['/login'],

            ['/rejestracja'],
            ['/register'],

            ['/wykaz/1'],
            ['/list/1'],

            ['/profil/test_user'],
            ['/profile/test_user'],

            ['/szukaj?s=test'],
            ['/search?s=test'],
        ];

        foreach ($urls as $data) {
            yield $data[0] => $data;
        }
    }

    static public function rlv2PublicUrlsProvider(): iterable
    {
        $urls = [
            ['/strona-glowna', '/'],
            ['/wszystkie-wykazy?a=1', '/wszystkie-wykazy'],
            ['/wszystkie-wykazy?a=2', '/wszystkie-wykazy/2'],
            ['/wszystkie-wykazy?a=3', '/wszystkie-wykazy/3'],
            ['/wykaz?id=1', '/wykaz/1'],
            ['/profil?u=test_user', '/profil/test_user'],
        ];

        foreach ($urls as $data) {
            yield $data[0] => $data;
        }
    }

    static public function authenticatedUrlsProvider(): iterable
    {
        $urls = [
            ['/utworz-wykaz'],
            ['/create-list'],

            ['/moje-wykazy'],
            ['/my-lists'],

            ['/ustawienia-konta'],
            ['/account-settings'],

            ['/wyloguj', 'http://localhost/'],
            ['/logout', 'http://localhost/en'],

            ['/wykaz/1/dodaj-stacje'],
            ['/list/1/add-station'],

            ['/wykaz/1/edytuj-stacje/1'],
            ['/list/1/edit-station/1'],

            ['/wykaz/1/kopiuj-stacje/1'],
            ['/list/1/copy-station/1'],

            ['/wykaz/1/usun-stacje/1', '/wykaz/1', 'POST'],
            ['/list/1/delete-station/1', '/list/1', 'POST'],

            ['/wykaz/1/ustawienia'],
            ['/list/1/settings'],

            ['/wykaz/1/kolumny'],
            ['/list/1/columns'],

            ['/wykaz/1/eksport'],
            ['/list/1/export'],

            ['/wykaz/1/usun', '/moje-wykazy', 'POST'],
            ['/list/1/delete', '/my-lists', 'POST'],
        ];

        foreach ($urls as $data) {
            yield $data[0] => $data;
        }
    }

    #[DataProvider('publicUrlsProvider')]
    #[DataProvider('rlv2PublicUrlsProvider')]
    public function test_public_page_seems_to_be_working(string $url, ?string $redirectUrl = null, string $method = 'GET'): void
    {
        $this->client->request($method, $url);

        $response = $this->client->getResponse();
        $this->assertResponse($response, $redirectUrl);
    }

    #[DataProvider('authenticatedUrlsProvider')]
    public function test_authenticated_page_seems_to_be_working(string $url, ?string $redirectUrl = null, string $method = 'GET'): void
    {
        $this->client->loginUserByName('test_user');
        $this->client->request($method, $url);

        $response = $this->client->getResponse();
        $this->assertResponse($response, $redirectUrl);
    }

    private function assertResponse(Response $response, ?string $redirectUrl = null): void
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
