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
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/1/dodaj-stacje');
        $form = $crawler->filter('form')->form();
        $form['radio_station_edit[frequency]'] = '91.25';
        $form['radio_station_edit[name]'] = 'EXAMPLE_RADIO_STATION_NAME_9125';
        $client->submit($form);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $client->request('GET', '/wykaz/1');
        $content = $client->getResponse()->getContent();
        $this->assertContains('EXAMPLE_RADIO_STATION_NAME_9125', $content);
    }

    public function testEditRadioStation(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/1/edytuj-stacje/1');
        $form = $crawler->filter('form')->form();
        $form['radio_station_edit[name]'] = 'CHANGED_RADIO_STATION_NAME';
        $client->submit($form);

        $client->request('GET', '/wykaz/1');
        $content = $client->getResponse()->getContent();
        $this->assertContains('CHANGED_RADIO_STATION_NAME', $content);
        $this->assertNotContains('test_radio_station_name', $content);
    }

    public function testCopyRadioStation(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/1/kopiuj-stacje/1');
        $form = $crawler->filter('form')->form();
        $currentValue = $form['radio_station_edit[name]']->getValue();
        $form['radio_station_edit[name]'] = 'COPIED_RADIO_STATION_NAME';
        $client->submit($form);

        $client->request('GET', '/wykaz/1');
        $content = $client->getResponse()->getContent();
        $this->assertContains('COPIED_RADIO_STATION_NAME', $content);
    }

    public function testRemoveRadioStation(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/wykaz/1/usun-stacje/1');
        $form = $crawler->filter('form[name="radio_station_remove"]')->form();
        $client->submit($form); // Radiostation is selected automatically.

        $client->request('GET', '/wykaz/1');
        $content = $client->getResponse()->getContent();
        $this->assertNotContains('test_radio_station_name', $content);
    }
}
