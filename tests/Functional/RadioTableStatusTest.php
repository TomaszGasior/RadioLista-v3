<?php

namespace App\Tests\Functional;

use App\Entity\Enum\RadioTable\Status;
use App\Tests\LoginUserTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableStatusTest extends WebTestCase
{
    use LoginUserTrait;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    static public function publicStatusProvider(): iterable
    {
        yield 'status: public' => [Status::PUBLIC];
    }

    static public function unlistedStatusProvider(): iterable
    {
        yield 'status: unlisted' => [Status::UNLISTED];
    }

    static public function privateStatusProvider(): iterable
    {
        yield 'status: private' => [Status::PRIVATE];
    }

    #[DataProvider('publicStatusProvider')]
    #[DataProvider('unlistedStatusProvider')]
    public function test_anonymous_user_can_access_public_radio_table(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('privateStatusProvider')]
    public function test_anonymous_user_cannot_access_private_radio_table(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('publicStatusProvider')]
    #[DataProvider('unlistedStatusProvider')]
    public function test_user_can_access_public_radio_table_owned_by_another_user(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $this->loginUserByName($this->client, 'test_user_second');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('privateStatusProvider')]
    public function test_user_cannot_access_private_radio_table_owned_by_another_user(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $this->loginUserByName($this->client, 'test_user_second');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('publicStatusProvider')]
    public function test_public_radio_table_does_not_have_noindex_meta_tag(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wykaz/1');

        $robotsTag = $crawler->filter('meta[name="robots"]');
        $this->assertCount(0, $robotsTag);
    }

    #[DataProvider('unlistedStatusProvider')]
    #[DataProvider('privateStatusProvider')]
    public function test_private_radio_table_has_noindex_meta_tag(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $this->loginUserByName($this->client, 'test_user');
        $crawler = $this->client->request('GET', '/wykaz/1');

        $robotsTag = $crawler->filter('meta[name="robots"]');
        $this->assertStringContainsString('noindex', $robotsTag->attr('content'));
    }

    #[DataProvider('publicStatusProvider')]
    #[DataProvider('unlistedStatusProvider')]
    #[DataProvider('privateStatusProvider')]
    public function test_user_always_has_access_to_own_radio_table(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $this->loginUserByName($this->client, 'test_user');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('publicStatusProvider')]
    #[DataProvider('unlistedStatusProvider')]
    #[DataProvider('privateStatusProvider')]
    public function test_administrator_always_has_access_to_radio_table(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $this->loginUserByName($this->client, 'test_user_admin');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('publicStatusProvider')]
    public function test_public_radio_table_is_visible_in_all_radio_tables_list(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wszystkie-wykazy');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(1, $anchors);
    }

    #[DataProvider('unlistedStatusProvider')]
    #[DataProvider('privateStatusProvider')]
    public function test_private_radio_table_is_not_visible_in_all_radio_tables_list(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wszystkie-wykazy');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(0, $anchors);
    }

    #[DataProvider('publicStatusProvider')]
    public function test_public_radio_table_is_visible_in_owners_public_profile(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/profil/test_user');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(1, $anchors);
    }

    #[DataProvider('unlistedStatusProvider')]
    #[DataProvider('privateStatusProvider')]
    public function test_private_radio_table_is_not_visible_in_owners_public_profile(Status $status): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/profil/test_user');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');
        $this->assertCount(0, $anchors);
    }

    private function setRadioTableStatus(Status $newStatus): void
    {
        $this->loginUserByName($this->client, 'test_user');
        $crawler = $this->client->request('GET', '/wykaz/1/ustawienia');

        $form = $crawler->filter('form')->form();
        $form['radio_table_settings[status]'] = $newStatus->value;
        $this->client->submit($form);

        $this->client->restart();
    }
}
