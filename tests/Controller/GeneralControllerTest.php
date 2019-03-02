<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeneralControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testGeneralPagesWork($url): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function urlProvider(): array
    {
        return [
            [''],
            ['/strona-glowna'],
            ['/o-stronie'],
            ['/regulamin'],
            ['/kontakt'],
            ['/wszystkie-wykazy'],
            ['/wszystkie-wykazy/2'],
            ['/wszystkie-wykazy/3'],
        ];
    }
}
