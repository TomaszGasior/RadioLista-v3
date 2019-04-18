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

    public function testAddRadioTable(): void
    {
        $radioTable = $this->createRadioTable();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $client->request('GET', '/moje-wykazy');
        $content = $client->getResponse()->getContent();
        $this->assertContains($radioTable->getName(), $content);
    }

    public function testEditRadioTable(): void
    {
        $radioTable = $this->createRadioTable();

        $previousRadioTableName = $radioTable->getName();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId() . '/ustawienia');
        $form = $crawler->filter('form')->form();
        $form['radio_table_settings[name]'] = 'CHANGED_RADIOTABLE_NAME';
        $client->submit($form);

        $client->request('GET', '/moje-wykazy');
        $content = $client->getResponse()->getContent();
        $this->assertContains('CHANGED_RADIOTABLE_NAME', $content);
        $this->assertNotContains($previousRadioTableName, $content);
    }

    public function testRemoveRadioTable(): void
    {
        $radioTable = $this->createRadioTable();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId() . '/usun');
        $form = $crawler->filter('form[name="radio_table_remove"]')->form();
        $form['radio_table_remove[confirm]'] = '1';
        $client->submit($form);

        $client->request('GET', '/moje-wykazy');
        $content = $client->getResponse()->getContent();
        $this->assertNotContains($radioTable->getName(), $content);
    }

    private function createRadioTable(): RadioTable
    {
        self::bootKernel();
        $radioTableRepository = self::$container->get('App\Repository\RadioTableRepository');

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $crawler = $client->request('GET', '/utworz-wykaz');

        $form = $crawler->filter('form')->form();
        $form['radio_table_create[name]'] = 'EXAMPLE_RADIOTABLE_NAME';
        $client->submit($form);

        return $radioTableRepository->findOneByName('EXAMPLE_RADIOTABLE_NAME');
    }
}
