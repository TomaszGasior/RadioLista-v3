<?php

namespace App\Tests\Controller;

use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_sitemap_contains_URLs(): void
    {
        $this->client->request('GET', '/sitemap.xml');
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('<loc>http://localhost/wykaz/1</loc>', $response->getContent());
        $this->assertStringContainsString('<loc>http://localhost/profil/test_user</loc>', $response->getContent());
    }
}
