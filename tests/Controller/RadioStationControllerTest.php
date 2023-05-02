<?php

namespace App\Tests\Controller;

use App\Entity\RadioStation;
use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
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
        $form['radio_station_add[frequency]'] = '91.25';
        $form['radio_station_add[name]'] = 'EXAMPLE_RADIO_STATION_NAME_9125';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/wykaz/1');
        $content = $crawler->filter('main')->html();
        $this->assertStringContainsString('EXAMPLE_RADIO_STATION_NAME_9125', $content);
    }

    public function test_user_can_modify_radio_station(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/edytuj-stacje/1');
        $form = $crawler->filter('form')->form();
        $form['radio_station_edit[name]'] = 'CHANGED_RADIO_STATION_NAME';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/wykaz/1');
        $content = $crawler->filter('main')->html();
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

        $crawler = $this->client->request('GET', '/wykaz/1');
        $content = $crawler->filter('main')->html();
        $this->assertStringContainsString('COPIED_RADIO_STATION_NAME', $content);
    }

    public function test_all_text_fields_are_empty_when_adding_radio_station(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/dodaj-stacje');

        $textFields = $crawler->filter('form input:not([type="hidden"]):not([type="checkbox"]), form textarea');
        $values = $textFields->each(function(Crawler $field) {
            return 'textarea' == $field->nodeName() ? $field->innerText() : $field->attr('value');
        });

        $nonEmptyValues = array_filter($values, function($value) { return null !== $value && '' !== $value; });
        $this->assertEmpty($nonEmptyValues);
    }

    public function test_not_all_text_fields_are_empty_when_copying_radio_station(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/kopiuj-stacje/1');

        $textFields = $crawler->filter('form input:not([type="hidden"]):not([type="checkbox"]), form textarea');
        $values = $textFields->each(function(Crawler $field) {
            return 'textarea' == $field->nodeName() ? $field->innerText() : $field->attr('value');
        });

        $nonEmptyValues = array_filter($values, function($value) { return null !== $value && '' !== $value; });
        $this->assertNotEmpty($nonEmptyValues);
    }

    public function test_only_name_and_frequency_are_required_by_HTML_form_when_adding_radio_station(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/dodaj-stacje');

        $fields = $crawler->filter('form input, form textarea, form select');
        $fields->each(function(Crawler $field) {
            $isRequired = $field->getNode(0)->hasAttribute('required');

            if (in_array($field->attr('name'), ['radio_station_add[frequency]', 'radio_station_add[name]'])) {
                return;
            }
            $this->assertFalse($isRequired, $field->attr('name'));
        });
    }

    public function test_user_can_remove_one_radio_station(): void
    {
        $crawler = $this->client->request('GET', '/wykaz/1/edytuj-stacje/1?remove=1');
        $form = $crawler->filter('.remove-dialog.no-JS-fallback form')->selectButton('Usuń')->form();
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/wykaz/1');
        $content = $crawler->filter('main')->html();
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
            $checkbox = $form['radio_station_bulk_remove[radioStationsToRemove]['.$i.']'];
            $checkbox->tick();
        }
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/wykaz/1');
        $content = $crawler->filter('main')->html();
        $this->assertStringNotContainsString('test_radio_station_name', $content);
        $this->assertStringNotContainsString('test_second_radio_station_name', $content);
        $this->assertStringContainsString('test_third_radio_station_name', $content);
    }
}
