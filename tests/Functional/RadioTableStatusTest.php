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

    public function statusAndHttpCodeProvider(): array
    {
        return [
            'public' => [RadioTable::STATUS_PUBLIC, 200, false],
            'unlisted' => [RadioTable::STATUS_UNLISTED, 200, true],
            'private' => [RadioTable::STATUS_PRIVATE, 404, true],
        ];
    }

    public function statusAndAnchorVisibilityProvider(): array
    {
        return [
            'public' => [RadioTable::STATUS_PUBLIC, true],
            'unlisted' => [RadioTable::STATUS_UNLISTED, false],
            'private' => [RadioTable::STATUS_PRIVATE, false],
        ];
    }

    /**
     * @dataProvider statusAndHttpCodeProvider
     */
    public function testRadioTablePublicAccess($status, int $expectedHttpCode,
                                               bool $expectedNoIndexTag): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wykaz/1');

        $this->assertSame($expectedHttpCode, $this->client->getResponse()->getStatusCode());

        $robotsTag = $crawler->filter('meta[name="robots"]');
        if ($expectedNoIndexTag) {
            $this->assertStringContainsString('noindex', $robotsTag->attr('content'));
        }
        else {
            $this->assertCount(0, $robotsTag);
        }

        $this->client->loginUserByName('test_user_second');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame($expectedHttpCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider statusAndHttpCodeProvider
     */
    public function testOwnerAlwaysHasAccess($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->loginUserByName('test_user');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider statusAndHttpCodeProvider
     */
    public function testAdminAlwaysHasAccess($status): void
    {
        $this->setRadioTableStatus($status);

        $this->client->loginUserByName('test_user');
        $this->client->request('GET', '/wykaz/1');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider statusAndAnchorVisibilityProvider
     */
    public function testVisibilityInAllRadioTablesList($status, bool $expectedVisibleAnchor): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/wszystkie-wykazy');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');

        $this->assertCount($expectedVisibleAnchor ? 1 : 0, $anchors);
    }

    /**
     * @dataProvider statusAndAnchorVisibilityProvider
     */
    public function testVisibilityInOwnerPublicProfile($status, bool $expectedVisibleAnchor): void
    {
        $this->setRadioTableStatus($status);

        $crawler = $this->client->request('GET', '/profil/test_user');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');

        $this->assertCount($expectedVisibleAnchor ? 1 : 0, $anchors);
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
