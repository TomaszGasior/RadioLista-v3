<?php

namespace App\Tests\Functional;

use App\Entity\RadioTable;
use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableStatusTest extends WebTestCase
{
    /**
     * @dataProvider statusAndHttpCodeProvider
     */
    public function testRadioTablePublicAccess($status,
                                               int $expectedHttpCode,
                                               bool $expectedNoIndexTag): void
    {
        $this->setRadioTableStatus($status);

        $client = static::createClient();
        $crawler = $client->request('GET', '/wykaz/1');

        $this->assertSame($expectedHttpCode, $client->getResponse()->getStatusCode());

        $robotsTag = $crawler->filter('meta[name="robots"]');
        if ($expectedNoIndexTag) {
            $this->assertContains('noindex', $robotsTag->attr('content'));
        }
        else {
            $this->assertCount(0, $robotsTag);
        }

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user_second',
            'PHP_AUTH_PW' => 'test_user_second',
        ]);
        $client->request('GET', '/wykaz/1');

        $this->assertSame($expectedHttpCode, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider statusAndHttpCodeProvider
     */
    public function testOwnerAlwaysHasAccess($status): void
    {
        $this->setRadioTableStatus($status);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $client->request('GET', '/wykaz/1');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function statusAndHttpCodeProvider(): array
    {
        return [
            [RadioTable::STATUS_PUBLIC, 200, false],
            [RadioTable::STATUS_UNLISTED, 200, true],
            [RadioTable::STATUS_PRIVATE, 404, true],
        ];
    }

    /**
     * @dataProvider statusAndAnchorVisibilityProvider
     */
    public function testVisibilityInAllRadioTablesList($status,
                                                       bool $expectedVisibleAnchor): void
    {
        $this->setRadioTableStatus($status);

        $client = static::createClient();
        $crawler = $client->request('GET', '/wszystkie-wykazy');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');

        $this->assertCount($expectedVisibleAnchor ? 1 : 0, $anchors);
    }

    /**
     * @dataProvider statusAndAnchorVisibilityProvider
     */
    public function testVisibilityInOwnerPublicProfile($status,
                                                       bool $expectedVisibleAnchor): void
    {
        $this->setRadioTableStatus($status);

        $client = static::createClient();
        $crawler = $client->request('GET', '/profil/test_user');

        $anchors = $crawler->filter('a[href="/wykaz/1"]');

        $this->assertCount($expectedVisibleAnchor ? 1 : 0, $anchors);
    }

    public function statusAndAnchorVisibilityProvider(): array
    {
        return [
            [RadioTable::STATUS_PUBLIC, true],
            [RadioTable::STATUS_UNLISTED, false],
            [RadioTable::STATUS_PRIVATE, false],
        ];
    }

    private function setRadioTableStatus($newStatus): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $crawler = $client->request('GET', '/wykaz/1/ustawienia');

        $form = $crawler->filter('form')->form();
        $form['radio_table_settings[status]'] = $newStatus;
        $client->submit($form);
    }
}
