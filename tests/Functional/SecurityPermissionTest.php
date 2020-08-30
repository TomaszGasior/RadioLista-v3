<?php

namespace App\Tests\Functional;

use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
            '/wykaz/1/usun-stacje/1',
            '/wykaz/1/ustawienia',
            '/wykaz/1/eksport/html',
            '/wykaz/1/eksport/csv',
            '/wykaz/1/eksport/pdf',
            '/wykaz/1/usun',
        ];

        foreach ($urls as $url) {
            yield $url => [$url];
        }
    }

    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testAnonymousCantAccessRestrictedPage(string $url): void
    {
        $this->skipPdfGenerator($url);

        $this->client->request('GET', $url);

        /** @var RedirectResponse */
        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());
    }

    /**
     * @dataProvider onlyForLoggedInUrlProvider
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testUserCanAccessRestrictedPage(string $url): void
    {
        $this->skipPdfGenerator($url);

        $this->client->loginUserByName('test_user');
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @dataProvider ownedByTestUserUrlProvider
     */
    public function testUserCantAccessPageOfOtherUser(string $url): void
    {
        $this->skipPdfGenerator($url);

        $this->client->loginUserByName('test_user_second');
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * Skip PDF related test if wkhtmltopdf is not installed.
     */
    private function skipPdfGenerator(string $url): void
    {
        if ('/wykaz/1/eksport/pdf' === $url && false === file_exists($_SERVER['WKHTMLTOPDF_PATH'])) {
            $this->markTestSkipped('wkhtmltopdf is not installed');
        }
    }
}
