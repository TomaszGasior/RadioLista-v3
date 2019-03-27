<?php

namespace App\Tests\Controller;

use App\Entity\RadioTable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableControllerTest extends WebTestCase
{
    /**
     * @dataProvider radioTableProvider
     */
    public function testRadioTableWorks(RadioTable $radioTable, array $radioStations): void
    {
        $client = static::createClient();
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

    public function radioTableProvider(): array
    {
        self::bootKernel();
        $radioTableRepository = self::$container->get('App\Repository\RadioTableRepository');
        $radioStationRepository = self::$container->get('App\Repository\RadioStationRepository');

        $radioTable = $radioTableRepository->findOneBy([]);
        $radioStations = $radioStationRepository->findForRadioTable($radioTable);

        return [
            [$radioTable, $radioStations]
        ];
    }
}
