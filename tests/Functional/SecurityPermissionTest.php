<?php

namespace App\Tests\Functional;

use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityPermissionTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function onlyForLoggedInUrlProvider(): iterable
    {
        $urls = [
            '/utworz-wykaz',
            '/moje-wykazy',
            '/ustawienia-konta',
        ];

        foreach ($urls as $url) {
            yield $url => [$url];
        }
    }

    public function ownedByTestUserUrlProvider(): iterable
    {
        $urls = [
            '/wykaz/1/dodaj-stacje',
            '/wykaz/1/edytuj-stacje/1',
            '/wykaz/1/kopiuj-stacje/1',
            '/wykaz/1/ustawienia',
            '/wykaz/1/kolumny',
            '/wykaz/1/eksport',
            '/wykaz/1/eksport/html',
            '/wykaz/1/eksport/csv',
            '/wykaz/1/eksport/pdf',
            '/wykaz/1/eksport/xlsx',
            '/wykaz/1/eksport/ods',
        ];

        foreach ($urls as $url) {
            yield $url => [$url];
        }

        $postUrls = [
            '/wykaz/1/usun-stacje/1',
            '/wykaz/1/usun',
        ];

        foreach ($postUrls as $url) {
            yield $url => [$url, 'POST'];
        }
    }

    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function test_anonymous_user_cannot_access_restricted_page(string $url, string $method = 'GET'): void
    {
        $this->skipPdfGenerator($url);

        $this->client->request($method, $url);

        /** @var RedirectResponse */
        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());
    }

    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function test_logged_in_user_can_access_restricted_page(string $url, string $method = 'GET'): void
    {
        $this->skipPdfGenerator($url);

        $this->client->loginUserByName('test_user');
        $this->client->request($method, $url);

        // POST-only endpoints always redirect to another page.
        if ('POST' === $method) {
            $response = $this->client->getResponse();
            $this->assertSame(302, $response->getStatusCode());

            $this->client->followRedirect();
        }

        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function test_one_user_cannot_access_page_of_another_user(string $url, string $method = 'GET'): void
    {
        $this->skipPdfGenerator($url);

        $this->client->loginUserByName('test_user_second');
        $this->client->request($method, $url);

        $response = $this->client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * Skip PDF-related test if wkhtmltopdf is not installed.
     */
    private function skipPdfGenerator(string $url): void
    {
        if ('/wykaz/1/eksport/pdf' === $url && false === file_exists($_SERVER['WKHTMLTOPDF_PATH'])) {
            $this->markTestSkipped('wkhtmltopdf is not installed');
        }
    }
}
