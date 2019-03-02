<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableControllerTest extends WebTestCase
{
    public function testRadioTableWorks(): void
    {
        $client = static::createClient();

        $radioTableRepository = self::$container->get('App\\Repository\\RadioTableRepository');
        $radioStationRepository = self::$container->get('App\\Repository\\RadioStationRepository');

        $radioTable = $radioTableRepository->findOneBy([]);
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId());

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertEquals($radioTable->getName(), $crawler->filter('h1')->text());

        $details = $crawler->filter('section.radiotable-details')->html();
        $this->assertContains($radioTable->getDescription(), $details);
        $this->assertContains($radioTable->getOwner()->getName(), $details);
        $this->assertContains($radioTable->getLastUpdateTime()->format('Y-m-d'), $details);

        $table = $crawler->filter('table')->html();
        foreach ($radioStations as $radioStation) {
            $this->assertContains($radioStation->getName(), $table);

            $frequency = str_replace('.', ',', $radioStation->getFrequency());
            $this->assertContains($frequency, $table);
        }
    }
}
