<?php

namespace App\Tests\Controller;

use App\Tests\LoginUserTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableControllerTest extends WebTestCase
{
    use LoginUserTrait;

    private KernelBrowser $client;

    public function setUp(): void
    {
        /** @var KernelBrowser */
        $this->client = static::createClient();

        $this->loginUserByName($this->client, 'test_user');
    }

    public function test_radio_table_is_properly_rendered_and_contains_stations(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals('test_radio_table_name', $crawler->filter('h1')->text());

        $details = $crawler->filter('.radio-table-details')->html();
        $this->assertStringContainsString('test_radio_table_description', $details);
        $this->assertStringContainsString('test_user', $details);
        $this->assertStringContainsString('2018-05-01', $details);

        $table = $crawler->filter('table')->html();
        foreach ([['test_radio_station_name', '100,95'], ['test_second_radio_station_name', '91,20']] as $data) {
            list($name, $frequency) = $data;
            $this->assertStringContainsString($name, $table);
            $this->assertStringContainsString($frequency, $table);
        }
    }

    public function test_user_can_create_radio_table(): void
    {
        $crawler = $this->client->request('GET', '/utworz-wykaz');

        $form = $crawler->filter('form')->form();
        $form['radio_table_create[name]'] = 'EXAMPLE_RADIO_TABLE_NAME';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/moje-wykazy');
        $content = $crawler->filter('main')->html();
        $this->assertStringContainsString('EXAMPLE_RADIO_TABLE_NAME', $content);
    }

    public function test_user_can_modify_radio_table(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/ustawienia');
        $form = $crawler->filter('form')->form();
        $form['radio_table_settings[name]'] = 'CHANGED_RADIO_TABLE_NAME';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/moje-wykazy');
        $content = $crawler->filter('main')->html();
        $this->assertStringContainsString('CHANGED_RADIO_TABLE_NAME', $content);
        $this->assertStringNotContainsString('test_radio_table_name', $content);
    }

    public function test_user_can_remove_radio_table(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/ustawienia?remove=1');
        $form = $crawler->filter('.remove-dialog.no-JS-fallback form')->selectButton('Usuń')->form();
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/moje-wykazy');
        $content = $crawler->filter('main')->html();
        $this->assertStringNotContainsString('test_radio_table_name', $content);
    }
}
