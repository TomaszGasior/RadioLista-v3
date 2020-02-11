<?php

namespace App\Tests\Controller;

use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
    }

    public function testRadioTableRenderedProperly(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals('test_radio_table_name', $crawler->filter('h1')->text());

        $details = $crawler->filter('section.radiotable-details')->html();
        $this->assertContains('test_radio_table_description', $details);
        $this->assertContains('test_user', $details);
        $this->assertContains('2018-05-01', $details);

        $table = $crawler->filter('table')->html();
        foreach ([['test_radio_station_name', '100,95'], ['test_second_radio_station_name', '91,20']] as $data) {
            list($name, $frequency) = $data;
            $this->assertContains($name, $table);
            $this->assertContains($frequency, $table);
        }
    }

    public function testCreateRadioTable(): void
    {
        $crawler = $this->client->request('GET', '/utworz-wykaz');

        $form = $crawler->filter('form')->form();
        $form['radio_table_create[name]'] = 'EXAMPLE_RADIO_TABLE_NAME';
        $this->client->submit($form);

        $this->client->request('GET', '/moje-wykazy');
        $content = $this->client->getResponse()->getContent();
        $this->assertContains('EXAMPLE_RADIO_TABLE_NAME', $content);
    }

    public function testEditRadioTable(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/ustawienia');
        $form = $crawler->filter('form')->form();
        $form['radio_table_settings[name]'] = 'CHANGED_RADIO_TABLE_NAME';
        $this->client->submit($form);

        $this->client->request('GET', '/moje-wykazy');
        $content = $this->client->getResponse()->getContent();
        $this->assertContains('CHANGED_RADIO_TABLE_NAME', $content);
        $this->assertNotContains('test_radio_table_name', $content);
    }

    public function testRemoveRadioTable(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/usun');
        $form = $crawler->filter('form[name="radio_table_remove"]')->form();
        $form['radio_table_remove[confirm]'] = '1';
        $this->client->submit($form);

        $this->client->request('GET', '/moje-wykazy');
        $content = $this->client->getResponse()->getContent();
        $this->assertNotContains('test_radio_table_name', $content);
    }
}
