<?php

namespace App\Tests\Controller;

use App\Entity\RadioStation;
use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioStationControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        /** @var KernelBrowser */
        $this->client = static::createClient();

        $this->client->loginUserByName('test_user');
    }

    public function testAddRadioStation(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/dodaj-stacje');
        $form = $crawler->filter('form')->form();
        $form['radio_station_edit[frequency]'] = '91.25';
        $form['radio_station_edit[name]'] = 'EXAMPLE_RADIO_STATION_NAME_9125';
        $this->client->submit($form);

        $this->client->request('GET', '/wykaz/1');
        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('EXAMPLE_RADIO_STATION_NAME_9125', $content);
    }

    public function testEditRadioStation(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/edytuj-stacje/1');
        $form = $crawler->filter('form')->form();
        $form['radio_station_edit[name]'] = 'CHANGED_RADIO_STATION_NAME';
        $this->client->submit($form);

        $this->client->request('GET', '/wykaz/1');
        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('CHANGED_RADIO_STATION_NAME', $content);
        $this->assertStringNotContainsString('test_radio_station_name', $content);
    }

    public function testCopyRadioStation(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/kopiuj-stacje/1');
        $form = $crawler->filter('form')->form();
        $currentValue = $form['radio_station_edit[name]']->getValue();
        $form['radio_station_edit[name]'] = 'COPIED_RADIO_STATION_NAME';
        $this->client->submit($form);

        $this->client->request('GET', '/wykaz/1');
        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('COPIED_RADIO_STATION_NAME', $content);
    }

    public function testRemoveRadioStation(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/usun-stacje/1');
        $form = $crawler->filter('form[name="radio_station_remove"]')->form();
        $this->client->submit($form); // Radiostation is selected automatically.

        $this->client->request('GET', '/wykaz/1');
        $content = $this->client->getResponse()->getContent();
        $this->assertStringNotContainsString('test_radio_station_name', $content);
    }
}
