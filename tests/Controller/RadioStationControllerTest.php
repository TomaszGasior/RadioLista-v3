<?php

namespace App\Tests\Controller;

use App\Entity\RadioStation;
use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;

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

    public function test_user_can_add_radio_station(): void
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

    public function test_user_can_modify_radio_station(): void
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

    public function test_user_can_copy_radio_station(): void
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

    public function test_user_can_remove_one_radio_station(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/edytuj-stacje/1?remove=1');
        $form = $crawler->filter('.remove-dialog.no-JS-fallback form')->selectButton('Usuń')->form();
        $this->client->submit($form);

        $this->client->request('GET', '/wykaz/1');
        $content = $this->client->getResponse()->getContent();
        $this->assertStringNotContainsString('test_radio_station_name', $content);
        $this->assertStringContainsString('test_second_radio_station_name', $content);
        $this->assertStringContainsString('test_third_radio_station_name', $content);
    }

    public function test_user_can_remove_many_radio_stations_at_once(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/usun-stacje');
        $form = $crawler->filter('form[name="radio_station_bulk_remove"]')->form();
        foreach ([0, 1] as $i) {
            // In DomCrawler checkbox is chosen by order, not by input's "value".
            /** @var ChoiceFormField */
            $checkbox = $form['radio_station_bulk_remove[chosenToRemove]['.$i.']'];
            $checkbox->tick();
        }
        $this->client->submit($form);

        $this->client->request('GET', '/wykaz/1');
        $content = $this->client->getResponse()->getContent();
        $this->assertStringNotContainsString('test_radio_station_name', $content);
        $this->assertStringNotContainsString('test_second_radio_station_name', $content);
        $this->assertStringContainsString('test_third_radio_station_name', $content);
    }
}
