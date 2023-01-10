<?php

namespace App\Tests\Functional;

use App\Entity\RadioTable;
use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableStatusTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function publicStatusProvider(): iterable
    {
        yield 'status: public' => [RadioTable::STATUS_PUBLIC];
    }

    public function unlistedStatusProvider(): iterable
    {
        yield 'status: unlisted' => [RadioTable::STATUS_UNLISTED];
    }

    public function privateStatusProvider(): iterable
    {
        yield 'status: private' => [RadioTable::STATUS_PRIVATE];
    }

    /**
     * @dataProvider publicStatusProvider
     * @dataProvider unlistedStatusProvider
     */
    public function test_anonymous_user_can_access_public_radio_table($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider privateStatusProvider
     */
    public function test_anonymous_user_cannot_access_private_radio_table($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider publicStatusProvider
     * @dataProvider unlistedStatusProvider
     */
    public function test_user_can_access_public_radio_table_owned_by_another_user($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->loginUserByName('test_user_second');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider privateStatusProvider
     */
    public function test_user_cannot_access_private_radio_table_owned_by_another_user($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->loginUserByName('test_user_second');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider publicStatusProvider
     */
    public function test_public_radio_table_does_not_have_noindex_meta_tag($status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wykaz/1');

        $robotsTag = $crawler->filter('meta[name="robots"]');
        $this->assertCount(0, $robotsTag);
    }

    /**
     * @dataProvider unlistedStatusProvider
     * @dataProvider privateStatusProvider
     */
    public function test_private_radio_table_has_noindex_meta_tag($status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wykaz/1');

        $robotsTag = $crawler->filter('meta[name="robots"]');
        $this->assertStringContainsString('noindex', $robotsTag->attr('content'));
    }

    /**
     * @dataProvider publicStatusProvider
     * @dataProvider unlistedStatusProvider
     * @dataProvider privateStatusProvider
     */
    public function test_user_always_has_access_to_own_radio_table($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->loginUserByName('test_user');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider publicStatusProvider
     * @dataProvider unlistedStatusProvider
     * @dataProvider privateStatusProvider
     */
    public function test_administrator_always_has_access_to_radio_table($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->loginUserByName('test_user');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider publicStatusProvider
     */
    public function test_public_radio_table_is_visible_in_all_radio_tables_list($status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wszystkie-wykazy');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(1, $anchors);
    }

    /**
     * @dataProvider unlistedStatusProvider
     * @dataProvider privateStatusProvider
     */
    public function test_private_radio_table_is_not_visible_in_all_radio_tables_list($status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wszystkie-wykazy');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(0, $anchors);
    }

    /**
     * @dataProvider publicStatusProvider
     */
    public function test_public_radio_table_is_visible_in_owners_public_profile($status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/profil/test_user');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(1, $anchors);
    }

    /**
     * @dataProvider unlistedStatusProvider
     * @dataProvider privateStatusProvider
     */
    public function test_private_radio_table_is_not_visible_in_owners_public_profile($status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/profil/test_user');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(0, $anchors);
    }

    private function setRadioTableStatus($newStatus): void
    {
        $this->client->loginUserByName('test_user');
        $crawler = $this->client->request('GET', '/wykaz/1/ustawienia');

        $form = $crawler->filter('form')->form();
        $form['radio_table_settings[status]'] = $newStatus;
        $this->client->submit($form);

        $this->client->restart();
    }
}
