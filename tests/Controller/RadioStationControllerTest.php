<?php

namespace App\Tests\Controller;

use App\Entity\RadioStation;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioStationControllerTest extends WebTestCase
{
    public function testAddRadioStation(): void
    {
        $radioStation = $this->createRadioStation();
        $radioTable = $radioStation->getRadioTable();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $client->request('GET', '/wykaz/' . $radioTable->getId());
        $content = $client->getResponse()->getContent();
        $this->assertContains($radioStation->getName(), $content);
    }

    public function testEditRadioStation(): void
    {
        $radioStation = $this->createRadioStation();
        $radioTable = $radioStation->getRadioTable();

        $previousRadioStationName = $radioStation->getName();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId() . '/edytuj-stacje/' . $radioStation->getId());
        $form = $crawler->filter('form')->form();
        $form['radio_station_edit[name]'] = 'CHANGED_RADIOSTATION_NAME';
        $client->submit($form);

        $client->request('GET', '/wykaz/' . $radioTable->getId());
        $content = $client->getResponse()->getContent();
        $this->assertContains('CHANGED_RADIOSTATION_NAME', $content);
        $this->assertNotContains($previousRadioStationName, $content);
    }

    public function testCopyRadioStation(): void
    {
        $radioStation = $this->createRadioStation();
        $radioTable = $radioStation->getRadioTable();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId() . '/kopiuj-stacje/' . $radioStation->getId());
        $form = $crawler->filter('form')->form();
        $currentValue = $form['radio_station_edit[name]']->getValue();
        $form['radio_station_edit[name]'] = $currentValue . '___COPIED';
        $client->submit($form);

        $client->request('GET', '/wykaz/' . $radioTable->getId());
        $content = $client->getResponse()->getContent();
        $this->assertContains($radioStation->getName() . '___COPIED', $content);
    }

    public function testRemoveRadioStation(): void
    {
        $radioStation = $this->createRadioStation();
        $radioTable = $radioStation->getRadioTable();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId() . '/usun-stacje/' . $radioStation->getId());
        $form = $crawler->filter('form[name="radio_station_remove"]')->form();
        // Radiostation is selected automatically.
        $client->submit($form);

        $client->request('GET', '/wykaz/' . $radioTable->getId());
        $content = $client->getResponse()->getContent();
        $this->assertNotContains($radioStation->getName(), $content);
    }

    private function createRadioStation(): RadioStation
    {
        self::bootKernel();
        $radioTableRepository = self::$container->get(RadioTableRepository::class);
        $radioStationRepository = self::$container->get(RadioStationRepository::class);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/utworz-wykaz');
        $form = $crawler->filter('form')->form();
        $form['radio_table_create[name]'] = 'EXAMPLE_RADIOTABLE_NAME';
        $client->submit($form);

        $radioTable = $radioTableRepository->findOneByName('EXAMPLE_RADIOTABLE_NAME');

        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId() . '/dodaj-stacje');
        $form = $crawler->filter('form')->form();
        $form['radio_station_edit[frequency]'] = '100.00';
        $form['radio_station_edit[name]'] = 'EXAMPLE_RADIOSTATION_NAME';
        $client->submit($form);

        return $radioStationRepository->findOneByName('EXAMPLE_RADIOSTATION_NAME');
    }
}
